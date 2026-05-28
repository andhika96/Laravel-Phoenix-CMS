<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuParentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_parent')->insert([
        [
            'id' => 1,
            'is_parent' => 'false',
            'module' => 'manage_aboutus',
            'parent_name' => 'Manage About Us',
            'parent_code' => 'manage_aboutus',
            'icon' => '<i class=\\"fad fa-address-card fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 2,
            'is_parent' => 'false',
            'module' => 'manage_contactus',
            'parent_name' => 'Manage Contact Us',
            'parent_code' => 'manage_contactus',
            'icon' => '<i class=\\"fad fa-phone fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 3,
            'is_parent' => 'false',
            'module' => 'manage_event',
            'parent_name' => 'Manage Event',
            'parent_code' => 'manage_event',
            'icon' => '<i class=\\"fad fa-newspaper fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 4,
            'is_parent' => 'false',
            'module' => 'manage_gallery',
            'parent_name' => 'Manage Gallery',
            'parent_code' => 'manage_gallery',
            'icon' => '<i class=\\"fad fa-phone fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 5,
            'is_parent' => 'false',
            'module' => 'manage_news',
            'parent_name' => 'Manage News',
            'parent_code' => 'manage_news',
            'icon' => '<i class=\\"fad fa-newspaper fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 6,
            'is_parent' => 'false',
            'module' => 'manage_portofolio',
            'parent_name' => 'Manage Portofolio',
            'parent_code' => 'manage_portofolio',
            'icon' => '<i class=\\"fad fa-newspaper fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 7,
            'is_parent' => 'false',
            'module' => 'manage_promotion',
            'parent_name' => 'Manage Promotion',
            'parent_code' => 'manage_promotion',
            'icon' => '<i class=\\"fad fa-newspaper fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 8,
            'is_parent' => 'false',
            'module' => 'manage_appearance',
            'parent_name' => 'Manage Appearance',
            'parent_code' => 'manage_appearance',
            'icon' => '<i class=\\"fad fa-palette fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 9,
            'is_parent' => 'false',
            'module' => 'manage_section_content',
            'parent_name' => 'Manage Section Content',
            'parent_code' => 'manage_section_content',
            'icon' => '<i class=\\"fad fa-columns fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 10,
            'is_parent' => 'false',
            'module' => 'manage_dropdown',
            'parent_name' => 'Manage Dropdown Menu',
            'parent_code' => 'manage_dropdown',
            'icon' => '<i class=\\"fad fa-newspaper fa-fw me-2\\"></i>',
            'roles' => '99'
        ],
        [
            'id' => 11,
            'is_parent' => 'false',
            'module' => 'manage_header',
            'parent_name' => 'Manage Header Menu',
            'parent_code' => 'manage_header',
            'icon' => '<i class=\\"fad fa-newspaper fa-fw me-2\\"></i>',
            'roles' => '99'
        ]
        ]);
    }
}
