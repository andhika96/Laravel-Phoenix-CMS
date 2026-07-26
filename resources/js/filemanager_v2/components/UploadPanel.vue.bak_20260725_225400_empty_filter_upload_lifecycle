<script setup>
import { computed } from 'vue';

const props = defineProps({
  uploads: Array,
  minimized: Boolean,
  paused: Boolean,
});

const emit = defineEmits(['toggle-minimize', 'toggle-pause', 'close', 'remove', 'retry']);
const completed = computed(() => props.uploads.filter((item) => item.status === 'done').length);
const overall = computed(() => {
  if (!props.uploads.length) return 0;
  return Math.round(props.uploads.reduce((total, item) => total + item.progress, 0) / props.uploads.length);
});
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
        <button @click="emit('toggle-pause')">
          <i class="bi" :class="paused ? 'bi-play-fill' : 'bi-pause-fill'"></i>
          {{ paused ? 'Resume all' : 'Pause all' }}
        </button>
      </div>
      <div class="upload-list">
        <div v-for="item in uploads" :key="item.id" class="upload-item">
          <div class="upload-file-icon"><i class="bi bi-file-earmark"></i></div>
          <div class="upload-item-copy">
            <strong>{{ item.name }}</strong>
            <small v-if="item.status === 'error'" class="text-danger">{{ item.error }}</small>
            <small v-else-if="item.status === 'done'" class="text-success">Uploaded · {{ item.size }}</small>
            <small v-else>{{ item.size }} · {{ item.progress }}%</small>
            <div v-if="item.status === 'uploading'" class="progress">
              <div class="progress-bar" :style="{ width: item.progress + '%' }"></div>
            </div>
          </div>
          <div class="upload-item-status">
            <i v-if="item.status === 'done'" class="bi bi-check-circle-fill text-success"></i>
            <button v-else-if="item.status === 'error'" @click="emit('retry', item)"><i class="bi bi-arrow-clockwise"></i></button>
            <button v-else @click="emit('remove', item)"><i class="bi bi-x"></i></button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
