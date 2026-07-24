<script setup>
import { computed } from 'vue';

const props = defineProps({
  activeStorage: String,
  profile: Object,
  profiles: Object,
  activeNav: String,
  navItems: Array,
  folders: Array,
  activeFolder: String,
});

const emit = defineEmits(['change-storage', 'change-nav', 'change-folder', 'open-settings']);
const usagePercent = computed(() => Math.round((props.profile.used / props.profile.total) * 100));
</script>

<template>
  <aside class="fm-sidebar">
    <div class="brand">
      <div class="brand-mark"><i class="bi bi-stack"></i></div>
      <div>
        <strong>Arunika Files</strong>
        <small>Asset manager</small>
      </div>
    </div>

    <div class="storage-switcher dropdown">
      <button class="storage-current" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="storage-icon"><i class="bi" :class="profile.icon"></i></span>
        <span class="storage-copy">
          <small>Active storage</small>
          <strong>{{ profile.shortName }}</strong>
        </span>
        <span class="status-dot"></span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </button>
      <div class="dropdown-menu storage-menu shadow-sm">
        <button
          v-for="item in profiles"
          :key="item.id"
          class="dropdown-item storage-option"
          :class="{ active: item.id === activeStorage }"
          @click="emit('change-storage', item.id)"
        >
          <i class="bi" :class="item.icon"></i>
          <span>
            <strong>{{ item.name }}</strong>
            <small>{{ item.root }}</small>
          </span>
          <i v-if="item.id === activeStorage" class="bi bi-check2 ms-auto"></i>
        </button>
      </div>
    </div>

    <nav class="sidebar-nav" aria-label="File manager navigation">
      <button
        v-for="item in navItems"
        :key="item.id"
        :class="{ active: activeNav === item.id && !activeFolder }"
        @click="emit('change-nav', item.id)"
      >
        <i class="bi" :class="item.icon"></i>
        <span>{{ item.label }}</span>
        <span v-if="item.id === 'all'" class="nav-count">1,052</span>
      </button>
    </nav>

    <div class="folder-section">
      <div class="section-heading">
        <span>Folders</span>
        <button title="Create folder"><i class="bi bi-plus-lg"></i></button>
      </div>
      <div class="folder-list">
        <button
          v-for="folder in folders"
          :key="folder.id"
          :class="{ active: activeFolder === folder.id }"
          @click="emit('change-folder', folder.id)"
        >
          <i class="bi" :class="folder.icon"></i>
          <span>{{ folder.name }}</span>
          <small>{{ folder.count }}</small>
        </button>
      </div>
    </div>

    <div class="sidebar-footer">
      <div class="usage-row">
        <span>Storage used</span>
        <strong>{{ profile.used }} {{ profile.unit }} / {{ profile.total }} {{ profile.unit }}</strong>
      </div>
      <div class="progress storage-progress" role="progressbar" :aria-valuenow="usagePercent">
        <div class="progress-bar" :style="{ width: usagePercent + '%' }"></div>
      </div>
      <button class="settings-link" @click="emit('open-settings')">
        <i class="bi bi-gear"></i> Storage & upload settings
      </button>
    </div>
  </aside>
</template>
