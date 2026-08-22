<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
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
const mobileViewportQuery = window.matchMedia('(max-width: 560px)');
const isMobileViewport = ref(mobileViewportQuery.matches);
const compactViewportQuery = window.matchMedia('(max-width: 1000px)');
const isCompactViewport = ref(compactViewportQuery.matches);
const detailAsset = ref(isCompactViewport.value ? null : assets[0]);
const settingsOpen = ref(false);
const actionModal = ref(null);
const actionSubmitState = ref('idle');
const folderName = ref('');
const moveDestination = ref('root');
const uploadOpen = ref(false);
const uploadMinimized = ref(false);
const uploadsPaused = ref(false);
const isDragging = ref(false);
const toast = ref(null);
const mobileSidebarOpen = ref(false);
const uploadInput = ref(null);
const folderInput = ref(null);
const timers = new Map();
let actionCloseTimer;
const themeQuery = window.matchMedia('(prefers-color-scheme: dark)');
const savedTheme = window.localStorage.getItem('arunika-files-theme');
const themePreference = ref(['light', 'dark', 'system'].includes(savedTheme) ? savedTheme : 'system');
const systemTheme = ref(themeQuery.matches ? 'dark' : 'light');

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
const moveDestinationName = computed(() => (moveDestination.value === 'root' ? 'All assets' : folders.find((folder) => folder.id === moveDestination.value)?.name || 'All assets'));
const resolvedTheme = computed(() => (themePreference.value === 'system' ? systemTheme.value : themePreference.value));
const themeIcon = computed(() => (resolvedTheme.value === 'dark' ? 'bi-moon-stars-fill' : 'bi-sun-fill'));
const themeLabel = computed(() => ({ light: 'Light', dark: 'Dark', system: 'System' }[themePreference.value]));

const seededUploads = [
  { id: 901, name: 'campaign-master-2026.psd', size: '1.8 GB', progress: 73, status: 'uploading' },
  { id: 902, name: 'product-angle-04.png', size: '22.4 MB', progress: 100, status: 'done' },
  { id: 903, name: 'interview-b-roll.mov', size: '4.7 GB', progress: 36, status: 'uploading' },
];
const uploads = ref([]);

function notify(message, status = 'success') {
  toast.value = { message, status };
  window.clearTimeout(notify.timer);
  notify.timer = window.setTimeout(() => (toast.value = null), 3000);
}
function applyTheme(theme) {
  document.documentElement.dataset.theme = theme;
  document.documentElement.style.colorScheme = theme;
  document.querySelector('meta[name="theme-color"]')?.setAttribute('content', theme === 'dark' ? '#111827' : '#f6f7fb');
}

function setTheme(preference) {
  themePreference.value = preference;
  window.localStorage.setItem('arunika-files-theme', preference);
  notify(`Theme set to ${themeLabel.value}`);
}

function syncSystemTheme(event) {
  systemTheme.value = event.matches ? 'dark' : 'light';
}

function closeMobileSidebar() {
  mobileSidebarOpen.value = false;
}

function handleGlobalKeydown(event) {
  if (event.key !== 'Escape') return;
  if (actionModal.value) {
    closeActionModal();
    return;
  }
  closeMobileSidebar();
}

function syncMobileViewport(event) {
  isMobileViewport.value = event.matches;
  if (event.matches) {
    detailAsset.value = null;
    closeMobileSidebar();
  }
}

function syncCompactViewport(event) {
  isCompactViewport.value = event.matches;
  if (event.matches) detailAsset.value = null;
}

function changeStorage(id) {
  activeStorage.value = id;
  selectedIds.value = new Set();
  detailAsset.value = null;
  closeMobileSidebar();
  notify(`Switched to ${storageProfiles[id].name}`);
}

function changeNav(id) {
  activeNav.value = id;
  activeFolder.value = '';
  closeMobileSidebar();
}

function changeFolder(id) {
  activeFolder.value = id;
  activeNav.value = 'all';
  closeMobileSidebar();
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
  if (isCompactViewport.value) {
    detailAsset.value = null;
    return;
  }
  detailAsset.value = next.has(asset.id) ? asset : null;
}

function openAssetDetails(asset) {
  detailAsset.value = asset;
}

function clearSelection() {
  selectedIds.value = new Set();
  detailAsset.value = null;
}
function openActionModal(type) {
  if ((type === 'move' || type === 'delete') && !selectedCount.value) return;
  window.clearTimeout(actionCloseTimer);
  actionSubmitState.value = 'idle';
  actionModal.value = type;
  folderName.value = '';
  moveDestination.value = 'root';
}

