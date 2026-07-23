<template>
						<div class="pb-tabs-settings pb-widget-settings pb-widget-settings--tabs">
							<details class="pb-collapsible" open>
								<summary>Tabs</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<div class="pb-label-row">
											<label class="pb-form-label mb-0">Tabs Items</label>
										</div>
										<div class="pb-tabs-items-list">
											<div
												v-for="(item, index) in editor.tabsItemsForNode(node)"
												:key="item.id"
												class="pb-tabs-item-row"
												:class="{ active: node.settings.activeTabId===item.id }"
											>
												<button type="button" class="pb-tabs-item-main" @click="editor.selectTabsItem(node, item.id)">
													<span>{{ editor.tabsItemSummary(item, index) }}</span>
												</button>
												<button type="button" class="pb-tabs-item-action" title="Duplicate Tab" @click="editor.duplicateTabsItem(node, item.id)">
													<i class="far fa-copy"></i>
												</button>
												<button type="button" class="pb-tabs-item-action" title="Delete Tab" :disabled="editor.tabsItemsForNode(node).length<=1" @click="editor.removeTabsItem(node, item.id)">
													<i class="fas fa-times"></i>
												</button>
											</div>
										</div>
										<button type="button" class="pb-btn pb-tabs-add-btn" @click="editor.addTabsItem(node)">
											<i class="fas fa-plus"></i>
											<span>Add Tab</span>
										</button>
									</div>

									<div v-if="activeItem" class="pb-tabs-item-fields">
										<div class="pb-form-group">
											<label class="pb-form-label">Title</label>
											<input class="pb-input" v-model="activeItem.title" placeholder="Tab title">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Icon Class</label>
											<input class="pb-input" v-model="activeItem.iconClass" placeholder="far fa-star">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Active Icon Class</label>
											<input class="pb-input" v-model="activeItem.activeIconClass" placeholder="fas fa-star">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">CSS ID</label>
											<input class="pb-input" v-model="activeItem.cssId" placeholder="tab-one">
										</div>
									</div>

									<div class="pb-form-group">
										<label class="pb-form-label">Direction</label>
										<div class="pb-seg-group">
											<button class="pb-seg-btn" :class="{active:node.settings.direction==='row'}" @click="node.settings.direction='row'" title="Row"><i class="fas fa-arrow-right"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.direction==='column'}" @click="node.settings.direction='column'" title="Column"><i class="fas fa-arrow-down"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.direction==='row-reverse'}" @click="node.settings.direction='row-reverse'" title="Row Reverse"><i class="fas fa-arrow-left"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.direction==='column-reverse'}" @click="node.settings.direction='column-reverse'" title="Column Reverse"><i class="fas fa-arrow-up"></i></button>
										</div>
									</div>
									<div class="pb-form-group pb-tabs-width-control" v-if="editor.tabsSelectedRowDirection(node)">
										<label class="pb-form-label">Width</label>
										<div class="pb-range-value-row">
											<input type="range" class="pb-range" min="1" :max="editor.tabsWidthMax(node)" :step="editor.tabsWidthStep(node)" :value="editor.tabsWidthValue(node)" @input="editor.onTabsWidthInput(node, $event)">
											<div class="pb-value-with-unit">
												<input class="pb-input pb-input-compact" type="number" min="1" :max="editor.tabsWidthMax(node)" :step="editor.tabsWidthStep(node)" :value="editor.tabsWidthValue(node)" @input="editor.onTabsWidthInput(node, $event)">
												<select class="pb-mini-unit" :value="editor.tabsWidthUnit(node)" @change="editor.setTabsWidthUnit(node, $event.target.value)">
												<option v-for="unit in editor.tabsWidthUnits" :key="'tabs-width-unit-' + unit" :value="unit">{{ unit }}</option>
												</select>
											</div>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Justify</label>
										<div class="pb-seg-group">
											<button class="pb-seg-btn" :class="{active:node.settings.justify==='flex-start'}" @click="node.settings.justify='flex-start'" title="Start"><i class="fas fa-align-left"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.justify==='center'}" @click="node.settings.justify='center'" title="Center"><i class="fas fa-align-center"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.justify==='flex-end'}" @click="node.settings.justify='flex-end'" title="End"><i class="fas fa-align-right"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.justify==='stretch'}" @click="node.settings.justify='stretch'" title="Stretch"><i class="fas fa-align-justify"></i></button>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Align Title</label>
										<div class="pb-seg-group">
											<button class="pb-seg-btn" :class="{active:node.settings.alignTitle==='left'}" @click="node.settings.alignTitle='left'" title="Left"><i class="fas fa-align-left"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.alignTitle==='center'}" @click="node.settings.alignTitle='center'" title="Center"><i class="fas fa-align-center"></i></button>
											<button class="pb-seg-btn" :class="{active:node.settings.alignTitle==='right'}" @click="node.settings.alignTitle='right'" title="Right"><i class="fas fa-align-right"></i></button>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">CSS Class</label>
										<input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-tabs">
									</div>
								</div>
							</details>
							<details class="pb-collapsible" open>
								<summary>Additional Settings</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<label class="pb-form-label">Horizontal Scroll</label>
										<select class="pb-select" v-model="node.settings.horizontalScroll">
											<option :value="false">Disable</option>
											<option :value="true">Enable</option>
										</select>
										<div class="pb-form-note">Scroll tabs if they don't fit into their parent container.</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Breakpoint</label>
										<select class="pb-select" v-model="node.settings.breakpoint">
											<option v-for="option in editor.tabsBreakpointOptions" :key="'tabs-breakpoint-' + option.value" :value="option.value">{{ option.label }}</option>
										</select>
										<div class="pb-form-note">Choose at which breakpoint tabs will automatically switch to a vertical ('accordion') layout.</div>
									</div>
								</div>
							</details>
						</div>
</template>

<script>
export default {
    name: 'TabsWidgetSettings',
    props: {
        node: { type: Object, required: true },
        editor: { type: Object, required: true },
    },
    computed: {
        activeItem() {
            return this.editor.tabsActiveItem(this.node);
        },
    },
};
</script>
