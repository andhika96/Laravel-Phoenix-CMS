<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthPersonalAccessClientsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('oauth_personal_access_clients')->insert([
        [
            'id' => 1,
            'client_id' => 1,
            'created_at' => '2024-05-16 21:55:15',
            'updated_at' => '2024-05-16 21:55:15'
        ]
        ]);
    }
}
