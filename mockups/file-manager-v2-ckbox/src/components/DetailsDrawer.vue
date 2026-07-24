<script setup>
defineProps({ asset: Object });
const emit = defineEmits(['close', 'notify']);
</script>

<template>
  <aside class="details-drawer">
    <div class="drawer-head">
      <div>
        <small>Asset details</small>
        <strong>{{ asset.name }}</strong>
      </div>
      <button @click="emit('close')" aria-label="Close details"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="drawer-preview" :style="{ background: asset.color }">
      <img v-if="asset.src" :src="asset.src" :alt="asset.name" />
      <i v-else class="bi bi-file-earmark file-kind-icon"></i>
    </div>

    <div class="drawer-actions">
      <button @click="emit('notify', 'Download started')"><i class="bi bi-download"></i> Download</button>
      <button @click="emit('notify', 'Secure URL copied')"><i class="bi bi-link-45deg"></i> Copy URL</button>
      <button><i class="bi bi-three-dots"></i></button>
    </div>

    <div class="drawer-section">
      <label>Name</label>
      <div class="editable-field">{{ asset.name }} <i class="bi bi-pencil"></i></div>
    </div>
    <div class="drawer-section info-grid">
      <div><label>Type</label><strong>{{ asset.ext }}</strong></div>
      <div><label>Size</label><strong>{{ asset.size }}</strong></div>
      <div><label>Dimensions</label><strong>{{ asset.dimensions || '—' }}</strong></div>
      <div><label>Modified</label><strong>{{ asset.modified }}</strong></div>
    </div>
    <div class="drawer-section">
      <label>Location</label>
      <button class="location-link"><i class="bi bi-folder2"></i> {{ asset.folder }}</button>
    </div>
    <div class="drawer-section">
      <label>Tags</label>
      <div class="tag-list">
        <span v-for="tag in asset.tags" :key="tag">{{ tag }}</span>
        <button><i class="bi bi-plus"></i></button>
      </div>
    </div>
    <div class="drawer-section">
      <label>Description</label>
      <textarea rows="3" placeholder="Add a useful description…"></textarea>
    </div>
  </aside>
</template>
