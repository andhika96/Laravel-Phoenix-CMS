<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import StorageSidebar from './components/StorageSidebar.vue';
import AssetCard from './components/AssetCard.vue';
import DetailsDrawer from './components/DetailsDrawer.vue';
import UploadPanel from './components/UploadPanel.vue';
import FilePondUploadEngine from './components/FilePondUploadEngine.vue';
import StorageSettingsModal from './components/StorageSettingsModal.vue';
import { assets, createFolder as createFolderApi, deleteAsset, deleteAssets, folders, getUploadOptions, initializeWorkspace, loadAssets, loadFolderDetails as loadFolderDetailsApi, loadFolders, loadStorageSettings, moveAssets, navItems, previewDelete, refreshStorageProfiles, renameAsset, replaceAssets, replaceFolders, saveStorageSettings, storageProfiles, testStorageConnection, toggleAssetStar, upsertAsset } from './data/live';
import { createFolderUploadCoordinator } from './data/folderUploadBatch';

const activeStorage = ref('local');
const activeNav = ref('all');
const activeFolder = ref('');
const search = ref('');
const filter = ref('all');
const sort = ref('modified');
const viewMode = ref('grid');
const selectedIds = ref(new Set());
const uploads = ref([]);
const mobileViewportQuery = window.matchMedia('(max-width: 560px)');
const isMobileViewport = ref(mobileViewportQuery.matches);
const compactViewportQuery = window.matchMedia('(max-width: 1000px)');
const isCompactViewport = ref(compactViewportQuery.matches);
const detailAsset = ref(null);
const settingsOpen = ref(false);
const storageSettings = ref(null);
const uploadOpen = ref(false);
const uploadMinimized = ref(false);
const uploadsPaused = ref(false);
const isDragging = ref(false);
const toast = ref(null);
const mobileSidebarOpen = ref(false);
const uploadInput = ref(null);
const folderInput = ref(null);
const uploadEngine = ref(null);
const breadcrumbScroller = ref(null);
const breadcrumbOverflow = ref({ left: false, right: false });
const actionModal = ref(null);
const actionSubmitState = ref('idle');
const actionModalItem = ref(null);
const actionModalCount = ref(0);
const deleteImpact = ref(null);
const deleteImpactLoading = ref(false);
const deletePaths = ref([]);
const folderName = ref('');
const moveDestination = ref('');
const assetName = ref('');
const FILE_MANAGER_HISTORY_KEY = 'fileManagerV2';
let actionCloseTimer;
let refreshSequence = 0;
const workspaceLoadState = ref('idle');
const WORKSPACE_CACHE_LIMIT = 24;
const workspaceCache = new Map();
const folderCache = new Map();
let deleteImpactSequence = 0;
let workspaceReady = false;
const folderUploadBatches = new Map();
let folderBatchRefreshTimer;
const themeQuery = window.matchMedia('(prefers-color-scheme: dark)');
const savedTheme = window.localStorage.getItem('arunika-files-theme');
const themePreference = ref(['light', 'dark', 'system'].includes(savedTheme) ? savedTheme : 'system');
const systemTheme = ref(themeQuery.matches ? 'dark' : 'light');

const activeProfile = computed(() => storageProfiles[activeStorage.value] || { shortName: 'LOCAL', name: 'Local storage', icon: 'bi-device-hdd', root: '', usedBytes: 0, quotaBytes: 0, usagePercent: 0, usedLabel: '0 B', quotaLabel: 'No limit' });
const activeNavItem = computed(() => navItems.find((item) => item.id === activeNav.value) || navItems[0]);
const activeFolderName = computed(() => activeFolder.value ? activeFolder.value.split('/').at(-1) : activeNavItem.value.label);
const hasUploadCenter = computed(() => uploads.value.length > 0);
const activeUploadCount = computed(() => uploads.value.filter((item) => ['queued', 'uploading'].includes(item.status)).length);
const failedUploadCount = computed(() => uploads.value.filter((item) => item.status === 'error').length);
const uploadCenterCount = computed(() => activeUploadCount.value || failedUploadCount.value || uploads.value.length);
const breadcrumbSegments = computed(() => activeFolder.value.split('/').filter(Boolean).map((name, index, parts) => ({ name, path: parts.slice(0, index + 1).join('/') })));
const selectedAssets = computed(() => assets.filter((asset) => selectedIds.value.has(asset.id)));
const selectedFiles = computed(() => selectedAssets.value.filter((asset) => asset.type === 'file'));
const selectedAssetsAreFilesOnly = computed(() => selectedAssets.value.length > 0 && selectedFiles.value.length === selectedAssets.value.length);
const selectedAssetsAreAllStarred = computed(() => selectedAssetsAreFilesOnly.value && selectedFiles.value.every((asset) => asset.starred));
function countLabel(count, singular, plural = `${singular}s`) {
  return `${count} ${count === 1 ? singular : plural}`;
}

const deleteModalTitle = computed(() => {
  const impact = deleteImpact.value;
  if (!impact) return `Delete ${countLabel(actionModalCount.value, 'selected asset')}?`;
  if (impact.selectedFolderCount > 0 && impact.hasFolderContents) {
    return impact.selectedFolderCount === 1 ? 'Delete folder and its contents?' : 'Delete folders and their contents?';
  }

  const targets = [
    impact.selectedFolderCount ? countLabel(impact.selectedFolderCount, 'folder') : null,
    impact.selectedFileCount ? countLabel(impact.selectedFileCount, 'file') : null,
  ].filter(Boolean);

  return `Delete ${targets.join(' and ')}?`;
});

const deleteImpactSummary = computed(() => {
  const impact = deleteImpact.value;
  if (!impact) return 'This action cannot be undone.';

  const contents = [
    impact.fileCount ? countLabel(impact.fileCount, 'file') : null,
    impact.folderCount ? countLabel(impact.folderCount, 'folder') : null,
  ].filter(Boolean);

  return `This permanently deletes ${contents.join(' and ')} (${impact.size}). This action cannot be undone.`;
});

