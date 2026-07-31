<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MockupController extends Controller
{
    public function shirt()
    {
        return $this->editor('shirt');
    }

    public function sweatshirt()
    {
        return $this->editor('sweat');
    }

    public function hoodie()
    {
        return $this->editor('hoodie');
    }

    private function editor(string $type)
    {
        $catalog = config('catalog.types.'.$type);
        $template = (object) [
            'category' => $catalog['label'],
            'mockup_image' => 'mockups/white-'.$type.'-front.png',
            'mockup_image_2' => 'mockups/white-'.$type.'-back.png',
            'min' => $catalog['price'],
            'commission' => config('catalog.commission_rate') * 100,
            'type' => $type,
            'front_position' => $catalog['front_position'],
            'back_position' => $catalog['back_position'],
        ];
        $colors = config('catalog.colors');
        return view('mockup.editor', ['template' => $template, 'colors' => $colors]);
    }
}
