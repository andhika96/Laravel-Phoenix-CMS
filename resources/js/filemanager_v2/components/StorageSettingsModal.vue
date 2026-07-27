<script setup>
import { computed, reactive, ref, watch } from 'vue';

const MEBIBYTE = 1024 * 1024;

const props = defineProps({
  show: Boolean,
  activeStorage: String,
  settings: Object,
  saveHandler: Function,
  testHandler: Function,
});

const emit = defineEmits(['close']);
const activeTab = ref('connections');
const selectedConnectionId = ref('local');
const isSubmitting = ref(false);
const isTesting = ref(false);
const submitNotice = ref(null);
const form = reactive({
  defaultStorage: 'local',
  connections: [],
  upload: {
    maxFileSize: 1024 * MEBIBYTE,
    chunkSize: 8 * MEBIBYTE,
    chunkThreshold: 16 * MEBIBYTE,
    maxParallel: 3,
    retryAttempts: 2,
  },
});

const selectedConnection = computed(() => form.connections.find((connection) => connection.id === selectedConnectionId.value) || form.connections[0] || null);

function megabytes(bytes) {
  return Math.round(Number(bytes || 0) / MEBIBYTE);
}

function setMegabytes(target, key, value) {
  target[key] = Math.max(0, Number(value || 0)) * MEBIBYTE;
}

function connectionDefaults(type) {
  return {
    local: { name: 'Local storage', shortName: 'LOCAL', icon: 'bi-device-hdd', region: '', endpoint: '', usePathStyle: false },
    s3: { name: 'AWS S3', shortName: 'AWS S3', icon: 'bi-cloud', region: 'us-east-1', endpoint: '', usePathStyle: false },
    s3_compatible: { name: 'S3-compatible storage', shortName: 'S3 API', icon: 'bi-cloud', region: 'us-east-1', endpoint: '', usePathStyle: true },
    r2: { name: 'Cloudflare R2', shortName: 'R2', icon: 'bi-cloud', region: 'auto', endpoint: '', usePathStyle: false },
  }[type];
}

function hydrate(value) {
  const settings = value || {};
  form.defaultStorage = settings.defaultStorage || props.activeStorage || 'local';
  form.connections.splice(0, form.connections.length, ...(settings.connections || []).map((connection) => ({
    ...connectionDefaults(connection.type),
    ...connection,
    accessKey: '',
    secretKey: '',
  })));
  Object.assign(form.upload, settings.upload || {});
  selectedConnectionId.value = form.connections.some((connection) => connection.id === form.defaultStorage) ? form.defaultStorage : form.connections[0]?.id || 'local';
}

function chooseConnection(id) {
  selectedConnectionId.value = id;
}

function setDefaultConnection(id) {
  const connection = form.connections.find((item) => item.id === id);
  if (!connection) return;
  connection.enabled = true;
  form.defaultStorage = id;
  submitNotice.value = null;
}

function changeConnectionType(connection) {
  const defaults = connectionDefaults(connection.type);
  Object.assign(connection, defaults, {
    id: connection.id,
    enabled: connection.enabled,
    root: connection.root || '',
    quotaBytes: connection.quotaBytes,
    bucket: connection.bucket || '',
    accessKey: connection.accessKey || '',
    secretKey: connection.secretKey || '',
    credentialsConfigured: connection.credentialsConfigured,
  });
}

function addConnection() {
  const index = form.connections.filter((connection) => connection.id.startsWith('s3-')).length + 1;
  const connection = {
    id: `s3-${index}`,
    type: 's3',
    ...connectionDefaults('s3'),
    enabled: true,
    root: '',
    quotaBytes: 1024 * MEBIBYTE,
    bucket: '',
    accessKey: '',
    secretKey: '',
    credentialsConfigured: false,
  };
  form.connections.push(connection);
  chooseConnection(connection.id);
}

