<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Models\Page_Builder\Page_Builder;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Illuminate\Routing\Route as IlluminateRoute;
use Tests\TestCase;

class PageBuilderElementorV24BaselineIsolationTest extends TestCase
{
    public function test_v24_version_contract_and_route_family_are_registered_separately(): void
    {
        $this->assertTrue(defined(Page_Builder::class.'::EDITOR_VERSION_V24'));
        $this->assertSame('2.4', constant(Page_Builder::class.'::EDITOR_VERSION_V24'));

        $expected = [
            'cms.core.pagebuilder_elementor_v24.create' => 'pagebuilder-elementor/v2.4/create',
            'cms.core.pagebuilder_elementor_v24.store' => 'pagebuilder-elementor/v2.4/store',
            'cms.core.pagebuilder_elementor_v24.edit' => 'pagebuilder-elementor/v2.4/edit/{idOrSlug}',
            'cms.core.pagebuilder_elementor_v24.update' => 'pagebuilder-elementor/v2.4/update/{idOrSlug}',
            'cms.core.pagebuilder_elementor_v24.data' => 'pagebuilder-elementor/v2.4/data/{idOrSlug}',
            'cms.core.pagebuilder_elementor_v24.image_rendition' => 'pagebuilder-elementor/v2.4/image-rendition',
            'cms.core.pagebuilder_elementor_v24.preview' => 'pagebuilder-elementor/v2.4/preview/{idOrSlug}',
            'cms.core.pagebuilder_elementor_v24.form.editor_draft' => 'pagebuilder-elementor/v2.4/form/editor-draft',
            'cms.core.pagebuilder_elementor_v24.form.submit' => 'pagebuilder-elementor/v2.4/form/{idOrSlug}/{nodeId}',
            'cms.core.pagebuilder_elementor_v24.datasets.index' => 'pagebuilder-elementor/v2.4/datasets',
            'cms.core.pagebuilder_elementor_v24.datasets.store' => 'pagebuilder-elementor/v2.4/datasets',
            'cms.core.pagebuilder_elementor_v24.datasets.update' => 'pagebuilder-elementor/v2.4/datasets/{datasetId}',
            'cms.core.pagebuilder_elementor_v24.datasets.destroy' => 'pagebuilder-elementor/v2.4/datasets/{datasetId}',
        ];

        foreach ($expected as $name => $uri) {
            $route = app('router')->getRoutes()->getByName($name);
            $this->assertInstanceOf(IlluminateRoute::class, $route, $name);
            $this->assertSame($uri, $route->uri(), $name);
            $this->assertStringContainsString('PageBuilderElementorV24', $route->getActionName(), $name);
        }
    }

    public function test_v24_has_its_own_active_source_boundaries(): void
    {
        foreach ([
            app_path('Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php'),
            app_path('Http/Controllers/Web/PageBuilderElementorV24/FormDatasetController.php'),
            app_path('Http/Requests/Page_Builder_Elementor_V24/AddPageBuilderElementorV24Request.php'),
            app_path('Http/Requests/Page_Builder_Elementor_V24/EditPageBuilderElementorV24Request.php'),
            app_path('Mail/PageBuilderElementorV24FormMail.php'),
            app_path('Models/PageBuilderElementorV24/FormDataset.php'),
            app_path('Support/PageBuilderElementorV24/FormSubmissionHandler.php'),
            app_path('Support/PageBuilderElementorV24/ModuleCatalog.php'),
            public_path('js/pagebuilder_elementor_v24/app.js'),
            public_path('js/pagebuilder_elementor_v24/frontend-runtime.js'),
            public_path('js/pagebuilder_elementor_v24/widget-registry.js'),
            public_path('assets/css/pagebuilder_elementor_v24.css'),
            public_path('assets/css/frontend_elementor_v24.css'),
            resource_path('data/pagebuilder_elementor_v24_shapes.json'),
            resource_path('views/pagebuilder_elementor_v24/editor_shell.blade.php'),
            resource_path('views/pagebuilder_elementor_v24/frontend_renderer.blade.php'),
            resource_path('views/emails/pagebuilder-elementor-v24-form-text.blade.php'),
            base_path('routes/pagebuilder_elementor_v24.php'),
        ] as $file) {
            $this->assertFileExists($file);
        }

        $this->assertDirectoryExists(resource_path('pagebuilder_elementor_v24/modules'));
        $this->assertFileExists(resource_path('pagebuilder_elementor_v24/shared/AdvancedControls.vue'));
        $this->assertCount(53, (new ModuleCatalog)->all());
        $this->assertFileDoesNotExist(config_path('pagebuilder_elementor_v24_widgets.php'));
        $this->assertDirectoryDoesNotExist(public_path('js/pagebuilder_elementor_v24/widgets'));
    }

    public function test_public_page_route_uses_the_version_neutral_dispatcher(): void
    {
        $route = app('router')->getRoutes()->getByName('cms.public.pagebuilder_elementor_v23.show');

        $this->assertInstanceOf(IlluminateRoute::class, $route);
        $this->assertSame('pages/{uri}', $route->uri());
        $this->assertStringContainsString('PageBuilderElementorPublishedPageController', $route->getActionName());
    }

    public function test_authenticated_v23_and_v24_shells_load_only_their_own_assets(): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => 1,
            'email' => 'version-isolation@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);

        $v23 = $this->get(route('cms.core.pagebuilder_elementor_v23.create'))->assertOk()->getContent();
        $v24 = $this->get(route('cms.core.pagebuilder_elementor_v24.create'))->assertOk()->getContent();

        $this->assertStringContainsString('id="pbElementorV23App"', $v23);
        $this->assertStringContainsString('js/pagebuilder_elementor_v23/app.js', $v23);
        $this->assertStringNotContainsString('js/pagebuilder_elementor_v24/', $v23);
        $this->assertStringContainsString('id="pbElementorV24App"', $v24);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/app.js', $v24);
        $this->assertStringNotContainsString('js/pagebuilder_elementor_v23/', $v24);
    }
}
