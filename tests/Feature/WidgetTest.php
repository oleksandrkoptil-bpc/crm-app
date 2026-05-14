<?php

namespace Tests\Feature;

use Tests\TestCase;

class WidgetTest extends TestCase
{
    public function test_widget_page_is_available(): void
    {
        $this->get('/widget')
            ->assertOk()
            ->assertSee('ticket-widget-form')
            ->assertSee('/api/tickets', false)
            ->assertSee('data-token=', false);
    }
}
