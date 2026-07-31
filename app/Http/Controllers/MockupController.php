<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MockupController extends Controller
{
    public function redirectToStudio(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, config('catalog.types')), 404);

        return redirect()->route('product.create', ['type' => $type]);
    }
}
