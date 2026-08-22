<template>
						<div class="pb-grid-settings pb-grid-settings--layout">
							<div class="pb-tab-nav">
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='layout'}"   @click="editor.settingsTab='layout'"><i class="fas fa-th-large"></i><span>Layout</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='style'}"    @click="editor.settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
							</div>
						<!-- TAB LAYOUT (GRID) -->
						<div v-if="editor.settingsTab==='layout'" class="pb-tab-content pb-grid-settings__tab">
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row pb-grid-settings__section-head">
									<div class="pb-prop-section-title mb-0">Grid Layout</div>
									<div class="pb-control-device-wrap">
										<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(node.type + '-grid-layout')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
										<div v-if="editor.isControlResponsiveMenuOpen(node.type + '-grid-layout')" class="pb-control-device-menu">
											<button v-for="device in editor.responsiveDevices" :key="node.type + '-grid-layout-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(node.type + '-grid-layout', device.value)">
												<i :class="device.icon"></i>
												<span>{{ editor.deviceOptionLabel(device) }}</span>
											</button>
										</div>
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Columns <span class="pb-form-hint">1-12</span></label>
									<input class="pb-input" :value="editor.gridColumnsValue(node)" @input="editor.setGridColumnsValue(node, $event.target.value)" type="number" min="1" max="12">
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Grid Auto Height</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridAutoHeight-' + node.id" v-model="node.settings.gridAutoHeight"><label :for="'gridAutoHeight-' + node.id"></label></div></div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Rows</label>
									<input class="pb-input" :value="editor.gridRowsValue(node)" @input="editor.setGridRowsValue(node, $event.target.value)" type="number" min="1" max="12">
								</div>
				<div class="pb-form-group">
					<label class="pb-form-label">Column Structure</label>
					<div class="pb-layout-item-list">
						<div v-for="track in editor.gridColumnTracks(node)" :key="track.index" class="pb-layout-item-row pb-grid-column-track-row" :class="{'is-active': editor.gridColumnStyleTargetLabel(node) === 'Column Style — ' + track.label}">
							<button type="button" class="pb-layout-item-main pb-grid-column-track-trigger" @click="expandedGridTrack = expandedGridTrack === track.index ? -1 : track.index; editor.selectGridColumnStyleTarget(node, track.index)">
								<span class="pb-layout-item-icon"><i class="fas fa-columns"></i></span>
								<span class="pb-layout-item-copy"><strong>{{ track.label }}</strong><small>{{ track.itemCount }} item{{ track.itemCount === 1 ? '' : 's' }}<em v-if="track.hasOverride"> · Custom</em></small></span>
								<i class="fas pb-grid-column-track-chevron" :class="expandedGridTrack === track.index ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
							</button>
							<button type="button" class="pb-layout-item-delete" :disabled="!track.canRemove" :aria-label="'Delete ' + track.label" @click.stop="editor.requestGridColumnRemoval(node, track.index)">
								<i class="fas fa-trash"></i>
							</button>
							<div v-if="expandedGridTrack === track.index" class="pb-grid-column-cell-list">
								<button v-for="cell in editor.gridColumnTrackCells(node, track.index)" :key="cell.cellIndex" type="button" class="pb-grid-column-cell-row" :class="{'is-active': editor.gridColumnStyleTargetLabel(node) === 'Cell Override — ' + track.label + ' / Row ' + (cell.rowIndex + 1)}" @click.stop="editor.selectGridColumnStyleTarget(node, track.index, cell.cellIndex)">
									<span class="pb-layout-item-icon"><i class="fas fa-square"></i></span><span class="pb-layout-item-copy"><strong>{{ cell.label }}</strong><small>{{ cell.itemCount }} item{{ cell.itemCount === 1 ? '' : 's' }}<em v-if="cell.hasOverride"> · Custom</em></small></span>
								</button>
							</div>
						</div>
					</div>
					<div class="pb-form-note">The final column cannot be deleted.</div>
								</div>
								<div class="pb-form-group" v-if="editor.responsiveDevice==='desktop'">
									<label class="pb-form-label">Grid Template Columns</label>
									<input class="pb-input" v-model="node.settings.gridTemplateColumns" placeholder="repeat(3, minmax(0, 1fr))">
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Column / Row Gap</label><button class="pb-link-btn" @click="node.settings.gapLinked=!node.settings.gapLinked" :title="node.settings.gapLinked?'Unlink':'Link'"><i :class="node.settings.gapLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									<div class="pb-grid-gap-controls mt-1">
										<div>
											<label class="pb-form-label">Column</label>
											<div class="pb-range-value-row">
												<input type="range" min="0" :max="editor.sizeControlMax(node, 'columnGap', '20px')" :step="editor.sizeControlStep(node, 'columnGap', '20px')" :value="editor.sizeControlDisplayValue(node, 'columnGap', '20px')" @input="editor.onSizeControlInput(node, 'columnGap', $event, { fallback: '20px' }); editor.syncGridGap(node.settings, 'columnGap')">
												<input class="pb-input pb-range-number" type="number" :step="editor.sizeControlStep(node, 'columnGap', '20px')" :value="editor.sizeControlDisplayValue(node, 'columnGap', '20px')" @input="editor.onSizeControlInput(node, 'columnGap', $event, { fallback: '20px' }); editor.syncGridGap(node.settings, 'columnGap')">
												<select class="pb-mini-unit" :value="editor.sizeControlUnit(node, 'columnGap', '20px')" @change="editor.setSizeControlUnit(node, 'columnGap', $event.target.value, { fallback: '20px' }); editor.syncGridGap(node.settings, 'columnGap')">
													<option v-for="unit in editor.spacingControlUnits" :key="'column-gap-'+unit" :value="unit">{{ unit }}</option>
												</select>
											</div>
										</div>
										<div>
											<label class="pb-form-label">Row</label>
											<div class="pb-range-value-row">
												<input type="range" min="0" :max="editor.sizeControlMax(node, 'rowGap', '20px')" :step="editor.sizeControlStep(node, 'rowGap', '20px')" :value="editor.sizeControlDisplayValue(node, 'rowGap', '20px')" @input="editor.onSizeControlInput(node, 'rowGap', $event, { fallback: '20px' }); editor.syncGridGap(node.settings, 'rowGap')">
												<input class="pb-input pb-range-number" type="number" :step="editor.sizeControlStep(node, 'rowGap', '20px')" :value="editor.sizeControlDisplayValue(node, 'rowGap', '20px')" @input="editor.onSizeControlInput(node, 'rowGap', $event, { fallback: '20px' }); editor.syncGridGap(node.settings, 'rowGap')">
												<select class="pb-mini-unit" :value="editor.sizeControlUnit(node, 'rowGap', '20px')" @change="editor.setSizeControlUnit(node, 'rowGap', $event.target.value, { fallback: '20px' }); editor.syncGridGap(node.settings, 'rowGap')">
													<option v-for="unit in editor.spacingControlUnits" :key="'row-gap-'+unit" :value="unit">{{ unit }}</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Auto Flow</label>
									<select class="pb-select" v-model="node.settings.autoFlow">
										<option value="row">Row</option>
										<option value="column">Column</option>
										<option value="dense">Dense</option>
									</select>
								</div>
							</div>
						</div>
		<!-- TAB STYLE (GRID) -->
		<div v-if="editor.settingsTab==='style'" class="pb-tab-content pb-grid-settings__tab">
			<component v-if="editor.gridColumnStyleTargetLabel(node)" :is="editor.gridColumnStyleControls" :node="node" :editor="editor" />
			<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row pb-grid-settings__section-head">
									<div class="pb-prop-section-title mb-0">Spacing</div>
								</div>
								<div class="pb-form-group pb-spacing-control-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Padding</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(node.type + '-padding')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen(node.type + '-padding')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="node.type + '-padding-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(node.type + '-padding', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button>
												</div>
											</div>
											<select class="pb-mini-unit pb-edge-unit-select" :value="editor.spacingUnit(node, 'padding')" @change="editor.setSpacingUnit(node, 'padding', $event.target.value)">
												<option v-for="unit in editor.spacingControlUnits" :key="'padding-'+unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1">
										<template v-for="side in ['Top','Right','Bottom','Left']" :key="'grid-padding-'+side">
											<label class="pb-side-input">
												<input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'padding', side)" @input="editor.onSpacingSideInput(node, 'padding', side, $event)">
												<span>{{ side }}</span>
											</label>
										</template>
										<div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:node.settings.paddingLinked}" @click="node.settings.paddingLinked=!node.settings.paddingLinked" :title="node.settings.paddingLinked?'Unlink':'Link'"><i :class="node.settings.paddingLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									</div>
								</div>
								<div class="pb-form-group pb-spacing-control-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Margin</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(node.type + '-margin')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
												<div v-if="editor.isControlResponsiveMenuOpen(node.type + '-margin')" class="pb-control-device-menu">
													<button v-for="device in editor.responsiveDevices" :key="node.type + '-margin-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(node.type + '-margin', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button>
												</div>
											</div>
											<select class="pb-mini-unit pb-edge-unit-select" :value="editor.spacingUnit(node, 'margin')" @change="editor.setSpacingUnit(node, 'margin', $event.target.value)">
												<option v-for="unit in editor.spacingControlUnits" :key="'margin-'+unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1">
										<template v-for="side in ['Top','Right','Bottom','Left']" :key="'grid-margin-'+side">
											<label class="pb-side-input">
												<input class="pb-input" type="number" :value="editor.spacingSideValue(node, 'margin', side)" @input="editor.onSpacingSideInput(node, 'margin', side, $event)">
												<span>{{ side }}</span>
											</label>
										</template>
										<div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:node.settings.marginLinked}" @click="node.settings.marginLinked=!node.settings.marginLinked" :title="node.settings.marginLinked?'Unlink':'Link'"><i :class="node.settings.marginLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									</div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Background</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Type</label>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:node.settings.bgType==='none'}"     @click="node.settings.bgType='none'">None</button>
										<button class="pb-seg-btn" :class="{active:node.settings.bgType==='color'}"    @click="node.settings.bgType='color'">Color</button>
										<button class="pb-seg-btn" :class="{active:node.settings.bgType==='gradient'}" @click="node.settings.bgType='gradient'">Gradient</button>
										<button class="pb-seg-btn" :class="{active:node.settings.bgType==='image'}"    @click="node.settings.bgType='image'">Image</button>
									</div>
								</div>
								<template v-if="node.settings.bgType==='color'">
									<div class="pb-form-group"><label class="pb-form-label">Background Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.bgColor" placeholder="#ffffff"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round((node.settings.bgOpacity ?? 1)*100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="node.settings.bgOpacity"></div>
								</template>
								<template v-if="node.settings.bgType==='gradient'">
									<div class="pb-form-group"><label class="pb-form-label">Gradient Type</label><div class="pb-btn-group"><button class="pb-seg-btn" :class="{active:node.settings.bgGradientType==='linear'}" @click="node.settings.bgGradientType='linear'">Linear</button><button class="pb-seg-btn" :class="{active:node.settings.bgGradientType==='radial'}" @click="node.settings.bgGradientType='radial'">Radial</button></div></div>
									<div class="pb-form-group" v-if="node.settings.bgGradientType==='linear'"><label class="pb-form-label">Angle <span class="pb-form-hint">{{ node.settings.bgGradientAngle ?? 90 }}&deg;</span></label><input type="range" class="pb-range" min="0" max="360" step="1" v-model.number="node.settings.bgGradientAngle"></div>
									<div class="pb-form-group"><label class="pb-form-label">Start Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.bgGradientStart"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">End Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.bgGradientEnd"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Position <span class="pb-form-hint">{{ node.settings.bgGradientPosition ?? 50 }}%</span></label><input type="range" class="pb-range" min="0" max="100" step="1" v-model.number="node.settings.bgGradientPosition"></div>
								</template>
								<template v-if="node.settings.bgType==='image'">
									<div class="pb-form-group">
										<label class="pb-form-label">Image</label>
										<div class="pb-bg-media-field" :class="{ 'has-image': !!node.settings.bgImage }">
											<div class="pb-bg-media-preview" :style="node.settings.bgImage ? { backgroundImage: 'url(' + node.settings.bgImage + ')' } : {}">
												<button type="button" class="pb-bg-media-center-btn" :title="node.settings.bgImage ? 'Change Image' : 'Choose Image'" @click="editor.chooseBgImage(node)">
													<i :class="node.settings.bgImage ? 'fas fa-pen' : 'fas fa-plus'"></i>
												</button>
											</div>
											<div class="pb-bg-media-actions">
												<button type="button" class="pb-bg-media-choose" @click="editor.chooseBgImage(node)">Choose Image</button>
												<button type="button" class="pb-bg-media-remove" :disabled="!node.settings.bgImage" title="Remove Image" @click="editor.clearBgImage(node)">
													<i class="fas fa-trash-alt"></i>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" v-model="node.settings.bgSize"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" v-model="node.settings.bgPosition"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" v-model="node.settings.bgRepeat"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" v-model="node.settings.bgAttachment"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
								</template>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Border</div>
								<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="node.settings.borderType"><option value="none">None</option><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option><option value="double">Double</option></select></div>
								<template v-if="node.settings.borderType!=='none'">
									<div class="pb-form-group">
										<label class="pb-form-label">Border Width</label>
										<div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="20" step="1" :value="dimensionValue('borderWidth', 'px') || 0" @input="setDimensionValue('borderWidth', $event, 'px')"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" step="1" :value="dimensionValue('borderWidth', 'px')" @input="setDimensionValue('borderWidth', $event, 'px')"><select class="pb-mini-unit" :value="dimensionUnit('borderWidth', 'px')" @change="setDimensionUnit('borderWidth', $event.target.value, 'px')"><option v-for="unit in ['px','pt','em','rem']" :key="'grid-border-width-'+unit" :value="unit">{{ unit }}</option></select></div></div>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Border Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.borderColor"></div></div>
								</template>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device pb-radius-control-header"><label class="pb-form-label mb-0">Border Radius</label><div class="pb-label-tools"><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('grid-border-radius')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('grid-border-radius')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'grid-border-radius-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('grid-border-radius', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="dimensionGroupUnit(responsiveRadiusKeys(), 'px')" @change="setRadiusDimensionGroupUnit($event.target.value, 'px')"><option v-for="unit in ['px','%','em','rem','vw']" :key="'grid-radius-'+unit" :value="unit">{{ unit }}</option></select></div></div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1"><template v-for="corner in [{key:'borderRadiusTL',label:'TL'},{key:'borderRadiusTR',label:'TR'},{key:'borderRadiusBR',label:'BR'},{key:'borderRadiusBL',label:'BL'}]" :key="corner.key"><label class="pb-side-input"><input class="pb-input" type="number" min="0" :value="radiusDimensionValue(corner.key, 'px')" @input="setLinkedRadiusDimensionValue(corner.key, $event, 'px')"><span>{{ corner.label }}</span></label></template><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" @click="node.settings.borderRadiusLinked=!node.settings.borderRadiusLinked" :title="node.settings.borderRadiusLinked?'Unlink':'Link'"><i :class="node.settings.borderRadiusLinked?'fas fa-link':'fas fa-unlink'"></i></button></div></div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row"><div class="pb-prop-section-title mb-0">Box Shadow</div><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridShadowEnable-' + node.id" v-model="node.settings.shadowEnabled"><label :for="'gridShadowEnable-' + node.id"></label></div></div>
								<template v-if="node.settings.shadowEnabled">
									<div class="pb-label-row mt-2"><label class="pb-form-label mb-0">Shadow Dimensions</label><select class="pb-mini-unit" :value="dimensionGroupUnit(['shadowH','shadowV','shadowBlur','shadowSpread'], 'px')" @change="setDimensionGroupUnit(['shadowH','shadowV','shadowBlur','shadowSpread'], $event.target.value, 'px')"><option v-for="unit in ['px','em','rem']" :key="'grid-shadow-'+unit" :value="unit">{{ unit }}</option></select></div>
									<div class="pb-four-sides mt-1"><template v-for="control in [{key:'shadowH',label:'H'},{key:'shadowV',label:'V'},{key:'shadowBlur',label:'Blur'},{key:'shadowSpread',label:'Spread'}]" :key="control.key"><label class="pb-side-input"><input class="pb-input" type="number" :min="control.key==='shadowBlur' ? 0 : null" :value="dimensionValue(control.key, 'px')" @input="setDimensionValue(control.key, $event, 'px')"><span>{{ control.label }}</span></label></template></div>
									<div class="pb-form-group mt-2"><label class="pb-form-label">Shadow Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.shadowColor"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Shadow Opacity <span class="pb-form-hint">{{ Math.round((node.settings.shadowOpacity ?? 0.3)*100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="node.settings.shadowOpacity"></div>
								</template>
							</div>
						</div>
						<!-- TAB ADVANCED (GRID) -->
						<div v-if="editor.settingsTab==='advanced'" class="pb-tab-content pb-grid-settings__tab">
            <component
                :is="editor.widgetAdvancedControls"
                :node="node"
                :responsive-device="editor.responsiveDevice"
                :elementor-choices="true"
                @responsive-device="editor.setResponsiveDevice"
                @choose-media="editor.chooseMedia(node.settings,$event)"
                @clear-media="editor.clearMedia(node.settings,$event)"
                @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')"
            />
