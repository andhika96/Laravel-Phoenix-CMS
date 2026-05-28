<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoverImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cover_image')->insert([
        [
            'id' => 1,
            'user_id' => 1,
            'uri' => 'homepage',
            'cover_type' => 'background_image',
            'cover_page_name' => 'Homepage',
            'cover_bgimage_vars' => '[{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgb(55, 131, 194)\\",\\"background_size\\":\\"boi_size\\",\\"desktop_content_position\\":\\"center-left\\",\\"mobile_content_position\\":\\"center-center\\",\\"title\\":\\"Awal Cerita <br\\\\/> Keluarga Anda\\",\\"description\\":null,\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 1\\",\\"link\\":\\"https:\\\\/\\\\/www.getbootstrap.com\\"},{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 2\\",\\"link\\":\\"https:\\\\/\\\\/www.getbootstrap.com\\"}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":null,\\"desktop_position\\":\\"center-left\\",\\"mobile_position\\":\\"center-left\\"},\\"desktop_image\\":\\"coverimage\\\\/032026\\\\/date_01\\\\/bfcea5e3106c13975f4ebb7460e8518a.png\\",\\"mobile_image\\":\\"coverimage\\\\/012026\\\\/date_28\\\\/de938cafb514a7f036bc605ca4504f1c.png\\"}]',
            'cover_slideshow_vars' => null,
            'cover_slideshow_direction' => 'horizontal',
            'cover_slideshow_desktop_direction' => 'horizontal',
            'cover_slideshow_mobile_direction' => 'horizontal',
            'cover_autoplay_slideshow' => 'active',
            'cover_autoplay_slideshow_interval' => 3000,
            'cover_looping_slideshow' => 'active',
            'cover_is_active' => 'active',
            'updated_at' => '2026-01-23 05:46:18',
            'created_at' => '2025-10-03 09:21:11'
        ],
        [
            'id' => 2,
            'user_id' => 1,
            'uri' => 'homepage2',
            'cover_type' => 'slideshow',
            'cover_page_name' => 'Homepage 2',
            'cover_bgimage_vars' => '[{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.4)\\",\\"background_size\\":\\"md_size\\",\\"title\\":\\"Testing\\",\\"description\\":null,\\"button\\":[{\\"is_active\\":\\"active\\",\\"title\\":null,\\"link\\":null},{\\"is_active\\":\\"active\\",\\"title\\":null,\\"link\\":null}],\\"desktop_content_position\\":\\"center-center\\",\\"mobile_content_position\\":\\"center-center\\",\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":\\"31\\\\/10\\\\/2025 15:50\\",\\"content_default\\":\\"Fri Oct 31 2025 15:50:00 GMT+0700 (Western Indonesia Time)\\",\\"desktop_position\\":\\"default\\",\\"mobile_position\\":\\"default\\"},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_07\\\\/8fd67759c6f8aeb74b22d1395045ed36.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_07\\\\/0385ffa38bf78201cde18367e2a5e75f.jpg\\"}]',
            'cover_slideshow_vars' => '[{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.3)\\",\\"background_size\\":\\"boi_size\\",\\"desktop_content_position\\":\\"center-center\\",\\"mobile_content_position\\":\\"center-center\\",\\"title\\":\\"Testing 2\\",\\"description\\":\\"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit\\",\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Testing\\",\\"link\\":\\"https:\\\\/\\\\/www.mgmotor.id\\"},{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null}],\\"link\\":{\\"is_active\\":\\"active\\",\\"content\\":\\"https:\\\\/\\\\/www.bmkk.co.id\\"},\\"countdown\\":{\\"is_active\\":\\"active\\",\\"content\\":\\"30\\\\/4\\\\/2026 10:29\\",\\"content_default\\":\\"Thu Apr 30 2026 10:29:00 GMT+0700 (Western Indonesia Time)\\",\\"desktop_position\\":\\"default\\",\\"mobile_position\\":\\"bottom-left\\"},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_30\\\\/c6fc410401067f3874db73450b8ef623.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_30\\\\/823437a7ada4d459d3b5659057fa90aa.jpg\\"},{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.4)\\",\\"background_size\\":\\"boi_size\\",\\"desktop_content_position\\":\\"top-left\\",\\"mobile_content_position\\":\\"top-right\\",\\"title\\":\\"Roboto\\",\\"description\\":\\"Delivering finest quality. Building excellence into interior spaces.\\",\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"link\\",\\"desktop_position\\":\\"bottom-left\\",\\"mobile_position\\":\\"bottom-left\\",\\"text\\":\\"Explore Our Projects\\",\\"link\\":\\"https:\\\\/\\\\/www.getbootstrap.com\\"},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 1\\",\\"link\\":null},{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 2\\",\\"link\\":null}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":\\"01\\\\/11\\\\/2025 14:34\\",\\"content_default\\":\\"Sat Nov 01 2025 14:34:00 GMT+0700 (Western Indonesia Time)\\",\\"desktop_position\\":\\"default\\",\\"mobile_position\\":\\"bottom-left\\"},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_30\\\\/ce3c87f758de8f0cfb141b0faeb75566.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_30\\\\/9d5b2f66a84d54c4b9a21e8ad0553eac.jpg\\"},{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.3)\\",\\"background_size\\":\\"boi_size\\",\\"desktop_content_position\\":\\"center-center\\",\\"mobile_content_position\\":\\"center-center\\",\\"title\\":\\"Testing 3\\",\\"description\\":\\"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit\\",\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null},{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":\\"Thu Oct 30 2025 10:29:46 GMT+0700 (Western Indonesia Time)\\",\\"desktop_position\\":\\"default\\",\\"mobile_position\\":\\"default\\"},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_30\\\\/9813eb551b9f57440f2c57e3ef3e5eff.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_30\\\\/0d177c940670dd0da538fde66325b946.jpg\\"}]',
            'cover_slideshow_direction' => 'horizontal',
            'cover_slideshow_desktop_direction' => 'vertical',
            'cover_slideshow_mobile_direction' => 'horizontal',
            'cover_autoplay_slideshow' => 'inactive',
            'cover_autoplay_slideshow_interval' => 3000,
            'cover_looping_slideshow' => 'active',
            'cover_is_active' => 'active',
            'updated_at' => '2026-04-04 21:14:40',
            'created_at' => '2025-10-03 09:27:50'
        ],
        [
            'id' => 3,
            'user_id' => 1,
            'uri' => 'homepage3',
            'cover_type' => 'slideshow',
            'cover_page_name' => 'Homepage 3',
            'cover_bgimage_vars' => null,
            'cover_slideshow_vars' => '[{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.4)\\",\\"desktop_content_position\\":\\"center-left\\",\\"mobile_content_position\\":\\"center-left\\",\\"title\\":\\"Testing 3\\",\\"description\\":\\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\\",\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 1\\",\\"link\\":\\"https:\\\\/\\\\/getbootstrap.com\\\\/\\"},{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 2\\",\\"link\\":\\"https:\\\\/\\\\/getbootstrap.com\\\\/\\"}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":null},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_03\\\\/656a2afa6abda8f41a08887e0df3ed7f.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_03\\\\/7366f0242acdbee6ab8aba9a25df7aa3.jpg\\"},{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.4)\\",\\"desktop_content_position\\":\\"center-center\\",\\"mobile_content_position\\":\\"center-center\\",\\"title\\":\\"Testing 1\\",\\"description\\":\\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\\",\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 1\\",\\"link\\":\\"https:\\\\/\\\\/getbootstrap.com\\\\/\\"},{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 2\\",\\"link\\":\\"https:\\\\/\\\\/getbootstrap.com\\\\/\\"}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":null},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_03\\\\/f973602708afba76344ad7a8214573cb.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_03\\\\/082f986a432ad59fb8c84b871e014e37.jpg\\"},{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.4)\\",\\"desktop_content_position\\":\\"center-right\\",\\"mobile_content_position\\":\\"center-right\\",\\"title\\":\\"Testing 2\\",\\"description\\":\\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\\",\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 1\\",\\"link\\":\\"https:\\\\/\\\\/getbootstrap.com\\\\/\\"},{\\"is_active\\":\\"inactive\\",\\"title\\":\\"Link 2\\",\\"link\\":\\"https:\\\\/\\\\/getbootstrap.com\\\\/\\"}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":null},\\"desktop_image\\":\\"coverimage\\\\/102025\\\\/date_03\\\\/c5193b7c03561ee89048a73d7370811b.jpg\\",\\"mobile_image\\":\\"coverimage\\\\/102025\\\\/date_03\\\\/672a787b29de640ce7d8fa00cb81d5c5.jpg\\"}]',
            'cover_slideshow_direction' => 'horizontal',
            'cover_slideshow_desktop_direction' => 'vertical',
            'cover_slideshow_mobile_direction' => 'vertical',
            'cover_autoplay_slideshow' => 'active',
            'cover_autoplay_slideshow_interval' => 3000,
            'cover_looping_slideshow' => 'active',
            'cover_is_active' => 'active',
            'updated_at' => '2025-12-16 08:58:57',
            'created_at' => '2025-10-03 09:28:44'
        ],
        [
            'id' => 4,
            'user_id' => 1,
            'uri' => 'news',
            'cover_type' => 'background_image',
            'cover_page_name' => 'News',
            'cover_bgimage_vars' => '[{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.3)\\",\\"background_size\\":\\"md_size\\",\\"title\\":null,\\"description\\":null,\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null},{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null}],\\"desktop_content_position\\":\\"center-center\\",\\"mobile_content_position\\":\\"center-center\\",\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":\\"Wed Oct 29 2025 16:37:55 GMT+0700 (Western Indonesia Time)\\",\\"desktop_position\\":\\"default\\",\\"mobile_position\\":\\"default\\"}}]',
            'cover_slideshow_vars' => null,
            'cover_slideshow_direction' => 'horizontal',
            'cover_slideshow_desktop_direction' => 'horizontal',
            'cover_slideshow_mobile_direction' => 'horizontal',
            'cover_autoplay_slideshow' => 'active',
            'cover_autoplay_slideshow_interval' => 3000,
            'cover_looping_slideshow' => 'active',
            'cover_is_active' => 'active',
            'updated_at' => '2025-10-29 09:38:07',
            'created_at' => '2025-10-29 09:38:07'
        ],
        [
            'id' => 5,
            'user_id' => 0,
            'uri' => 'homepage4',
            'cover_type' => 'background_image',
            'cover_page_name' => 'Homepage 4',
            'cover_bgimage_vars' => '[{\\"cover_is_active\\":\\"active\\",\\"disable_content\\":\\"inactive\\",\\"background_overlay\\":\\"rgba(0, 0, 0, 0.3)\\",\\"background_size\\":\\"md_size\\",\\"desktop_content_position\\":\\"center-center\\",\\"mobile_content_position\\":\\"center-center\\",\\"title\\":null,\\"description\\":null,\\"second_content\\":{\\"is_active\\":\\"inactive\\",\\"type\\":\\"text\\",\\"desktop_position\\":\\"center-center\\",\\"mobile_position\\":\\"center-center\\",\\"text\\":null},\\"button\\":[{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null},{\\"is_active\\":\\"inactive\\",\\"title\\":null,\\"link\\":null}],\\"link\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null},\\"countdown\\":{\\"is_active\\":\\"inactive\\",\\"content\\":null,\\"content_default\\":\\"Sun Mar 22 2026 23:38:01 GMT+0700 (Western Indonesia Time)\\",\\"desktop_position\\":\\"default\\",\\"mobile_position\\":\\"default\\"}}]',
            'cover_slideshow_vars' => null,
            'cover_slideshow_direction' => 'horizontal',
            'cover_slideshow_desktop_direction' => 'horizontal',
            'cover_slideshow_mobile_direction' => 'horizontal',
            'cover_autoplay_slideshow' => 'active',
            'cover_autoplay_slideshow_interval' => 3000,
            'cover_looping_slideshow' => 'active',
            'cover_is_active' => 'active',
            'updated_at' => '2026-03-22 16:38:27',
            'created_at' => '2026-03-22 16:38:27'
        ]
        ]);
    }
}
