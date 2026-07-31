import { reactive } from 'vue';
import uploadFilePlaceholder from '../assets/file-upload-placeholder.svg';

const config = window.FILEMANAGER_V2_CONFIG || {};

export const storageProfiles = reactive({});
export const folders = reactive([]);
export const assets = reactive([]);

export const navItems = [
  { id: 'all', label: 'All assets', icon: 'bi-grid' },
  { id: 'recent', label: 'Recently used', icon: 'bi-clock-history' },
  { id: 'starred', label: 'Starred', icon: 'bi-star' },
];

export const iconForKind = {
  folder: 'bi-folder2',
  image: 'bi-file-earmark-image',
  document: 'bi-file-earmark-text',
  video: 'bi-file-earmark-play',
  archive: 'bi-file-earmark-zip',
  audio: 'bi-file-earmark-music',
};

export const uploadOptions = reactive({
  chunkSize: 8 * 1024 * 1024,
  chunkThreshold: 16 * 1024 * 1024,
  maxParallel: 3,
  maxFileSize: 1024 * 1024 * 1024,
  retryAttempts: 2,
});

export function getUploadOptions() {
  return uploadOptions;
}

function csrfHeaders(headers = {}) {
  return {
    Accept: 'application/json',
    'X-CSRF-TOKEN': config.csrfToken,
    ...headers,
  };
}

async function request(path, options = {}) {
  const response = await fetch(`${config.apiBase}${path}`, {
    credentials: 'same-origin',
    ...options,
    headers: csrfHeaders(options.headers),
  });

  if (response.status === 204) return null;

  const body = await response.json().catch(() => ({}));
  if (!response.ok) {
    const message = body.message || body.errors?.[Object.keys(body.errors)[0]]?.[0] || 'Permintaan File Manager V2 gagal.';
    const error = new Error(message);
    error.status = response.status;
    throw error;
  }

  return body.data;
}

function uploadFormData(path, data, onProgress = () => {}, bindAbort = () => {}) {
  return new Promise((resolve, reject) => {
    const request = new XMLHttpRequest();
    bindAbort(() => request.abort());
    request.open('POST', `${config.apiBase}${path}`, true);
    request.withCredentials = true;
    Object.entries(csrfHeaders()).forEach(([header, value]) => {
      if (value) request.setRequestHeader(header, value);
    });
    request.upload.addEventListener('progress', (event) => {
      if (event.lengthComputable) onProgress(Math.round((event.loaded / event.total) * 100));
    });
    request.addEventListener('load', () => {
      let body = null;
      try {
        body = JSON.parse(request.responseText || '{}');
      } catch {
        // Reverse proxies can return an HTML error page (for example, nginx 502).
      }
      if (request.status >= 200 && request.status < 300) {
        if (body?.data !== undefined) {
          resolve(body.data);
          return;
        }

        const error = new Error(`Upload berhasil, tetapi respons server tidak valid (HTTP ${request.status}).`);
        error.status = request.status;
        reject(error);
        return;
      }

      const error = new Error(body?.message || body?.errors?.[Object.keys(body.errors)[0]]?.[0] || `Upload gagal (HTTP ${request.status}).`);
      error.status = request.status;
      reject(error);
    });
    request.addEventListener('error', () => reject(new Error('Koneksi upload terputus.')));
    request.addEventListener('abort', () => reject(new Error('Upload dibatalkan.')));
    request.send(data);
  });
}

async function withUploadRetry(task, { waitForResume, isAborted }) {
  const attempts = Math.max(0, Number(uploadOptions.retryAttempts) || 0);

  for (let attempt = 0; attempt <= attempts; attempt += 1) {
    try {
      return await task();
    } catch (error) {
      const clientError = Number(error?.status || 0) >= 400 && Number(error.status) < 500;
      if (isAborted() || clientError || attempt === attempts) throw error;

      await waitForResume();
      await new Promise((resolve) => window.setTimeout(resolve, Math.min(2000, 250 * (2 ** attempt))));
    }
  }

  throw new Error('Upload gagal setelah seluruh percobaan ulang dijalankan.');
}

