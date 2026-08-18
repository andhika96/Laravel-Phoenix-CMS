<template>
						<div class="pb-layout-settings pb-layout-settings--container">
							<div class="pb-tab-nav">
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='layout'}" @click="editor.settingsTab='layout'"><i class="far fa-square"></i><span>Layout</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='style'}" @click="editor.settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
							</div>
						<!-- TAB LAYOUT -->
						<div v-if="editor.settingsTab==='layout'" class="pb-tab-content pb-layout-settings__tab">
							<details class="pb-collapsible" open>
								<summary>Container</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group">
									<label class="pb-form-label">Container Layout</label>
									<select class="pb-select" v-model="node.settings.displayType" @change="editor.onContainerDisplayTypeChange(node)">
										<option value="flex">Flexbox</option>
										<option value="grid">Grid</option>
									</select>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Content Width</label>
									<select class="pb-select" v-model="node.settings.contentWidth" @change="editor.onContainerContentWidthChange(node)">
										<option value="full">Full Width</option>
										<option value="boxed">Boxed</option>
									</select>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Width</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-width')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-width')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-width-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-width', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" :max="editor.containerWidthMax(node)" :step="editor.containerWidthStep(node)" :value="editor.containerWidthValue(node)" @input="editor.onContainerWidthInput(node, $event)">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" :max="editor.containerWidthMax(node)" :step="editor.containerWidthStep(node)" :value="editor.containerWidthValue(node)" @input="editor.onContainerWidthInput(node, $event)">
											<select class="pb-mini-unit" :value="editor.containerWidthUnit(node)" @change="editor.setContainerWidthUnit(node, $event.target.value)">
												<option v-for="unit in editor.sizeControlUnits" :key="'container-width-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Min Height</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-min-height')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-min-height')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-min-height-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-min-height', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" max="1000" step="1" :value="editor.minHeightValue(node) || 0" @input="editor.setMinHeightValue(node, $event.target.value)">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" max="1000" step="1" :value="editor.minHeightValue(node)" @input="editor.setMinHeightValue(node, $event.target.value)" placeholder="auto">
											<select class="pb-mini-unit" :value="editor.minHeightUnit(node)" @change="editor.setMinHeightUnit(node, $event.target.value)">
												<option value="px">px</option>
												<option value="vh">vh</option>
											</select>
										</div>
									</div>
									<div class="pb-form-note">To achieve full height Container use 100vh.</div>
								</div>
								</div>
							</details>
							<details class="pb-collapsible" v-if="node.settings.displayType==='flex'||!node.settings.displayType" open>
								<summary>Items</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Direction</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-direction')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-direction')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-direction-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-direction', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'direction','row')==='row'}"            @click="editor.setContainerResponsiveSetting(node.settings,'direction','row')"            title="Row"><i class="fas fa-arrow-right"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'direction','row')==='column'}"         @click="editor.setContainerResponsiveSetting(node.settings,'direction','column')"         title="Column"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'direction','row')==='row-reverse'}"    @click="editor.setContainerResponsiveSetting(node.settings,'direction','row-reverse')"    title="Row Reverse"><i class="fas fa-arrow-left"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'direction','row')==='column-reverse'}" @click="editor.setContainerResponsiveSetting(node.settings,'direction','column-reverse')" title="Col Reverse"><i class="fas fa-arrow-up"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Justify Content</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-justify-content')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-justify-content')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-justify-content-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-justify-content', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'justifyContent','flex-start')==='flex-start'}"    @click="editor.setContainerResponsiveSetting(node.settings,'justifyContent','flex-start')"    title="Start"><i class="fas fa-align-left"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'justifyContent','flex-start')==='center'}"        @click="editor.setContainerResponsiveSetting(node.settings,'justifyContent','center')"        title="Center"><i class="fas fa-align-center"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'justifyContent','flex-start')==='flex-end'}"      @click="editor.setContainerResponsiveSetting(node.settings,'justifyContent','flex-end')"      title="End"><i class="fas fa-align-right"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'justifyContent','flex-start')==='space-between'}" @click="editor.setContainerResponsiveSetting(node.settings,'justifyContent','space-between')" title="Space Between"><i class="fas fa-arrows-alt-h"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'justifyContent','flex-start')==='space-around'}"  @click="editor.setContainerResponsiveSetting(node.settings,'justifyContent','space-around')"  title="Space Around"><i class="fas fa-grip-lines-vertical"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'justifyContent','flex-start')==='space-evenly'}"  @click="editor.setContainerResponsiveSetting(node.settings,'justifyContent','space-evenly')"  title="Space Evenly"><i class="fas fa-grip-lines"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Items</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-align-items')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-align-items')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-align-items-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-align-items', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignItems','flex-start')==='flex-start'}" @click="editor.setContainerResponsiveSetting(node.settings,'alignItems','flex-start')" title="Start"><i class="fas fa-arrow-up"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignItems','flex-start')==='center'}"    @click="editor.setContainerResponsiveSetting(node.settings,'alignItems','center')"    title="Center"><i class="fas fa-arrows-alt-v"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignItems','flex-start')==='flex-end'}"  @click="editor.setContainerResponsiveSetting(node.settings,'alignItems','flex-end')"  title="End"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignItems','flex-start')==='stretch'}"   @click="editor.setContainerResponsiveSetting(node.settings,'alignItems','stretch')"   title="Stretch"><i class="fas fa-expand-arrows-alt"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label">Gaps</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-gaps')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen('container-gaps')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="'container-gaps-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-gaps', device.value)">
														<i :class="device.icon"></i>
														<span>{{ editor.deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>

											<select class="pb-mini-unit pb-container-gap-control__unit" :value="editor.sizeControlUnit(node, 'flexColumnGap', '20px')" @change="editor.setSizeControlUnit(node, 'flexColumnGap', $event.target.value, {fallback:'20px'}); editor.setSizeControlUnit(node, 'flexRowGap', $event.target.value, {fallback:'20px'}); editor.syncContainerGap(node.settings, 'column')"><option v-for="unit in editor.spacingControlUnits" :key="'flex-gap-'+unit" :value="unit">{{ unit }}</option></select>
										</div>
									</div>
									<div class="pb-gap-row pb-gap-row-with-link pb-container-gap-control pb-container-gap-control__values">
										<div v-for="control in [{key:'flexColumnGap',label:'Column',sync:'column'},{key:'flexRowGap',label:'Row',sync:'row'}]" :key="control.key" class="pb-gap-field"><input class="pb-input pb-input-compact" type="number" min="0" :value="editor.sizeControlDisplayValue(node, control.key, '20px')" @input="editor.onSizeControlInput(node, control.key, $event, {fallback:'20px'}); editor.syncContainerGap(node.settings, control.sync)"><span>{{ control.label }}</span></div>
										<button type="button" class="pb-link-btn pb-container-gap-control__link" @click="node.settings.containerGapLinked=!node.settings.containerGapLinked" :title="node.settings.containerGapLinked?'Unlink':'Link'"><i :class="node.settings.containerGapLinked?'fas fa-link':'fas fa-unlink'"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Wrap</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-wrap')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-wrap')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-wrap-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-wrap', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'flexWrap','nowrap')==='nowrap'}" @click="editor.setContainerResponsiveSetting(node.settings,'flexWrap','nowrap')" title="No Wrap"><i class="fas fa-long-arrow-alt-right"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'flexWrap','nowrap')==='wrap'}" @click="editor.setContainerResponsiveSetting(node.settings,'flexWrap','wrap')" title="Wrap"><i class="fas fa-level-down-alt"></i></button>
									</div>
									<div class="pb-form-note">Items within the container can stay in a single line (No wrap), or break into multiple lines (Wrap).</div>
								</div>
								<div class="pb-form-group" v-if="editor.containerResponsiveValue(node.settings,'flexWrap','nowrap')==='wrap' || editor.containerResponsiveValue(node.settings,'flexWrap','nowrap')==='wrap-reverse'">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Content</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-align-content')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-align-content')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-align-content-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-align-content', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignContent','stretch')==='flex-start'}"    @click="editor.setContainerResponsiveSetting(node.settings,'alignContent','flex-start')"    title="Start"><i class="fas fa-align-left"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignContent','stretch')==='center'}"        @click="editor.setContainerResponsiveSetting(node.settings,'alignContent','center')"        title="Center"><i class="fas fa-align-center"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignContent','stretch')==='flex-end'}"      @click="editor.setContainerResponsiveSetting(node.settings,'alignContent','flex-end')"      title="End"><i class="fas fa-align-right"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignContent','stretch')==='space-between'}" @click="editor.setContainerResponsiveSetting(node.settings,'alignContent','space-between')" title="Space Between"><i class="fas fa-arrows-alt-h"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignContent','stretch')==='space-around'}"  @click="editor.setContainerResponsiveSetting(node.settings,'alignContent','space-around')"  title="Space Around"><i class="fas fa-grip-lines-vertical"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'alignContent','stretch')==='stretch'}"       @click="editor.setContainerResponsiveSetting(node.settings,'alignContent','stretch')"       title="Stretch"><i class="fas fa-arrows-alt-h"></i></button>
									</div>
								</div>
								</div>
							</details>
							<details class="pb-collapsible" v-if="(node.settings?.displayType || 'flex') === 'flex'" open>
								<summary>Child Containers</summary>
								<div class="pb-collapsible-body">
									<div class="pb-container-child-actions">
										<span>{{ (node.children || []).filter((child) => child && ['container','container_fluid'].includes(child.type)).length }} containers</span>
										<button type="button" class="pb-container-add-child" aria-label="Add Container" @click="editor.addContainerChild(node)">
											<i class="fas fa-plus"></i><span>Add Container</span>
										</button>
									</div>
									<div v-if="editor.childContainersFor(node).length" class="pb-layout-item-list">
										<div v-for="(child, index) in editor.childContainersFor(node)" :key="child.id" class="pb-layout-item-row">
											<button type="button" class="pb-layout-item-main" @click="editor.selectNode(child, { revealPanel: true })">
												<span class="pb-layout-item-icon"><i class="far fa-square"></i></span>
												<span class="pb-layout-item-copy">
													<strong>{{ editor.displayNodeLabel(child) || ('Container ' + (index + 1)) }}</strong>
													<small>{{ editor.nestedItemCount(child) }} nested item{{ editor.nestedItemCount(child) === 1 ? '' : 's' }}</small>
												</span>
											</button>
											<button type="button" class="pb-layout-item-delete" :aria-label="'Delete ' + (editor.displayNodeLabel(child) || ('Container ' + (index + 1)))" @click.stop="editor.removeNode(child.id)">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</div>
									<div class="pb-form-note">Each layout item is a selectable Container with its own Layout, Style, and Advanced settings.</div>
								</div>
							</details>
							<details class="pb-collapsible" v-if="node.settings.displayType==='grid'" open>
								<summary>Items</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Grid Outline</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerGridOutline-' + node.id" v-model="node.settings.gridOutline"><label :for="'containerGridOutline-' + node.id"></label></div></div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Columns</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-grid-columns')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen('container-grid-columns')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="'container-grid-columns-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-grid-columns', device.value)">
														<i :class="device.icon"></i>
														<span>{{ editor.deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<div class="pb-control-unit-wrap">fr</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="1" max="12" step="1" :value="editor.containerGridColumnsValue(node)" @input="editor.setContainerGridColumnsValue(node, $event.target.value)">
										<input class="pb-input pb-input-compact" type="number" min="1" max="12" step="1" :value="editor.containerGridColumnsValue(node)" @input="editor.setContainerGridColumnsValue(node, $event.target.value)">
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Rows</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-grid-rows')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen('container-grid-rows')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="'container-grid-rows-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-grid-rows', device.value)">
														<i :class="device.icon"></i>
														<span>{{ editor.deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<div class="pb-control-unit-wrap">rows</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="1" max="12" step="1" :value="editor.containerGridRowsValue(node)" @input="editor.setContainerGridRowsValue(node, $event.target.value)">
										<input class="pb-input pb-input-compact" type="number" min="1" max="12" step="1" :value="editor.containerGridRowsValue(node)" @input="editor.setContainerGridRowsValue(node, $event.target.value)">
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Column Structure</label>
									<div class="pb-layout-item-list">
										<div v-for="track in editor.gridColumnTracks(node)" :key="track.index" class="pb-layout-item-row">
											<div class="pb-layout-item-main pb-layout-item-main--static">
												<span class="pb-layout-item-icon"><i class="fas fa-columns"></i></span>
												<span class="pb-layout-item-copy"><strong>{{ track.label }}</strong><small>{{ track.itemCount }} item{{ track.itemCount === 1 ? '' : 's' }}</small></span>
											</div>
											<button type="button" class="pb-layout-item-delete" :disabled="!track.canRemove" :aria-label="'Delete ' + track.label" @click.stop="editor.requestGridColumnRemoval(node, track.index)">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</div>
									<div class="pb-form-note">The final column cannot be deleted.</div>
								</div>
								<div class="pb-form-group pb-container-gap-control__group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label">Gaps</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-grid-gaps')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen('container-grid-gaps')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="'container-grid-gaps-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-grid-gaps', device.value)">
														<i :class="device.icon"></i>
														<span>{{ editor.deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>

											<select class="pb-mini-unit pb-container-gap-control__unit" :value="editor.sizeControlUnit(node, 'gridColumnGap', '10px')" @change="editor.setSizeControlUnit(node, 'gridColumnGap', $event.target.value, {fallback:'10px'}); editor.setSizeControlUnit(node, 'gridRowGap', $event.target.value, {fallback:'10px'}); editor.syncContainerGap(node.settings, 'gridColumn')"><option v-for="unit in editor.spacingControlUnits" :key="'grid-gap-'+unit" :value="unit">{{ unit }}</option></select>
										</div>
									</div>
									<div class="pb-gap-row pb-gap-row-with-link pb-container-gap-control pb-container-gap-control__values">
										<div v-for="control in [{key:'gridColumnGap',label:'Column',sync:'gridColumn'},{key:'gridRowGap',label:'Row',sync:'gridRow'}]" :key="control.key" class="pb-gap-field"><input class="pb-input pb-input-compact" type="number" min="0" :value="editor.sizeControlDisplayValue(node, control.key, '10px')" @input="editor.onSizeControlInput(node, control.key, $event, {fallback:'10px'}); editor.syncContainerGap(node.settings, control.sync)"><span>{{ control.label }}</span></div>
										<button type="button" class="pb-link-btn pb-container-gap-control__link" @click="node.settings.containerGapLinked=!node.settings.containerGapLinked" :title="node.settings.containerGapLinked?'Unlink':'Link'"><i :class="node.settings.containerGapLinked?'fas fa-link':'fas fa-unlink'"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Auto Flow</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-grid-auto-flow')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-grid-auto-flow')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-grid-auto-flow-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-grid-auto-flow', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<select class="pb-select" :value="editor.containerResponsiveValue(node.settings,'autoFlow',node.settings.autoFlow || 'row')" @change="editor.setContainerResponsiveSetting(node.settings,'autoFlow',$event.target.value)">
										<option value="row">Row</option>
										<option value="column">Column</option>
									</select>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Justify Items</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-grid-justify-items')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-grid-justify-items')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-grid-justify-items-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-grid-justify-items', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridJustifyItems','stretch')==='start'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridJustifyItems','start')" title="Start"><i class="fas fa-align-left"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridJustifyItems','stretch')==='center'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridJustifyItems','center')" title="Center"><i class="fas fa-align-center"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridJustifyItems','stretch')==='end'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridJustifyItems','end')" title="End"><i class="fas fa-align-right"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridJustifyItems','stretch')==='stretch'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridJustifyItems','stretch')" title="Stretch"><i class="fas fa-arrows-alt-h"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Items</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-grid-align-items')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-grid-align-items')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-grid-align-items-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-grid-align-items', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridAlignItems','start')==='start'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridAlignItems','start')" title="Start"><i class="fas fa-arrow-up"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridAlignItems','start')==='center'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridAlignItems','center')" title="Center"><i class="fas fa-arrows-alt-v"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridAlignItems','start')==='end'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridAlignItems','end')" title="End"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings,'gridAlignItems','start')==='stretch'}" @click="editor.setContainerResponsiveSetting(node.settings,'gridAlignItems','stretch')" title="Stretch"><i class="fas fa-expand-arrows-alt"></i></button>
									</div>
								</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Additional Options</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<label class="pb-form-label">Overflow</label>
										<select class="pb-select" :value="['hidden','auto'].includes(node.settings.overflow) ? node.settings.overflow : 'default'" @change="node.settings.overflow=$event.target.value">
											<option value="default">Default</option>
											<option value="hidden">Hidden</option>
											<option value="auto">Auto</option>
										</select>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">HTML Tag</label>
										<select class="pb-select" v-model="node.settings.htmlTag">
											<option value="default">Default</option>
											<option value="div">DIV</option>
											<option value="section">SECTION</option>
											<option value="header">HEADER</option>
											<option value="main">MAIN</option>
											<option value="article">ARTICLE</option>
											<option value="aside">ASIDE</option>
											<option value="footer">FOOTER</option>
											<option value="nav">NAV</option>
											<option value="a">A (Link)</option>
										</select>
									</div>
									<div v-if="node.settings.htmlTag==='a'" class="pb-form-group">
										<label class="pb-form-label">Link</label>
										<input class="pb-input" v-model="node.settings.linkUrl" type="url" placeholder="https://example.com">
										<div class="pb-advanced-switch-grid mt-2">
											<div class="pb-form-group"><div class="pb-label-row"><label class="pb-form-label mb-0">Open in new window</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerLinkTarget-' + node.id" v-model="node.settings.linkTargetBlank"><label :for="'containerLinkTarget-' + node.id"></label></div></div></div>
											<div class="pb-form-group"><div class="pb-label-row"><label class="pb-form-label mb-0">Add nofollow</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerLinkNofollow-' + node.id" v-model="node.settings.linkNofollow"><label :for="'containerLinkNofollow-' + node.id"></label></div></div></div>
										</div>
									</div>
								</div>
							</details>
						</div><!-- /tab layout container -->
						<!-- TAB STYLE -->
						<div v-if="editor.settingsTab==='style'" class="pb-tab-content pb-layout-settings__tab">
							<details class="pb-collapsible" open>
								<summary>Background</summary>
								<div class="pb-collapsible-body">
								<div class="pb-mini-tab-nav">
									<button class="pb-mini-tab" :class="{active:(node.settings.bgState||'normal')==='normal'}" @click.prevent="editor.setBgState(node, 'normal')">Normal</button>
									<button class="pb-mini-tab" :class="{active:node.settings.bgState==='hover'}" @click.prevent="editor.setBgState(node, 'hover')">Hover</button>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Background Type</label>
									<div class="pb-btn-group pb-icon-btn-group" :class="{ 'pb-icon-btn-group-hover': editor.isBgHoverState(node) }">
										<button class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgType')]==='color'}" @click.prevent="editor.setBgTypeForState(node, 'color')" title="Classic">
											<i class="fas fa-brush"></i>
										</button>
										<button class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgType')]==='gradient'}" @click.prevent="editor.setBgTypeForState(node, 'gradient')" title="Gradient">
											<i class="fas fa-fill-drip"></i>
										</button>
										<button v-if="!editor.isBgHoverState(node)" class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgType')]==='image'}" @click.prevent="editor.setBgTypeForState(node, 'image')" title="Image">
											<i class="far fa-image"></i>
										</button>
										<button v-if="!editor.isBgHoverState(node)" class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgType')]==='video'}" @click.prevent="editor.setBgTypeForState(node, 'video')" title="Video">
											<i class="fas fa-video"></i>
										</button>
										<button v-if="!editor.isBgHoverState(node)" class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgType')]==='slideshow'}" @click.prevent="editor.setBgTypeForState(node, 'slideshow')" title="Slideshow">
											<i class="fas fa-images"></i>
										</button>
										<button v-if="!editor.isBgHoverState(node)" class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgType')]==='none'}" @click.prevent="editor.setBgTypeForState(node, 'none')" title="None">
											<i class="fas fa-ban"></i>
										</button>
									</div>
								</div>
								<template v-if="node.settings[editor.bgStateKey(node,'bgType')]==='color'">
									<div class="pb-form-group">
										<label class="pb-form-label">Color</label>
										<div class="pb-color-row">

											<input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'bgColor')]" placeholder="#ffffff">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round((node.settings[editor.bgStateKey(node,'bgOpacity')] ?? 1)*100) }}%</span></label>
										<input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="node.settings[editor.bgStateKey(node,'bgOpacity')]">
									</div>
								</template>
								<template v-if="node.settings[editor.bgStateKey(node,'bgType')]==='gradient'">
									<div class="pb-form-group">
										<label class="pb-form-label">Gradient Type</label>
										<div class="pb-btn-group">
											<button class="pb-seg-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgGradientType')]==='linear'}" @click="editor.setBgStateValue(node, 'bgGradientType', 'linear')">Linear</button>
											<button class="pb-seg-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgGradientType')]==='radial'}" @click="editor.setBgStateValue(node, 'bgGradientType', 'radial')">Radial</button>
										</div>
									</div>
									<div class="pb-form-group" v-if="node.settings[editor.bgStateKey(node,'bgGradientType')]==='linear'">
										<label class="pb-form-label">Angle <span class="pb-form-hint">{{ node.settings[editor.bgStateKey(node,'bgGradientAngle')] ?? 90 }}&deg;</span></label>
										<input type="range" class="pb-range" min="0" max="360" step="1" v-model.number="node.settings[editor.bgStateKey(node,'bgGradientAngle')]">
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Start Color</label>
										<div class="pb-color-row">

											<input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'bgGradientStart')]">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">End Color</label>
										<div class="pb-color-row">

											<input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'bgGradientEnd')]">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Position <span class="pb-form-hint">{{ node.settings[editor.bgStateKey(node,'bgGradientPosition')] ?? 50 }}%</span></label>
										<input type="range" class="pb-range" min="0" max="100" step="1" v-model.number="node.settings[editor.bgStateKey(node,'bgGradientPosition')]">
									</div>
								</template>
								<template v-if="node.settings[editor.bgStateKey(node,'bgType')]==='image'">
									<div class="pb-form-group">
										<label class="pb-form-label">Image</label>
										<div class="pb-bg-media-field" :class="{ 'has-image': !!node.settings[editor.bgStateKey(node,'bgImage')] }">
											<div class="pb-bg-media-preview" :style="node.settings[editor.bgStateKey(node,'bgImage')] ? { backgroundImage: 'url(' + node.settings[editor.bgStateKey(node,'bgImage')] + ')' } : {}">
												<button type="button" class="pb-bg-media-center-btn" :title="node.settings[editor.bgStateKey(node,'bgImage')] ? 'Change Image' : 'Choose Image'" @click="editor.chooseBgImage(node, editor.bgStateKey(node,'bgImage'))">
													<i :class="node.settings[editor.bgStateKey(node,'bgImage')] ? 'fas fa-pen' : 'fas fa-plus'"></i>
												</button>
											</div>
											<div class="pb-bg-media-actions">
												<button type="button" class="pb-bg-media-choose" @click="editor.chooseBgImage(node, editor.bgStateKey(node,'bgImage'))">Choose Image</button>
												<button type="button" class="pb-bg-media-remove" :disabled="!node.settings[editor.bgStateKey(node,'bgImage')]" title="Remove Image" @click="editor.clearBgImage(node, editor.bgStateKey(node,'bgImage'))">
													<i class="fas fa-trash-alt"></i>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgSize')]"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgPosition')]"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgRepeat')]"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgAttachment')]"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
								</template>
								<template v-if="!editor.isBgHoverState(node) && node.settings.bgType==='video'">
									<div class="pb-form-group"><label class="pb-form-label">Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings.bgColor" placeholder="#ffffff"></div>
									<div class="pb-form-group"><label class="pb-form-label">Video Link</label><div class="pb-input-with-action"><input class="pb-input" type="url" v-model.trim="node.settings.bgVideoLink" placeholder="https://www.youtube.com/watch?v=XHOmBV4js_E"><button type="button" class="pb-field-action-btn" title="Choose Video from CKFinder" aria-label="Choose video from CKFinder" @click="editor.chooseMedia(node.settings, 'bgVideoLink', 'Paste video URL')"><i class="fas fa-folder-open"></i></button></div><p class="pb-form-note">Paste a YouTube/Vimeo link, or choose an MP4 video from CKFinder.</p></div>
									<div class="pb-two-column-row"><div class="pb-form-group"><label class="pb-form-label">Start Time</label><input class="pb-input" type="number" min="0" step="0.1" v-model.number="node.settings.bgVideoStart"><p class="pb-form-note">Seconds</p></div><div class="pb-form-group"><label class="pb-form-label">End Time</label><input class="pb-input" type="number" min="0" step="0.1" v-model.number="node.settings.bgVideoEnd"><p class="pb-form-note">Seconds</p></div></div>
									<div v-for="control in [{key:'bgVideoPlayOnce',label:'Play Once'},{key:'bgVideoPlayOnMobile',label:'Play On Mobile'},{key:'bgVideoPrivacy',label:'Privacy Mode'}]" :key="control.key" class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0">{{ control.label }}</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'container-'+node.id+'-'+control.key" type="checkbox" class="pb-toggle" v-model="node.settings[control.key]"><label :for="'container-'+node.id+'-'+control.key"></label></div><span class="pb-toggle-state">{{ node.settings[control.key] ? 'Yes' : 'No' }}</span></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Background Fallback</label><div class="pb-bg-media-field" :class="{ 'has-image': !!node.settings.bgVideoFallback }"><div class="pb-bg-media-preview" :style="node.settings.bgVideoFallback ? { backgroundImage: 'url(' + node.settings.bgVideoFallback + ')' } : {}"><button type="button" class="pb-bg-media-center-btn" :title="node.settings.bgVideoFallback ? 'Change Image' : 'Choose Image'" @click="editor.chooseBgImage(node, 'bgVideoFallback')"><i :class="node.settings.bgVideoFallback ? 'fas fa-pen' : 'fas fa-plus'"></i></button></div><div class="pb-bg-media-actions"><button type="button" class="pb-bg-media-choose" @click="editor.chooseBgImage(node, 'bgVideoFallback')">Choose Image</button><button type="button" class="pb-bg-media-remove" :disabled="!node.settings.bgVideoFallback" title="Remove Image" @click="editor.clearBgImage(node, 'bgVideoFallback')"><i class="fas fa-trash-alt"></i></button></div></div><p class="pb-form-note">This cover image replaces the background video if it cannot be loaded.</p></div>
								</template>
								<template v-if="!editor.isBgHoverState(node) && node.settings.bgType==='slideshow'">
									<div class="pb-form-group pb-container-slideshow-images-control"><label class="pb-form-label">Images</label><div class="pb-container-slideshow-picker" :class="{'is-empty':!node.settings.bgSlideshowImages.length}"><div class="pb-container-slideshow-picker__head"><span>{{ node.settings.bgSlideshowImages.length ? node.settings.bgSlideshowImages.length+' Images Selected' : 'No Images Selected' }}</span><button type="button" class="pb-btn icon-sm" title="Add Images" aria-label="Add images" @click="editor.chooseMediaGallery(node.settings, 'bgSlideshowImages')"><i class="fas fa-plus"></i></button></div><div v-for="(image,index) in node.settings.bgSlideshowImages" :key="image.id" class="pb-container-slideshow-picker__item"><img :src="image.url" :alt="image.alt || ''"><div class="pb-container-slideshow-picker__meta"><input class="pb-input" v-model="image.alt" placeholder="Alt text"></div><div class="pb-container-slideshow-picker__actions"><button type="button" :disabled="index===0" title="Move Up" @click="editor.moveMediaGalleryItem(node.settings, 'bgSlideshowImages', image.id, -1)"><i class="fas fa-chevron-up"></i></button><button type="button" :disabled="index===node.settings.bgSlideshowImages.length-1" title="Move Down" @click="editor.moveMediaGalleryItem(node.settings, 'bgSlideshowImages', image.id, 1)"><i class="fas fa-chevron-down"></i></button><button type="button" title="Remove Image" @click="editor.removeMediaGalleryItem(node.settings, 'bgSlideshowImages', image.id)"><i class="fas fa-trash-alt"></i></button></div></div><button type="button" class="pb-container-slideshow-picker__add" @click="editor.chooseMediaGallery(node.settings, 'bgSlideshowImages')"><i class="fas fa-plus-circle"></i><span>Add Images</span></button></div></div>
									<div class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0">Infinite Loop</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'container-'+node.id+'-slideshow-loop'" type="checkbox" class="pb-toggle" v-model="node.settings.bgSlideshowInfiniteLoop"><label :for="'container-'+node.id+'-slideshow-loop'"></label></div><span class="pb-toggle-state">{{ node.settings.bgSlideshowInfiniteLoop ? 'Yes' : 'No' }}</span></div></div>
									<div class="pb-two-column-row"><div class="pb-form-group"><label class="pb-form-label">Duration (ms)</label><input class="pb-input" type="number" min="1000" step="100" v-model.number="node.settings.bgSlideshowDuration"></div><div class="pb-form-group"><label class="pb-form-label">Transition Duration (ms)</label><input class="pb-input" type="number" min="100" step="50" v-model.number="node.settings.bgSlideshowTransitionDuration"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Transition</label><select class="pb-select" v-model="node.settings.bgSlideshowTransition"><option value="fade">Fade</option><option value="slide-right">Slide Right</option><option value="slide-left">Slide Left</option><option value="slide-up">Slide Up</option><option value="slide-down">Slide Down</option></select></div>
									<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Background Size</label><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-slideshow-size')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('container-slideshow-size')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'container-slideshow-size-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-slideshow-size', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div><select class="pb-select" :value="node.settings[editor.activeResponsiveKey('bgSlideshowSize')] || 'default'" @change="editor.setResponsiveSetting(node.settings, 'bgSlideshowSize', $event.target.value)"><option value="default">Default</option><option value="auto">Auto</option><option value="cover">Cover</option><option value="contain">Contain</option></select></div>
									<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Background Position</label><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-slideshow-position')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('container-slideshow-position')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'container-slideshow-position-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-slideshow-position', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div><select class="pb-select" :value="node.settings[editor.activeResponsiveKey('bgSlideshowPosition')] || 'center center'" @change="editor.setResponsiveSetting(node.settings, 'bgSlideshowPosition', $event.target.value)"><option value="center center">Center Center</option><option value="center left">Center Left</option><option value="center right">Center Right</option><option value="top center">Top Center</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom center">Bottom Center</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
									<div v-for="control in [{key:'bgSlideshowLazyload',label:'Lazyload'},{key:'bgSlideshowKenBurns',label:'Ken Burns Effect'}]" :key="control.key" class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0">{{ control.label }}</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'container-'+node.id+'-'+control.key" type="checkbox" class="pb-toggle" v-model="node.settings[control.key]"><label :for="'container-'+node.id+'-'+control.key"></label></div><span class="pb-toggle-state">{{ node.settings[control.key] ? 'Yes' : 'No' }}</span></div></div>
								</template>
								<div class="pb-form-group" v-if="editor.isBgHoverState(node)">
									<label class="pb-form-label">Transition Duration <span class="pb-form-hint">{{ node.settings.bgTransitionDuration ?? 300 }}ms</span></label>
									<input type="range" class="pb-range" min="0" max="3000" step="50" v-model.number="node.settings.bgTransitionDuration">
								</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Background Overlay</summary>
								<div class="pb-collapsible-body">
									<div class="pb-mini-tab-nav">
										<button class="pb-mini-tab" :class="{active:(node.settings.bgState||'normal')==='normal'}" @click.prevent="editor.setBgState(node, 'normal')">Normal</button>
										<button class="pb-mini-tab" :class="{active:node.settings.bgState==='hover'}" @click.prevent="editor.setBgState(node, 'hover')">Hover</button>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Background Type</label>
										<div class="pb-btn-group pb-icon-btn-group">
											<button class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgOverlayType')]==='color'}" @click.prevent="editor.setBgOverlayTypeForState(node, 'color')" title="Classic"><i class="fas fa-brush"></i></button>
											<button class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgOverlayType')]==='gradient'}" @click.prevent="editor.setBgOverlayTypeForState(node, 'gradient')" title="Gradient"><i class="fas fa-fill-drip"></i></button>
											<button class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgOverlayType')]==='image'}" @click.prevent="editor.setBgOverlayTypeForState(node, 'image')" title="Image"><i class="far fa-image"></i></button>
											<button class="pb-seg-btn pb-icon-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgOverlayType')]==='none'}" @click.prevent="editor.setBgOverlayTypeForState(node, 'none')" title="None"><i class="fas fa-ban"></i></button>
										</div>
									</div>
									<template v-if="node.settings[editor.bgStateKey(node,'bgOverlayType')]==='color'">
										<div class="pb-form-group">
											<label class="pb-form-label">Color</label>
											<div class="pb-color-row">

												<input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'bgOverlayColor')]" placeholder="#000000">
											</div>
										</div>
									</template>
									<template v-if="node.settings[editor.bgStateKey(node,'bgOverlayType')]==='gradient'">
										<div class="pb-form-group">
											<label class="pb-form-label">Gradient Type</label>
											<div class="pb-btn-group">
												<button class="pb-seg-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgOverlayGradientType')]==='linear'}" @click="editor.setBgStateValue(node, 'bgOverlayGradientType', 'linear')">Linear</button>
												<button class="pb-seg-btn" :class="{active:node.settings[editor.bgStateKey(node,'bgOverlayGradientType')]==='radial'}" @click="editor.setBgStateValue(node, 'bgOverlayGradientType', 'radial')">Radial</button>
											</div>
										</div>
										<div class="pb-form-group" v-if="node.settings[editor.bgStateKey(node,'bgOverlayGradientType')]==='linear'">
											<label class="pb-form-label">Angle <span class="pb-form-hint">{{ node.settings[editor.bgStateKey(node,'bgOverlayGradientAngle')] ?? 180 }}&deg;</span></label>
											<input type="range" class="pb-range" min="0" max="360" step="1" v-model.number="node.settings[editor.bgStateKey(node,'bgOverlayGradientAngle')]">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Start Color</label>
											<div class="pb-color-row">

												<input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'bgOverlayGradientStart')]">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">End Color</label>
											<div class="pb-color-row">

												<input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'bgOverlayGradientEnd')]">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Position <span class="pb-form-hint">{{ node.settings[editor.bgStateKey(node,'bgOverlayGradientPosition')] ?? 100 }}%</span></label>
											<input type="range" class="pb-range" min="0" max="100" step="1" v-model.number="node.settings[editor.bgStateKey(node,'bgOverlayGradientPosition')]">
										</div>
									</template>
									<template v-if="node.settings[editor.bgStateKey(node,'bgOverlayType')]==='image'">
										<div class="pb-form-group">
											<label class="pb-form-label">Image</label>
											<div class="pb-bg-media-field" :class="{ 'has-image': !!node.settings[editor.bgStateKey(node,'bgOverlayImage')] }">
												<div class="pb-bg-media-preview" :style="node.settings[editor.bgStateKey(node,'bgOverlayImage')] ? { backgroundImage: 'url(' + node.settings[editor.bgStateKey(node,'bgOverlayImage')] + ')' } : {}">
													<button type="button" class="pb-bg-media-center-btn" :title="node.settings[editor.bgStateKey(node,'bgOverlayImage')] ? 'Change Image' : 'Choose Image'" @click="editor.chooseBgImage(node, editor.bgStateKey(node,'bgOverlayImage'))">
														<i :class="node.settings[editor.bgStateKey(node,'bgOverlayImage')] ? 'fas fa-pen' : 'fas fa-plus'"></i>
													</button>
												</div>
												<div class="pb-bg-media-actions">
													<button type="button" class="pb-bg-media-choose" @click="editor.chooseBgImage(node, editor.bgStateKey(node,'bgOverlayImage'))">Choose Image</button>
													<button type="button" class="pb-bg-media-remove" :disabled="!node.settings[editor.bgStateKey(node,'bgOverlayImage')]" title="Remove Image" @click="editor.clearBgImage(node, editor.bgStateKey(node,'bgOverlayImage'))"><i class="fas fa-trash-alt"></i></button>
												</div>
											</div>
										</div>
										<div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgOverlaySize')]"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
										<div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgOverlayPosition')]"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
										<div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgOverlayRepeat')]"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
										<div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgOverlayAttachment')]"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
									</template>
									<div class="pb-form-group" v-if="node.settings[editor.bgStateKey(node,'bgOverlayType')]!=='none'">
										<label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round((node.settings[editor.bgStateKey(node,'bgOverlayOpacity')] ?? 0.5) * 100) }}%</span></label>
										<input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="node.settings[editor.bgStateKey(node,'bgOverlayOpacity')]">
									</div>
									<div class="pb-form-group" v-if="node.settings[editor.bgStateKey(node,'bgOverlayType')]!=='none'">
										<label class="pb-form-label">Blend Mode</label>
										<select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'bgOverlayBlendMode')]">
											<option value="normal">Normal</option>
											<option value="multiply">Multiply</option>
											<option value="screen">Screen</option>
											<option value="overlay">Overlay</option>
											<option value="darken">Darken</option>
											<option value="lighten">Lighten</option>
											<option value="color-dodge">Color Dodge</option>
											<option value="saturation">Saturation</option>
											<option value="color">Color</option>
											<option value="luminosity">Luminosity</option>
										</select>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Border</summary>
								<div class="pb-collapsible-body">
									<div class="pb-mini-tab-nav">
										<button class="pb-mini-tab" :class="{active:(node.settings.bgState||'normal')==='normal'}" @click.prevent="editor.setBgState(node, 'normal')">Normal</button>
										<button class="pb-mini-tab" :class="{active:node.settings.bgState==='hover'}" @click.prevent="editor.setBgState(node, 'hover')">Hover</button>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="node.settings[editor.bgStateKey(node,'borderType')]"><option value="none">None</option><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option><option value="double">Double</option></select></div>
									<template v-if="node.settings[editor.bgStateKey(node,'borderType')]!=='none'">
										<div class="pb-form-group"><label class="pb-form-label">Border Width</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="20" step="1" :value="dimensionValue(editor.bgStateKey(node,'borderWidth'), 'px') || 0" @input="setDimensionValue(editor.bgStateKey(node,'borderWidth'), $event, 'px')"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" :value="dimensionValue(editor.bgStateKey(node,'borderWidth'), 'px')" @input="setDimensionValue(editor.bgStateKey(node,'borderWidth'), $event, 'px')"><select class="pb-mini-unit" :value="dimensionUnit(editor.bgStateKey(node,'borderWidth'), 'px')" @change="setDimensionUnit(editor.bgStateKey(node,'borderWidth'), $event.target.value, 'px')"><option v-for="unit in ['px','pt','em','rem']" :key="'container-border-width-'+unit" :value="unit">{{ unit }}</option></select></div></div></div>
										<div class="pb-form-group"><label class="pb-form-label">Border Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'borderColor')]"></div></div>
									</template>
									<div class="pb-form-group">
										<div class="pb-label-row pb-label-row-device pb-radius-control-header"><label class="pb-form-label mb-0">Border Radius</label><div class="pb-label-tools"><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-border-radius')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('container-border-radius')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'container-border-radius-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-border-radius', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="dimensionGroupUnit(responsiveRadiusKeys(), 'px')" @change="setRadiusDimensionGroupUnit($event.target.value, 'px')"><option v-for="unit in ['px','%','em','rem','vw']" :key="'container-radius-'+unit" :value="unit">{{ unit }}</option></select></div></div>
										<div class="pb-four-sides pb-four-sides-with-link mt-1"><template v-for="corner in [{key:'borderRadiusTL',label:'Top'},{key:'borderRadiusTR',label:'Right'},{key:'borderRadiusBR',label:'Bottom'},{key:'borderRadiusBL',label:'Left'}]" :key="corner.key"><label class="pb-side-input"><input class="pb-input" type="number" min="0" :value="radiusDimensionValue(corner.key, 'px')" @input="setLinkedRadiusDimensionValue(corner.key, $event, 'px')"><span>{{ corner.label }}</span></label></template><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" @click="node.settings.borderRadiusLinked=!node.settings.borderRadiusLinked" :title="node.settings.borderRadiusLinked?'Unlink':'Link'"><i :class="node.settings.borderRadiusLinked?'fas fa-link':'fas fa-unlink'"></i></button></div></div>

									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Box Shadow</summary>
								<div class="pb-collapsible-body">
									<div class="pb-mini-tab-nav">
										<button class="pb-mini-tab" :class="{active:(node.settings.bgState||'normal')==='normal'}" @click.prevent="editor.setBgState(node, 'normal')">Normal</button>
										<button class="pb-mini-tab" :class="{active:node.settings.bgState==='hover'}" @click.prevent="editor.setBgState(node, 'hover')">Hover</button>
									</div>
									<div class="pb-label-row"><label class="pb-form-label mb-0">Enable Box Shadow</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerShadowEnable-' + node.id + '-' + (node.settings.bgState==='hover' ? 'hover' : 'normal')" v-model="node.settings[editor.bgStateKey(node,'shadowEnabled')]"><label :for="'containerShadowEnable-' + node.id + '-' + (node.settings.bgState==='hover' ? 'hover' : 'normal')"></label></div></div>
									<template v-if="node.settings[editor.bgStateKey(node,'shadowEnabled')]">
										<div class="pb-label-row mt-2"><label class="pb-form-label mb-0">Shadow Dimensions</label><select class="pb-mini-unit" :value="dimensionGroupUnit([editor.bgStateKey(node,'shadowH'),editor.bgStateKey(node,'shadowV'),editor.bgStateKey(node,'shadowBlur'),editor.bgStateKey(node,'shadowSpread')], 'px')" @change="setDimensionGroupUnit([editor.bgStateKey(node,'shadowH'),editor.bgStateKey(node,'shadowV'),editor.bgStateKey(node,'shadowBlur'),editor.bgStateKey(node,'shadowSpread')], $event.target.value, 'px')"><option v-for="unit in ['px','em','rem']" :key="'container-shadow-'+unit" :value="unit">{{ unit }}</option></select></div>
										<div class="pb-four-sides mt-1"><template v-for="control in [{key:'shadowH',label:'H'},{key:'shadowV',label:'V'},{key:'shadowBlur',label:'Blur'},{key:'shadowSpread',label:'Spread'}]" :key="control.key"><label class="pb-side-input"><input class="pb-input" type="number" :min="control.key==='shadowBlur' ? 0 : null" :value="dimensionValue(editor.bgStateKey(node,control.key), 'px')" @input="setDimensionValue(editor.bgStateKey(node,control.key), $event, 'px')"><span>{{ control.label }}</span></label></template></div>
										<div class="pb-form-group mt-2"><label class="pb-form-label">Shadow Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings[editor.bgStateKey(node,'shadowColor')]"></div></div>
										<div class="pb-form-group"><label class="pb-form-label">Shadow Opacity <span class="pb-form-hint">{{ Math.round((node.settings[editor.bgStateKey(node,'shadowOpacity')] ?? 0.3)*100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="node.settings[editor.bgStateKey(node,'shadowOpacity')]"></div>
									</template>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Shape Divider</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<label class="pb-form-label">Side</label>
										<div class="pb-btn-group">
											<button class="pb-seg-btn" :class="{active:(node.settings.shapeDividerSide||'top')==='top'}" @click="node.settings.shapeDividerSide='top'">Top</button>
											<button class="pb-seg-btn" :class="{active:node.settings.shapeDividerSide==='bottom'}" @click="node.settings.shapeDividerSide='bottom'">Bottom</button>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Type</label>
										<select class="pb-select" v-model="node.settings[(node.settings.shapeDividerSide==='bottom'?'shapeDividerBottomType':'shapeDividerTopType')]">
											<option v-for="option in editor.shapeDividerTypeOptions" :key="'shape-divider-' + option.value" :value="option.value">{{ option.label }}</option>
										</select>
									</div>
									<template v-if="node.settings[(node.settings.shapeDividerSide==='bottom'?'shapeDividerBottomType':'shapeDividerTopType')]!=='none'">
										<div class="pb-form-group">
											<label class="pb-form-label">Color</label>
											<div class="pb-color-row">

												<input class="pb-input coloris pb-coloris-input" v-model="node.settings[(node.settings.shapeDividerSide==='bottom'?'shapeDividerBottomColor':'shapeDividerTopColor')]">
											</div>
										</div>
										<div class="pb-form-group" v-if="editor.shapeDividerHasWidth(node)">
											<label class="pb-form-label">Width</label>
											<div class="pb-range-value-row">
												<input type="range" class="pb-range" min="0" max="300" step="1" :value="editor.shapeDividerWidthValue(node)" @input="editor.setShapeDividerWidthValue(node, $event.target.value)">
												<div class="pb-value-with-unit">
													<input class="pb-input pb-input-compact" type="number" min="0" max="300" step="1" :value="editor.shapeDividerWidthValue(node)" @input="editor.setShapeDividerWidthValue(node, $event.target.value)">
													<select class="pb-mini-unit" :value="editor.shapeDividerWidthUnit(node)" @change="editor.setShapeDividerWidthUnit(node, $event.target.value)">
														<option value="%">%</option>
													</select>
												</div>
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Height</label>
											<div class="pb-range-value-row">
												<input type="range" class="pb-range" min="0" max="500" step="1" :value="editor.shapeDividerHeightValue(node)" @input="editor.setShapeDividerHeightValue(node, $event.target.value)">
												<div class="pb-value-with-unit">
													<input class="pb-input pb-input-compact" type="number" min="0" max="500" step="1" :value="editor.shapeDividerHeightValue(node)" @input="editor.setShapeDividerHeightValue(node, $event.target.value)">
													<select class="pb-mini-unit" :value="editor.shapeDividerHeightUnit(node)" @change="editor.setShapeDividerHeightUnit(node, $event.target.value)">
														<option value="px">px</option>
													</select>
												</div>
											</div>
										</div>
										<div class="pb-form-group pb-toggle-label-row" v-if="editor.shapeDividerHasFlip(node)">
											<label class="pb-form-label mb-0">Flip</label>
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'shapeDividerFlip-' + node.id" v-model="node.settings[(node.settings.shapeDividerSide==='bottom'?'shapeDividerBottomFlip':'shapeDividerTopFlip')]"><label :for="'shapeDividerFlip-' + node.id"></label></div>
										</div>
										<div class="pb-form-group pb-toggle-label-row" v-if="editor.shapeDividerHasInvert(node)">
											<label class="pb-form-label mb-0">Invert</label>
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'shapeDividerInvert-' + node.id" v-model="node.settings[(node.settings.shapeDividerSide==='bottom'?'shapeDividerBottomNegative':'shapeDividerTopNegative')]"><label :for="'shapeDividerInvert-' + node.id"></label></div>
										</div>
										<div class="pb-form-group pb-toggle-label-row">
											<label class="pb-form-label mb-0">Bring to Front</label>
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'shapeDividerFront-' + node.id" v-model="node.settings[(node.settings.shapeDividerSide==='bottom'?'shapeDividerBottomFront':'shapeDividerTopFront')]"><label :for="'shapeDividerFront-' + node.id"></label></div>
										</div>
									</template>
								</div>
							</details>
						</div><!-- /tab style container -->
						<!-- TAB ADVANCED -->
						<div v-if="editor.settingsTab==='advanced'" class="pb-tab-content pb-layout-settings__tab">
							<details class="pb-collapsible" open>
								<summary>Layout</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group pb-spacing-control-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Margin</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-margin')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen('container-margin')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="'container-margin-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-margin', device.value)">
														<i :class="device.icon"></i>
														<span>{{ editor.deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<select class="pb-mini-unit pb-edge-unit-select" :value="editor.spacingUnit(node, 'margin')" @change="editor.setSpacingUnit(node, 'margin', $event.target.value)">
												<option v-for="unit in editor.spacingControlUnits" :key="'container-margin-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1">
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'margin', 'Top')" @input="editor.onSpacingSideInput(node, 'margin', 'Top', $event)" placeholder=""><span>Top</span></div>
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'margin', 'Right')" @input="editor.onSpacingSideInput(node, 'margin', 'Right', $event)" placeholder=""><span>Right</span></div>
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'margin', 'Bottom')" @input="editor.onSpacingSideInput(node, 'margin', 'Bottom', $event)" placeholder=""><span>Bottom</span></div>
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'margin', 'Left')" @input="editor.onSpacingSideInput(node, 'margin', 'Left', $event)" placeholder=""><span>Left</span></div>
										<div class="pb-side-link-cell"><button class="pb-link-btn" @click="node.settings.marginLinked=!node.settings.marginLinked" :title="node.settings.marginLinked?'Unlink':'Link'"><i :class="node.settings.marginLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									</div>
								</div>
								<div class="pb-form-group pb-spacing-control-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Padding</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-padding')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen('container-padding')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="'container-padding-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-padding', device.value)">
														<i :class="device.icon"></i>
														<span>{{ editor.deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<select class="pb-mini-unit pb-edge-unit-select" :value="editor.spacingUnit(node, 'padding')" @change="editor.setSpacingUnit(node, 'padding', $event.target.value)">
												<option v-for="unit in editor.spacingControlUnits" :key="'container-padding-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1">
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'padding', 'Top')" @input="editor.onSpacingSideInput(node, 'padding', 'Top', $event)" placeholder=""><span>Top</span></div>
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'padding', 'Right')" @input="editor.onSpacingSideInput(node, 'padding', 'Right', $event)" placeholder=""><span>Right</span></div>
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'padding', 'Bottom')" @input="editor.onSpacingSideInput(node, 'padding', 'Bottom', $event)" placeholder=""><span>Bottom</span></div>
										<div class="pb-side-input"><input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'padding', 'Left')" @input="editor.onSpacingSideInput(node, 'padding', 'Left', $event)" placeholder=""><span>Left</span></div>
										<div class="pb-side-link-cell"><button class="pb-link-btn" @click="node.settings.paddingLinked=!node.settings.paddingLinked" :title="node.settings.paddingLinked?'Unlink':'Link'"><i :class="node.settings.paddingLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Self</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-align-self')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-align-self')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-align-self-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-align-self', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'alignSelf', 'auto')==='auto'}" @click="editor.setContainerResponsiveSetting(node.settings, 'alignSelf', 'auto')" title="Auto"><i class="fas fa-minus"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'alignSelf', 'auto')==='flex-start'}" @click="editor.setContainerResponsiveSetting(node.settings, 'alignSelf', 'flex-start')" title="Start"><i class="fas fa-arrow-up"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'alignSelf', 'auto')==='center'}" @click="editor.setContainerResponsiveSetting(node.settings, 'alignSelf', 'center')" title="Center"><i class="fas fa-arrows-alt-v"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'alignSelf', 'auto')==='flex-end'}" @click="editor.setContainerResponsiveSetting(node.settings, 'alignSelf', 'flex-end')" title="End"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'alignSelf', 'auto')==='stretch'}" @click="editor.setContainerResponsiveSetting(node.settings, 'alignSelf', 'stretch')" title="Stretch"><i class="fas fa-expand-arrows-alt"></i></button>
									</div>
									<div class="pb-form-note">This control affects this container inside its parent layout.</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Order</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-order')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-order')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-order-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-order', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'order', '')==='-1'}" @click="editor.setContainerResponsiveSetting(node.settings, 'order', '-1')" title="Start"><i class="fas fa-arrow-left"></i></button>
										<button class="pb-seg-btn" :class="{active:['', null, 'default'].includes(editor.containerResponsiveValue(node.settings, 'order', ''))}" @click="editor.setContainerResponsiveSetting(node.settings, 'order', 'default')" title="Default"><i class="fas fa-ellipsis-v"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'order', '')==='1'}" @click="editor.setContainerResponsiveSetting(node.settings, 'order', '1')" title="End"><i class="fas fa-arrow-right"></i></button>
									</div>
									<div class="pb-form-note">This control affects this container inside its parent layout.</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Size</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('container-size')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
											<div v-if="editor.isControlResponsiveMenuOpen('container-size')" class="pb-control-device-menu">
												<button v-for="device in editor.responsiveDevices" :key="'container-size-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('container-size', device.value)">
													<i :class="device.icon"></i>
													<span>{{ editor.deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'sizeMode', 'default')==='default'}" @click="editor.setContainerResponsiveSetting(node.settings, 'sizeMode', 'default')" title="Default"><i class="fas fa-ban"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'sizeMode', 'default')==='grow'}" @click="editor.setContainerResponsiveSetting(node.settings, 'sizeMode', 'grow')" title="Grow"><i class="fas fa-arrows-alt-h"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'sizeMode', 'default')==='shrink'}" @click="editor.setContainerResponsiveSetting(node.settings, 'sizeMode', 'shrink')" title="Shrink"><i class="fas fa-compress"></i></button>
										<button class="pb-seg-btn" :class="{active:editor.containerResponsiveValue(node.settings, 'sizeMode', 'default')==='custom'}" @click="editor.setContainerResponsiveSetting(node.settings, 'sizeMode', 'custom')" title="Custom"><i class="fas fa-ellipsis-h"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Position</label>
									<select class="pb-select" v-model="node.settings.position">
										<option value="default">Default</option>
										<option value="absolute">Absolute</option>
										<option value="fixed">Fixed</option>
									</select>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row">
										<label class="pb-form-label mb-0">Z-Index</label>
										<div class="pb-control-unit-wrap"></div>
									</div>
									<input class="pb-input pb-input-compact" v-model="node.settings.zIndex" type="number" placeholder="">
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">CSS ID</label>
									<input class="pb-input" v-model="node.settings.cssId" placeholder="">
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">CSS Classes</label>
									<input class="pb-input" v-model="node.settings.cssClass" placeholder="">
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row">
										<label class="pb-form-label mb-0">Display Conditions</label>
										<button class="pb-field-action-btn" type="button" title="Display Conditions" @click="editor.showUnsupportedControlNotice('Display Conditions', 'Display Conditions panel belum tersedia di builder ini. Untuk saat ini yang aktif baru hide per device.')"><i class="fas fa-sitemap"></i></button>
									</div>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Motion Effects</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<div class="pb-inline-action-row" role="button" tabindex="0" title="Animate With AI" @click="editor.showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')" @keydown.enter.prevent="editor.showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')" @keydown.space.prevent="editor.showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')">
											<label class="pb-form-label mb-0">Animate With AI</label>
										</div>
									</div>
									<div class="pb-form-group pb-toggle-label-row">
										<label class="pb-form-label mb-0">Scrolling Effects</label>
										<div class="pb-toggle-switch-wrap">
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollEffects-' + node.id" v-model="node.settings.scrollingEffects"><label :for="'containerScrollEffects-' + node.id"></label></div>
											<span class="pb-toggle-state">{{ node.settings.scrollingEffects ? 'on' : 'off' }}</span>
										</div>
									</div>
									<template v-if="node.settings.scrollingEffects">
										<div class="pb-form-group">
											<label class="pb-form-label">Scroll Effect Type</label>
											<select class="pb-select" v-model="node.settings.scrollEffectType">
												<option value="vertical">Vertical</option>
												<option value="horizontal">Horizontal</option>
												<option value="transparency">Transparency</option>
												<option value="blur">Blur</option>
												<option value="rotate">Rotate</option>
												<option value="scale">Scale</option>
											</select>
										</div>
										<div class="pb-gap-row">
											<div class="pb-gap-field">
												<label class="pb-form-label">Direction</label>
												<select class="pb-select" v-model="node.settings.scrollDirection">
													<option value="up">Up</option>
													<option value="down">Down</option>
													<option value="left">Left</option>
													<option value="right">Right</option>
													<option value="in">In</option>
													<option value="out">Out</option>
												</select>
											</div>
											<div class="pb-gap-field">
												<label class="pb-form-label">Speed</label>
												<input class="pb-input" type="number" min="0" max="10" step="0.1" v-model.number="node.settings.scrollSpeed">
											</div>
										</div>
										<div class="pb-gap-row">
											<div class="pb-gap-field">
												<label class="pb-form-label">Viewport Start</label>
												<input class="pb-input" type="number" min="0" max="100" step="1" v-model.number="node.settings.scrollViewportStart">
											</div>
											<div class="pb-gap-field">
												<label class="pb-form-label">Viewport End</label>
												<input class="pb-input" type="number" min="0" max="100" step="1" v-model.number="node.settings.scrollViewportEnd">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Relative To</label>
											<select class="pb-select" v-model="node.settings.scrollRelativeTo">
												<option value="default">Default</option>
												<option value="viewport">Viewport</option>
											</select>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollDesktop-' + node.id" v-model="node.settings.scrollApplyDesktop"><label :for="'containerScrollDesktop-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollTablet-' + node.id" v-model="node.settings.scrollApplyTablet"><label :for="'containerScrollTablet-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollMobile-' + node.id" v-model="node.settings.scrollApplyMobile"><label :for="'containerScrollMobile-' + node.id"></label></div></div>
										</div>
									</template>
									<div class="pb-form-group pb-toggle-label-row">
										<label class="pb-form-label mb-0">Mouse Effects</label>
										<div class="pb-toggle-switch-wrap">
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseEffects-' + node.id" v-model="node.settings.mouseEffects"><label :for="'containerMouseEffects-' + node.id"></label></div>
											<span class="pb-toggle-state">{{ node.settings.mouseEffects ? 'on' : 'off' }}</span>
										</div>
									</div>
									<template v-if="node.settings.mouseEffects">
										<div class="pb-form-group">
											<label class="pb-form-label">Mouse Effect Type</label>
											<select class="pb-select" v-model="node.settings.mouseEffectType">
												<option value="track">Track</option>
												<option value="tilt">3D Tilt</option>
												<option value="parallax">Parallax</option>
											</select>
										</div>
										<div class="pb-gap-row">
											<div class="pb-gap-field">
												<label class="pb-form-label">Direction</label>
												<select class="pb-select" v-model="node.settings.mouseDirection">
													<option value="direct">Direct</option>
													<option value="opposite">Opposite</option>
												</select>
											</div>
											<div class="pb-gap-field">
												<label class="pb-form-label">Speed</label>
												<input class="pb-input" type="number" min="0" max="10" step="0.1" v-model.number="node.settings.mouseSpeed">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Relative To</label>
											<select class="pb-select" v-model="node.settings.mouseRelativeTo">
												<option value="default">Default</option>
												<option value="viewport">Viewport</option>
											</select>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseDesktop-' + node.id" v-model="node.settings.mouseApplyDesktop"><label :for="'containerMouseDesktop-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseTablet-' + node.id" v-model="node.settings.mouseApplyTablet"><label :for="'containerMouseTablet-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseMobile-' + node.id" v-model="node.settings.mouseApplyMobile"><label :for="'containerMouseMobile-' + node.id"></label></div></div>
										</div>
									</template>
									<div class="pb-form-group">
										<label class="pb-form-label">Sticky</label>
										<select class="pb-select" v-model="node.settings.sticky">
											<option value="none">None</option>
											<option value="top">Top</option>
											<option value="bottom">Bottom</option>
										</select>
									</div>
									<template v-if="node.settings.sticky !== 'none'">
										<div class="pb-grid-gap-controls">
										<div v-for="control in [{key:'stickyOffset',label:'Sticky Offset'},{key:'stickyEffectsOffset',label:'Effects Offset'}]" :key="control.key" class="pb-form-group"><label class="pb-form-label">{{ control.label }}</label><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" :value="dimensionValue(control.key, 'px')" @input="setDimensionValue(control.key, $event, 'px')"><select class="pb-mini-unit" :value="dimensionUnit(control.key, 'px')" @change="setDimensionUnit(control.key, $event.target.value, 'px')"><option v-for="unit in ['px','%','em','rem','vw']" :key="control.key+'-'+unit" :value="unit">{{ unit }}</option></select></div></div>
									</div>
									<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerStickyDesktop-' + node.id" v-model="node.settings.stickyOnDesktop"><label :for="'containerStickyDesktop-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerStickyTablet-' + node.id" v-model="node.settings.stickyOnTablet"><label :for="'containerStickyTablet-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerStickyMobile-' + node.id" v-model="node.settings.stickyOnMobile"><label :for="'containerStickyMobile-' + node.id"></label></div></div>
										</div>
									</template>
									<div class="pb-form-group">
										<label class="pb-form-label">Entrance Animation</label>
										<select class="pb-select" v-model="node.settings.entranceAnimation">
											<option value="">None</option>
											<option value="fadeIn">Fade In</option>
											<option value="fadeInUp">Fade In Up</option>
											<option value="fadeInDown">Fade In Down</option>
											<option value="fadeInLeft">Fade In Left</option>
											<option value="fadeInRight">Fade In Right</option>
											<option value="zoomIn">Zoom In</option>
											<option value="slideInUp">Slide In Up</option>
											<option value="slideInDown">Slide In Down</option>
											<option value="slideInLeft">Slide In Left</option>
											<option value="slideInRight">Slide In Right</option>
											<option value="bounceIn">Bounce In</option>
										</select>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Transform</summary>
								<div class="pb-collapsible-body">
									<div class="pb-grid-gap-controls">
									<div v-for="control in [{key:'transformRotate',label:'Rotate'},{key:'transformSkewX',label:'Skew X'},{key:'transformSkewY',label:'Skew Y'}]" :key="control.key" class="pb-form-group"><label class="pb-form-label">{{ control.label }}</label><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :value="dimensionValue(control.key, 'deg')" @input="setDimensionValue(control.key, $event, 'deg')"><select class="pb-mini-unit" disabled><option>deg</option></select></div></div>
									<div v-for="control in [{key:'transformOffsetX',label:'Offset X'},{key:'transformOffsetY',label:'Offset Y'}]" :key="control.key" class="pb-form-group"><label class="pb-form-label">{{ control.label }}</label><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :value="dimensionValue(control.key, 'px')" @input="setDimensionValue(control.key, $event, 'px')"><select class="pb-mini-unit" :value="dimensionUnit(control.key, 'px')" @change="setDimensionUnit(control.key, $event.target.value, 'px')"><option v-for="unit in ['px','%','em','rem','vw']" :key="control.key+'-'+unit" :value="unit">{{ unit }}</option></select></div></div>
									<div v-for="control in [{key:'transformScaleX',label:'Scale X'},{key:'transformScaleY',label:'Scale Y'}]" :key="control.key" class="pb-form-group"><label class="pb-form-label">{{ control.label }}</label><input class="pb-input" type="number" step="0.1" v-model.number="node.settings[control.key]" placeholder="1"></div>
								</div>

								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Responsive</summary>
								<div class="pb-collapsible-body">
									<div class="pb-advanced-switch-grid">
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Hide Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerHideDesktop-' + node.id" v-model="node.settings.hideDesktop"><label :for="'containerHideDesktop-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Hide Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerHideTablet-' + node.id" v-model="node.settings.hideTablet"><label :for="'containerHideTablet-' + node.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Hide Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerHideMobile-' + node.id" v-model="node.settings.hideMobile"><label :for="'containerHideMobile-' + node.id"></label></div></div>
										</div>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Attributes</summary>
								<div class="pb-collapsible-body">
									<div class="pb-label-row"><div class="pb-form-label mb-0">Custom Attributes</div><button type="button" class="pb-seg-btn pb-mini-btn" aria-label="Add Attribute" @click="node.settings.attributes=(node.settings.attributes||[]).concat({name:'',value:''})"><i class="fas fa-plus"></i></button></div>
									<div v-for="(attr,index) in node.settings.attributes" :key="'container-attr-'+index" class="pb-attr-row mt-2">
										<input class="pb-input" v-model="attr.name" placeholder="data-name">
										<input class="pb-input" v-model="attr.value" placeholder="value">
										<button type="button" class="pb-seg-btn pb-mini-btn" aria-label="Remove Attribute" @click="node.settings.attributes.splice(index,1)"><i class="fas fa-trash-alt"></i></button>
									</div>
									<div v-if="!(node.settings.attributes||[]).length" class="pb-form-note">Add valid HTML attributes such as data-name or aria-label.</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Custom CSS</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group"><label class="pb-form-label">CSS Code</label><textarea class="pb-textarea pb-code-editor" v-model="node.settings.customCssCode" placeholder="selector { property: value; }"></textarea></div>
									<div class="pb-form-note">Use selector to target this Container or Grid.</div>
								</div>
							</details>
						</div><!-- /tab advanced container -->
						</div>