</div>
						</div>
</template>

<script>
export default {
	name: 'GridWidgetSettings',
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	data() { return { expandedGridTrack: -1 }; },
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
		setDimensionValue(key, event, fallbackUnit = 'px', emptyToken = '') {
			const raw = String(event?.target?.value ?? '').trim();
			if (raw === '') { this.node.settings[key] = emptyToken; return; }
			const value = Number(raw);
			if (!Number.isFinite(value)) return;
			this.node.settings[key] = String(value) + this.dimensionUnit(key, fallbackUnit);
		},
		setDimensionUnit(key, unit, fallbackUnit = 'px') {
			const value = this.dimensionValue(key, fallbackUnit);
			if (value === '') return;
			this.node.settings[key] = String(value) + String(unit || fallbackUnit);
		},
		dimensionGroupUnit(keys, fallbackUnit = 'px') {
			for (const key of keys) if (this.dimensionValue(key, fallbackUnit) !== '') return this.dimensionUnit(key, fallbackUnit);
			return fallbackUnit;
		},
		setDimensionGroupUnit(keys, unit, fallbackUnit = 'px') { keys.forEach((key) => this.setDimensionUnit(key, unit, fallbackUnit)); },
		setLinkedDimensionValue(key, keys, event, fallbackUnit = 'px', linked = false) {
			this.setDimensionValue(key, event, fallbackUnit);
			if (!linked) return;
			const value = this.node.settings[key];
			keys.forEach((target) => { if (target !== key) this.node.settings[target] = value; });
		},
		responsiveRadiusKeys() { return ['borderRadiusTL', 'borderRadiusTR', 'borderRadiusBR', 'borderRadiusBL'].map((key) => this.editor.activeResponsiveKey(key)); },
		radiusDimensionValue(key, fallbackUnit = 'px') { return this.dimensionValue(this.editor.activeResponsiveKey(key), fallbackUnit); },
		setRadiusDimensionGroupUnit(unit, fallbackUnit = 'px') { this.node.settings.borderRadius = ''; this.setDimensionGroupUnit(this.responsiveRadiusKeys(), unit, fallbackUnit); },
		setLinkedRadiusDimensionValue(key, event, fallbackUnit = 'px') { this.node.settings.borderRadius = ''; this.setLinkedDimensionValue(this.editor.activeResponsiveKey(key), this.responsiveRadiusKeys(), event, fallbackUnit, this.node.settings.borderRadiusLinked); },
	},
};
</script>
