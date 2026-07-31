<script setup>
import { computed, ref, watch } from 'vue';
import vueFilePond from 'vue-filepond';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import 'filepond/dist/filepond.min.css';
import { ensureFolders, getUploadOptions, uploadFile } from '../data/live';

const FilePond = vueFilePond(FilePondPluginFileValidateSize);

const props = defineProps({
  paused: Boolean,
});

const emit = defineEmits(['added', 'progress', 'attempt', 'retrying', 'done', 'failed', 'removed']);
const pond = ref(null);
const resumeWaiters = [];
const releasedIds = new Set();
const uploadOptions = getUploadOptions();
const maxParallelUploads = computed(() => Math.max(1, Number(uploadOptions.maxParallel) || 3));
const maxFileSize = computed(() => `${Math.max(1, Math.floor((Number(uploadOptions.maxFileSize) || (1024 * 1024 * 1024)) / (1024 * 1024)))}MB`);

function waitForResume() {
  if (!props.paused) return Promise.resolve();
  return new Promise((resolve) => resumeWaiters.push(resolve));
}

watch(() => props.paused, (paused) => {
  if (!paused) resumeWaiters.splice(0).forEach((resolve) => resolve());
});

function metadataFor(file) {
  return {
    storage: file.getMetadata('storage'),
    path: file.getMetadata('path') || '',
    relativePath: file.getMetadata('relativePath') || '',
    idempotencyKey: file.getMetadata('idempotencyKey') || '',
  };
}

function makeUploadKey() {
  return window.crypto?.randomUUID?.() || `filepond-${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

const server = {
  process(fieldName, file, metadata, load, error, progress, abort) {
    let cancelled = false;
    let cancelUpload = () => {};
    const target = {
      storage: metadata.storage,
      path: metadata.path || '',
      relativePath: metadata.relativePath || '',
      idempotencyKey: metadata.idempotencyKey || '',
    };

    void (async () => {
      try {
        await waitForResume();
        if (target.relativePath.includes('/')) {
          const folderPath = target.relativePath.split('/').slice(0, -1).join('/');
          target.path = await ensureFolders(target.storage, target.path ? `${target.path}/${folderPath}` : folderPath);
        }
        const asset = await uploadFile(file, {
          storage: target.storage,
          path: target.path,
          idempotencyKey: target.idempotencyKey,
          onProgress: (percentage) => progress(true, Math.round(file.size * (percentage / 100)), file.size),
          waitForResume,
          onAbort: (abortRequest) => { cancelUpload = abortRequest; },
          onAttempt: ({ attempt, maxAttempts }) => {
            if (!cancelled) emit('attempt', { id: file.id, attempt, maxAttempts });
          },
          onRetry: ({ attempt, maxAttempts, delayMs, error }) => {
            if (!cancelled) emit('retrying', {
              id: file.id,
              attempt,
              maxAttempts,
              retryAt: Date.now() + delayMs,
              error: error?.message || 'Upload sementara gagal.',
            });
          },
        });
        if (!cancelled) load(JSON.stringify(asset));
      } catch (uploadError) {
        if (!cancelled) error(uploadError.message || 'Upload gagal.');
      }
    })();

    return {
      abort: () => {
        cancelled = true;
        cancelUpload();
        abort();
      },
    };
  },
};

function handleAddFile(addError, file) {
  if (addError || !file) {
    emit('failed', { id: file?.id, message: addError?.main || 'File tidak dapat ditambahkan.' });
    return;
  }
  const target = metadataFor(file);
  emit('added', {
    id: file.id,
    name: file.filename,
    size: file.fileSize,
    storage: target.storage,
    path: target.path,
  });
}

function handleProgress(file, progress) {
  emit('progress', { id: file.id, progress: Math.round(progress * 100) });
}

function handleProcessFile(processError, file) {
  if (!file) return;
  if (processError) {
    emit('failed', { id: file.id, message: processError.main || 'Upload gagal.' });
    return;
  }

  let asset = null;
  try {
    asset = JSON.parse(file.serverId || '{}');
  } catch {
    emit('failed', { id: file.id, message: 'Respons upload tidak valid.' });
    return;
  }
  emit('done', { id: file.id, asset, storage: metadataFor(file).storage });
  releasedIds.add(file.id);
  window.queueMicrotask(() => pond.value?.removeFile(file.id));
}

function handleRemoveFile(removeError, file) {
  if (removeError || !file) return;
  if (releasedIds.delete(file.id)) return;
  emit('removed', { id: file.id });
}

function addFiles(fileList, target) {
  for (const file of Array.from(fileList || [])) {
    void pond.value?.addFile(file, {
      metadata: {
        storage: target.storage,
        path: target.path || '',
        relativePath: file.webkitRelativePath || '',
        idempotencyKey: makeUploadKey(),
      },
    });
  }
}

function retryFile(id) {
  return pond.value?.processFile(id);
}

function removeFile(id) {
  return pond.value?.removeFile(id);
}

defineExpose({ addFiles, retryFile, removeFile });
</script>

<template>
  <FilePond
    ref="pond"
    name="file"
    class-name="filemanager-v2-filepond-engine"
    :allow-multiple="true"
    :allow-revert="false"
    :instant-upload="true"
    :max-parallel-uploads="maxParallelUploads"
    :max-file-size="maxFileSize"
    :server="server"
    @addfile="handleAddFile"
    @processfileprogress="handleProgress"
    @processfile="handleProcessFile"
    @removefile="handleRemoveFile"
  />
</template>

<style scoped>
:deep(.filemanager-v2-filepond-engine) {
  position: fixed;
  width: 1px;
  height: 1px;
  overflow: hidden;
  opacity: 0;
  pointer-events: none;
}
</style>
