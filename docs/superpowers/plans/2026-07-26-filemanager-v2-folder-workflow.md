# File Manager V2 Folder Workflow Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the V2 folder tree collapsible, allow folders to be selected and inspected, calculate aggregate folder statistics only on demand, and provide safe rename flows for files and folders.

**Architecture:** Keep the V2 isolated storage service as the source of filesystem truth. A new read-on-demand details endpoint calculates recursive folder statistics behind the existing root-revision cache invalidation; a rename endpoint owns collision checks and starred-path rewrites. Vue uses the existing unified action-modal lifecycle for rename, while the sidebar derives a collapsible tree from the existing flat folder list.

**Tech Stack:** Laravel 13, Flysystem/Laravel Storage, Vue 3 composition API, Bootstrap Icons, Vite, PHPUnit.

## Global Constraints

- Work only in `D:\Laragon\www\laravel-13-phoenix` and preserve the dirty worktree.
- Never change the legacy File Manager or its storage root.
- V2 stays under `storage/app/public/filemanager_v2/files`; do not expose metadata, cache, or runtime directories.
- Do not calculate recursive sizes while browsing the root, a folder, or the sidebar tree.
- Folder selection must not expose file-only actions: Download, Copy URL, Move, and Star remain file-only.
- All rename/create/delete/move modal submissions use the existing loading, success, failed notice, and fade lifecycle.
- Back up every existing production/test file before editing; build output requires a backup because Vite replaces it.
- Do not commit Graphify output or reset/clean unrelated files.

---

### Task 1: Add storage-level folder details and safe rename behavior

**Files:**
- Modify: `app/Services/FileManagerV2/FileManagerV2Storage.php`
- Modify: `tests/Feature/FileManagerV2/FileManagerV2StorageTest.php`

**Interfaces:**
- Produces `folderDetails(string $storage, string|null $path): array` with `asset`, `fileCount`, `folderCount`, `bytes`, and formatted `size`.
- Produces `rename(string $storage, string|null $path, string $name): array` returning the renamed file or folder asset.
- Both methods reuse `path()`, `fileName()`, `assetForPath()`, and `bust()`.

- [ ] **Step 1: Write failing storage tests**

```php
public function test_it_calculates_folder_totals_only_for_the_requested_folder(): void
{
    $service = app(FileManagerV2Storage::class);
    $service->makeDirectory('local', '', 'Campaigns');
    $service->makeDirectory('local', 'Campaigns', 'Images');
    $service->upload('local', 'Campaigns', UploadedFile::fake()->createWithContent('brief.txt', '1234'));
    $service->upload('local', 'Campaigns/Images', UploadedFile::fake()->createWithContent('logo.txt', '12'));

    $details = $service->folderDetails('local', 'Campaigns');

    $this->assertSame(2, $details['fileCount']);
    $this->assertSame(1, $details['folderCount']);
    $this->assertSame(6, $details['bytes']);
}
```

Add separate tests that rename a starred file and a folder containing a starred descendant, then assert the filesystem path, browse result, and Starred collection all use the new path.

- [ ] **Step 2: Run the focused storage test and confirm RED**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2StorageTest.php`

Expected: failure because `folderDetails()` and `rename()` do not exist.

- [ ] **Step 3: Implement the smallest service behavior**

```php
public function folderDetails(string $storage, string|null $path): array
{
    $path = $this->path($path);
    abort_unless($path !== '' && $this->disk($storage)->directoryExists($path), 404, 'Folder tidak ditemukan.');

    return $this->cache()->remember($this->folderDetailsKey($storage, $path), 60, function () use ($storage, $path): array {
        // listContents($path, true), sum fileSize(), and count descendant files/directories.
    });
}
```

Use the root revision already incremented by `bust()` in `folderDetailsKey()`. Rename must reject the root, invalid names, and an occupied sibling target; a directory rename rewrites every starred path equal to or below the old prefix.

- [ ] **Step 4: Run the focused storage test and confirm GREEN**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2StorageTest.php`

Expected: all existing storage behavior plus the three new aggregate/rename cases pass.

### Task 2: Expose details and rename through the isolated V2 API

**Files:**
- Modify: `routes/filemanager_v2.php`
- Modify: `app/Http/Controllers/Api/V2/FileManagerV2Controller.php`
- Modify: `tests/Feature/FileManagerV2/FileManagerV2StorageTest.php`

**Interfaces:**
- `GET /api/v2/file-manager/assets/details?storage={storage}&path={folder}` calls `folderDetails()`.
- `PATCH /api/v2/file-manager/assets` accepts `storage`, `path`, and `name`, then calls `rename()`.

- [ ] **Step 1: Extend the failing contract test**

```php
$this->withoutMiddleware();
$this->getJson('/api/v2/file-manager/assets/details?storage=local&path=Campaigns')
    ->assertOk()
    ->assertJsonPath('data.fileCount', 2);
```

Add a PATCH assertion that returns the renamed asset path and a 422 assertion for an occupied sibling name.

- [ ] **Step 2: Run the focused test and confirm RED**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2StorageTest.php`

Expected: route/controller behavior is missing.

- [ ] **Step 3: Add validation and route bindings**

```php
Route::get('/assets/details', [FileManagerV2Controller::class, 'details'])->name('assets.details');
Route::patch('/assets', [FileManagerV2Controller::class, 'rename'])->name('assets.rename');
```

Validate storage/path/name using the same length limits as create-folder. Keep these routes inside the V2 authenticated group.

- [ ] **Step 4: Run the focused test and confirm GREEN**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2StorageTest.php`

Expected: route, validation, totals, and rename tests pass.

### Task 3: Make folder cards selectable and render folder details safely

**Files:**
- Modify: `resources/js/filemanager_v2/data/live.js`
- Modify: `resources/js/filemanager_v2/App.vue`
- Modify: `resources/js/filemanager_v2/components/AssetCard.vue`
- Modify: `resources/js/filemanager_v2/components/DetailsDrawer.vue`
- Modify: `tests/Feature/FileManagerV2/FileManagerV2FolderPresentationTest.php`

**Interfaces:**
- `loadFolderDetails(storage, path)` and `renameAsset(storage, path, name)` call the new V2 API.
- `AssetCard` emits `open-folder` on double-click; one click remains selection for both files and folders.
- `DetailsDrawer` receives a folder aggregate when `asset.type === 'folder'` and emits `rename` and `open-folder`.

- [ ] **Step 1: Add failing static contract assertions**

```php
