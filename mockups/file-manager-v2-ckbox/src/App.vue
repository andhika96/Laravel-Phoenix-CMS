<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import StorageSidebar from './components/StorageSidebar.vue';
import AssetCard from './components/AssetCard.vue';
import DetailsDrawer from './components/DetailsDrawer.vue';
import UploadPanel from './components/UploadPanel.vue';
import SettingsModal from './components/SettingsModal.vue';
import { assets, folders, navItems, storageProfiles } from './data/assets';

const activeStorage = ref('local');
const activeNav = ref('all');
const activeFolder = ref('');
const search = ref('');
const filter = ref('all');
const sort = ref('modified');
const viewMode = ref('grid');
const selectedIds = ref(new Set([1]));
const detailAsset = ref(assets[0]);
const settingsOpen = ref(false);
const uploadOpen = ref(false);
const uploadMinimized = ref(false);
const uploadsPaused = ref(false);
const isDragging = ref(false);
const toast = ref('');
const uploadInput = ref(null);
const folderInput = ref(null);
const timers = new Map();

const activeProfile = computed(() => storageProfiles[activeStorage.value]);
const activeFolderName = computed(() => folders.find((item) => item.id === activeFolder.value)?.name || 'All assets');
const filteredAssets = computed(() => {
  let list = assets.filter((asset) => {
    const queryMatch = asset.name.toLowerCase().includes(search.value.toLowerCase());
    const typeMatch = filter.value === 'all' || asset.kind === filter.value;
    const folderMatch = !activeFolder.value || asset.folder === activeFolderName.value;
    const navMatch = activeNav.value !== 'starred' || [1, 4, 9].includes(asset.id);
    return queryMatch && typeMatch && folderMatch && navMatch;
  });
  if (sort.value === 'name') list = [...list].sort((a, b) => a.name.localeCompare(b.name));
  if (sort.value === 'size') list = [...list].sort((a, b) => a.size.localeCompare(b.size));
  return list;
});
const selectedCount = computed(() => selectedIds.value.size);

const seededUploads = [
  { id: 901, name: 'campaign-master-2026.psd', size: '1.8 GB', progress: 73, status: 'uploading' },
  { id: 902, name: 'product-angle-04.png', size: '22.4 MB', progress: 100, status: 'done' },
  { id: 903, name: 'interview-b-roll.mov', size: '4.7 GB', progress: 36, status: 'uploading' },
];
const uploads = ref([]);

function notify(message) {
  toast.value = message;
  window.clearTimeout(notify.timer);
  notify.timer = window.setTimeout(() => (toast.value = ''), 2400);
}

function changeStorage(id) {
  activeStorage.value = id;
  selectedIds.value = new Set();
  detailAsset.value = null;
  notify(`Switched to ${storageProfiles[id].name}`);
}

function changeNav(id) {
  activeNav.value = id;
  activeFolder.value = '';
}

function changeFolder(id) {
  activeFolder.value = id;
  activeNav.value = 'all';
}

function selectAsset(asset, event) {
  const next = new Set(selectedIds.value);
  if (event.ctrlKey || event.metaKey) {
    next.has(asset.id) ? next.delete(asset.id) : next.add(asset.id);
  } else {
    next.clear();
    next.add(asset.id);
  }
  selectedIds.value = next;
  detailAsset.value = next.has(asset.id) ? asset : null;
}

function clearSelection() {
  selectedIds.value = new Set();
  detailAsset.value = null;
}

function openDemoUpload() {
  uploads.value = seededUploads.map((item) => ({ ...item }));
  uploadOpen.value = true;
  uploadMinimized.value = false;
  uploads.value.filter((item) => item.status === 'uploading').forEach(startProgress);
}

function addFiles(fileList) {
  const items = [...fileList].map((file, index) => ({
    id: Date.now() + index,
    name: file.name,
    size: formatBytes(file.size),
    progress: Math.floor(Math.random() * 8),
    status: 'uploading',
  }));
  uploads.value = [...uploads.value, ...items];
  uploadOpen.value = true;
  uploadMinimized.value = false;
  items.forEach(startProgress);
}