const deleteImpactIncludedText = computed(() => {
  const count = deleteImpact.value?.includedByFolderCount || 0;
  if (!count) return '';

  return `${countLabel(count, 'selected item')} inside a selected folder is included and is not counted twice.`;
});
const moveDestinations = computed(() => [{ path: '', name: 'All assets' }, ...folders]);
const filteredAssets = computed(() => {
  let list = assets.filter((asset) => {
    const queryMatch = asset.name.toLowerCase().includes(search.value.toLowerCase());
    const typeMatch = filter.value === 'all' || asset.kind === filter.value;
    return queryMatch && typeMatch;
  });
  if (sort.value === 'name') list = [...list].sort((a, b) => a.name.localeCompare(b.name));
  if (sort.value === 'size') list = [...list].sort((a, b) => a.size.localeCompare(b.size));
  return list;
});
const selectedCount = computed(() => selectedIds.value.size);
const hasActiveFilters = computed(() => search.value.trim() !== '' || filter.value !== 'all');
const activeFilterCount = computed(() => Number(search.value.trim() !== '') + Number(filter.value !== 'all'));
const emptyState = computed(() => {
  if (activeNav.value === 'shared') {
    return { title: 'No shared assets yet', description: 'Sharing permissions have not been configured for File Manager V2 yet.' };
  }
  if (hasActiveFilters.value) {
    return { title: 'No matching assets', description: 'Try changing the search term or active filters.' };
  }
  if (activeNav.value === 'starred') {
    return { title: 'No starred assets yet', description: 'Use the action menu on a file to add it to Starred.' };
  }
  if (activeNav.value === 'recent') {
    return { title: 'No recently used assets yet', description: 'Uploaded files will appear here.' };
  }
  if (activeFolder.value) {
    return { title: 'This folder is empty', description: 'Upload files here or create a subfolder.' };
  }
  return { title: 'No assets yet', description: 'Upload files or create a folder to begin.' };
});
const resolvedTheme = computed(() => (themePreference.value === 'system' ? systemTheme.value : themePreference.value));
const themeIcon = computed(() => (resolvedTheme.value === 'dark' ? 'bi-moon-stars-fill' : 'bi-sun-fill'));
const themeLabel = computed(() => ({ light: 'Light', dark: 'Dark', system: 'System' }[themePreference.value]));
const workspaceLoadMessage = computed(() => `${workspaceLoadState.value === 'refreshing' ? 'Refreshing' : 'Loading'} ${activeProfile.value.name}...`);


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

function updateBreadcrumbOverflow() {
  const scroller = breadcrumbScroller.value;
  if (!scroller) return;

  const maxScrollLeft = Math.max(0, scroller.scrollWidth - scroller.clientWidth);
  breadcrumbOverflow.value = {
    left: maxScrollLeft > 1 && scroller.scrollLeft > 1,
    right: maxScrollLeft > 1 && scroller.scrollLeft < maxScrollLeft - 1,
  };
}

