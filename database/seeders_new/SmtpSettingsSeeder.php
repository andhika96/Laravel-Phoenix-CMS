<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmtpSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('smtp_settings')->insert([
        [
            'id' => 1,
            'smtp_service' => 'Mailjet',
            'smtp_host' => 'in-v3.mailjet.com',
            'smtp_sender_name' => 'Noreply Aruna Dev',
            'smtp_sender_address' => 'noreply@aruna-dev.com',
            'smtp_username' => 'fe10820f07fa2a86b96cf7176aedd3aa',
            'smtp_password' => '95d84b5231b7924b7d306505eb3d64d4',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls'
        ],
        [
            'id' => 2,
            'smtp_service' => 'SMTP2GO SSL',
            'smtp_host' => 'mail.smtp2go.com',
            'smtp_sender_name' => 'Noreply Aruna Dev',
            'smtp_sender_address' => 'noreply@aruna-dev.com',
            'smtp_username' => 'noreply@aruna-dev.com',
            'smtp_password' => 'AUSjtkcmnOwieSOj',
            'smtp_port' => 465,
            'smtp_encryption' => 'ssl'
        ],
        [
            'id' => 89,
            'smtp_service' => 'SMTP2GO TLS',
            'smtp_host' => 'mail.smtp2go.com',
            'smtp_sender_name' => 'Noreply Aruna Dev',
            'smtp_sender_address' => 'noreply@aruna-dev.com',
            'smtp_username' => 'noreply@aruna-dev.com',
            'smtp_password' => 'AUSjtkcmnOwieSOj',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls'
        ]
        ]);
    }
}
