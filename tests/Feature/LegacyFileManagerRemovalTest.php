<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegacyFileManagerRemovalTest extends TestCase
{
    public function test_legacy_routes_are_removed_while_ckfinder_and_file_manager_v2_remain(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('filemanager'));
        $this->assertNull(Route::getRoutes()->getByName('filemanager.thumbnail'));
        $this->assertNull(Route::getRoutes()->getByName('cms.core.file_manager'));
        $this->assertNull(Route::getRoutes()->getByName('cms.admin.awesome_admin.file_manager'));

        $this->assertNotNull(Route::getRoutes()->getByName('filemanager_v2.index'));
        $this->assertFileExists(public_path('assets/plugins/ckfinder/core/connector/php/connector.php'));
    }

    public function test_legacy_source_files_are_deleted(): void
    {
        $files = [
            app_path('Events/FileManagerBulkProgress.php'),
            app_path('Http/Controllers/Api/v1/FileManagerApiKeyController.php'),
            app_path('Http/Controllers/Api/v1/FileManagerController.php'),
            app_path('Http/Controllers/Api/v1/FileManagerPermissionController.php'),
            app_path('Http/Controllers/Web/Awesome_Admin/Awesome_Admin_File_Manager_Controller.php'),
            app_path('Http/Controllers/Web/File_Manager/File_Manager_Controller.php'),
            app_path('Http/Middleware/FileManagerAuth.php'),
            app_path('Http/Middleware/FmAdminMiddleware.php'),
            app_path('Models/FmApiKey.php'),
            app_path('Models/FmFile.php'),
            app_path('Models/FmFolder.php'),
            app_path('Models/FmQuota.php'),
            app_path('Services/FileManagerService.php'),
            config_path('filemanager.php'),
            base_path('routes/filemanager.php'),
            resource_path('views/awesome_admin/awesome_admin_file_manager.blade.php'),
            resource_path('views/file_manager/file_manager.blade.php'),
            resource_path('views/filemanager/filemanager.blade.php'),
            public_path('assets/js/vue3/filemanager/vueV3-filemanager-2026.js'),
            public_path('assets/js/vue3/filemanager/vueV3-filemanager-embed-206.js'),
        ];

        foreach ($files as $file) {
            $this->assertFileDoesNotExist($file, $file);
        }
    }

    public function test_active_bootstrap_routes_views_and_seeders_have_no_legacy_wiring(): void
    {
        $files = [
            base_path('bootstrap/app.php'),
            base_path('routes/api.php'),
            base_path('routes/experimentalFeaturesWebv2.php'),
            base_path('routes/web.php'),
            base_path('routes/breadcrumbs.php'),
            resource_path('views/awesome_admin/awesome_admin.blade.php'),
            database_path('seeders_new/CustomPermissionsSeeder.php'),
            database_path('seeders_new/MenuParentmenuJsonSeeder.php'),
            database_path('seeders_new/MigrationsSeeder.php'),
        ];

        $forbidden = [
            'FileManagerAuth',
            'FmAdminMiddleware',
            "routes/filemanager.php",
            'FileManagerController',
            'awesome_admin.file_manager',
            'awesome_admin/filemanager',
            "'menu_link' => 'filemanager'",
            'create_fm_api_keys_table',
            'create_file_manager_files_table',
            'create_file_manager_folders_table',
            'create_file_manager_quotas_table',
        ];

        foreach ($files as $file) {
            $source = file_get_contents($file);
            foreach ($forbidden as $needle) {
                $this->assertFalse(str_contains($source, $needle), $file.' contains '.$needle);
            }
        }

        $this->assertFileExists(config_path('filemanager_v2.php'));
        $this->assertFileExists(base_path('routes/filemanager_v2.php'));
    }
}