function normalizeProfile(profile) {
  return {
    ...profile,
    status: profile.connected ? 'Connected' : 'Unavailable',
    tone: profile.connected ? 'success' : 'secondary',
    usedBytes: Number(profile.usedBytes || 0),
    quotaBytes: Number(profile.quotaBytes || 0),
    usagePercent: Number(profile.usagePercent || 0),
    usedLabel: profile.usedLabel || '0 B',
    quotaLabel: profile.quotaLabel || 'No limit',
  };
}

function colorForKind(kind) {
  return {
    image: '#dce9ff',
    video: '#e5e7ff',
    audio: '#e4f0ff',
    archive: '#eee8da',
    document: '#fde2e2',
    folder: '#e9edff',
  }[kind] || '#eef1f7';
}

function isSvgAsset(asset) {
  return String(asset.extension || '').toLowerCase() === 'svg';
}

function normalizeAsset(asset) {
  return {
    ...asset,
    ext: asset.extension || (asset.type === 'folder' ? 'FOLDER' : 'FILE'),
    src: isSvgAsset(asset) ? uploadFilePlaceholder : asset.previewUrl,
    modified: asset.modifiedLabel || '—',
    dimensions: '—',
    tags: [],
    color: colorForKind(asset.kind),
    starred: Boolean(asset.starred),
  };
}
export function upsertAsset(asset) {
  const normalized = normalizeAsset(asset);
  const index = assets.findIndex((item) => item.id === normalized.id);
  if (index >= 0) {
    assets.splice(index, 1, normalized);
  } else {
    assets.unshift(normalized);
  }
  return normalized;

}

export function replaceAssets(payload) {
  assets.splice(0, assets.length, ...payload.items.map(normalizeAsset));
}

export function replaceFolders(folderItems) {
  folders.splice(0, folders.length, ...folderItems.map((folder) => ({
    ...folder,
    id: folder.path,
    depth: folder.path.split('/').filter(Boolean).length - 1,
    icon: 'bi-folder2',
    count: '',
  })));
}
export async function initializeWorkspace() {
  const bootstrap = await request('/bootstrap');
  replaceStorageProfiles(bootstrap.profiles);
  Object.assign(uploadOptions, bootstrap.upload);

  return bootstrap.defaultStorage;
}

function replaceStorageProfiles(profiles) {
  Object.keys(storageProfiles).forEach((key) => delete storageProfiles[key]);
  profiles.forEach((profile) => {
    storageProfiles[profile.id] = normalizeProfile(profile);
  });
}

export async function refreshStorageProfiles() {
  const profiles = await request('/profiles');
  replaceStorageProfiles(profiles);
}

export async function loadStorageSettings() {
  return request('/settings');
}

export async function saveStorageSettings(settings) {
  const saved = await request('/settings', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(settings),
  });
  await refreshStorageProfiles();

  return saved;
}

export async function testStorageConnection(storage) {
  return request('/settings/test', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage }),
  });
}

export async function loadAssets(storage, options = {}) {
  const params = new URLSearchParams({
    storage,
    path: options.path || '',
    search: options.search || '',
    type: options.type || 'all',
    sort: options.sort || 'modified',
    collection: options.collection || 'all',
  });
  return request(`/assets?${params.toString()}`);
}

export async function loadFolders(storage) {
  const params = new URLSearchParams({ storage });
  return request(`/folders?${params.toString()}`);
}
export async function loadFolderDetails(storage, path) {
  const params = new URLSearchParams({ storage, path });
  return request(`/assets/details?${params.toString()}`);
}

export async function renameAsset(storage, path, name) {
  return request('/assets', {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, path, name }),
  });
}


export async function createFolder(storage, path, name) {
  return request('/folders', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, path, name }),
  });
}