function startProgress(item) {
  window.clearInterval(timers.get(item.id));
  const timer = window.setInterval(() => {
    if (uploadsPaused.value || item.status !== 'uploading') return;
    item.progress = Math.min(100, item.progress + Math.ceil(Math.random() * 6));
    if (item.progress === 100) {
      item.status = 'done';
      window.clearInterval(timer);
      timers.delete(item.id);
    }
  }, 520);
  timers.set(item.id, timer);
}

function retryUpload(item) {
  item.status = 'uploading';
  item.progress = Math.max(5, item.progress);
  startProgress(item);
}

function removeUpload(item) {
  window.clearInterval(timers.get(item.id));
  timers.delete(item.id);
  uploads.value = uploads.value.filter((candidate) => candidate.id !== item.id);
}

function closeUploads() {
  uploads.value.forEach((item) => window.clearInterval(timers.get(item.id)));
  timers.clear();
  uploadOpen.value = false;
}

function saveSettings(settings) {
  activeStorage.value = settings.defaultStorage;
  settingsOpen.value = false;
  notify('Storage and upload settings saved');
}

function formatBytes(bytes) {
  if (!bytes) return '0 B';
  const units = ['B', 'KB', 'MB', 'GB'];
  const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
  return `${(bytes / 1024 ** index).toFixed(index > 1 ? 1 : 0)} ${units[index]}`;
}

function onDrop(event) {
  isDragging.value = false;
  if (event.dataTransfer.files?.length) addFiles(event.dataTransfer.files);
}

onBeforeUnmount(() => timers.forEach((timer) => window.clearInterval(timer)));
</script>

