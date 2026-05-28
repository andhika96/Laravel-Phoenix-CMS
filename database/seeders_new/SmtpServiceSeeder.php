<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmtpServiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('smtp_service')->insert([
        [
            'id' => 1,
            'service_id' => 89,
            'service_name' => 'SMTP2GO TLS'
        ]
        ]);
    }
}
