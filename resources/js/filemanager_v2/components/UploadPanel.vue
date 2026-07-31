<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import uploadFilePlaceholder from '../assets/file-upload-placeholder.svg';

const props = defineProps({
  uploads: Array,
  minimized: Boolean,
  paused: Boolean,
});

const emit = defineEmits(['toggle-minimize', 'toggle-pause', 'close', 'remove', 'retry', 'retry-failed']);
const completed = computed(() => props.uploads.filter((item) => item.status === 'done').length);
const failed = computed(() => props.uploads.filter((item) => item.status === 'error').length);
const hasPendingUploads = computed(() => props.uploads.some((item) => ['queued', 'uploading', 'retrying'].includes(item.status)));
const retryClock = ref(Date.now());
let retryClockTimer = null;
onMounted(() => {
  retryClockTimer = window.setInterval(() => {
    if (props.uploads.some((item) => item.status === 'retrying')) retryClock.value = Date.now();
  }, 250);
});
onBeforeUnmount(() => {
  if (retryClockTimer) window.clearInterval(retryClockTimer);
});
const retrySecondsRemaining = (item) => Math.max(0, Math.ceil((Number(item.retryAt || retryClock.value) - retryClock.value) / 1000));
const overall = computed(() => {
  if (!props.uploads.length) return 0;
  return Math.round(props.uploads.reduce((total, item) => total + item.progress, 0) / props.uploads.length);
});
const visibleUploads = computed(() => {
  const visible = [];
  const statuses = ['uploading', 'retrying', 'error', 'queued', 'done', 'cancelled'];

  for (const status of statuses) {
    for (const item of props.uploads) {
      if (item.status !== status) continue;
      visible.push(item);
      if (visible.length === 120) return visible;
    }
  }

  return visible;
});
const hiddenUploads = computed(() => Math.max(0, props.uploads.length - visibleUploads.value.length));
</script>

<template>
  <section class="upload-panel" :class="{ minimized }">
    <header>
      <div class="upload-title">
        <span class="upload-state-icon"><i class="bi bi-cloud-arrow-up"></i></span>
        <span>
          <strong>{{ overall === 100 ? 'Upload complete' : `Uploading ${uploads.length} items` }}</strong>
          <small>{{ completed }} of {{ uploads.length }} complete · {{ overall }}%</small>
        </span>
      </div>
      <div class="upload-window-actions">
        <button @click="emit('toggle-minimize')" :title="minimized ? 'Expand' : 'Minimize'">
          <i class="bi" :class="minimized ? 'bi-chevron-up' : 'bi-dash-lg'"></i>
        </button>
        <button @click="emit('close')" title="Close"><i class="bi bi-x-lg"></i></button>
      </div>
    </header>

    <div v-if="!minimized" class="upload-body">
      <div class="upload-summary">
        <div class="progress"><div class="progress-bar" :style="{ width: overall + '%' }"></div></div>
        <div class="upload-summary-actions">
          <button v-if="failed" type="button" :disabled="hasPendingUploads" :title="hasPendingUploads ? 'Tunggu antrean upload selesai' : `Upload ulang ${failed} file gagal`" @click="emit('retry-failed')">
            <i class="bi bi-arrow-repeat"></i> Retry failed ({{ failed }})
          </button>
          <button v-if="hasPendingUploads" type="button" @click="emit('toggle-pause')">
            <i class="bi" :class="paused ? 'bi-play-fill' : 'bi-pause-fill'"></i>
            {{ paused ? 'Resume all' : 'Pause all' }}
          </button>
        </div>
      </div>
      <div class="upload-list">
        <div v-for="item in visibleUploads" :key="item.id" class="upload-item">
          <div class="upload-file-icon">
            <img class="upload-file-placeholder" :src="uploadFilePlaceholder" alt="" aria-hidden="true">
          </div>
          <div class="upload-item-copy">
            <strong>{{ item.name }}</strong>
            <small v-if="item.status === 'error'" class="text-danger">{{ item.error }}</small>
            <small v-else-if="item.status === 'retrying'" class="text-warning"><i class="bi bi-arrow-repeat"></i> Retrying {{ item.attempt }}/{{ item.maxAttempts }} &middot; next attempt in {{ retrySecondsRemaining(item) }}s &middot; {{ item.error }}</small>
            <small v-else-if="item.status === 'done'" class="text-success">Uploaded · {{ item.size }}</small>
            <small v-else-if="item.status === 'queued'" class="text-muted">Waiting in queue &middot; {{ item.size }}</small>
            <small v-else><template v-if="item.attempt > 1">Retry {{ item.attempt }}/{{ item.maxAttempts }} &middot; </template>{{ item.size }} &middot; {{ item.progress }}%</small>
            <div v-if="['uploading', 'retrying'].includes(item.status)" class="progress">
              <div class="progress-bar" :style="{ width: item.progress + '%' }"></div>
            </div>
          </div>
          <div class="upload-item-status">
            <button v-if="item.status === 'done'" type="button" title="Delete uploaded file" aria-label="Delete uploaded file" @click="emit('remove', item)"><i class="bi bi-arrow-counterclockwise"></i></button>
            <button v-else-if="item.status === 'error'" type="button" :disabled="item.folderBatch && hasPendingUploads" title="Retry file" @click="emit('retry', item)"><i class="bi bi-arrow-clockwise"></i></button>
            <button v-else @click="emit('remove', item)"><i class="bi bi-x"></i></button>
          </div>
        </div>
      </div>
      <p v-if="hiddenUploads" class="upload-list-more">Showing active items and the first 120 of {{ uploads.length }} files.</p>
    </div>
  </section>
</template>
