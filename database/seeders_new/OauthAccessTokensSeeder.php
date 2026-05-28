<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthAccessTokensSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('oauth_access_tokens')->insert([
        [
            'id' => '001ad5f210bf668a5129b7905b42d027244726d90a9eb1d821fa9878896aa0b7f54747c71c62fcde',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-02-25 08:49:05',
            'updated_at' => '2026-02-25 08:49:05',
            'expires_at' => '2026-08-25 15:49:05'
        ],
        [
            'id' => '0e9df8a2fd9a687c59273cb2e9128c2806cdddbc999d085646f89c602f0cb1b1bbba22a40ce43102',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-28 09:15:21',
            'updated_at' => '2026-03-28 09:15:21',
            'expires_at' => '2027-03-28 16:15:21'
        ],
        [
            'id' => '205a429dd04476c16129b0e8c76ea07b3392b31378bb6e9796274ed7b51c3eef50a39d9703deb52d',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-14 05:28:31',
            'updated_at' => '2025-11-14 05:28:31',
            'expires_at' => '2026-11-14 12:28:31'
        ],
        [
            'id' => '2e18dc91fd4eac5a82a06ea97bbf7581ab6c963e2a809049089e81a0cb918dfa7bd361c070bcc00b',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-14 06:07:39',
            'updated_at' => '2025-11-14 06:07:39',
            'expires_at' => '2026-11-14 13:07:39'
        ],
        [
            'id' => '380b12239904c9097fc06eb77c984bc029ff8d0242b8a929b033a7f1d85ee5aad993f87504d6ce8d',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-27 20:12:46',
            'updated_at' => '2026-03-27 20:12:46',
            'expires_at' => '2027-03-28 03:12:46'
        ],
        [
            'id' => '3d6d20341c75939052689aa80982643998958471442085bc1c1d3baee00b2bb2202a6dbb0dd3e96f',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-27 03:34:11',
            'updated_at' => '2025-11-27 03:34:11',
            'expires_at' => '2026-11-27 10:34:11'
        ],
        [
            'id' => '47e18ea9ce8820a4906c09946b0588439ad37e2cbb3fc08aecbca2dc7160e1bf53cddc68f83f869f',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-27 20:13:38',
            'updated_at' => '2026-03-27 20:13:39',
            'expires_at' => '2027-03-28 03:13:38'
        ],
        [
            'id' => '66c10e6a705c03a4486173cb16eb78008e28bbb78545064725c5cc565cef34d9f357f295352af216',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-12-24 09:22:43',
            'updated_at' => '2025-12-24 09:22:43',
            'expires_at' => '2026-12-24 16:22:43'
        ],
        [
            'id' => '747cff52b95b7a7ff238cbc9f8bbdff875eaa800d5124077059b2439a8bd0685dd2e8bcbc1fa67bf',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-27 19:59:46',
            'updated_at' => '2026-03-27 19:59:46',
            'expires_at' => '2027-03-28 02:59:46'
        ],
        [
            'id' => '8e656f16c69118119a85f9ebd1aa8cb23894e2da5177e21677b2adbdc586da6b9b5c7fae74d83538',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-14 03:06:43',
            'updated_at' => '2025-11-14 03:06:43',
            'expires_at' => '2026-11-14 10:06:43'
        ],
        [
            'id' => '9034daeebf67a310490e4798ba823fc025ceb4219c1da557de6d12c35f7578ea23c6facd44204965',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-12-29 08:45:05',
            'updated_at' => '2025-12-29 08:45:05',
            'expires_at' => '2026-12-29 15:45:05'
        ],
        [
            'id' => '95f5bf0fc5277b54be500ffb4512334cdb5165945cad503a0d95a29dc03bb943d2ae0337a52377ef',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-02-09 09:18:14',
            'updated_at' => '2026-02-09 09:18:14',
            'expires_at' => '2026-08-09 16:18:14'
        ],
        [
            'id' => 'a616c3fb27793d14e7790db446b488188b215f3ac9c0328a416ec2a2d23a0bdec2df22cffcb3ae0f',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-14 04:16:55',
            'updated_at' => '2025-11-14 04:16:55',
            'expires_at' => '2026-11-14 11:16:55'
        ],
        [
            'id' => 'ab71d8081acfc968c67177818367c87859a7d05e38d4e0d7b20d262ccf0e386269b3d54e69aa17d2',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-01-02 16:41:18',
            'updated_at' => '2026-01-02 16:41:18',
            'expires_at' => '2027-01-02 23:41:18'
        ],
        [
            'id' => 'c231262dbe7b5281099bcf0f1b1734d91848de6a09ba319d8d9db3b9eaf5c63ae7694d5ed8e2c362',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-27 09:37:57',
            'updated_at' => '2025-11-27 09:37:57',
            'expires_at' => '2026-11-27 16:37:57'
        ],
        [
            'id' => 'c54627fe2f439040d0aa669c57cbfdceb6b6e48ebe2573f00b59010f05b94cb0448a2d4a1a7d682f',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2025-11-14 05:24:08',
            'updated_at' => '2025-11-14 05:24:08',
            'expires_at' => '2026-11-14 12:24:08'
        ],
        [
            'id' => 'd5328b7f2b8835a1be117017af505d6307c490e55536e4799fd46136d5dcae590857dbc44df28576',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-27 20:16:36',
            'updated_at' => '2026-03-27 20:16:36',
            'expires_at' => '2027-03-28 03:16:36'
        ],
        [
            'id' => 'f0babb9c62bdda4d4f8f2b1634606732d5b8a1e31231ea67e0d4375c9fc3d6132d11f121c88edd9a',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-27 20:23:37',
            'updated_at' => '2026-03-27 20:23:37',
            'expires_at' => '2027-03-28 03:23:37'
        ],
        [
            'id' => 'f2a40b820c737808c476fb22cc8dc7a5d602a5505065d91a42b9fd8f096a7a5e3d25dc65f9fd0008',
            'user_id' => 1,
            'client_id' => 1,
            'name' => 'api',
            'scopes' => '[]',
            'revoked' => 0,
            'created_at' => '2026-03-27 20:39:40',
            'updated_at' => '2026-03-27 20:39:40',
            'expires_at' => '2027-03-28 03:39:40'
        ]
        ]);
    }
}
