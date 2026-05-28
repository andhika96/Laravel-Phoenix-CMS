<?php

/**
 * ─────────────────────────────────────────────────────────────────────────────
 *  Arunika CMS — File Manager Configuration
 *  File: config/filemanager.php
 * ─────────────────────────────────────────────────────────────────────────────
 */

return [

	// ── Storage ────────────────────────────────────────────────────────────────

	'default_disk' => env('FM_DEFAULT_DISK', 's3_r2', 's3_idcloudhost'),

	/**
	 * Root folder di dalam disk (path RELATIF dari root disk).
	 * BENAR  : uploads  |  media/files  |  (kosong = root disk)
	 * SALAH  : /storage/app/public/uploads  |  storage/app/public/uploads
	 */
	'root_path' => env('FM_ROOT_PATH', ''),

	/**
	 * Disk yang tersedia di File Manager.
	 * Isi dengan nama disk yang ada di config/filesystems.php.
	 * Bisa tambah disk S3 sebanyak apapun.
	 *
	 * Format: ['disk_key' => 'Label Tampilan di UI']
	 *
	 * Contoh dengan 2 S3:
	 *   'available_disks' => [
	 *       'public'    => 'Local/Public',
	 *       's3_aws'    => 'Amazon S3 (AWS)',
	 *       's3_do'     => 'DigitalOcean Spaces',
	 *   ],
	 */
	
	// Tambahkan disk S3 lain di sini, sesuai nama disk di config/filesystems.php
	'available_disks' => 
	[
		'public'  			=> 'Local/Public',
		's3_r2'   			=> 'Cloudflare R2',
		's3_idcloudhost'	=> 'IDCloudHost S3',
		// 's3'      => 'Amazon S3',
		// 's3_do'   => 'DigitalOcean Spaces',
		// 's3_b2'   => 'Backblaze B2',
	],

	/**
	 * Disk mana yang berjenis S3 (untuk validasi koneksi).
	 * Semua disk yang namanya ada di array ini akan dicek koneksi S3-nya.
	 */
	's3_disks' => ['s3', 's3_do', 's3_r2', 's3_idcloudhost', 's3_aws', 's3_minio'],

	/**
	 * Disk yang mendukung per-object ACL (S3 ACL: public-read, private, dll).
	 * Cloudflare R2 TIDAK support per-object ACL → jangan masukkan 's3_r2' di sini.
	 * AWS S3, IDCloudhost S3, DigitalOcean Spaces = support ACL.
	 *
	 * Jika disk aktif tidak ada di list ini, UI akan menampilkan notifikasi
	 * bahwa fitur ACL tidak tersedia, dan tombol permission di-disable.
	 */
	'acl_supported_disks' => ['s3', 's3_aws', 's3_do', 's3_idcloudhost'],

	// ── Quota ──────────────────────────────────────────────────────────────────

	'default_max_storage'   => env('FM_DEFAULT_MAX_STORAGE', 10737418240),   // 10 GB
	'default_max_file_size' => env('FM_DEFAULT_MAX_FILE_SIZE', 104857600),   // 100 MB

	// ── Upload ─────────────────────────────────────────────────────────────────

	'allowed_mime_types' => [],

	'forbidden_extensions' => [
		'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
		'exe', 'sh', 'bat', 'cmd', 'ps1',
		'js',  'vbs', 'wsf',
	],

	// ── Auth & Access ──────────────────────────────────────────────────────────

	'allow_api_key_access' => env('FM_ALLOW_API_KEY', true),
	'admin_roles'          => ['Super Admin', 'Administrator'],

	// ── Image Editor ───────────────────────────────────────────────────────────

	/**
	 * Aktifkan image editor (crop, resize, rotate, flip, filter, dll).
	 * Membutuhkan package intervention/image v3.
	 */
	'image_editor_enabled' => env('FM_IMAGE_EDITOR', true),

	/**
	 * Format output hasil edit gambar.
	 * Nilai: 'original' (ikut format asli) | 'jpg' | 'png' | 'webp'
	 */
	'image_editor_output_format' => env('FM_IMAGE_EDITOR_FORMAT', 'original'),

	/**
	 * Kualitas kompresi hasil edit (1-100, berlaku untuk jpg & webp).
	 */
	'image_editor_quality' => env('FM_IMAGE_EDITOR_QUALITY', 90),

	/**
	 * Simpan hasil edit sebagai file baru (tidak overwrite asli).
	 * true  = simpan sebagai file_edited.jpg
	 * false = overwrite file asli
	 */
	'image_editor_save_as_new' => env('FM_IMAGE_EDITOR_SAVE_NEW', true),

	// ── Thumbnail ──────────────────────────────────────────────────────────────

	'auto_thumbnail'   => env('FM_AUTO_THUMBNAIL', false),
	'thumbnail_width'  => env('FM_THUMBNAIL_WIDTH', 300),
	'thumbnail_height' => env('FM_THUMBNAIL_HEIGHT', 300),
	'thumbnail_folder' => env('FM_THUMBNAIL_FOLDER', '_thumbs'),

	// ── UI ─────────────────────────────────────────────────────────────────────

	'items_per_page'  => 100,
	'preview_max_size'=> 5242880,  // 5 MB
];
