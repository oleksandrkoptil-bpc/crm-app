<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    public function test_swagger_documentation_is_available(): void
    {
        Artisan::call('l5-swagger:generate');

        $this->get('/api/documentation')->assertOk();
        $this->get('/docs/api-docs.json')->assertOk();
    }
}
