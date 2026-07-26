<script setup>
defineProps({ asset: Object });
const emit = defineEmits(['close', 'download', 'copy-url', 'rename', 'open-folder']);
</script>

<template>
  <aside class="details-drawer">
    <div class="drawer-head">
      <div>
        <small>{{ asset.type === 'folder' ? 'Folder details' : 'Asset details' }}</small>
        <strong>{{ asset.name }}</strong>
      </div>
      <button @click="emit('close')" aria-label="Close details"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="drawer-preview" :style="{ background: asset.color }">
      <img v-if="asset.src" :src="asset.src" :alt="asset.name" />
      <i v-else class="bi file-kind-icon" :class="asset.type === 'folder' ? 'bi-folder2' : 'bi-file-earmark'"></i>
    </div>

    <div class="drawer-actions" :class="{ 'folder-drawer-actions': asset.type === 'folder' }">
      <template v-if="asset.type === 'folder'">
        <button @click="emit('open-folder', asset)"><i class="bi bi-folder2-open"></i> Open folder</button>
      </template>
      <template v-else>
        <button @click="emit('download', asset)"><i class="bi bi-download"></i> Download</button>
        <button @click="emit('copy-url', asset)"><i class="bi bi-link-45deg"></i> Copy URL</button>
      </template>
      <button @click="emit('rename', asset)" aria-label="Rename asset" title="Rename"><i class="bi bi-pencil"></i></button>
    </div>

    <div class="drawer-section">
      <label>Name</label>
      <button class="editable-field" type="button" @click="emit('rename', asset)" title="Rename">
        <span>{{ asset.name }}</span>
        <i class="bi bi-pencil"></i>
      </button>
    </div>
    <div class="drawer-section info-grid">
      <div><label>Type</label><strong>{{ asset.ext }}</strong></div>
      <div><label>Size</label><strong>{{ asset.detailsLoading ? 'Calculating?' : asset.size }}</strong></div>
      <template v-if="asset.type === 'folder'">
        <div><label>Files</label><strong>{{ asset.detailsLoading ? '?' : asset.fileCount }}</strong></div>
        <div><label>Subfolders</label><strong>{{ asset.detailsLoading ? '?' : asset.folderCount }}</strong></div>
      </template>
      <div><label>Dimensions</label><strong>{{ asset.dimensions || '—' }}</strong></div>
      <div><label>Modified</label><strong>{{ asset.modified }}</strong></div>
    </div>
    <div class="drawer-section">
      <label>Location</label>
      <button class="location-link"><i class="bi bi-folder2"></i> {{ asset.folder || 'All assets' }}</button>
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