function closeActionModal(force = false) {
  if (!force && actionSubmitState.value !== 'idle') return;
  window.clearTimeout(actionCloseTimer);
  actionCloseTimer = undefined;
  actionSubmitState.value = 'idle';
  actionModal.value = null;
  folderName.value = '';
  moveDestination.value = 'root';
}

function actionResponse(type) {
  const count = selectedCount.value;
  if (type === 'create') {
    const name = folderName.value.trim();
    const folderExists = folders.some((folder) => folder.name.trim().toLocaleLowerCase() === name.toLocaleLowerCase());
    if (folderExists) return { status: 'failed', message: `A folder named ${name} already exists.` };
    return { status: 'success', message: `Folder ${name} created in ${activeFolderName.value}.` };
  }

  if (type === 'move') {
    const currentDestination = activeFolder.value || 'root';
    if (moveDestination.value === currentDestination) return { status: 'failed', message: `Selected assets are already in ${moveDestinationName.value}.` };
    return { status: 'success', message: `${count} asset${count === 1 ? '' : 's'} moved to ${moveDestinationName.value}.` };
  }

  return { status: 'success', message: `${count} asset${count === 1 ? '' : 's'} deleted.` };
}

function applyActionSuccess(type) {
  if (type === 'move' || type === 'delete') clearSelection();
}

