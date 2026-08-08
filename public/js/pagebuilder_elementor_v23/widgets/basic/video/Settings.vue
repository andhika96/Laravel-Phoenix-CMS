<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--video">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'style'}" @click="editor.settingsTab = 'style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Video</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Source</label><select class="pb-select" :value="editor.videoCurrentSource(node)" @change="editor.setVideoSourceType(node, $event.target.value)"><option v-for="option in editor.videoSourceOptions" :key="option.value" :value="option.value">{{ option.label }}</option></select></div>
					<div v-if="editor.videoUsesHostedPicker(node)" class="pb-form-group pb-toggle-label-row pb-video-settings__compact-toggle"><label class="pb-form-label mb-0">External URL</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'video-external-url-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings.externalUrl"><label :for="'video-external-url-' + node.id"></label></div><span class="pb-toggle-state">{{ node.settings.externalUrl ? 'On' : 'Off' }}</span></div></div>
					<div v-if="editor.videoLinkField(node)" class="pb-form-group"><label class="pb-form-label">{{ editor.videoLinkField(node).label }}</label><input class="pb-input" v-model="node.settings[editor.videoLinkField(node).key]" :placeholder="editor.videoLinkField(node).placeholder"></div>
					<div v-if="editor.videoUsesHostedPicker(node) && !node.settings.externalUrl" class="pb-form-group"><label class="pb-form-label">Choose Video File</label><MediaField :value="node.settings.fileUrl" kind="video" @choose="editor.chooseMedia(node.settings, 'fileUrl', 'Paste video URL')" @clear="editor.clearMedia(node.settings, 'fileUrl')" /></div>
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>Playback</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Start Time</label><input class="pb-input" type="number" min="0" v-model.number="node.settings.startTime" placeholder="0"><div class="pb-form-note">Specify a start time in seconds.</div></div>
					<div v-if="editor.videoShowsEndTime(node)" class="pb-form-group"><label class="pb-form-label">End Time</label><input class="pb-input" type="number" min="0" v-model.number="node.settings.endTime" placeholder="0"><div class="pb-form-note">Specify an end time in seconds.</div></div>
					<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Aspect Ratio</label><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('video-ratio')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('video-ratio')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice === device.value}" @click.stop="editor.applyResponsiveDevice('video-ratio', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div><select class="pb-select" :value="editor.videoAspectRatioValue(node)" @change="editor.setVideoAspectRatioValue(node, $event.target.value)"><option v-for="option in editor.videoAspectRatioOptions" :key="option.value" :value="option.value">{{ option.label }}</option></select></div>
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>Video Options</summary>
				<div class="pb-collapsible-body">
					<div class="pb-video-settings__option-list"><template v-for="option in editor.videoToggleOptions(node)" :key="option.key"><div class="pb-video-settings__option-row"><div class="pb-form-group pb-toggle-label-row pb-video-settings__toggle-row"><label class="pb-form-label mb-0">{{ option.label }}</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'video-toggle-' + option.key + '-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings[option.key]"><label :for="'video-toggle-' + option.key + '-' + node.id"></label></div><span class="pb-toggle-state">{{ editor.videoToggleStateLabel(option, node.settings[option.key]) }}</span></div></div><div v-if="option.key === 'autoplay'" class="pb-form-note pb-video-settings__toggle-note">Autoplay can still be affected by browser policy, especially when audio is enabled.</div><div v-else-if="option.key === 'privacyMode' && ['youtube','vimeo'].includes(editor.videoCurrentSource(node))" class="pb-form-note pb-video-settings__toggle-note">Privacy mode limits visitor information until the video is played.</div></div></template></div>
					<div v-for="field in editor.videoSelectOptions(node)" :key="field.key" class="pb-form-group"><label class="pb-form-label">{{ field.label }}</label><select class="pb-select" v-model="node.settings[field.key]"><option v-for="option in field.options" :key="option.value" :value="option.value">{{ option.label }}</option></select></div>
					<div v-if="editor.videoShowsPoster(node)" class="pb-form-group"><label class="pb-form-label">Poster</label><MediaField :value="node.settings.poster" @choose="editor.chooseMedia(node.settings, 'poster', 'Paste image URL')" @clear="editor.clearMedia(node.settings, 'poster')" /></div>
				</div>
			</details>

			<details v-if="editor.videoShowsOverlay(node)" class="pb-collapsible" open>
				<summary>Image Overlay</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group pb-toggle-label-row pb-video-settings__compact-toggle"><label class="pb-form-label mb-0">Image Overlay</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'video-image-overlay-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings.imageOverlay"><label :for="'video-image-overlay-' + node.id"></label></div><span class="pb-toggle-state">{{ node.settings.imageOverlay ? 'Show' : 'Hide' }}</span></div></div><div v-if="node.settings.imageOverlay" class="pb-form-group"><MediaField :value="node.settings.overlayImage" @choose="editor.chooseMedia(node.settings, 'overlayImage', 'Paste image URL')" @clear="editor.clearMedia(node.settings, 'overlayImage')" /></div></div>
			</details>
		</div>

		<div v-show="editor.settingsTab === 'style'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Player</summary>
				<div class="pb-collapsible-body">
					<div v-if="editor.videoUsesControlsColor(node)" class="pb-form-group"><label class="pb-form-label">Controls Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.controlsColor" placeholder="#ff3366"></div></div>
					<div v-else class="pb-form-note">This video source does not expose a custom player color.</div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab === 'advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Attributes</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-video"></div></div>
			</details>
		</div>
	</div>
</template>

<script>
const MediaField = {
	props: { value: String, kind: { type: String, default: 'image' } },
	emits: ['choose', 'clear'],
	template: `<div class="pb-bg-media-field pb-video-settings__media-field" :class="{'has-image': !!value}"><div class="pb-bg-media-preview" :style="kind==='image' && value ? {backgroundImage:'url(' + value + ')'} : {}"><button type="button" class="pb-bg-media-center-btn" :title="value ? 'Change Media' : 'Choose Media'" @click="$emit('choose')"><i :class="value ? 'fas fa-pen' : 'fas fa-plus'"></i></button></div><div class="pb-bg-media-actions"><button type="button" class="pb-bg-media-choose" @click="$emit('choose')">Choose {{ kind==='video' ? 'Video' : 'Image' }}</button><button type="button" class="pb-bg-media-remove" :disabled="!value" title="Remove Media" @click="$emit('clear')"><i class="fas fa-trash-alt"></i></button></div></div>`,
};
export default { name: 'VideoWidgetSettings', components: { MediaField }, props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>