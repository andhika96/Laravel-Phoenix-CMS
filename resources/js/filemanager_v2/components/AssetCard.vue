<script setup>
import { iconForKind } from '../data/live';

defineProps({
  asset: Object,
  selected: Boolean,
  viewMode: String,
});

const emit = defineEmits(['select', 'toggle-star', 'open-details', 'open-folder']);
</script>

<template>
  <article
    class="asset-card"
    :class="[{ selected }, `asset-${asset.kind}`, { 'list-card': viewMode === 'list' }]"
    @click="asset.type === 'folder' && emit('open-folder', asset)"
  >
    <button
      class="asset-check"
      type="button"
      :aria-label="selected ? `Deselect ${asset.name}` : `Select ${asset.name}`"
      :aria-pressed="selected"
      @click.stop="emit('select', asset, $event)"
      @dblclick.stop
    >
      <i class="bi" :class="selected ? 'bi-check-circle-fill' : 'bi-circle'"></i>
    </button>
    <span v-if="asset.starred" class="asset-starred-indicator" role="img" aria-label="Starred" title="Starred">
      <i class="bi bi-star-fill"></i>
    </span>
    <div class="asset-preview" :style="{ background: asset.color }">
      <img v-if="asset.src" :src="asset.src" :alt="asset.name" loading="lazy" decoding="async" />
      <i v-else class="bi file-kind-icon" :class="iconForKind[asset.kind]"></i>
      <span class="asset-extension">{{ asset.ext }}</span>
    </div>
    <div class="asset-copy">
      <strong :title="asset.name">{{ asset.name }}</strong>
      <span>{{ asset.size }} <b>·</b> {{ asset.modified }}</span>
    </div>
    <button class="asset-menu" aria-label="Open file information" title="Open file information" @click.stop="emit('open-details', asset)">
      <i class="bi bi-info-circle"></i>
    </button>
  </article>
</template>
