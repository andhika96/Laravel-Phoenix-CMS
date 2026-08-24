<h6 class="card-title border-bottom pb-3 mb-3">{{ t('Thumbnail') }}</h6>

<input type="hidden" name="remove_thumbnail" :value="articleThumbnailRemove ? '1' : '0'">
<input type="hidden" name="thumbnail_source" :value="thumbnailSource">
<input type="hidden" name="thumbnail_ckfinder_url" :value="thumbnailCkfinderUrl">

<div class="btn-group w-100 mb-3" role="group" aria-label="{{ t('Thumbnail source') }}">
	<button type="button" class="btn" :class="thumbnailSource === 'upload' ? 'ph-btn-theme' : 'btn-outline-secondary'" :aria-pressed="thumbnailSource === 'upload'" @click="setArticleThumbnailSource('upload')">{{ t('Upload file') }}</button>
	<button type="button" class="btn" :class="thumbnailSource === 'ckfinder' ? 'ph-btn-theme' : 'btn-outline-secondary'" :aria-pressed="thumbnailSource === 'ckfinder'" @click="setArticleThumbnailSource('ckfinder')">{{ t('CKFinder library') }}</button>
</div>

<div v-if="thumbnailSource === 'upload'" class="input-group rounded mb-3">
	<input ref="thumbnailInput" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" class="form-control font-size-inherit" v-on:focus="focusForm($event)" v-on:blur="blurForm" @change="previewArticleThumbnail">
	<button v-if="showButtonRemoveImage" type="button" class="btn btn-outline-danger" @click="removeArticleThumbnailPreview" aria-label="{{ t('Remove thumbnail') }}"><i class="fas fa-trash-alt fa-fw"></i></button>
</div>

<div v-else class="mb-3">
	<button type="button" class="btn ph-btn-theme-outline w-100 mb-2" @click="openArticleThumbnailCkfinder"><i class="fas fa-folder-open fa-fw me-1"></i>{{ t('Browse CKFinder') }}</button>
	<div v-if="thumbnailCkfinderUrl" class="input-group rounded">
		<input :value="thumbnailCkfinderLabel" class="form-control font-size-inherit" readonly>
		<button v-if="showButtonRemoveImage" type="button" class="btn btn-outline-danger" @click="removeArticleThumbnailPreview" aria-label="{{ t('Remove thumbnail') }}"><i class="fas fa-trash-alt fa-fw"></i></button>
	</div>
</div>

<div class="position-relative text-center d-flex justify-content-center" style="width:auto;height:350px;background-image:linear-gradient(45deg,#c3c4c7 25%,transparent 25%,transparent 75%,#c3c4c7 75%,#c3c4c7),linear-gradient(45deg,#c3c4c7 25%,transparent 25%,transparent 75%,#c3c4c7 75%,#c3c4c7);background-position:0 0,10px 10px;background-size:20px 20px;">
	<img v-if="imageEncoded" :src="imageEncoded" id="img-preview" alt="{{ t('Article thumbnail preview') }}" class="img-fluid object-fit-contain">
	<span v-else class="align-self-center text-muted small">{{ t('No thumbnail selected') }}</span>
</div>
