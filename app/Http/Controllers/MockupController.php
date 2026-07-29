<?php

namespace App\Http\Controllers;

use App\Models\ProductTemplate;
use Illuminate\Http\Request;

class MockupController extends Controller
{
    public function shirt()
    {
        $template = ProductTemplate::where('category', 'Shirt')->firstOrFail();
        $colors = explode(',', $template->color);
        return view('mockup.shirt', compact('template', 'colors'));
    }

    public function oversized()
    {
        $template = ProductTemplate::where('category', 'Oversized')->firstOrFail();
        $colors = explode(',', $template->color);
        return view('mockup.oversized', compact('template', 'colors'));
    }

    public function customShirt()
    {
        $template = ProductTemplate::where('category', 'Shirt')->firstOrFail();
        $colors = explode(',', $template->color);
        return view('custom.shirt', compact('template', 'colors'));
    }

    public function customOversized()
    {
        $template = ProductTemplate::where('category', 'Oversized')->firstOrFail();
        $colors = explode(',', $template->color);
        return view('custom.oversized', compact('template', 'colors'));
    }
}
