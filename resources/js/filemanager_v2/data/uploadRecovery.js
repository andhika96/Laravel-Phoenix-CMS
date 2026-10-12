const journalKey = 'filemanager-v2-upload-recovery';
const lifetime = 24 * 60 * 60 * 1000;
let currentJournal;
let pendingCount = 0;

function identity(file, storage, path) {
  return JSON.stringify([window.FILEMANAGER_V2_CONFIG?.recoveryScope || '', storage, path, file.name, file.size, file.lastModified || 0]);
}

function readJournal(refresh = false) {
  if (currentJournal && !refresh) return currentJournal;
  try {
    const journal = JSON.parse(window.localStorage.getItem(journalKey) || '{}');
    currentJournal = Object.fromEntries(Object.entries(journal).filter(([id, record]) => {
      if (!record || record.expiresAt <= Date.now() || typeof record.key !== 'string') return false;
      if (currentJournal?.[id]?.key === record.key && currentJournal[id].done) record.done = true;
      return true;
    }));
    pendingCount = Object.values(currentJournal).filter((record) => !record.done).length;
    return currentJournal;
  } catch {
    throw new Error('Catatan pemulihan upload di browser tidak dapat dibaca. Periksa izin penyimpanan browser.');
  }
}

function writeJournal(journal) {
  try {
    window.localStorage.setItem(journalKey, JSON.stringify(journal));
    pendingCount = Object.values(journal).filter((record) => !record.done).length;
  } catch {
    throw new Error('Catatan pemulihan upload tidak dapat disimpan. Periksa ruang dan izin penyimpanan browser.');
  }
}

export function prepareUploadRecovery(files, storage, parent = '', useRelativePath = false) {
  const journal = readJournal(true);
  const keys = Array.from(files, (file) => {
    const relativeParent = useRelativePath ? String(file.webkitRelativePath || '').replace(/\\/g, '/').split('/').slice(0, -1).join('/') : '';
    const path = [parent, relativeParent].filter(Boolean).join('/');
    const id = identity(file, storage, path);
    journal[id] ||= { key: window.crypto?.randomUUID?.() || `upload-${Date.now()}-${Math.random().toString(36).slice(2)}`, expiresAt: Date.now() + lifetime, done: false };
    return journal[id].key;
  });
  writeJournal(journal);
  return keys;
}

export function finishUploadRecovery(file, storage, path = '') {
  const journal = readJournal();
  const id = identity(file, storage, path);
  if (journal[id] && !journal[id].done) {
    journal[id].done = true;
    pendingCount -= 1;
  }
  // Initial keys are durable; server receipts recover completion after refresh
  // without rewriting thousands of journal entries for every uploaded file.
  if (pendingCount === 0) {
    window.localStorage.removeItem(journalKey);
    currentJournal = {};
  }
}

export function prepareFolderRecovery(keys, storage, path) {
  const journal = readJournal();
  const id = JSON.stringify(['folder', window.FILEMANAGER_V2_CONFIG?.recoveryScope || '', storage, path, [...keys].sort()]);
  journal[id] ||= { key: window.crypto?.randomUUID?.() || `folder-${Date.now()}-${Math.random().toString(36).slice(2)}`, expiresAt: Date.now() + lifetime, done: false };
  writeJournal(journal);
  return journal[id].key;
}

export function finishFolderRecovery(key) {
  const journal = readJournal();
  for (const record of Object.values(journal)) {
    if (record.key === key && !record.done) {
      record.done = true;
      pendingCount -= 1;
    }
  }
  if (pendingCount === 0) {
    window.localStorage.removeItem(journalKey);
    currentJournal = {};
  }
  else writeJournal(journal);
}
