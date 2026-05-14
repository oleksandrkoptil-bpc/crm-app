<?php

namespace App\Http\Controllers;

use App\Support\WidgetApiToken;
use Illuminate\View\View;

class WidgetController extends Controller
{
    public function __invoke(): View
    {
        return view('widget', [
            'widgetToken' => WidgetApiToken::make(),
        ]);
    }
}
