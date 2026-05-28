<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthClientsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('oauth_clients')->insert([
        [
            'id' => 1,
            'user_id' => null,
            'name' => 'Laravel Personal Access Client',
            'secret' => 'cbWO0VQXmRYfa2V0AEbBpsnWeJGcXoZEQpRmmdc3',
            'provider' => null,
            'redirect' => 'http://localhost',
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => '2024-05-16 21:55:15',
            'updated_at' => '2024-05-16 21:55:15'
        ],
        [
            'id' => 2,
            'user_id' => null,
            'name' => 'Laravel Password Grant Client',
            'secret' => 'rwcNywwYRoPRcOzv3COakTd8XxiGmyp2KzrFfmUt',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => '2024-05-16 21:55:15',
            'updated_at' => '2024-05-16 21:55:15'
        ]
        ]);
    }
}
