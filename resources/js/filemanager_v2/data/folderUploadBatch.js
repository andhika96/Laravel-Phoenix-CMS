import { beginFolderUploadBatch, completeFolderUploadBatch, uploadFile } from './live';

function relativeParent(file) {
  const relativePath = String(file.webkitRelativePath || file.name || '').replace(/\\/g, '/');
  const parts = relativePath.split('/').filter(Boolean);
  parts.pop();

  return parts.join('/');
}

function targetPath(basePath, relativePath) {
  return [basePath, relativePath].filter(Boolean).join('/');
}

function makeId() {
  return window.crypto?.randomUUID?.() || `folder-${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

function terminal(status) {
  return ['done', 'error', 'cancelled'].includes(status);
}

/**
 * Coordinates a single directory selection without FilePond's static queue.
 * New work is admitted whenever `getMaxParallel()` changes, so settings apply
 * to the remaining queue without cancelling requests already in flight.
 */
export function createFolderUploadCoordinator({
  files,
  storage,
  path = '',
  getMaxParallel,
  getMaxAttempts,
  onItemAdded = () => {},
  onItemUpdated = () => {},
  onItemDone = () => {},
  onStartError = () => {},
  onFinished = () => {},
}) {
  const maximumAttempts = () => Math.max(1, Number(getMaxAttempts?.() || 5));
  const jobs = Array.from(files, (file) => ({
    id: makeId(),
    file,
    name: file.name,
    bytes: Number(file.size || 0),
    relativeParent: relativeParent(file),
    path: targetPath(path, relativeParent(file)),
    progress: 0,
    status: 'queued',
    error: '',
    attempt: 1,
    maxAttempts: maximumAttempts(),
    retryAt: null,
    abort: null,
    cancelRequested: false,
    useBatchReservation: true,
  }));
  const queue = [...jobs];
  const resumeWaiters = new Set();
  let batch = null;
  let active = 0;
  let paused = false;
  let started = false;
  let preflightComplete = false;
  let batchCompleted = false;
  let finalizing = false;

  const update = (job, changes) => {
    Object.assign(job, changes);
    onItemUpdated({ ...job });
  };

  const waitForResume = () => {
    if (!paused) return Promise.resolve();

    return new Promise((resolve) => resumeWaiters.add(resolve));
  };

  const releasePauseWaiters = () => {
    resumeWaiters.forEach((resolve) => resolve());
    resumeWaiters.clear();
  };

  const finishIfIdle = async () => {
    if (!preflightComplete || finalizing || active > 0 || jobs.some((job) => !terminal(job.status))) return;
    finalizing = true;

    try {
      if (!batchCompleted && batch?.id) {
        await completeFolderUploadBatch(batch.id);
        batchCompleted = true;
      }
    } finally {
      finalizing = false;
      onFinished({ batch, jobs: jobs.map((job) => ({ ...job })) });
    }
  };

  const process = async (job) => {
    active += 1;
    update(job, { status: 'uploading', error: '', retryAt: null });

    try {
      const asset = await uploadFile(job.file, {
        storage,
        path: job.path,
        batchId: job.useBatchReservation ? batch?.id : null,
        idempotencyKey: job.id,
        waitForResume,
        onProgress: (progress) => update(job, { progress }),
        onAttempt: ({ attempt, maxAttempts }) => update(job, { status: 'uploading', attempt, maxAttempts, retryAt: null }),
        onRetry: ({ attempt, maxAttempts, delayMs, error }) => update(job, {
          status: 'retrying',
          attempt,
          maxAttempts,
          retryAt: Date.now() + delayMs,
          error: error?.message || 'Upload sementara gagal.',
        }),
        onAbort: (abort) => {
          job.abort = abort;
          if (job.cancelRequested) abort();
        },
      });
      update(job, { status: 'done', progress: 100 });
      onItemDone({ ...job }, asset);
    } catch (error) {
      update(job, {
        status: job.cancelRequested ? 'cancelled' : 'error',
        error: job.cancelRequested ? '' : (error?.message || 'Upload folder gagal.'),
      });
    } finally {
      job.abort = null;
      active -= 1;
      pump();
    }
  };

  const pump = () => {
    if (!preflightComplete || paused || finalizing) return;

    const maxParallel = Math.max(1, Number(getMaxParallel?.() || 1));
    while (active < maxParallel && queue.length > 0) {
      const next = queue.shift();
      if (!next || next.status !== 'queued') continue;
      void process(next);
    }

    if (active === 0 && queue.length === 0) void finishIfIdle();
  };

  return {
    async start() {
      if (started) return;
      started = true;

      try {
        const folders = [...new Set(jobs.map((job) => job.relativeParent).filter(Boolean))]
          .sort((left, right) => left.split('/').length - right.split('/').length);
        batch = await beginFolderUploadBatch({
          storage,
          path,
          folders,
          totalBytes: jobs.reduce((total, job) => total + job.bytes, 0),
          fileCount: jobs.length,
        });
        preflightComplete = true;
        jobs.forEach((job) => onItemAdded({ ...job }));
        pump();
      } catch (error) {
        jobs.forEach((job) => onItemAdded({ ...job, status: 'error', error: error?.message || 'Preflight folder upload gagal.' }));
        onStartError(error);
      }
    },

    setPaused(nextPaused) {
      paused = Boolean(nextPaused);
      if (!paused) {
        releasePauseWaiters();
        pump();
      }
    },

    refreshConcurrency() {
      pump();
    },

    retry(id) {
      const job = jobs.find((candidate) => candidate.id === id);
      if (!job || job.status !== 'error' || !batchCompleted) return false;

      job.cancelRequested = false;
      job.useBatchReservation = false;
      update(job, { status: 'queued', progress: 0, error: '', attempt: 1, maxAttempts: maximumAttempts(), retryAt: null });
      queue.push(job);
      pump();

      return true;
    },

    retryFailed() {
      if (!batchCompleted) return 0;

      const failedJobs = jobs.filter((job) => job.status === 'error');
      failedJobs.forEach((job) => {
        job.cancelRequested = false;
        job.useBatchReservation = false;
        update(job, { status: 'queued', progress: 0, error: '', attempt: 1, maxAttempts: maximumAttempts(), retryAt: null });
        queue.push(job);
      });
      pump();

      return failedJobs.length;
    },

    cancel(id) {
      const job = jobs.find((candidate) => candidate.id === id);
      if (!job || terminal(job.status)) return;

      job.cancelRequested = true;
      if (job.status === 'queued') {
        const queueIndex = queue.indexOf(job);
        if (queueIndex >= 0) queue.splice(queueIndex, 1);
        update(job, { status: 'cancelled', error: '' });
        void finishIfIdle();

        return;
      }

      job.abort?.();
    },

    get batchId() {
      return batch?.id || null;
    },
  };
}
