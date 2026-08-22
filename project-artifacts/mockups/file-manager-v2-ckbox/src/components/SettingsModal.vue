<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  activeStorage: String,
  profiles: Object,
});

const emit = defineEmits(['close', 'save', 'test']);

const settings = reactive({
  defaultStorage: props.activeStorage,
  localRoot: 'storage/app/public/media',
  r2Bucket: 'arunika-production',
  r2Endpoint: 'https://••••••••••••.r2.cloudflarestorage.com',
  concurrency: 4,
  chunkSize: 64,
  retries: 5,
  conflict: 'rename',
  checksum: true,
  resume: true,
});

watch(() => props.activeStorage, (value) => {
  settings.defaultStorage = value;
});
</script>

<template>
  <div v-if="show" class="modal-layer" @click.self="emit('close')">
    <section class="settings-modal">
      <header>
        <div>
          <small>File manager</small>
          <h2>Storage & upload settings</h2>
        </div>
        <button @click="emit('close')"><i class="bi bi-x-lg"></i></button>
      </header>

      <div class="settings-body">
        <nav class="settings-nav">
          <button class="active"><i class="bi bi-hdd-stack"></i> Storage connections</button>
          <button><i class="bi bi-cloud-arrow-up"></i> Upload behavior</button>
          <button><i class="bi bi-shield-check"></i> Validation & security</button>
          <button><i class="bi bi-speedometer2"></i> Performance</button>
        </nav>

        <main class="settings-content">
          <section>
            <div class="settings-section-head">
              <div><h3>Storage connections</h3><p>Choose where assets are stored by default.</p></div>
              <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus-lg"></i> Add S3-compatible</button>
            </div>
            <div class="connection-grid">
              <button
                v-for="profile in profiles"
                :key="profile.id"
                class="connection-card"
                :class="{ selected: settings.defaultStorage === profile.id }"
                @click="settings.defaultStorage = profile.id"
              >
                <span class="connection-icon"><i class="bi" :class="profile.icon"></i></span>
                <span>
                  <strong>{{ profile.name }}</strong>
                  <small>{{ profile.root }}</small>
                </span>
                <span class="connection-status"><i class="bi bi-check-circle-fill"></i> Connected</span>
              </button>
            </div>
          </section>

          <section class="settings-form-section">
            <div class="settings-section-head">
              <div><h3>{{ settings.defaultStorage === 'local' ? 'Local storage' : 'Cloudflare R2' }}</h3><p>Connection details and custom root.</p></div>
              <button class="btn btn-sm btn-outline-primary" @click="emit('test', settings.defaultStorage)">
                <i class="bi bi-plug"></i> Test connection
              </button>
            </div>
            <div v-if="settings.defaultStorage === 'local'" class="form-grid">
              <label class="span-2">Custom storage root<input v-model="settings.localRoot" class="form-control" /></label>
              <label>Visibility<select class="form-select"><option>Private by default</option><option>Public</option></select></label>
              <label>Public URL prefix<input class="form-control" value="/media" /></label>
            </div>
            <div v-else class="form-grid">
              <label>Bucket name<input v-model="settings.r2Bucket" class="form-control" /></label>
              <label>Region<input class="form-control" value="auto" disabled /></label>
              <label class="span-2">S3 API endpoint<input v-model="settings.r2Endpoint" class="form-control" /></label>
              <label>Access key ID<input class="form-control" value="••••••••••••••••" /></label>
              <label>Secret access key<input class="form-control" value="••••••••••••••••••••••••" /></label>
            </div>
          </section>

          <section class="settings-form-section">
            <div class="settings-section-head">
              <div><h3>Bulk upload behavior</h3><p>Safe defaults for large queues and unstable connections.</p></div>
              <span class="recommended-badge">Recommended preset</span>
            </div>
            <div class="form-grid upload-settings-grid">
              <label>Parallel files<input v-model="settings.concurrency" type="number" min="1" max="10" class="form-control" /><small>Maximum simultaneous files</small></label>
              <label>Multipart chunk<input v-model="settings.chunkSize" type="number" min="8" max="512" class="form-control" /><small>Size per chunk in MB</small></label>
              <label>Automatic retries<input v-model="settings.retries" type="number" min="0" max="10" class="form-control" /><small>Exponential backoff</small></label>
              <label>Filename conflict<select v-model="settings.conflict" class="form-select"><option value="rename">Keep both (auto rename)</option><option value="replace">Replace existing</option><option value="skip">Skip existing</option><option value="ask">Ask every time</option></select></label>
            </div>
            <div class="switch-row">
              <div><strong>Resumable uploads</strong><small>Continue interrupted multipart uploads.</small></div>
              <div class="form-check form-switch"><input v-model="settings.resume" class="form-check-input" type="checkbox" /></div>
            </div>
            <div class="switch-row">
              <div><strong>Verify checksum</strong><small>Confirm file integrity after upload completes.</small></div>
              <div class="form-check form-switch"><input v-model="settings.checksum" class="form-check-input" type="checkbox" /></div>
            </div>
          </section>
        </main>
      </div>
      <footer>
        <button class="btn btn-light" @click="emit('close')">Cancel</button>
        <button class="btn btn-primary" @click="emit('save', { ...settings })">Save settings</button>
      </footer>
    </section>
  </div>
</template>