</template>

<script>
export default {
	name: 'ContainerWidgetSettings',
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	methods: {
		dimensionParts(key, fallbackUnit = 'px') {
			const raw = String(this.node.settings[key] ?? '').trim();
			if (!raw || raw.toLowerCase() === 'auto') return { value: '', unit: fallbackUnit };
			const match = raw.match(/^(-?(?:\d+\.?\d*|\.\d+))\s*(px|pt|em|rem|%|vw|deg)?$/i);
			if (!match) return { value: '', unit: fallbackUnit };
			return { value: match[1], unit: (match[2] || fallbackUnit).toLowerCase() };
		},
		dimensionValue(key, fallbackUnit = 'px') { return this.dimensionParts(key, fallbackUnit).value; },
		dimensionUnit(key, fallbackUnit = 'px') { return this.dimensionParts(key, fallbackUnit).unit; },
		setDimensionValue(key, event, fallbackUnit = 'px', emptyToken = '') { const raw = String(event?.target?.value ?? '').trim(); if (raw === '') { this.node.settings[key] = emptyToken; return; } const value = Number(raw); if (!Number.isFinite(value)) return; this.node.settings[key] = String(value) + this.dimensionUnit(key, fallbackUnit); },
		setDimensionUnit(key, unit, fallbackUnit = 'px') { const value = this.dimensionValue(key, fallbackUnit); if (value === '') return; this.node.settings[key] = String(value) + String(unit || fallbackUnit); },
		dimensionGroupUnit(keys, fallbackUnit = 'px') { for (const key of keys) if (this.dimensionValue(key, fallbackUnit) !== '') return this.dimensionUnit(key, fallbackUnit); return fallbackUnit; },
		setDimensionGroupUnit(keys, unit, fallbackUnit = 'px') { keys.forEach((key) => this.setDimensionUnit(key, unit, fallbackUnit)); },
		setLinkedDimensionValue(key, keys, event, fallbackUnit = 'px', linked = false) { this.setDimensionValue(key, event, fallbackUnit); if (!linked) return; const value = this.node.settings[key]; keys.forEach((target) => { if (target !== key) this.node.settings[target] = value; }); },
		responsiveRadiusKeys() { return ['borderRadiusTL', 'borderRadiusTR', 'borderRadiusBR', 'borderRadiusBL'].map((key) => this.editor.activeResponsiveKey(key)); },
		radiusDimensionValue(key, fallbackUnit = 'px') { return this.dimensionValue(this.editor.activeResponsiveKey(key), fallbackUnit); },
		setRadiusDimensionGroupUnit(unit, fallbackUnit = 'px') { this.node.settings.borderRadius = ''; this.setDimensionGroupUnit(this.responsiveRadiusKeys(), unit, fallbackUnit); },
		setLinkedRadiusDimensionValue(key, event, fallbackUnit = 'px') { this.node.settings.borderRadius = ''; this.setLinkedDimensionValue(this.editor.activeResponsiveKey(key), this.responsiveRadiusKeys(), event, fallbackUnit, this.node.settings.borderRadiusLinked); },
	},
};
</script>

