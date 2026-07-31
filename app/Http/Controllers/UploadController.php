<?php

namespace App\Http\Controllers;

use App\Models\Profile;
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
            $filename = uniqid('181') . '-' . Auth::user()->name . '.' . $extension;
            $file->storeAs('public/image-front/', $filename);

            TemporaryFile::create([
                'filename' => $filename
            ]);
            return $filename;
        }
        if ($request->hasFile('image_back')) {
            $file = $request->file('image_back');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('181') . '-' . Auth::user()->name . '.' . $extension;
            $file->storeAs('public/image-back/', $filename);

            TemporaryFile::create([
                'filename' => $filename
            ]);
            return $filename;
        }
        // request from views/
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('1d1') . '-' . Auth::user()->name . '.' . $extension;
            $file->storeAs('public/cover-image/', $filename);

            TemporaryFile::create([
                'filename' => $filename
            ]);

            return $filename;
        }
    }
    // upload cover image
    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover_image' => 'required|string',
            'bio' => 'string|max:750',
        ]);

        $temp = TemporaryFile::where('filename', $request->cover_image)->first();
        if ($temp) {
            $temp->delete();
        }

        Profile::updateOrCreate(['user_id' => Auth::user()->id], [
            'cover_image' => $request->cover_image,
        ])->save();

        session()->flash('message', 'Profile Updated');
        return redirect()->route('profile.show');
    }
}