function removeConnection() {
  const connection = selectedConnection.value;
  if (!connection || connection.id === 'local') return;
  if (connection.id === form.defaultStorage) {
    submitNotice.value = { type: 'error', message: 'Choose another default storage before deleting this connection.' };
    return;
  }

  form.connections.splice(form.connections.findIndex((item) => item.id === connection.id), 1);
  selectedConnectionId.value = form.connections.find((item) => item.id === form.defaultStorage)?.id || form.connections[0]?.id || 'local';
  submitNotice.value = { type: 'info', message: 'This connection will be removed after you save settings.' };
}

function close() {
  if (!isSubmitting.value && !isTesting.value) emit('close');
}

async function testConnection() {
  const connection = selectedConnection.value;
  if (isTesting.value || !connection || typeof props.testHandler !== 'function') return;

  isTesting.value = true;
  submitNotice.value = null;
  try {
    const result = await props.testHandler({
      storage: connection.id,
      settings: JSON.parse(JSON.stringify(form)),
    });
    const name = result.name || connection.name || connection.id;
    submitNotice.value = result.connected
      ? { type: 'success', message: `Connection to ${name} succeeded. Credentials saved securely.` }
      : { type: 'error', message: `Connection to ${name} could not be established. Check the endpoint, bucket, region, and credentials.` };
  } catch (error) {
    submitNotice.value = { type: 'error', message: error?.message || 'Connection test failed. Please review the storage settings.' };
  } finally {
    isTesting.value = false;
  }
}

async function submit() {
  if (isSubmitting.value || typeof props.saveHandler !== 'function') return;

  isSubmitting.value = true;
  submitNotice.value = null;
  try {
    await props.saveHandler(JSON.parse(JSON.stringify(form)));
  } catch (error) {
    submitNotice.value = { type: 'error', message: error?.message || 'Settings could not be saved. Please try again.' };
  } finally {
    isSubmitting.value = false;
  }
}

watch(() => props.settings, hydrate, { immediate: true, deep: true });
watch(() => props.show, (visible) => {
  if (visible) {
    activeTab.value = 'connections';
    isSubmitting.value = false;
    submitNotice.value = null;
  }
});
</script>

