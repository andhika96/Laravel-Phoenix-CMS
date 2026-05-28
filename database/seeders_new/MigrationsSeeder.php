<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('migrations')->insert([
        [
            'id' => 1,
            'migration' => '0001_01_01_000000_create_users_table',
            'batch' => 1
        ],
        [
            'id' => 2,
            'migration' => '0001_01_01_000001_create_cache_table',
            'batch' => 1
        ],
        [
            'id' => 3,
            'migration' => '0001_01_01_000002_create_jobs_table',
            'batch' => 1
        ],
        [
            'id' => 4,
            'migration' => '2024_05_10_022940_create_permission_tables',
            'batch' => 2
        ],
        [
            'id' => 5,
            'migration' => '2024_05_10_094534_create_personal_access_tokens_table',
            'batch' => 3
        ],
        [
            'id' => 6,
            'migration' => '2024_05_10_094727_create_oauth_auth_codes_table',
            'batch' => 3
        ],
        [
            'id' => 7,
            'migration' => '2024_05_10_094728_create_oauth_access_tokens_table',
            'batch' => 3
        ],
        [
            'id' => 8,
            'migration' => '2024_05_10_094729_create_oauth_refresh_tokens_table',
            'batch' => 3
        ],
        [
            'id' => 9,
            'migration' => '2024_05_10_094730_create_oauth_clients_table',
            'batch' => 3
        ],
        [
            'id' => 10,
            'migration' => '2024_05_10_094731_create_oauth_personal_access_clients_table',
            'batch' => 3
        ],
        [
            'id' => 11,
            'migration' => '2024_05_14_075652_create_testings_table',
            'batch' => 3
        ],
        [
            'id' => 12,
            'migration' => '2024_05_17_045334_create_oauth_auth_codes_table',
            'batch' => 4
        ],
        [
            'id' => 13,
            'migration' => '2024_05_17_045335_create_oauth_access_tokens_table',
            'batch' => 4
        ],
        [
            'id' => 14,
            'migration' => '2024_05_17_045336_create_oauth_refresh_tokens_table',
            'batch' => 4
        ],
        [
            'id' => 15,
            'migration' => '2024_05_17_045337_create_oauth_clients_table',
            'batch' => 4
        ],
        [
            'id' => 16,
            'migration' => '2024_05_17_045338_create_oauth_personal_access_clients_table',
            'batch' => 4
        ],
        [
            'id' => 17,
            'migration' => '2024_05_17_045506_create_oauth_auth_codes_table',
            'batch' => 5
        ],
        [
            'id' => 18,
            'migration' => '2024_05_17_045507_create_oauth_access_tokens_table',
            'batch' => 5
        ],
        [
            'id' => 19,
            'migration' => '2024_05_17_045508_create_oauth_refresh_tokens_table',
            'batch' => 5
        ],
        [
            'id' => 20,
            'migration' => '2024_05_17_045509_create_oauth_clients_table',
            'batch' => 5
        ],
        [
            'id' => 21,
            'migration' => '2024_05_17_045510_create_oauth_personal_access_clients_table',
            'batch' => 5
        ],
        [
            'id' => 22,
            'migration' => '2025_08_14_151502_create_account_login_history_table',
            'batch' => 0
        ],
        [
            'id' => 23,
            'migration' => '2025_08_14_151502_create_account_status_table',
            'batch' => 0
        ],
        [
            'id' => 24,
            'migration' => '2025_08_14_151502_create_accounts_table',
            'batch' => 0
        ],
        [
            'id' => 25,
            'migration' => '2025_08_14_151502_create_authentication_log_table',
            'batch' => 0
        ],
        [
            'id' => 26,
            'migration' => '2025_08_14_151502_create_blog_article_table',
            'batch' => 0
        ],
        [
            'id' => 27,
            'migration' => '2025_08_14_151502_create_blog_category_table',
            'batch' => 0
        ],
        [
            'id' => 28,
            'migration' => '2025_08_14_151502_create_blog_subcategory_table',
            'batch' => 0
        ],
        [
            'id' => 29,
            'migration' => '2025_08_14_151502_create_cache_table',
            'batch' => 0
        ],
        [
            'id' => 30,
            'migration' => '2025_08_14_151502_create_cache_locks_table',
            'batch' => 0
        ],
        [
            'id' => 31,
            'migration' => '2025_08_14_151502_create_custom_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 32,
            'migration' => '2025_08_14_151502_create_failed_jobs_table',
            'batch' => 0
        ],
        [
            'id' => 33,
            'migration' => '2025_08_14_151502_create_job_batches_table',
            'batch' => 0
        ],
        [
            'id' => 34,
            'migration' => '2025_08_14_151502_create_jobs_table',
            'batch' => 0
        ],
        [
            'id' => 35,
            'migration' => '2025_08_14_151502_create_language_table',
            'batch' => 0
        ],
        [
            'id' => 36,
            'migration' => '2025_08_14_151502_create_menu_table',
            'batch' => 0
        ],
        [
            'id' => 37,
            'migration' => '2025_08_14_151502_create_menu_categorymenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 38,
            'migration' => '2025_08_14_151502_create_menu_parent_table',
            'batch' => 0
        ],
        [
            'id' => 39,
            'migration' => '2025_08_14_151502_create_menu_parentmenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 40,
            'migration' => '2025_08_14_151502_create_menu_submenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 41,
            'migration' => '2025_08_14_151502_create_model_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 42,
            'migration' => '2025_08_14_151502_create_model_has_roles_table',
            'batch' => 0
        ],
        [
            'id' => 43,
            'migration' => '2025_08_14_151502_create_notifications_table',
            'batch' => 0
        ],
        [
            'id' => 44,
            'migration' => '2025_08_14_151502_create_oauth_access_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 45,
            'migration' => '2025_08_14_151502_create_oauth_auth_codes_table',
            'batch' => 0
        ],
        [
            'id' => 46,
            'migration' => '2025_08_14_151502_create_oauth_clients_table',
            'batch' => 0
        ],
        [
            'id' => 47,
            'migration' => '2025_08_14_151502_create_oauth_personal_access_clients_table',
            'batch' => 0
        ],
        [
            'id' => 48,
            'migration' => '2025_08_14_151502_create_oauth_refresh_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 49,
            'migration' => '2025_08_14_151502_create_oil_palm_tree_v3_table',
            'batch' => 0
        ],
        [
            'id' => 50,
            'migration' => '2025_08_14_151502_create_page_theme_settings_table',
            'batch' => 0
        ],
        [
            'id' => 51,
            'migration' => '2025_08_14_151502_create_page_themes_table',
            'batch' => 0
        ],
        [
            'id' => 52,
            'migration' => '2025_08_14_151502_create_password_reset_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 53,
            'migration' => '2025_08_14_151502_create_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 54,
            'migration' => '2025_08_14_151502_create_personal_access_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 55,
            'migration' => '2025_08_14_151502_create_role_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 56,
            'migration' => '2025_08_14_151502_create_roles_table',
            'batch' => 0
        ],
        [
            'id' => 57,
            'migration' => '2025_08_14_151502_create_sessions_table',
            'batch' => 0
        ],
        [
            'id' => 58,
            'migration' => '2025_08_14_151502_create_site_config_table',
            'batch' => 0
        ],
        [
            'id' => 59,
            'migration' => '2025_08_14_151502_create_smtp_service_table',
            'batch' => 0
        ],
        [
            'id' => 60,
            'migration' => '2025_08_14_151502_create_smtp_settings_table',
            'batch' => 0
        ],
        [
            'id' => 61,
            'migration' => '2025_08_14_151502_create_testings_table',
            'batch' => 0
        ],
        [
            'id' => 62,
            'migration' => '2025_08_14_151502_create_theme_settings_table',
            'batch' => 0
        ],
        [
            'id' => 63,
            'migration' => '2025_08_14_151502_create_themes_table',
            'batch' => 0
        ],
        [
            'id' => 64,
            'migration' => '2025_08_14_151502_create_user_information_table',
            'batch' => 0
        ],
        [
            'id' => 65,
            'migration' => '2025_08_14_151502_create_users_table',
            'batch' => 0
        ],
        [
            'id' => 66,
            'migration' => '2025_08_14_151505_add_foreign_keys_to_model_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 67,
            'migration' => '2025_08_14_151505_add_foreign_keys_to_model_has_roles_table',
            'batch' => 0
        ],
        [
            'id' => 68,
            'migration' => '2025_08_14_151505_add_foreign_keys_to_role_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 69,
            'migration' => '2025_08_14_152746_create_account_login_history_table',
            'batch' => 0
        ],
        [
            'id' => 70,
            'migration' => '2025_08_14_152746_create_account_status_table',
            'batch' => 0
        ],
        [
            'id' => 71,
            'migration' => '2025_08_14_152746_create_accounts_table',
            'batch' => 0
        ],
        [
            'id' => 72,
            'migration' => '2025_08_14_152746_create_authentication_log_table',
            'batch' => 0
        ],
        [
            'id' => 73,
            'migration' => '2025_08_14_152746_create_blog_article_table',
            'batch' => 0
        ],
        [
            'id' => 74,
            'migration' => '2025_08_14_152746_create_blog_category_table',
            'batch' => 0
        ],
        [
            'id' => 75,
            'migration' => '2025_08_14_152746_create_blog_subcategory_table',
            'batch' => 0
        ],
        [
            'id' => 76,
            'migration' => '2025_08_14_152746_create_cache_table',
            'batch' => 0
        ],
        [
            'id' => 77,
            'migration' => '2025_08_14_152746_create_cache_locks_table',
            'batch' => 0
        ],
        [
            'id' => 78,
            'migration' => '2025_08_14_152746_create_custom_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 79,
            'migration' => '2025_08_14_152746_create_failed_jobs_table',
            'batch' => 0
        ],
        [
            'id' => 80,
            'migration' => '2025_08_14_152746_create_job_batches_table',
            'batch' => 0
        ],
        [
            'id' => 81,
            'migration' => '2025_08_14_152746_create_jobs_table',
            'batch' => 0
        ],
        [
            'id' => 82,
            'migration' => '2025_08_14_152746_create_language_table',
            'batch' => 0
        ],
        [
            'id' => 83,
            'migration' => '2025_08_14_152746_create_menu_table',
            'batch' => 0
        ],
        [
            'id' => 84,
            'migration' => '2025_08_14_152746_create_menu_categorymenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 85,
            'migration' => '2025_08_14_152746_create_menu_parent_table',
            'batch' => 0
        ],
        [
            'id' => 86,
            'migration' => '2025_08_14_152746_create_menu_parentmenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 87,
            'migration' => '2025_08_14_152746_create_menu_submenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 88,
            'migration' => '2025_08_14_152746_create_model_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 89,
            'migration' => '2025_08_14_152746_create_model_has_roles_table',
            'batch' => 0
        ],
        [
            'id' => 90,
            'migration' => '2025_08_14_152746_create_notifications_table',
            'batch' => 0
        ],
        [
            'id' => 91,
            'migration' => '2025_08_14_152746_create_oauth_access_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 92,
            'migration' => '2025_08_14_152746_create_oauth_auth_codes_table',
            'batch' => 0
        ],
        [
            'id' => 93,
            'migration' => '2025_08_14_152746_create_oauth_clients_table',
            'batch' => 0
        ],
        [
            'id' => 94,
            'migration' => '2025_08_14_152746_create_oauth_personal_access_clients_table',
            'batch' => 0
        ],
        [
            'id' => 95,
            'migration' => '2025_08_14_152746_create_oauth_refresh_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 96,
            'migration' => '2025_08_14_152746_create_oil_palm_tree_v3_table',
            'batch' => 0
        ],
        [
            'id' => 97,
            'migration' => '2025_08_14_152746_create_page_theme_settings_table',
            'batch' => 0
        ],
        [
            'id' => 98,
            'migration' => '2025_08_14_152746_create_page_themes_table',
            'batch' => 0
        ],
        [
            'id' => 99,
            'migration' => '2025_08_14_152746_create_password_reset_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 100,
            'migration' => '2025_08_14_152746_create_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 101,
            'migration' => '2025_08_14_152746_create_personal_access_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 102,
            'migration' => '2025_08_14_152746_create_role_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 103,
            'migration' => '2025_08_14_152746_create_roles_table',
            'batch' => 0
        ],
        [
            'id' => 104,
            'migration' => '2025_08_14_152746_create_sessions_table',
            'batch' => 0
        ],
        [
            'id' => 105,
            'migration' => '2025_08_14_152746_create_site_config_table',
            'batch' => 0
        ],
        [
            'id' => 106,
            'migration' => '2025_08_14_152746_create_smtp_service_table',
            'batch' => 0
        ],
        [
            'id' => 107,
            'migration' => '2025_08_14_152746_create_smtp_settings_table',
            'batch' => 0
        ],
        [
            'id' => 108,
            'migration' => '2025_08_14_152746_create_testings_table',
            'batch' => 0
        ],
        [
            'id' => 109,
            'migration' => '2025_08_14_152746_create_theme_settings_table',
            'batch' => 0
        ],
        [
            'id' => 110,
            'migration' => '2025_08_14_152746_create_themes_table',
            'batch' => 0
        ],
        [
            'id' => 111,
            'migration' => '2025_08_14_152746_create_user_information_table',
            'batch' => 0
        ],
        [
            'id' => 112,
            'migration' => '2025_08_14_152746_create_users_table',
            'batch' => 0
        ],
        [
            'id' => 113,
            'migration' => '2025_08_14_152749_add_foreign_keys_to_model_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 114,
            'migration' => '2025_08_14_152749_add_foreign_keys_to_model_has_roles_table',
            'batch' => 0
        ],
        [
            'id' => 115,
            'migration' => '2025_08_14_152749_add_foreign_keys_to_role_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 116,
            'migration' => '2025_08_15_091631_create_account_login_history_table',
            'batch' => 0
        ],
        [
            'id' => 117,
            'migration' => '2025_08_15_091631_create_account_status_table',
            'batch' => 0
        ],
        [
            'id' => 118,
            'migration' => '2025_08_15_091631_create_accounts_table',
            'batch' => 0
        ],
        [
            'id' => 119,
            'migration' => '2025_08_15_091631_create_authentication_log_table',
            'batch' => 0
        ],
        [
            'id' => 120,
            'migration' => '2025_08_15_091631_create_blog_article_table',
            'batch' => 0
        ],
        [
            'id' => 121,
            'migration' => '2025_08_15_091631_create_blog_category_table',
            'batch' => 0
        ],
        [
            'id' => 122,
            'migration' => '2025_08_15_091631_create_blog_subcategory_table',
            'batch' => 0
        ],
        [
            'id' => 123,
            'migration' => '2025_08_15_091631_create_cache_table',
            'batch' => 0
        ],
        [
            'id' => 124,
            'migration' => '2025_08_15_091631_create_cache_locks_table',
            'batch' => 0
        ],
        [
            'id' => 125,
            'migration' => '2025_08_15_091631_create_custom_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 126,
            'migration' => '2025_08_15_091631_create_failed_jobs_table',
            'batch' => 0
        ],
        [
            'id' => 127,
            'migration' => '2025_08_15_091631_create_job_batches_table',
            'batch' => 0
        ],
        [
            'id' => 128,
            'migration' => '2025_08_15_091631_create_jobs_table',
            'batch' => 0
        ],
        [
            'id' => 129,
            'migration' => '2025_08_15_091631_create_language_table',
            'batch' => 0
        ],
        [
            'id' => 130,
            'migration' => '2025_08_15_091631_create_menu_table',
            'batch' => 0
        ],
        [
            'id' => 131,
            'migration' => '2025_08_15_091631_create_menu_categorymenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 132,
            'migration' => '2025_08_15_091631_create_menu_parent_table',
            'batch' => 0
        ],
        [
            'id' => 133,
            'migration' => '2025_08_15_091631_create_menu_parentmenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 134,
            'migration' => '2025_08_15_091631_create_menu_submenu_json_table',
            'batch' => 0
        ],
        [
            'id' => 135,
            'migration' => '2025_08_15_091631_create_model_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 136,
            'migration' => '2025_08_15_091631_create_model_has_roles_table',
            'batch' => 0
        ],
        [
            'id' => 137,
            'migration' => '2025_08_15_091631_create_notifications_table',
            'batch' => 0
        ],
        [
            'id' => 138,
            'migration' => '2025_08_15_091631_create_oauth_access_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 139,
            'migration' => '2025_08_15_091631_create_oauth_auth_codes_table',
            'batch' => 0
        ],
        [
            'id' => 140,
            'migration' => '2025_08_15_091631_create_oauth_clients_table',
            'batch' => 0
        ],
        [
            'id' => 141,
            'migration' => '2025_08_15_091631_create_oauth_personal_access_clients_table',
            'batch' => 0
        ],
        [
            'id' => 142,
            'migration' => '2025_08_15_091631_create_oauth_refresh_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 143,
            'migration' => '2025_08_15_091631_create_oil_palm_tree_v3_table',
            'batch' => 0
        ],
        [
            'id' => 144,
            'migration' => '2025_08_15_091631_create_page_theme_settings_table',
            'batch' => 0
        ],
        [
            'id' => 145,
            'migration' => '2025_08_15_091631_create_page_themes_table',
            'batch' => 0
        ],
        [
            'id' => 146,
            'migration' => '2025_08_15_091631_create_password_reset_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 147,
            'migration' => '2025_08_15_091631_create_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 148,
            'migration' => '2025_08_15_091631_create_personal_access_tokens_table',
            'batch' => 0
        ],
        [
            'id' => 149,
            'migration' => '2025_08_15_091631_create_role_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 150,
            'migration' => '2025_08_15_091631_create_roles_table',
            'batch' => 0
        ],
        [
            'id' => 151,
            'migration' => '2025_08_15_091631_create_sessions_table',
            'batch' => 0
        ],
        [
            'id' => 152,
            'migration' => '2025_08_15_091631_create_site_config_table',
            'batch' => 0
        ],
        [
            'id' => 153,
            'migration' => '2025_08_15_091631_create_smtp_service_table',
            'batch' => 0
        ],
        [
            'id' => 154,
            'migration' => '2025_08_15_091631_create_smtp_settings_table',
            'batch' => 0
        ],
        [
            'id' => 155,
            'migration' => '2025_08_15_091631_create_testings_table',
            'batch' => 0
        ],
        [
            'id' => 156,
            'migration' => '2025_08_15_091631_create_theme_settings_table',
            'batch' => 0
        ],
        [
            'id' => 157,
            'migration' => '2025_08_15_091631_create_themes_table',
            'batch' => 0
        ],
        [
            'id' => 158,
            'migration' => '2025_08_15_091631_create_user_information_table',
            'batch' => 0
        ],
        [
            'id' => 159,
            'migration' => '2025_08_15_091631_create_users_table',
            'batch' => 0
        ],
        [
            'id' => 160,
            'migration' => '2025_08_15_091634_add_foreign_keys_to_model_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 161,
            'migration' => '2025_08_15_091634_add_foreign_keys_to_model_has_roles_table',
            'batch' => 0
        ],
        [
            'id' => 162,
            'migration' => '2025_08_15_091634_add_foreign_keys_to_role_has_permissions_table',
            'batch' => 0
        ],
        [
            'id' => 163,
            'migration' => '2025_07_30_093735_create_authentication_log_table',
            'batch' => 6
        ],
        [
            'id' => 164,
            'migration' => '2025_11_04_140126_create_credits_table',
            'batch' => 6
        ],
        [
            'id' => 165,
            'migration' => '2025_01_01_000004_create_fm_api_keys_table',
            'batch' => 7
        ],
        [
            'id' => 166,
            'migration' => '2025_01_01_000001_create_file_manager_folders_table',
            'batch' => 8
        ],
        [
            'id' => 167,
            'migration' => '2025_01_01_000003_create_file_manager_quotas_table',
            'batch' => 9
        ],
        [
            'id' => 168,
            'migration' => '2025_01_01_000002_create_file_manager_files_table',
            'batch' => 10
        ]
        ]);
    }
}