function queueBreadcrumbOverflowUpdate() {
  void nextTick(updateBreadcrumbOverflow);
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

function currentNavigationState() {
  return {
    storage: activeStorage.value,
    collection: activeNav.value,
    folder: activeFolder.value,
  };
}

function normalizeNavigationState(state, fallbackStorage) {
  const storage = typeof state?.storage === 'string' && storageProfiles[state.storage] ? state.storage : fallbackStorage;
  const collection = navItems.some((item) => item.id === state?.collection) ? state.collection : 'all';
  const folder = collection === 'all' && typeof state?.folder === 'string' ? state.folder.replace(/^\/+|\/+$/g, '') : '';

  return { storage, collection, folder };
}

function navigationStateFromUrl(fallbackStorage) {
  const url = new URL(window.location.href);
  const storage = url.searchParams.get('fm_storage');
  const collection = url.searchParams.get('fm_collection');
  const folder = url.searchParams.get('fm_folder');

  if (storage === null && collection === null && folder === null) return null;

  return normalizeNavigationState({ storage, collection, folder }, fallbackStorage);
}

function browserNavigationState(fallbackStorage) {
  const state = window.history.state?.[FILE_MANAGER_HISTORY_KEY];
  return state ? normalizeNavigationState(state, fallbackStorage) : navigationStateFromUrl(fallbackStorage);
}

function navigationHistoryUrl(state) {
  const url = new URL(window.location.href);
  url.searchParams.set('fm_storage', state.storage);
  url.searchParams.set('fm_collection', state.collection);

  if (state.folder) url.searchParams.set('fm_folder', state.folder); else url.searchParams.delete('fm_folder');

  return `${url.pathname}${url.search}${url.hash}`;
}

function sameNavigationState(first, second) {
  return first?.storage === second?.storage && first?.collection === second?.collection && first?.folder === second?.folder;
}

function writeBrowserHistory(mode = 'push') {
  const state = currentNavigationState();
  const currentState = window.history.state?.[FILE_MANAGER_HISTORY_KEY];
  if (mode === 'push' && sameNavigationState(currentState, state)) return;

  const historyState = window.history.state && typeof window.history.state === 'object' ? window.history.state : {};
  const payload = { ...historyState, [FILE_MANAGER_HISTORY_KEY]: state };
  const url = navigationHistoryUrl(state);

  if (mode === 'replace') {
    window.history.replaceState(payload, '', url);
    return;
  }

  window.history.pushState(payload, '', url);
}

function workspaceCacheKey(context) {
  return [context.storage, context.path, context.collection, context.search, context.type, context.sort].join('\u001f');
}

function rememberCacheEntry(cache, key, value) {
  if (cache.has(key)) cache.delete(key);
  cache.set(key, value);

  while (cache.size > WORKSPACE_CACHE_LIMIT) {
    cache.delete(cache.keys().next().value);
  }
}

function restoreWorkspaceFromCache(context, refreshFolders) {
  const key = workspaceCacheKey(context);
  const payload = workspaceCache.get(key);
  const folderItems = refreshFolders ? folderCache.get(context.storage) : null;

  if (payload) {
    rememberCacheEntry(workspaceCache, key, payload);
    replaceAssets(payload);
  }
  if (folderItems) {
    rememberCacheEntry(folderCache, context.storage, folderItems);
    replaceFolders(folderItems);
  }

  return Boolean(payload);
}

function rememberWorkspace(context, payload, folderItems) {
  rememberCacheEntry(workspaceCache, workspaceCacheKey(context), payload);
  if (folderItems) rememberCacheEntry(folderCache, context.storage, folderItems);
}

function clearWorkspaceCache() {
  workspaceCache.clear();
  folderCache.clear();
}

async function refreshAssets({ refreshFolders = false, refreshUsage = false } = {}) {
  const requestId = ++refreshSequence;
  const context = { storage: activeStorage.value, path: activeFolder.value, collection: activeNav.value, search: search.value, type: filter.value, sort: sort.value };
  const restored = restoreWorkspaceFromCache(context, refreshFolders);
  workspaceLoadState.value = restored ? 'refreshing' : 'loading';

  if (!restored) {
    replaceAssets({ items: [] });
    if (refreshFolders) replaceFolders([]);
  }

  try {
    const [payload, folderItems] = await Promise.all([
      loadAssets(context.storage, { path: context.path, collection: context.collection, search: context.search, type: context.type, sort: context.sort }),
      refreshFolders ? loadFolders(context.storage) : Promise.resolve(null),
      refreshUsage ? refreshStorageProfiles() : Promise.resolve(),
    ]);
    if (requestId !== refreshSequence) return;

    replaceAssets(payload);
    if (folderItems) replaceFolders(folderItems);
    rememberWorkspace(context, payload, folderItems);
  } finally {
    if (requestId === refreshSequence) workspaceLoadState.value = 'idle';
  }
}

async function applyNavigationState(state, { refreshFolders = false } = {}) {
  activeStorage.value = state.storage;
  activeNav.value = state.collection;
  activeFolder.value = state.folder;
  selectedIds.value = new Set();
  detailAsset.value = null;
  closeMobileSidebar();
  await refreshAssets({ refreshFolders });
}

async function changeStorage(id, { historyMode = 'push', notifyUser = true } = {}) {
  if (!storageProfiles[id]) return;

  await applyNavigationState({ storage: id, collection: 'all', folder: '' }, { refreshFolders: true });
  if (historyMode !== 'none') writeBrowserHistory(historyMode);
  if (notifyUser) notify(`Switched to ${storageProfiles[id].name}`);
}

async function changeNav(id, { historyMode = 'push' } = {}) {
  if (!navItems.some((item) => item.id === id)) return;

  await applyNavigationState({ storage: activeStorage.value, collection: id, folder: '' });
  if (historyMode !== 'none') writeBrowserHistory(historyMode);
}

async function changeFolder(id, { historyMode = 'push' } = {}) {
  const folder = typeof id === 'string' ? id.replace(/^\/+|\/+$/g, '') : '';

  await applyNavigationState({ storage: activeStorage.value, collection: 'all', folder });
  if (historyMode !== 'none') writeBrowserHistory(historyMode);
}

async function handleBrowserHistory(event) {
  if (!workspaceReady) return;

  const state = event.state?.[FILE_MANAGER_HISTORY_KEY]
    ? normalizeNavigationState(event.state[FILE_MANAGER_HISTORY_KEY], activeStorage.value)
    : navigationStateFromUrl(activeStorage.value);

  if (!state || sameNavigationState(state, currentNavigationState())) return;

  if (state.storage !== activeStorage.value) {
    await changeStorage(state.storage, { historyMode: 'none', notifyUser: false });
  }

  if (state.collection !== activeNav.value) {
    await changeNav(state.collection, { historyMode: 'none' });
  }

  if (state.folder !== activeFolder.value) {
    await changeFolder(state.folder, { historyMode: 'none' });
  }
}

function selectAsset(asset) {
  const next = new Set(selectedIds.value);
  next.has(asset.id) ? next.delete(asset.id) : next.add(asset.id);
  selectedIds.value = next;
  if (isCompactViewport.value) {
    detailAsset.value = null;
    return;
  }
  if (next.has(asset.id)) void openAssetDetails(asset); else detailAsset.value = null;
}

async function openAssetDetails(asset) {
  if (asset.type !== 'folder') {
    detailAsset.value = asset;
    return;
  }

  detailAsset.value = { ...asset, detailsLoading: true };
  try {
    const details = await loadFolderDetailsApi(activeStorage.value, asset.path);
    if (detailAsset.value?.id === asset.id) detailAsset.value = { ...asset, ...details, detailsLoading: false };
  } catch (error) {
    if (detailAsset.value?.id === asset.id) detailAsset.value = { ...asset, detailsLoading: false };
    notify(error.message, 'failed');
  }
}

function clearSelection() {
  selectedIds.value = new Set();
  detailAsset.value = null;
}

function openActionModal(type, item = null) {
  if ((type === 'move' || type === 'delete') && !selectedAssets.value.length) return;
  if (type === 'move' && !selectedAssetsAreFilesOnly.value) return;
  if (type === 'rename' && !item) return;
  window.clearTimeout(actionCloseTimer);
  actionSubmitState.value = 'idle';
  actionModalItem.value = item;
  actionModalCount.value = item ? 1 : selectedAssets.value.length;
  actionModal.value = type;
  folderName.value = '';
  assetName.value = type === 'rename' ? item.name : '';
  moveDestination.value = '';
}

function closeActionModal(force = false) {
  if (!force && actionSubmitState.value !== 'idle') return;
  window.clearTimeout(actionCloseTimer);
  actionCloseTimer = undefined;
  actionSubmitState.value = 'idle';
  actionModal.value = null;
  actionModalItem.value = null;
  actionModalCount.value = 0;
  folderName.value = '';
  assetName.value = '';
  moveDestination.value = '';
  deleteImpactSequence++;
  deleteImpact.value = null;
  deleteImpactLoading.value = false;
  deletePaths.value = [];
}

async function submitActionModal() {
  const type = actionModal.value;
  const selected = selectedAssets.value;
  const uploadItem = actionModalItem.value;

  if (type === 'create' && !folderName.value.trim()) {
    notify('Enter a folder name.', 'failed');
    return;
  }
  if (type === 'rename' && !assetName.value.trim()) {
    notify('Enter a new name.', 'failed');
    return;
  }

  actionSubmitState.value = 'submitting';
  clearWorkspaceCache();
  let successMessage = '';
  try {
    if (type === 'create') {
      await createFolderApi(activeStorage.value, activeFolder.value, folderName.value.trim());
      await refreshAssets({ refreshFolders: true });
      successMessage = 'Folder created.';
    } else if (type === 'move') {
      if (!selected.length) throw new Error('Select at least one asset to move.');
      const count = selected.length;
      await moveAssets(activeStorage.value, selected.map((asset) => asset.path), moveDestination.value);
      clearSelection();
      await refreshAssets({ refreshFolders: true });
      successMessage = `${count} asset${count === 1 ? '' : 's'} moved.`;
    } else if (type === 'rename') {
      const asset = actionModalItem.value;
      if (!asset?.path) throw new Error('The asset is no longer available.');
      const renamed = await renameAsset(activeStorage.value, asset.path, assetName.value.trim());
      const renamedActiveFolder = asset.type === 'folder' && (activeFolder.value === asset.path || activeFolder.value.startsWith(`${asset.path}/`));
      if (renamedActiveFolder) {
        activeFolder.value = renamed.path + activeFolder.value.slice(asset.path.length);
      }
      selectedIds.value = new Set([renamed.id]);
      detailAsset.value = renamed;
      await refreshAssets({ refreshFolders: true });
      if (renamedActiveFolder) writeBrowserHistory('replace');
      successMessage = `${asset.type === 'folder' ? 'Folder' : 'File'} renamed.`;
    } else if (type === 'delete') {
      const paths = deletePaths.value;
      if (!paths.length || !deleteImpact.value) throw new Error('The delete preview is no longer available. Please try again.');
      const result = await deleteAssets(activeStorage.value, paths);
      clearSelection();
      await refreshAssets({ refreshFolders: true, refreshUsage: true });
      if (result.failedCount) {
        actionSubmitState.value = 'idle';
        const deleted = countLabel(result.deletedCount, 'asset');
        const failed = countLabel(result.failedCount, 'asset');
        notify(result.deletedCount ? `${deleted} deleted. ${failed} could not be deleted.` : `${failed} could not be deleted.`, 'failed');
        closeActionModal(true);
        return;
      }
      successMessage = `${countLabel(result.deletedCount, 'asset')} deleted.`;
    } else if (type === 'delete-upload') {
      if (!uploadItem?.asset?.path) throw new Error('The uploaded file is no longer available.');
      await deleteAsset(uploadItem.storage, uploadItem.asset.path);
      onPondRemoved({ id: uploadItem.id });
      if (uploadItem.storage === activeStorage.value) await refreshAssets({ refreshFolders: true, refreshUsage: true });
      successMessage = 'Uploaded file deleted.';
    } else {
      throw new Error('Unsupported file action.');
    }
    actionSubmitState.value = 'success';
    notify(successMessage);
    actionCloseTimer = window.setTimeout(() => closeActionModal(true), 2200);
  } catch (error) {
    actionSubmitState.value = 'idle';
    notify(error.message, 'failed');
  }
}

function openDemoUpload() {
  notify('Choose files to start a real upload.');
}

function addFiles(fileList) {
  if (!fileList?.length || !uploadEngine.value) return;

  uploadOpen.value = true;
  uploadMinimized.value = false;
  uploadEngine.value.addFiles(fileList, {
    storage: activeStorage.value,
    path: activeFolder.value,
  });
}

function handleUploadInput(event) {
  addFiles(event.target.files);
  event.target.value = '';
}

function scheduleFolderBatchRefresh(storage) {
  window.clearTimeout(folderBatchRefreshTimer);
  folderBatchRefreshTimer = window.setTimeout(async () => {
    clearWorkspaceCache();
    if (storage !== activeStorage.value) return;
    try {
      await refreshAssets({ refreshFolders: true, refreshUsage: true });
    } catch (error) {
      notify(error.message, 'failed');
    }
  }, 100);
}

async function startFolderUpload(fileList) {
  if (!fileList?.length) return;

  uploadOpen.value = true;
  uploadMinimized.value = false;
  const storage = activeStorage.value;
  const path = activeFolder.value;
  let coordinator;
  coordinator = createFolderUploadCoordinator({
    files: fileList,
    storage,
    path,
    getMaxParallel: () => getUploadOptions().maxParallel,
    onItemAdded: (job) => {
      uploads.value.push({
        id: job.id,
        name: job.name,
        size: formatBytes(job.bytes),
        progress: job.progress,
        status: job.status,
        error: job.error,
        storage,
        path: job.path,
        folderBatch: true,
      });
      folderUploadBatches.set(job.id, coordinator);
    },
    onItemUpdated: (job) => {
      const item = uploads.value.find((candidate) => candidate.id === job.id);
      if (item) Object.assign(item, { progress: job.progress, status: job.status, error: job.error, path: job.path });
    },
    onItemDone: (job, asset) => {
      const item = uploads.value.find((candidate) => candidate.id === job.id);
      if (!item) return;
      item.asset = asset;
      item.path = asset.path;
      // A folder can contain thousands of files. The single final refresh below
      // keeps the asset grid from creating a preview request for every upload.
    },
    onStartError: (error) => notify(error?.message || 'Folder upload tidak dapat dimulai.', 'failed'),
    onFinished: ({ batch, jobs }) => {
      jobs.filter((job) => job.status !== 'error').forEach((job) => folderUploadBatches.delete(job.id));
      const failed = jobs.filter((job) => job.status === 'error').length;
      if (batch?.storage) scheduleFolderBatchRefresh(batch.storage);
      if (failed) notify(`${failed} file folder gagal diupload. Gunakan Retry setelah batch selesai.`, 'failed');
    },
  });
  coordinator.setPaused(uploadsPaused.value);
  await coordinator.start();
}

function handleFolderUploadInput(event) {
  void startFolderUpload(event.target.files);
  event.target.value = '';
}

function toggleUploadsPause() {
  uploadsPaused.value = !uploadsPaused.value;
  new Set(folderUploadBatches.values()).forEach((coordinator) => coordinator.setPaused(uploadsPaused.value));
}

function onPondAdded(item) {
  uploads.value.push({
    id: item.id,
    name: item.name,
    size: formatBytes(item.size),
    progress: 0,
    status: 'uploading',
    storage: item.storage,
    path: item.path,
  });
}

function onPondProgress({ id, progress }) {
  const item = uploads.value.find((candidate) => candidate.id === id);
  if (item) item.progress = progress;
}

async function onPondDone({ id, asset, storage }) {
  const item = uploads.value.find((candidate) => candidate.id === id);
  if (!item) return;
  item.status = 'done';
  item.progress = 100;
  item.asset = asset;
  item.storage = storage;
  item.path = asset.path;
  if (storage !== activeStorage.value) return;

  upsertAsset(asset);
  clearWorkspaceCache();
  try {
    await refreshAssets({ refreshFolders: true, refreshUsage: true });
  } catch (error) {
    notify(error.message, 'failed');
  } finally {
    upsertAsset(asset);
  }
}

function onPondFailed({ id, message }) {
  const item = uploads.value.find((candidate) => candidate.id === id);
  if (!item) return;
  item.status = 'error';
  item.error = message;
  notify(message, 'failed');
}

function onPondRemoved({ id }) {
  uploads.value = uploads.value.filter((candidate) => candidate.id !== id);
  if (!uploads.value.length) uploadOpen.value = false;
}

function retryUpload(item, { silent = false } = {}) {
  if (item.folderBatch) {
    const accepted = folderUploadBatches.get(item.id)?.retry(item.id);
    if (!accepted && !silent) notify('Tunggu batch folder selesai sebelum mencoba ulang file ini.', 'failed');

    return Boolean(accepted);
  }

  if (!uploadEngine.value) return false;
  item.status = 'uploading';
  item.progress = 0;
  item.error = '';
  void uploadEngine.value.retryFile(item.id);

  return true;
}

function retryFailedUploads() {
  let retried = 0;
  const retriedBatches = new Set();

  uploads.value.filter((item) => item.status === 'error' && item.folderBatch).forEach((item) => {
    const coordinator = folderUploadBatches.get(item.id);
    if (!coordinator || retriedBatches.has(coordinator)) return;
    retriedBatches.add(coordinator);
    retried += coordinator.retryFailed();
  });
  uploads.value
    .filter((item) => item.status === 'error' && !item.folderBatch)
    .forEach((item) => { if (retryUpload(item, { silent: true })) retried += 1; });

  if (!retried) {
    notify('Tidak ada file gagal yang siap dicoba ulang.', 'failed');
    return;
  }

  uploadOpen.value = true;
  uploadMinimized.value = false;
  notify(`${retried} file gagal dimasukkan kembali ke antrean upload.`);
}

function createFolder() {
  openActionModal('create');
}

async function deleteSelected() {
  const paths = selectedAssets.value.map((asset) => asset.path);
  if (!paths.length) return;

  const requestId = ++deleteImpactSequence;
  window.clearTimeout(actionCloseTimer);
  actionSubmitState.value = 'idle';
  actionModalItem.value = null;
  actionModalCount.value = paths.length;
  deletePaths.value = paths;
  deleteImpact.value = null;
  deleteImpactLoading.value = true;
  actionModal.value = 'delete';

  try {
    const impact = await previewDelete(activeStorage.value, paths);
    if (requestId !== deleteImpactSequence || actionModal.value !== 'delete') return;

    deleteImpact.value = impact;
    actionModalCount.value = impact.targetCount;
  } catch (error) {
    if (requestId !== deleteImpactSequence) return;
    closeActionModal(true);
    notify(error.message, 'failed');
  } finally {
    if (requestId === deleteImpactSequence) deleteImpactLoading.value = false;
  }
}

function downloadAsset(asset) {
  if (asset?.downloadUrl) window.location.assign(asset.downloadUrl);
}

async function copyAssetUrl(asset) {
  if (!asset?.previewUrl) return;
  try {
    await navigator.clipboard?.writeText(new URL(asset.previewUrl, window.location.origin).toString());
    notify('Secure preview URL copied');
  } catch {
    notify('Unable to copy the secure preview URL.', 'failed');
  }
}

function downloadSelected() {
  const selected = assets.find((asset) => selectedIds.value.has(asset.id));
  if (selected?.downloadUrl) window.location.assign(selected.downloadUrl);
}

async function copySelectedUrl() {
  const selected = assets.find((asset) => selectedIds.value.has(asset.id));
  if (!selected?.previewUrl) return;
  await navigator.clipboard?.writeText(new URL(selected.previewUrl, window.location.origin).toString());
  notify('Secure preview URL copied');
}

function openMoveDialog() {
  openActionModal('move');
}

async function toggleSelectedStar() {
  const selected = selectedAssets.value;
  if (!selected.length || !selectedAssetsAreFilesOnly.value) return;

  const willRemove = selectedAssetsAreAllStarred.value;
  clearWorkspaceCache();
  try {
    await Promise.all(selected.map((asset) => toggleAssetStar(activeStorage.value, asset.path)));
    await refreshAssets();
    notify(willRemove ? 'Removed from Starred' : 'Added to Starred');
  } catch (error) {
    notify(error.message, 'failed');
  }
}

function openSelectedDetails() {
  const selected = selectedAssets.value[0];
  if (selected) void openAssetDetails(selected);
}


function clearFilters() {
  search.value = '';
  filter.value = 'all';
}

async function removeUpload(item) {
  if (item.status === 'done') {
    if (!item.asset?.path) {
      onPondRemoved({ id: item.id });
      return;
    }
    openActionModal('delete-upload', item);
    return;
  }
  if (item.folderBatch) {
    if (item.status === 'error' || item.status === 'cancelled') {
      folderUploadBatches.delete(item.id);
      onPondRemoved({ id: item.id });
    } else {
      folderUploadBatches.get(item.id)?.cancel(item.id);
    }

    return;
  }

  if (!uploadEngine.value) {
    onPondRemoved({ id: item.id });
    return;
  }
  void uploadEngine.value.removeFile(item.id);
}

function closeUploads() {
  uploadOpen.value = false;
  uploadMinimized.value = false;
}

function openUploads() {
  if (!hasUploadCenter.value) return;
  uploadOpen.value = true;
  uploadMinimized.value = false;
}

async function openSettings() {
  closeMobileSidebar();
  try {
    storageSettings.value = await loadStorageSettings();
    settingsOpen.value = true;
  } catch (error) {
    notify(error.message, 'failed');
  }
}

async function saveSettings(settings) {
  const saved = await saveStorageSettings(settings);
  storageSettings.value = saved;
  clearWorkspaceCache();
  new Set(folderUploadBatches.values()).forEach((coordinator) => coordinator.refreshConcurrency());

  const nextStorage = storageProfiles[saved.defaultStorage] ? saved.defaultStorage : Object.keys(storageProfiles)[0];
  if (nextStorage) {
    await changeStorage(nextStorage, { historyMode: 'replace', notifyUser: false });
  }
  notify('Storage and upload settings saved');
  settingsOpen.value = false;
}

async function testConnection({ storage, settings } = {}) {
  const saved = await saveStorageSettings(settings);
  storageSettings.value = saved;
  clearWorkspaceCache();

  new Set(folderUploadBatches.values()).forEach((coordinator) => coordinator.refreshConcurrency());
  const profile = saved.connections?.find((connection) => connection.id === storage);
  const result = await testStorageConnection(storage);
  const name = profile?.name || storageProfiles[storage]?.name || storage;
  notify(result.connected ? `Connection to ${name} successful` : `Connection to ${name} is unavailable`, result.connected ? 'success' : 'failed');

  return { ...result, name };
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
watch([activeFolder, activeStorage, activeNav], queueBreadcrumbOverflowUpdate);
onMounted(() => {
  themeQuery.addEventListener('change', syncSystemTheme);
  mobileViewportQuery.addEventListener('change', syncMobileViewport);
  compactViewportQuery.addEventListener('change', syncCompactViewport);
  window.addEventListener('keydown', handleGlobalKeydown);
  window.addEventListener('popstate', handleBrowserHistory);
  window.addEventListener('resize', updateBreadcrumbOverflow);
  initializeWorkspace().then(async (defaultStorage) => {
    const initialState = browserNavigationState(defaultStorage) || { storage: defaultStorage, collection: 'all', folder: '' };
    await applyNavigationState(initialState, { refreshFolders: true });
    workspaceReady = true;
    writeBrowserHistory('replace');
    updateBreadcrumbOverflow();
  }).catch((error) => notify(error.message, 'failed'));
});
onBeforeUnmount(() => {
  themeQuery.removeEventListener('change', syncSystemTheme);
  mobileViewportQuery.removeEventListener('change', syncMobileViewport);
  compactViewportQuery.removeEventListener('change', syncCompactViewport);
  window.removeEventListener('keydown', handleGlobalKeydown);
  window.removeEventListener('popstate', handleBrowserHistory);
  window.removeEventListener('resize', updateBreadcrumbOverflow);
});
  window.clearTimeout(actionCloseTimer);
  window.clearTimeout(notify.timer);
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
      @create-folder="createFolder"
      @open-settings="openSettings"
      @close-mobile="closeMobileSidebar"
    />

    <button v-if="mobileSidebarOpen" class="mobile-sidebar-backdrop" type="button" aria-label="Close navigation" @click="closeMobileSidebar"></button>
    <main class="fm-workspace">
      <header class="topbar">
        <button class="mobile-sidebar-toggle" type="button" aria-controls="mobile-file-navigation" :aria-expanded="mobileSidebarOpen" aria-label="Open navigation" @click="mobileSidebarOpen = true">
          <i class="bi bi-list"></i>
        </button>
        <div class="breadcrumb-wrap" :class="{ 'has-left-overflow': breadcrumbOverflow.left, 'has-right-overflow': breadcrumbOverflow.right }">
          <small>{{ activeProfile.shortName }}</small>
          <div ref="breadcrumbScroller" class="breadcrumb-scroller" aria-label="Folder breadcrumb" @scroll.passive="updateBreadcrumbOverflow">
            <div class="breadcrumb-trail">
              <button type="button" @click="changeFolder('')">Assets</button>
              <template v-if="breadcrumbSegments.length">
                <template v-for="(segment, index) in breadcrumbSegments" :key="segment.path">
                  <i class="bi bi-chevron-right"></i>
                  <button v-if="index < breadcrumbSegments.length - 1" type="button" @click="changeFolder(segment.path)">{{ segment.name }}</button>
                  <strong v-else>{{ segment.name }}</strong>
                </template>
              </template>
              <template v-else>
                <i class="bi bi-chevron-right"></i>
                <strong>{{ activeFolderName }}</strong>
              </template>
            </div>
          </div>
        </div>
        <div class="topbar-actions">
          <div class="search-field">
            <i class="bi bi-search"></i>
            <input v-model="search" placeholder="Search assets, tags, or file types…" />
            <kbd>Ctrl K</kbd>
          </div>
          <button v-if="hasUploadCenter" class="icon-button upload-center-button" type="button" :title="`Open uploads (${uploadCenterCount})`" :aria-label="`Open uploads (${uploadCenterCount})`" @click="openUploads">
            <i class="bi bi-cloud-arrow-up"></i><span class="upload-center-count">{{ uploadCenterCount }}</span>
          </button>
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
          <span v-if="workspaceLoadState === 'refreshing'" class="workspace-refresh-state" role="status">
            <i class="bi bi-arrow-repeat"></i>{{ workspaceLoadMessage }}
          </span>
        </div>
        <div class="primary-actions">
          <button class="btn btn-light border" @click="createFolder"><i class="bi bi-folder-plus"></i> New folder</button>
          <div class="btn-group">
            <button class="btn btn-primary upload-button" @click="uploadInput.click()"><i class="bi bi-cloud-arrow-up"></i> Upload files</button>
            <button class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Upload options</span></button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm upload-menu">
              <li><button class="dropdown-item" @click="uploadInput.click()"><i class="bi bi-files me-2"></i>Upload files</button></li>
              <li><button class="dropdown-item" @click="folderInput.click()"><i class="bi bi-folder2-open me-2"></i>Upload folder</button></li>
              <li><button class="dropdown-item" @click="openDemoUpload"><i class="bi bi-play-circle me-2"></i>Run upload demo</button></li>
            </ul>
          </div>
          <input ref="uploadInput" class="d-none" type="file" multiple @change="handleUploadInput" />
          <input ref="folderInput" class="d-none" type="file" multiple webkitdirectory @change="handleFolderUploadInput" />
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
          <button class="btn btn-sm btn-light border" :class="{ active: hasActiveFilters }" :title="hasActiveFilters ? 'Clear active filters' : 'No active filters'" @click="hasActiveFilters && clearFilters()"><i class="bi bi-funnel"></i> Filters <span v-if="activeFilterCount">{{ activeFilterCount }}</span></button>
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
        <button v-if="selectedAssetsAreFilesOnly" @click="downloadSelected"><i class="bi bi-download"></i> Download</button>
        <button v-if="selectedAssetsAreFilesOnly" @click="openMoveDialog"><i class="bi bi-folder-symlink"></i> Move</button>
        <button v-if="selectedAssetsAreFilesOnly" @click="copySelectedUrl"><i class="bi bi-link-45deg"></i> Copy URL</button>
        <button @click="deleteSelected"><i class="bi bi-trash3"></i> Delete</button>
        <div class="dropdown">
          <button type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More selected file actions"><i class="bi bi-three-dots"></i></button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li v-if="selectedAssetsAreFilesOnly"><button class="dropdown-item" @click="toggleSelectedStar"><i class="bi me-2" :class="selectedAssetsAreAllStarred ? 'bi-star-fill' : 'bi-star'"></i>{{ selectedAssetsAreAllStarred ? 'Remove from Starred' : 'Add to Starred' }}</button></li>
            <li><button class="dropdown-item" @click="openSelectedDetails"><i class="bi bi-info-circle me-2"></i>View details</button></li>
          </ul>
        </div>
      </section>

      <section class="asset-area" :class="{ 'with-drawer': detailAsset }">
        <div class="asset-scroll">
          <div v-if="workspaceLoadState === 'loading'" class="workspace-loading" role="status">
            <span class="workspace-loading-spinner" aria-hidden="true"></span>
            <strong>{{ workspaceLoadMessage }}</strong>
            <small>Fetching files and folders...</small>
          </div>
          <div v-else-if="filteredAssets.length" class="asset-grid" :class="{ 'list-view': viewMode === 'list' }">
            <AssetCard
              v-for="asset in filteredAssets"
              :key="asset.id"
              :asset="asset"
              :selected="selectedIds.has(asset.id)"
              :view-mode="viewMode"
              @select="selectAsset"
              @open-details="openAssetDetails"
              @open-folder="changeFolder($event.path)"
            />
          </div>
          <div v-else class="empty-state">
            <span><i class="bi bi-search"></i></span>
            <h2>{{ emptyState.title }}</h2>
            <p>{{ emptyState.description }}</p>
            <button v-if="hasActiveFilters" class="btn btn-outline-primary" @click="clearFilters">Clear filters</button>
            <button v-else-if="activeNav === 'all' && !activeFolder" class="btn btn-primary" @click="uploadInput.click()">Upload files</button>
          </div>
        </div>
        <DetailsDrawer v-if="detailAsset" :asset="detailAsset" @close="detailAsset = null" @download="downloadAsset" @copy-url="copyAssetUrl" @rename="openActionModal('rename', $event)" @open-folder="changeFolder($event.path)" />
      </section>
    </main>

    <Transition name="action-modal">
      <div v-if="actionModal" class="modal-layer action-modal-layer" @click.self="closeActionModal">
        <section class="action-modal" :class="{ 'is-danger': actionModal === 'delete' || actionModal === 'delete-upload' }" role="dialog" aria-modal="true" aria-labelledby="action-modal-title">
          <header>
            <div>
              <small>FILE MANAGER</small>
              <h2 id="action-modal-title">
                <template v-if="actionModal === 'create'">Create folder</template>
                <template v-else-if="actionModal === 'move'">Move {{ actionModalCount }} selected asset<span v-if="actionModalCount !== 1">s</span></template>
                <template v-else-if="actionModal === 'delete'">{{ deleteModalTitle }}</template>
                <template v-else-if="actionModal === 'delete-upload'">Delete uploaded file</template>
                <template v-else>Rename {{ actionModalItem?.type === 'folder' ? 'folder' : 'file' }}</template>
              </h2>
            </div>
            <button type="button" aria-label="Close dialog" :disabled="actionSubmitState !== 'idle'" @click="closeActionModal"><i class="bi bi-x-lg"></i></button>
          </header>
          <form @submit.prevent="submitActionModal">
            <div class="action-modal-body">
              <template v-if="actionModal === 'create'">
                <label for="folder-name">Folder name</label>
                <input id="folder-name" v-model="folderName" class="form-control" type="text" maxlength="80" placeholder="e.g. Campaign assets" :disabled="actionSubmitState !== 'idle'" autofocus>
                <p class="action-modal-help">The folder will be created in {{ activeFolderName }}.</p>
              </template>
               <template v-else-if="actionModal === 'rename'">
                 <label for="asset-name">New name</label>
                 <input id="asset-name" v-model="assetName" class="form-control" type="text" maxlength="180" :disabled="actionSubmitState !== 'idle'" autofocus>
                 <p class="action-modal-help">Rename {{ actionModalItem?.type === 'folder' ? 'this folder and its contents' : 'this file' }}.</p>
               </template>
              <template v-else-if="actionModal === 'move'">
                <label for="move-destination">Destination folder</label>
                <select id="move-destination" v-model="moveDestination" class="form-select" :disabled="actionSubmitState !== 'idle'">
                  <option v-for="folder in moveDestinations" :key="folder.path" :value="folder.path">{{ folder.name }}</option>
                </select>
                <p class="action-modal-help">Selected assets will remain in {{ activeProfile.shortName }} storage.</p>
              </template>
              <template v-else-if="actionModal === 'delete'">
                <div v-if="deleteImpactLoading" class="action-modal-preview-loading">
                  <span class="action-preview-spinner"></span>
                  <span>Calculating deletion impact...</span>
                </div>
                <div v-else class="action-modal-alert">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  <div>
                    <strong>{{ deleteModalTitle }}</strong>
                    <p>{{ deleteImpactSummary }}</p>
                    <p v-if="deleteImpactIncludedText">{{ deleteImpactIncludedText }}</p>
                  </div>
                </div>
              </template>
              <template v-else>
                <div class="action-modal-alert">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  <div>
                    <strong>Delete {{ actionModalItem?.name }}?</strong>
                    <p>This action cannot be undone.</p>
                  </div>
                </div>
              </template>
            </div>
            <footer>
              <button type="button" class="btn btn-light border" :disabled="actionSubmitState !== 'idle'" @click="closeActionModal">Cancel</button>
              <button v-if="actionSubmitState === 'idle'" type="submit" class="btn" :class="actionModal === 'delete' || actionModal === 'delete-upload' ? 'btn-danger' : 'btn-primary'" :disabled="(actionModal === 'move' && moveDestination === activeFolder) || (actionModal === 'delete' && (deleteImpactLoading || !deleteImpact))">
                <template v-if="actionModal === 'create'">Create folder</template>
                <template v-else-if="actionModal === 'move'">Move assets</template>
                <template v-else-if="actionModal === 'rename'">Rename</template>
                <template v-else-if="actionModal === 'delete'">Delete permanently</template>
                <template v-else>Delete file</template>
              </button>
              <button v-else-if="actionSubmitState === 'submitting'" type="button" class="btn btn-secondary action-submit-state" disabled><span class="action-submit-spinner"></span><span><template v-if="actionModal === 'create'">Creating folder</template><template v-else-if="actionModal === 'move'">Moving assets</template><template v-else-if="actionModal === 'rename'">Renaming</template><template v-else>Deleting assets</template></span></button>
              <button v-else type="button" class="btn btn-success action-submit-state" disabled><i class="bi bi-check-circle-fill"></i><span>Success</span></button>
            </footer>
          </form>
        </section>
      </div>
    </Transition>


    <div v-if="isDragging" class="drop-overlay">
      <div><span><i class="bi bi-cloud-arrow-up"></i></span><h2>Drop files or folders here</h2><p>They will be uploaded to {{ activeFolderName }} on {{ activeProfile.shortName }}.</p></div>
    </div>

    <FilePondUploadEngine
      ref="uploadEngine"
      :paused="uploadsPaused"
      @added="onPondAdded"
      @progress="onPondProgress"
      @done="onPondDone"
      @failed="onPondFailed"
      @removed="onPondRemoved"
    />

    <UploadPanel
      v-if="uploadOpen"
      :uploads="uploads"
      :minimized="uploadMinimized"
      :paused="uploadsPaused"
      @toggle-minimize="uploadMinimized = !uploadMinimized"
      @toggle-pause="toggleUploadsPause"
      @close="closeUploads"
      @remove="removeUpload"
      @retry="retryUpload"
      @retry-failed="retryFailedUploads"
    />

    <StorageSettingsModal
      :show="settingsOpen"
      :active-storage="activeStorage"
      :settings="storageSettings"
      @close="settingsOpen = false"
      :save-handler="saveSettings"
      :test-handler="testConnection"
    />

    <div v-if="toast" class="fm-toast" :class="`is-${toast.status}`" role="status">
      <i class="bi" :class="toast.status === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"></i>
      <span>{{ toast.message }}</span>
    </div>
  </div>
</template>
