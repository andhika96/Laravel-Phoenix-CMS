<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  folder: {
    type: Object,
    required: true,
  },
  activeFolder: String,
});

const emit = defineEmits(['change-folder']);
const expanded = ref(true);
const hasChildren = computed(() => props.folder.children.length > 0);

function toggleChildren() {
  expanded.value = !expanded.value;
}
</script>

<template>
  <div class="folder-tree-node">
    <div class="folder-tree-row" :style="{ '--folder-depth': folder.depth }">
      <button
        v-if="hasChildren"
        class="folder-tree-toggle"
        type="button"
        :aria-label="expanded ? `Hide ${folder.name}` : `Show ${folder.name}`"
        :aria-expanded="expanded"
        @click.stop="toggleChildren"
      >
        <i class="bi" :class="expanded ? 'bi-chevron-down' : 'bi-chevron-right'"></i>
      </button>
      <span v-else class="folder-tree-toggle-placeholder" aria-hidden="true"></span>

      <button
        class="folder-tree-item"
        :class="{ active: activeFolder === folder.id }"
        type="button"
        @click="emit('change-folder', folder)"
      >
        <i class="bi" :class="folder.icon"></i>
        <span v-if="folder.depth" class="folder-tree-branch" aria-hidden="true"></span>
        <span>{{ folder.name }}</span>
        <small>{{ folder.count }}</small>
      </button>
    </div>

    <Transition name="folder-children">
      <div v-if="expanded && folder.children.length" class="folder-children">
        <div class="folder-children-inner">
          <FolderTreeNode
            v-for="child in folder.children"
            :key="child.id"
            :folder="child"
            :active-folder="activeFolder"
            @change-folder="emit('change-folder', $event)"
          />
        </div>
      </div>
    </Transition>
  </div>
</template>