<template>
  <div
    class="fm-shell"
    @dragenter.prevent="isDragging = true"
    @dragover.prevent
    @dragleave.self="isDragging = false"
    @drop.prevent="onDrop"
  >
    <StorageSidebar
      :active-storage="activeStorage"
      :profile="activeProfile"
      :profiles="storageProfiles"
      :active-nav="activeNav"
      :nav-items="navItems"
      :folders="folders"
      :active-folder="activeFolder"
      @change-storage="changeStorage"
      @change-nav="changeNav"
      @change-folder="changeFolder"
      @open-settings="settingsOpen = true"
    />

    <main class="fm-workspace">
      <header class="topbar">
        <div class="breadcrumb-wrap">
          <small>{{ activeProfile.shortName }}</small>
          <div><button>Assets</button><i class="bi bi-chevron-right"></i><strong>{{ activeFolderName }}</strong></div>
        </div>
        <div class="topbar-actions">
          <div class="search-field">
            <i class="bi bi-search"></i>
            <input v-model="search" placeholder="Search assets, tags, or file types…" />
            <kbd>Ctrl K</kbd>
          </div>
          <button class="icon-button" title="Notifications"><i class="bi bi-bell"></i><span></span></button>
          <button class="profile-button"><span>CA</span><i class="bi bi-chevron-down"></i></button>
        </div>
      </header>

      <section class="content-toolbar">
        <div>
          <h1>{{ activeFolderName }}</h1>
          <p>{{ filteredAssets.length }} assets · Updated a few seconds ago</p>
        </div>
        <div class="primary-actions">
          <button class="btn btn-light border" @click="notify('New folder dialog would open')"><i class="bi bi-folder-plus"></i> New folder</button>
          <div class="btn-group">
            <button class="btn btn-primary upload-button" @click="uploadInput.click()"><i class="bi bi-cloud-arrow-up"></i> Upload files</button>
            <button class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Upload options</span></button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
              <li><button class="dropdown-item" @click="uploadInput.click()"><i class="bi bi-files me-2"></i>Upload files</button></li>
              <li><button class="dropdown-item" @click="folderInput.click()"><i class="bi bi-folder2-open me-2"></i>Upload folder</button></li>
              <li><button class="dropdown-item" @click="openDemoUpload"><i class="bi bi-play-circle me-2"></i>Run upload demo</button></li>
            </ul>
          </div>
          <input ref="uploadInput" class="d-none" type="file" multiple @change="addFiles($event.target.files)" />
          <input ref="folderInput" class="d-none" type="file" multiple webkitdirectory @change="addFiles($event.target.files)" />
        </div>
      </section>

      <section class="filterbar">
        <div class="filter-tabs">
          <button :class="{ active: filter === 'all' }" @click="filter = 'all'">All</button>
          <button :class="{ active: filter === 'image' }" @click="filter = 'image'">Images</button>
          <button :class="{ active: filter === 'video' }" @click="filter = 'video'">Video</button>
          <button :class="{ active: filter === 'document' }" @click="filter = 'document'">Documents</button>
          <button :class="{ active: filter === 'audio' }" @click="filter = 'audio'">Audio</button>
        </div>
        <div class="filter-actions">
          <button class="btn btn-sm btn-light border"><i class="bi bi-funnel"></i> Filters <span>2</span></button>
          <select v-model="sort" class="form-select form-select-sm">
            <option value="modified">Last modified</option>
            <option value="name">Name</option>
            <option value="size">File size</option>
          </select>
          <div class="view-toggle">
            <button :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'"><i class="bi bi-grid-3x3-gap"></i></button>
            <button :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'"><i class="bi bi-list-ul"></i></button>
          </div>
        </div>
      </section>

      <section v-if="selectedCount" class="selection-toolbar">
        <button @click="clearSelection"><i class="bi bi-x-lg"></i></button>
        <strong>{{ selectedCount }} selected</strong>
        <span></span>
        <button><i class="bi bi-download"></i> Download</button>
        <button><i class="bi bi-folder-symlink"></i> Move</button>
        <button><i class="bi bi-link-45deg"></i> Copy URL</button>
        <button><i class="bi bi-trash3"></i> Delete</button>
        <button><i class="bi bi-three-dots"></i></button>
      </section>

      <section class="asset-area" :class="{ 'with-drawer': detailAsset }">
        <div class="asset-scroll">
          <div v-if="filteredAssets.length" class="asset-grid" :class="{ 'list-view': viewMode === 'list' }">
            <AssetCard
              v-for="asset in filteredAssets"
              :key="asset.id"
              :asset="asset"
              :selected="selectedIds.has(asset.id)"
              :view-mode="viewMode"
              @select="selectAsset"
            />
          </div>
          <div v-else class="empty-state">
            <span><i class="bi bi-search"></i></span>
            <h2>No assets found</h2>
            <p>Try changing the search term or active filters.</p>
            <button class="btn btn-outline-primary" @click="search = ''; filter = 'all'">Clear filters</button>
          </div>
        </div>
        <DetailsDrawer v-if="detailAsset" :asset="detailAsset" @close="detailAsset = null" @notify="notify" />
      </section>
    </main>

    <div v-if="isDragging" class="drop-overlay">
      <div><span><i class="bi bi-cloud-arrow-up"></i></span><h2>Drop files or folders here</h2><p>They will be uploaded to {{ activeFolderName }} on {{ activeProfile.shortName }}.</p></div>
    </div>

    <UploadPanel
      v-if="uploadOpen"
      :uploads="uploads"
      :minimized="uploadMinimized"
      :paused="uploadsPaused"
      @toggle-minimize="uploadMinimized = !uploadMinimized"
      @toggle-pause="uploadsPaused = !uploadsPaused"
      @close="closeUploads"
      @remove="removeUpload"
      @retry="retryUpload"
    />

    <SettingsModal
      :show="settingsOpen"
      :active-storage="activeStorage"
      :profiles="storageProfiles"
      @close="settingsOpen = false"
      @save="saveSettings"
      @test="notify(`Connection to ${storageProfiles[$event].name} successful`)"
    />

    <div v-if="toast" class="fm-toast"><i class="bi bi-check-circle-fill"></i>{{ toast }}</div>
  </div>
</template>
