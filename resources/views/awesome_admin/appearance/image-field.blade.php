<div class="ph-auth-image-field">
	<input type="file" class="form-control font-size-inherit" :id="fieldId(field.key)" accept="image/png,image/jpeg,image/webp"
		:multiple="field.type === 'images'" :disabled="imageState(field.key).uploading || imageState(field.key).removing" :aria-invalid="Boolean(imageState(field.key).error)"
		@change="uploadImages($event, field)">
	<p class="ph-auth-field-help mt-2 mb-0">{{ t('PNG, JPG or WebP. Maximum 10 MB per image.') }}</p>
	<div v-if="imageState(field.key).uploading" class="mt-3" role="status">
		<div class="d-flex justify-content-between gap-2 small mb-2">
			<span class="text-break">@{{ imageState(field.key).name }}</span><span>@{{ imageState(field.key).progress }}%</span>
		</div>
		<div class="progress ph-auth-upload-progress" role="progressbar" :aria-label="field.label" :aria-valuenow="imageState(field.key).progress" aria-valuemin="0" aria-valuemax="100">
			<div class="progress-bar" :style="{width: imageState(field.key).progress+'%'}"></div>
		</div>
		<small class="ph-auth-field-help">@{{ imageState(field.key).progress === 99 ? 'Processing image…' : 'Uploading…' }}</small>
	</div>
	<div v-if="!imagePaths(field.key).length && !imageState(field.key).uploading" class="ph-auth-image-empty mt-3">
		<i class="far fa-image fa-2x mb-2" aria-hidden="true"></i><span>{{ t('No image') }}</span><small>{{ t('Gray background') }}</small>
	</div>
	<div v-for="path in imagePaths(field.key)" :key="path" class="ph-auth-uploaded-image mt-3">
		<img :src="imageUrl(path)" :alt="field.label" class="ph-auth-image-thumb">
		<div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mt-2">
			<div class="ph-auth-image-name small"><span class="text-break">@{{ imageName(path) }}</span><small class="d-block" :class="isDefaultImage(path) ? 'text-body-secondary' : 'text-success'">@{{ isDefaultImage(path) ? 'Default image' : '100% · Upload complete' }}</small></div>
			<div class="d-flex gap-2">
				<button type="button" class="btn btn-outline-secondary ph-auth-image-action" :aria-label="'Preview '+field.label" :title="'Preview '+field.label"
					:disabled="imageState(field.key).uploading || imageState(field.key).removing" @click="showImage(path, field.label)"><i class="far fa-eye" aria-hidden="true"></i></button>
				<button type="button" class="btn btn-outline-danger ph-auth-image-action" :aria-label="'Remove '+field.label" :title="'Remove '+field.label"
					:disabled="imageState(field.key).uploading || imageState(field.key).removing" @click="removeImage(path, field)"><i class="far fa-trash-alt" aria-hidden="true"></i></button>
			</div>
		</div>
	</div>
</div>