async function submitActionModal() {
  const type = actionModal.value;
  if (type === 'create' && !folderName.value.trim()) {
    notify('Enter a folder name.', 'failed');
    return;
  }

  actionSubmitState.value = 'submitting';
  await new Promise((resolve) => window.setTimeout(resolve, 700));
  const response = actionResponse(type);

  if (response.status === 'failed') {
    actionSubmitState.value = 'idle';
    notify(response.message, 'failed');
    return;
  }

  actionSubmitState.value = 'success';
  notify(response.message);
  actionCloseTimer = window.setTimeout(() => {
    applyActionSuccess(type);
    closeActionModal(true);
  }, 2200);
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

watch(resolvedTheme, applyTheme, { immediate: true });
onMounted(() => {
  themeQuery.addEventListener('change', syncSystemTheme);
  mobileViewportQuery.addEventListener('change', syncMobileViewport);
  compactViewportQuery.addEventListener('change', syncCompactViewport);
  window.addEventListener('keydown', handleGlobalKeydown);
});
onBeforeUnmount(() => {
  timers.forEach((timer) => window.clearInterval(timer));
  window.clearTimeout(actionCloseTimer);
  themeQuery.removeEventListener('change', syncSystemTheme);
  mobileViewportQuery.removeEventListener('change', syncMobileViewport);
  compactViewportQuery.removeEventListener('change', syncCompactViewport);
  window.removeEventListener('keydown', handleGlobalKeydown);
});
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
      :mobile-open="mobileSidebarOpen"
      :profile="activeProfile"
      :profiles="storageProfiles"
      :active-nav="activeNav"
      :nav-items="navItems"
      :folders="folders"
      :active-folder="activeFolder"
      @change-storage="changeStorage"
      @change-nav="changeNav"
      @change-folder="changeFolder"
      @open-settings="settingsOpen = true; closeMobileSidebar()"
      @close-mobile="closeMobileSidebar"
    />

    <button v-if="mobileSidebarOpen" class="mobile-sidebar-backdrop" type="button" aria-label="Close navigation" @click="closeMobileSidebar"></button>
    <main class="fm-workspace">
      <header class="topbar">
        <button class="mobile-sidebar-toggle" type="button" aria-controls="mobile-file-navigation" :aria-expanded="mobileSidebarOpen" aria-label="Open navigation" @click="mobileSidebarOpen = true">
          <i class="bi bi-list"></i>
        </button>
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
          <div class="dropdown">
            <button class="icon-button theme-button" type="button" data-bs-toggle="dropdown" :title="`Theme: ${themeLabel}`" :aria-label="`Theme: ${themeLabel}`">
              <i class="bi" :class="themeIcon"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm theme-menu">
              <li><button class="dropdown-item" :class="{ active: themePreference === 'light' }" @click="setTheme('light')"><i class="bi bi-sun me-2"></i>Light</button></li>
              <li><button class="dropdown-item" :class="{ active: themePreference === 'dark' }" @click="setTheme('dark')"><i class="bi bi-moon-stars me-2"></i>Dark</button></li>
              <li><button class="dropdown-item" :class="{ active: themePreference === 'system' }" @click="setTheme('system')"><i class="bi bi-circle-half me-2"></i>System</button></li>
            </ul>
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
          <button class="btn btn-light border" @click="openActionModal('create')"><i class="bi bi-folder-plus"></i> New folder</button>
          <div class="btn-group">
            <button class="btn btn-primary upload-button" @click="uploadInput.click()"><i class="bi bi-cloud-arrow-up"></i> Upload files</button>
            <button class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Upload options</span></button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm upload-menu">
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
        <button @click="openActionModal('move')"><i class="bi bi-folder-symlink"></i> Move</button>
        <button><i class="bi bi-link-45deg"></i> Copy URL</button>
        <button @click="openActionModal('delete')"><i class="bi bi-trash3"></i> Delete</button>
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
              @open-details="openAssetDetails"
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

    <Transition name="action-modal">
      <div v-if="actionModal" class="modal-layer action-modal-layer" @click.self="closeActionModal">
      <section class="action-modal" :class="{ 'is-danger': actionModal === 'delete' }" role="dialog" aria-modal="true" aria-labelledby="action-modal-title">
        <header>
          <div>
            <small>FILE MANAGER</small>
            <h2 id="action-modal-title">
              <template v-if="actionModal === 'create'">Create folder</template>
              <template v-else-if="actionModal === 'delete'">Delete {{ selectedCount }} selected asset<span v-if="selectedCount !== 1">s</span></template>
              <template v-else>Move {{ selectedCount }} selected asset<span v-if="selectedCount !== 1">s</span></template>
            </h2>
          </div>
          <button type="button" aria-label="Close dialog" :disabled="actionSubmitState !== 'idle'" @click="closeActionModal"><i class="bi bi-x-lg"></i></button>
        </header>

        <form @submit.prevent="submitActionModal">
          <div class="action-modal-body">
            <template v-if="actionModal === 'create'">
              <label class="action-modal-label" for="folder-name">Folder name</label>
              <input id="folder-name" v-model="folderName" class="form-control" type="text" maxlength="80" placeholder="e.g. Campaign assets" :disabled="actionSubmitState !== 'idle'" autofocus />
              <p class="action-modal-help">The folder will be created in {{ activeFolderName }}.</p>
            </template>

            <template v-else-if="actionModal === 'delete'">
              <div class="action-modal-alert"><i class="bi bi-exclamation-triangle-fill"></i><div><strong>Delete selected assets?</strong><p>This action cannot be undone.</p></div></div>
            </template>

            <template v-else>
              <label class="action-modal-label" for="move-destination">Destination folder</label>
              <select id="move-destination" v-model="moveDestination" class="form-select" :disabled="actionSubmitState !== 'idle'">
                <option value="root">All assets</option>
                <option v-for="folder in folders" :key="folder.id" :value="folder.id">{{ folder.name }}</option>
              </select>
              <p class="action-modal-help">Selected assets will remain in {{ activeProfile.shortName }} storage.</p>
            </template>
          </div>

          <footer class="action-modal-footer">
            <button type="button" class="btn btn-light border" :disabled="actionSubmitState !== 'idle'" @click="closeActionModal">Cancel</button>
            <button v-if="actionSubmitState === 'idle'" type="submit" class="btn" :class="actionModal === 'delete' ? 'btn-danger' : 'btn-primary'">
              <template v-if="actionModal === 'create'">Create folder</template>
              <template v-else-if="actionModal === 'delete'">Delete assets</template>
              <template v-else>Move assets</template>
            </button>
            <button v-else-if="actionSubmitState === 'submitting'" type="button" class="btn btn-secondary action-submit-state" disabled>
              <i class="bi bi-arrow-repeat action-submit-spinner"></i>
              <template v-if="actionModal === 'create'">Creating folder</template>
              <template v-else-if="actionModal === 'delete'">Deleting assets</template>
              <template v-else>Moving assets</template>
            </button>
            <button v-else-if="actionSubmitState === 'success'" type="button" class="btn btn-success action-submit-state" disabled><i class="bi bi-check-circle-fill"></i> Success</button>
          </footer>
        </form>
      </section>
      </div>
    </Transition>
    <div v-if="toast" class="fm-toast" :class="`is-${toast.status}`" role="status"><i class="bi" :class="toast.status === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"></i><span>{{ toast.message }}</span></div>
  </div>
</template>
