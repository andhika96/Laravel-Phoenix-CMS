<?php

namespace Tests\Feature;

use App\Support\CkfinderSessionBridge;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class CkfinderSecurityTest extends TestCase
{
    public function test_role_mapping_is_explicit_and_unknown_roles_fail_closed(): void
    {
        $bridge = app(CkfinderSessionBridge::class);

        $this->assertSame('Super Admin', $bridge->normalizeRole('Super Admin'));
        $this->assertSame('Administrator', $bridge->normalizeRole('admin'));
        $this->assertSame('General Member', $bridge->normalizeRole('General Member'));
        $this->assertNull($bridge->normalizeRole('Editor'));
        $this->assertNull($bridge->normalizeRole(null));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_bridge_writes_scoped_session_state_and_clear_revokes_it(): void
    {
        $user = new class
        {
            public string $uuid = 'account-7';

            public function getAuthIdentifier(): int
            {
                return 7;
            }

            public function getRoleNames()
            {
                return collect(['General Member']);
            }
        };

        $request = \Illuminate\Http\Request::create('https://cms.example.test/editor', 'GET');
        $request->setUserResolver(static fn () => $user);
        $bridge = app(CkfinderSessionBridge::class);

        $this->assertTrue($bridge->prepare($request));
        $this->assertSame('7', $_SESSION['CKFinder_UserId']);
        $this->assertSame('account-7', $_SESSION['CKFinder_UserRole_UUID']);
        $this->assertSame('General Member', $_SESSION['CKFinder_UserRole']);
        $this->assertGreaterThan(time(), $_SESSION['CKFinder_AuthExpiresAt']);

        $bridge->clear($request);
        foreach (['CKFinder_UserId', 'CKFinder_UserRole_UUID', 'CKFinder_UserRole', 'CKFinder_AuthExpiresAt'] as $key) {
            $this->assertArrayNotHasKey($key, $_SESSION);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_bridge_does_not_reuse_a_stale_legacy_role_when_the_user_has_no_roles(): void
    {
        $user = new class
        {
            public string $uuid = 'account-no-role';

            public function getAuthIdentifier(): int
            {
                return 8;
            }

            public function getRoleNames()
            {
                return collect();
            }
        };

        $laravelSession = app('session')->driver();
        $laravelSession->put('LaraCKFinder_UserRole', 'Administrator');

        $request = \Illuminate\Http\Request::create('/editor', 'GET');
        $request->setLaravelSession($laravelSession);
        $request->setUserResolver(static fn () => $user);

        $this->assertFalse(app(CkfinderSessionBridge::class)->prepare($request));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_bridge_rotates_an_incomplete_native_session_before_authorizing(): void
    {
        $knownSessionId = 'ckfinder-known-session';
        session_id($knownSessionId);
        session_start();
        $_SESSION = [];
        session_write_close();

        $user = new class
        {
            public string $uuid = 'account-9';

            public function getAuthIdentifier(): int
            {
                return 9;
            }

            public function getRoleNames()
            {
                return collect(['General Member']);
            }
        };

        $request = \Illuminate\Http\Request::create('https://cms.example.test/editor', 'GET');
        $request->setUserResolver(static fn () => $user);

        $this->assertTrue(app(CkfinderSessionBridge::class)->prepare($request));
        $this->assertNotSame($knownSessionId, session_id());

        $rotatedSessionId = session_id();
        $this->assertTrue(app(CkfinderSessionBridge::class)->prepare($request));
        $this->assertSame($rotatedSessionId, session_id());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_bridge_uses_the_authenticated_id_when_uuid_is_blank(): void
    {
        $user = new class
        {
            public string $uuid = '';

            public function getAuthIdentifier(): int
            {
                return 10;
            }

            public function getRoleNames()
            {
                return collect(['General Member']);
            }
        };

        $request = \Illuminate\Http\Request::create('https://cms.example.test/editor', 'GET');
        $request->setUserResolver(static fn () => $user);

        $this->assertTrue(app(CkfinderSessionBridge::class)->prepare($request));
        $this->assertSame('10', $_SESSION['CKFinder_UserRole_UUID']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_bridge_rejects_an_invalid_non_empty_scope_instead_of_normalizing_it(): void
    {
        $user = new class
        {
            public string $uuid = 'tenant/../other';

            public function getAuthIdentifier(): int
            {
                return 11;
            }

            public function getRoleNames()
            {
                return collect(['General Member']);
            }
        };

        $request = \Illuminate\Http\Request::create('https://cms.example.test/editor', 'GET');
        $request->setUserResolver(static fn () => $user);

        $this->assertFalse(app(CkfinderSessionBridge::class)->prepare($request));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_bridge_rejects_a_principal_without_a_role_provider(): void
    {
        $user = new class
        {
            public string $uuid = 'account-no-role-provider';

            public function getAuthIdentifier(): int
            {
                return 12;
            }
        };

        $laravelSession = app('session')->driver();
        $laravelSession->put('LaraCKFinder_UserRole', 'Administrator');

        $request = \Illuminate\Http\Request::create('/editor', 'GET');
        $request->setLaravelSession($laravelSession);
        $request->setUserResolver(static fn () => $user);

        $this->assertFalse(app(CkfinderSessionBridge::class)->prepare($request));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_connector_authentication_requires_a_complete_unexpired_native_session(): void
    {
        session_id('ckfinder-security-test');
        $config = require public_path('assets/plugins/ckfinder/config.php');
        $authenticate = $config['authentication'];

        $_SESSION = [];
        $this->assertFalse($authenticate());

        $_SESSION = [
            'CKFinder_UserId' => '7',
            'CKFinder_UserRole_UUID' => 'account-7',
            'CKFinder_UserRole' => 'General Member',
            'CKFinder_AuthExpiresAt' => time() + 60,
        ];
        $this->assertTrue($authenticate());

        $_SESSION['CKFinder_AuthExpiresAt'] = time() - 1;
        $this->assertFalse($authenticate());

        $_SESSION['CKFinder_AuthExpiresAt'] = time() + 60;
        $_SESSION['CKFinder_UserRole'] = 'Unknown';
        $this->assertFalse($authenticate());
    }

    public function test_connector_source_has_no_hard_coded_cloud_credentials_or_debug_mode(): void
    {
        $source = file_get_contents(public_path('assets/plugins/ckfinder/config.php'));

        $this->assertSame(0, preg_match('/AKIA[0-9A-Z]{16}/', $source), 'CKFinder config still contains an AWS access key literal.');
        $this->assertFalse(str_contains($source, "'adapter'       => 's3'"), 'Unused AWS backend still exists.');
        $this->assertFalse(str_contains($source, "'adapter'       => 's3compatible'"), 'Unused S3-compatible backend still exists.');
        $this->assertTrue(str_contains($source, "\$config['debug'] = false;"), 'CKFinder debug mode must be disabled.');
    }

    public function test_active_ckfinder_entry_points_use_the_shared_bridge_and_logout_clears_it(): void
    {
        $files = [
            app_path('Http/Controllers/Web/Manage_Article/Manage_Article_Controller.php'),
            app_path('Http/Controllers/Web/PageBuilderElementor/PageBuilderElementor_Controller.php'),
            app_path('Http/Controllers/Web/PageBuilderElementorV23/PageBuilderElementorV23Controller.php'),
            app_path('Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php'),
        ];

        foreach ($files as $file) {
            $source = file_get_contents($file);
            $this->assertTrue(str_contains($source, 'CkfinderSessionBridge'), $file.' does not use the shared bridge.');
            $this->assertFalse(str_contains($source, "\$_SESSION['CKFinder_UserRole']"), $file.' still writes CKFinder session state directly.');
        }

        $auth = file_get_contents(app_path('Http/Controllers/Web/Auth/Auth_Controller.php'));
        $this->assertTrue(str_contains($auth, 'CkfinderSessionBridge'));
        $this->assertTrue(str_contains($auth, '->clear('));
    }
}