export async function deleteAsset(storage, path) {
  const params = new URLSearchParams({ storage, path });
  return request(`/assets?${params.toString()}`, { method: 'DELETE' });
}
export async function previewDelete(storage, paths) {
  return request('/assets/delete-preview', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, paths }),
  });
}

export async function deleteAssets(storage, paths) {
  return request('/assets/delete-batch', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, paths }),
  });
}


export async function moveAssets(storage, paths, destination) {
  return request('/assets/move', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, paths, destination }),
  });
}

export async function toggleAssetStar(storage, path) {
  return request('/assets/star', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, path }),
  });
}

export async function uploadFile(file, {
  storage,
  path = '',
  batchId = null,
  idempotencyKey = null,
  onProgress = () => {},
  waitForResume = async () => {},
  onAbort = () => {},
}) {
  let abortCurrentRequest = () => {};
  let uploadSessionId = null;
  let aborted = false;
  onAbort(() => {
    aborted = true;
    abortCurrentRequest();
    if (uploadSessionId) {
      void request(`/uploads/${uploadSessionId}`, { method: 'DELETE' }).catch(() => {});
    }
  });

  if (file.size < uploadOptions.chunkThreshold) {
    const asset = await withUploadRetry(() => {
      const data = new FormData();
      data.append('storage', storage);
      data.append('path', path);
      data.append('file', file);
      if (batchId) data.append('batchId', batchId);
      if (idempotencyKey) data.append('idempotencyKey', idempotencyKey);
      return uploadFormData('/assets/upload', data, onProgress, (abort) => {
        abortCurrentRequest = abort;
      });
    }, { waitForResume, isAborted: () => aborted });
    if (aborted) throw new Error('Upload dibatalkan.');
    return normalizeAsset(asset);
  }

  const parts = Math.ceil(file.size / uploadOptions.chunkSize);
  const session = await withUploadRetry(() => request('/uploads', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, path, name: file.name, size: file.size, parts, ...(batchId ? { batchId } : {}), ...(idempotencyKey ? { idempotencyKey } : {}) }),
  }), { waitForResume, isAborted: () => aborted });
  if (session.asset) return normalizeAsset(session.asset);
  uploadSessionId = session.id;

  for (let part = 0; part < parts; part += 1) {
    await waitForResume();
    if (aborted) throw new Error('Upload dibatalkan.');
    const completedShare = part / parts;
    const partShare = 1 / parts;
    await withUploadRetry(() => {
      const data = new FormData();
      data.append('chunk', file.slice(part * uploadOptions.chunkSize, Math.min(file.size, (part + 1) * uploadOptions.chunkSize)), `${file.name}.part`);
      return uploadFormData(`/uploads/${session.id}/chunks/${part}`, data, (progress) => {
        onProgress(Math.round((completedShare + ((progress / 100) * partShare)) * 100));
      }, (abort) => {
        abortCurrentRequest = abort;
      });
    }, { waitForResume, isAborted: () => aborted });
    if (aborted) throw new Error('Upload dibatalkan.');
    onProgress(Math.round(((part + 1) / parts) * 100));
  }

  return normalizeAsset(await withUploadRetry(() => request(`/uploads/${session.id}/complete`, { method: 'POST' }), { waitForResume, isAborted: () => aborted }));
}

export async function beginFolderUploadBatch({ storage, path = '', folders, totalBytes, fileCount }) {
  return request('/uploads/batches', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ storage, path, folders, totalBytes, fileCount }),
  });
}

export async function completeFolderUploadBatch(batchId) {
  return request(`/uploads/batches/${batchId}/complete`, { method: 'POST' });
}


export async function ensureFolders(storage, relativePath) {
  const parts = relativePath.split('/').filter(Boolean);
  let current = '';
  for (const part of parts) {
    const next = current ? `${current}/${part}` : part;
    try {
      await createFolder(storage, current, part);
    } catch (error) {
      if (!/sudah digunakan/i.test(error.message)) throw error;
    }
    current = next;
  }
  return current;
}