<template>
  <div v-if="show" class="modal-layer" @click.self="close">
    <section class="settings-modal" aria-label="Storage and upload settings">
      <header>
        <div>
          <small>File manager</small>
          <h2>Storage & upload settings</h2>
        </div>
        <button type="button" aria-label="Close settings" :disabled="isSubmitting || isTesting" @click="close"><i class="bi bi-x-lg"></i></button>
      </header>

      <fieldset class="settings-submit-fieldset" :disabled="isSubmitting || isTesting">
      <div class="settings-body">
        <nav class="settings-nav" aria-label="Settings sections">
          <button type="button" :class="{ active: activeTab === 'connections' }" @click="activeTab = 'connections'"><i class="bi bi-hdd-stack"></i> Storage connections</button>
          <button type="button" :class="{ active: activeTab === 'uploads' }" @click="activeTab = 'uploads'"><i class="bi bi-cloud-arrow-up"></i> Upload behavior</button>
          <button type="button" :class="{ active: activeTab === 'validation' }" @click="activeTab = 'validation'"><i class="bi bi-shield-check"></i> Validation & security</button>
        </nav>

        <main class="settings-content">
          <template v-if="activeTab === 'connections'">
            <section>
              <div class="settings-section-head">
                <div><h3>Storage connections</h3><p>Set the default destination, per-connection capacity, and connection credentials.</p></div>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="addConnection"><i class="bi bi-plus-lg"></i> Add connection</button>
              </div>
              <div class="connection-grid">
                <button
                  v-for="connection in form.connections"
                  :key="connection.id"
                  type="button"
                  class="connection-card"
                  :class="{ selected: selectedConnectionId === connection.id }"
                  @click="chooseConnection(connection.id)"
                >
                  <span class="connection-icon"><i class="bi" :class="connection.icon"></i></span>
                  <span>
                    <strong>{{ connection.name }}</strong>
                    <small>{{ connection.type === 'local' ? 'Isolated V2 local storage' : connection.type === 'r2' ? 'Cloudflare R2 (S3 API)' : connection.type === 's3' ? 'AWS S3' : 'S3-compatible API' }}</small>
                  </span>
                  <span class="connection-status" :class="connection.enabled ? '' : 'text-secondary'"><i class="bi" :class="connection.enabled ? 'bi-check-circle-fill' : 'bi-pause-circle'"></i> {{ connection.id === form.defaultStorage ? 'Default' : connection.enabled ? 'Active' : 'Inactive' }}</span>
                </button>
              </div>
            </section>

            <section v-if="selectedConnection" class="settings-form-section">
              <div class="settings-section-head">
                <div><h3>{{ selectedConnection.name }}</h3><p>Credentials are encrypted on the server and are never returned to this browser.</p></div>
                <div class="settings-section-actions">
                  <button v-if="selectedConnection.id !== form.defaultStorage" type="button" class="btn btn-sm btn-outline-secondary" @click="setDefaultConnection(selectedConnection.id)"><i class="bi bi-check2-circle"></i> Make default</button>
                  <button type="button" class="btn btn-sm btn-outline-primary" @click="testConnection"><i class="bi" :class="isTesting ? 'bi-arrow-repeat' : 'bi-plug'"></i> {{ isTesting ? 'Testing connection...' : 'Test connection' }}</button>
                  <button v-if="selectedConnection.id !== 'local'" type="button" class="btn btn-sm btn-outline-danger" @click="removeConnection"><i class="bi bi-trash3"></i> Delete connection</button>
                </div>
              </div>
              <div class="form-grid">
                <label>Connection name<input v-model.trim="selectedConnection.name" class="form-control" maxlength="80" /></label>
                <label v-if="selectedConnection.type !== 'local'">Storage type
                  <select v-model="selectedConnection.type" class="form-select" @change="changeConnectionType(selectedConnection)">
                    <option value="s3">AWS S3</option>
                    <option value="s3_compatible">S3-compatible</option>
                    <option value="r2">Cloudflare R2</option>
                  </select>
                </label>
                <label v-else>Location<input class="form-control" value="Server local storage" disabled /></label>
                <label class="span-2">{{ selectedConnection.type === 'local' ? 'V2 local subfolder' : 'Object key prefix' }}<input v-model.trim="selectedConnection.root" class="form-control" placeholder="Leave empty for the connection root" /><small>{{ selectedConnection.type === 'local' ? 'Always constrained inside the isolated File Manager V2 root.' : 'Only objects inside this prefix are visible to this connection.' }}</small></label>
                <label>Maximum capacity (MB)<input :value="megabytes(selectedConnection.quotaBytes)" type="number" min="0" class="form-control" @input="setMegabytes(selectedConnection, 'quotaBytes', $event.target.value)" /><small>Use 0 for no limit. Uploads exceeding the limit are rejected by the server.</small></label>
                <label>Connection active<select v-model="selectedConnection.enabled" class="form-select"><option :value="true">Active</option><option :value="false">Inactive</option></select></label>
              </div>

              <div v-if="selectedConnection.type !== 'local'" class="form-grid mt-3">
                <label>Bucket name<input v-model.trim="selectedConnection.bucket" class="form-control" /></label>
                <label>Region<input v-model.trim="selectedConnection.region" class="form-control" /></label>
                <label class="span-2">S3 API endpoint<input v-model.trim="selectedConnection.endpoint" class="form-control" :placeholder="selectedConnection.type === 's3' ? 'Optional for AWS S3' : 'https://endpoint.example.com'" /></label>
                <label>Access key ID<input v-model="selectedConnection.accessKey" autocomplete="off" class="form-control" :placeholder="selectedConnection.credentialsConfigured ? 'Leave blank to keep encrypted value' : 'Required to connect'" /></label>
                <label>Secret access key<input v-model="selectedConnection.secretKey" type="password" autocomplete="new-password" class="form-control" :placeholder="selectedConnection.credentialsConfigured ? 'Leave blank to keep encrypted value' : 'Required to connect'" /></label>
                <small class="span-2 form-text" :class="selectedConnection.credentialsConfigured ? 'text-success' : 'text-secondary'">
                  <i class="bi" :class="selectedConnection.credentialsConfigured ? 'bi-shield-check' : 'bi-key'"></i>
                  {{ selectedConnection.credentialsConfigured ? 'Credentials saved securely. Leave both fields blank to keep the encrypted values, or enter both values to replace them.' : 'Enter both credentials, then use Test connection to save and verify this connection.' }}
                </small>
                <div class="switch-row span-2"><div><strong>Use path-style endpoint</strong><small>Required by many S3-compatible providers; usually off for AWS S3 and R2.</small></div><div class="form-check form-switch"><input v-model="selectedConnection.usePathStyle" class="form-check-input" type="checkbox" /></div></div>
              </div>
            </section>
          </template>

          <section v-else-if="activeTab === 'uploads'">
            <div class="settings-section-head"><div><h3>Upload behavior</h3><p>These values are used by FilePond and the V2 chunk upload API.</p></div></div>
            <div class="form-grid upload-settings-grid">
              <label>Parallel files<input v-model.number="form.upload.maxParallel" type="number" min="1" max="10" class="form-control" /><small>Maximum simultaneous FilePond uploads.</small></label>
              <label>Multipart chunk (MB)<input :value="megabytes(form.upload.chunkSize)" type="number" min="1" class="form-control" @input="setMegabytes(form.upload, 'chunkSize', $event.target.value)" /><small>Each multipart request is enforced by the server.</small></label>
              <label>Chunk threshold (MB)<input :value="megabytes(form.upload.chunkThreshold)" type="number" min="1" class="form-control" @input="setMegabytes(form.upload, 'chunkThreshold', $event.target.value)" /><small>Files at or above this size use multipart upload.</small></label>
              <label>Automatic retries<input v-model.number="form.upload.retryAttempts" type="number" min="0" max="5" class="form-control" /><small>Retries transient connection and server failures with backoff.</small></label>
            </div>
          </section>

          <section v-else>
            <div class="settings-section-head"><div><h3>Validation & security</h3><p>Validation is enforced again on the server for direct and multipart uploads.</p></div></div>
            <div class="form-grid">
              <label>Maximum file size (MB)<input :value="megabytes(form.upload.maxFileSize)" type="number" min="1" class="form-control" @input="setMegabytes(form.upload, 'maxFileSize', $event.target.value)" /><small>FilePond and the backend reject files above this limit.</small></label>
              <label>Executable file protection<input class="form-control" value="PHP, scripts, and executable extensions are blocked" disabled /><small>Server policy remains enforced and is not client-controlled.</small></label>
            </div>
          </section>
        </main>
      </div>
      </fieldset>
      <p v-if="submitNotice" class="settings-submit-notice" :class="`is-${submitNotice.type}`" role="alert">
        <i class="bi" :class="submitNotice.type === 'error' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill'"></i>
        <span>{{ submitNotice.message }}</span>
      </p>
      <footer>
        <button type="button" class="btn btn-light" :disabled="isSubmitting || isTesting" @click="close">Cancel</button>
        <button v-if="!isSubmitting && !isTesting" type="button" class="btn btn-primary" @click="submit">Save settings</button>
        <button v-else type="button" class="btn btn-secondary action-submit-state" disabled><span class="action-submit-spinner"></span><span>Saving settings</span></button>
      </footer>
    </section>
  </div>
</template>
