<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2FolderPresentationTest extends TestCase
{
    public function test_nested_folders_are_kept_in_the_content_grid_and_sidebar_tree(): void
    {
        $card = file_get_contents(resource_path('js/filemanager_v2/components/AssetCard.vue'));
        $drawer = file_get_contents(resource_path('js/filemanager_v2/components/DetailsDrawer.vue'));
        $live = file_get_contents(resource_path('js/filemanager_v2/data/live.js'));
        $app = file_get_contents(resource_path('js/filemanager_v2/App.vue'));
        $sidebar = file_get_contents(resource_path('js/filemanager_v2/components/StorageSidebar.vue'));
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));
        $nodePath = resource_path('js/filemanager_v2/components/FolderTreeNode.vue');

        $this->assertFileExists($nodePath);
        $node = file_get_contents($nodePath);

        $this->assertStringContainsString('payload.items.map(normalizeAsset)', $live);
        $this->assertStringNotContainsString("payload.items.filter((item) => item.type === 'file')", $live);
        $this->assertStringContainsString("depth: folder.path.split('/').filter(Boolean).length - 1", $live);
        $this->assertStringContainsString('export async function loadFolderDetails', $live);
        $this->assertStringContainsString('export async function renameAsset', $live);
        $this->assertStringNotContainsString("if (asset.type === 'folder') {\n    void changeFolder(asset.path);", $app);
        $this->assertStringContainsString('openActionModal(\'rename\', $event)', $app);
        $this->assertStringContainsString('@open-folder="changeFolder($event.path)"', $app);
        $this->assertStringContainsString('@rename="openActionModal(\'rename\', $event)"', $app);
        $this->assertStringContainsString('@click="asset.type === \'folder\' && emit(\'open-folder\', asset)"', $card);
        $this->assertStringContainsString("defineEmits(['close', 'download', 'copy-url', 'rename', 'open-folder'])", $drawer);
        $this->assertStringContainsString("v-if=\"asset.type === 'folder'\"", $drawer);
        $this->assertStringContainsString('class="folder-tree-branch"', $node);
        $this->assertStringContainsString(":style=\"{ '--folder-depth': folder.depth }\"", $node);
        $this->assertStringContainsString('.folder-tree-branch {', $styles);
    }

    public function test_folder_tree_can_collapse_and_expand_descendants_with_motion(): void
    {
        $sidebar = file_get_contents(resource_path('js/filemanager_v2/components/StorageSidebar.vue'));
        $nodePath = resource_path('js/filemanager_v2/components/FolderTreeNode.vue');
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));

        $this->assertFileExists($nodePath);
        $node = file_get_contents($nodePath);
        $this->assertStringContainsString("import { computed } from 'vue';", $sidebar);
        $this->assertStringContainsString('const folderTree = computed(() =>', $sidebar);
        $this->assertStringContainsString("import FolderTreeNode from './FolderTreeNode.vue';", $sidebar);
        $this->assertStringContainsString('<FolderTreeNode', $sidebar);
        $this->assertStringNotContainsString('<TransitionGroup name="folder-tree"', $sidebar);
        $this->assertStringContainsString('bi-chevron-right', $node);
        $this->assertStringContainsString('bi-chevron-down', $node);
        $this->assertStringContainsString('.folder-children-enter-active', $styles);
        $this->assertStringContainsString('.folder-children-leave-active', $styles);
    }

    public function test_folder_tree_animates_each_descendant_group_as_one_subtree(): void
    {
        $sidebar = file_get_contents(resource_path('js/filemanager_v2/components/StorageSidebar.vue'));
        $nodePath = resource_path('js/filemanager_v2/components/FolderTreeNode.vue');
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));

        $this->assertFileExists($nodePath);
        $node = file_get_contents($nodePath);
        $this->assertStringContainsString("import FolderTreeNode from './FolderTreeNode.vue';", $sidebar);
        $this->assertStringContainsString('<FolderTreeNode', $sidebar);
        $this->assertStringContainsString('<Transition name="folder-children">', $node);
        $this->assertStringContainsString('v-if="expanded && folder.children.length"', $node);
        $this->assertStringContainsString('.folder-children-enter-active', $styles);
        $this->assertStringContainsString('.folder-children-inner', $styles);
    }

    public function test_action_modal_footer_is_targeted_through_its_form(): void
    {
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));

        $this->assertStringContainsString('.action-modal > form > footer {', $styles);
        $this->assertStringContainsString('.action-modal > form > footer .btn {', $styles);
        $this->assertStringNotContainsString('.action-modal > footer {', $styles);
        $this->assertMatchesRegularExpression('/\\.span-2\\s*\\{\\s*grid-column:\\s*span 1;\\s*\\}\\s*\\}\\s*@media \\(prefers-reduced-motion: reduce\\)/', $styles);
    }
}
