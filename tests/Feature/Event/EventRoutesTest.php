<?php

namespace Tests\Feature\Event;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EventRoutesTest extends TestCase
{
    public function test_event_route_family_is_registered(): void
    {
        foreach ([
            'cms.core.manage_event',
            'cms.core.manage_event.add',
            'cms.core.manage_event.store',
            'cms.core.manage_event.edit',
            'cms.core.manage_event.update',
            'cms.core.manage_event.delete',
            'cms.core.manage_event.listdata',
            'cms.core.event',
            'cms.core.event.detail',
            'cms.core.event.listdata',
            'cms.core.event.registrations',
            'cms.core.event.occurrence.register',
            'cms.core.event.occurrence.cancel',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName), "Expected route {$routeName} to exist.");
        }
    }
}