<style scoped>
.pb-two-column-row { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; }
.pb-two-column-row > .pb-form-group { margin-bottom:0; }
.pb-container-child-actions { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:9px; color:#657084; font-size:10px; font-weight:650; }
.pb-container-add-child { display:inline-flex; align-items:center; justify-content:center; gap:5px; min-height:28px; padding:4px 9px; border:1px solid #cbd5ff; border-radius:6px; background:#f3f4ff; color:#5549d7; font-size:10px; font-weight:700; cursor:pointer; }
.pb-container-slideshow-picker { overflow:hidden; border:1px solid #dce3ef; border-radius:6px; background:#fff; }
.pb-container-slideshow-picker__head { display:flex; align-items:center; justify-content:space-between; min-height:36px; padding:6px 8px; background:#f7f9fc; color:#334155; font-size:12px; font-weight:600; }
.pb-container-slideshow-picker__head .pb-btn.icon-sm { width:24px; min-width:24px; height:24px; min-height:24px; padding:0; border-radius:6px; }
.pb-container-slideshow-picker__item { display:grid; grid-template-columns:46px minmax(0, 1fr) 24px; gap:7px; padding:7px; border-top:1px solid #e7ebf2; }
.pb-container-slideshow-picker__item img { width:46px; height:46px; object-fit:cover; border-radius:4px; background:#edf1f7; }
.pb-container-slideshow-picker__meta { display:grid; align-content:center; min-width:0; }
.pb-container-slideshow-picker__meta .pb-input { min-height:28px; padding:5px 7px; font-size:12px; }
.pb-container-slideshow-picker__actions { display:flex; flex-direction:column; gap:1px; opacity:.5; transition:opacity .16s ease; }
.pb-container-slideshow-picker__item:hover .pb-container-slideshow-picker__actions, .pb-container-slideshow-picker__item:focus-within .pb-container-slideshow-picker__actions { opacity:1; }
.pb-container-slideshow-picker__actions button { width:24px; min-width:24px; height:20px; min-height:20px; padding:0; border:0; background:transparent; color:#667085; font-size:10px; }
.pb-container-slideshow-picker__actions button:last-child { color:#b42318; }
.pb-container-slideshow-picker__actions button:disabled { opacity:.35; }
.pb-container-slideshow-picker__add { display:flex; align-items:center; justify-content:center; gap:6px; width:100%; min-height:30px; padding:7px 8px; border:0; border-top:1px solid #e7ebf2; background:#fff; color:#4f46e5; font-size:12px; }
</style>
