<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        // request from views/mockup
        if ($request->hasFile('image_front')) {
            $file = $request->file('image_front');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('181').'-'.Auth::user()->name.'.'.$extension;
            $file->storeAs('public/image-front/', $filename);

            TemporaryFile::create([
                'filename' => $filename,
            ]);

            return $filename;
        }
        if ($request->hasFile('image_back')) {
            $file = $request->file('image_back');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('181').'-'.Auth::user()->name.'.'.$extension;
            $file->storeAs('public/image-back/', $filename);

            TemporaryFile::create([
                'filename' => $filename,
            ]);

            return $filename;
        }

        return null;
    }
}
