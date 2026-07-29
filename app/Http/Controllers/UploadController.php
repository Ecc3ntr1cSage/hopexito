<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\CustomProduct;
use App\Models\ProductCollection;
use App\Models\ProductTemplate;
use App\Models\TemporaryFile;
use App\Services\MockupGenerator;
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
        //  request from views/custom/
        // if ($request->hasFile('custom_image_front')) {
        //     $file = $request->file('custom_image_front');
        //     $extension = $file->getClientOriginalExtension();
        //     $filename = uniqid('120') . '-' . Auth::user()->name . '.' . $extension;
        //     $file->storeAs('public/custom-image-front/', $filename);

        //     TemporaryFile::create([
        //         'filename' => $filename
        //     ]);
        //     return $filename;
        // }
        // if ($request->hasFile('custom_image_back')) {
        //     $file = $request->file('custom_image_back');
        //     $extension = $file->getClientOriginalExtension();
        //     $filename = uniqid('120') . '-' . Auth::user()->name . '.' . $extension;
        //     $file->storeAs('public/custom-image-back/', $filename);

        //     TemporaryFile::create([
        //         'filename' => $filename
        //     ]);
        //     return $filename;
        // }
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
        // request from views/livewire/admin/template
        if ($request->hasFile('mockup_image')) {
            $file = $request->file('mockup_image');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('12b') . '-' . Auth::user()->name . '.' . $extension;
            $file->storeAs('public/mockup-image/', $filename);

            TemporaryFile::create([
                'filename' => $filename
            ]);

            return $filename;
        }
        // request from views/livewire/admin/template
        if ($request->hasFile('mockup_image_2')) {
            $file = $request->file('mockup_image_2');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('12b') . '-' . Auth::user()->name . '.' . $extension;
            $file->storeAs('public/mockup-image/', $filename);

            TemporaryFile::create([
                'filename' => $filename
            ]);

            return $filename;
        }
        // request from views/livewire/admin/product
        if ($request->hasFile('collection_image')) {
            $file = $request->file('collection_image');
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('7a9') . '-' . Auth::user()->name . '.' . $extension;
            $file->storeAs('public/collection-image/', $filename);

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

        Artist::updateOrCreate(['id' => Auth::user()->id], [
            'cover_image' => $request->cover_image,
        ])->save();

        session()->flash('message', 'Profile Updated');
        return redirect()->route('profile.show');
    }
    // upload product template image
    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'mockup_image' => 'required|string',
            'mockup_image_2' => 'required|string',
            'commission' => 'required|numeric',
            'min' => 'required|numeric',
            'color' => 'required'
        ]);

        $temp = TemporaryFile::where('filename', $request->mockup_image)->first();
        if ($temp) {
            $temp->delete();
        }
        $temp_2 = TemporaryFile::where('filename', $request->mockup_image_2)->first();
        if ($temp_2) {
            $temp_2->delete();
        }

        ProductTemplate::create([
            'category' => $request->category,
            'mockup_image' => $request->mockup_image,
            'mockup_image_2' => $request->mockup_image_2,
            'commission' => $request->commission,
            'status' => 1,
            'min' => $request->min,
            'color' => implode(',', $request->input('color'))
        ]);

        session()->flash('message', 'Product Template Created');
        return redirect()->route('admin.templates');
    }
    // upload collection image
    public function uploadCollection(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'collection_image' => 'required|string',
        ]);

        $temp = TemporaryFile::where('filename', $request->collection_image)->first();
        if ($temp) {
            $temp->delete();
        }

        ProductCollection::create([
            'id' => uniqid(),
            'name' => Auth::user()->name,
            'title' => $request->title,
            'collection_image' => $request->collection_image
        ]);

        session()->flash('message', 'New Collection Added');
        return redirect()->route('product.manage');
    }

    public function uploadCustom(Request $request, MockupGenerator $mockups)
    {
        $validated = $request->validate([
            'size' => 'required',
            'color' => 'required',
            'quantity' => 'required|integer|min:1',
            'custom_image_front' => 'required|image|max:8192',
            'custom_image_back' => 'nullable|image|max:8192',
            'template_front' => 'required|string',
            'template_back' => 'nullable|string',
            'front_x' => 'required|integer',
            'front_y' => 'required|integer',
            'front_w' => 'required|integer',
            'front_h' => 'required|integer',
            'back_x' => 'nullable|integer',
            'back_y' => 'nullable|integer',
            'back_w' => 'nullable|integer',
            'back_h' => 'nullable|integer',
        ]);

        $frontDesignPath = $request->file('custom_image_front')->store('custom-image-front', 'public');
        $backDesignPath = $request->file('custom_image_back')?->store('custom-image-back', 'public');

        $frontMockupPath = $mockups->generate(
            $request->file('custom_image_front'),
            $validated['template_front'],
            ['x' => $validated['front_x'], 'y' => $validated['front_y'], 'w' => $validated['front_w'], 'h' => $validated['front_h']],
            $validated['color'],
            'custom-product-front/'.uniqid('custom-', true).'.png'
        );

        $backMockupPath = null;
        if ($request->hasFile('custom_image_back') && $request->filled('template_back')) {
            $backMockupPath = $mockups->generate(
                $request->file('custom_image_back'),
                $validated['template_back'],
                [
                    'x' => $validated['back_x'] ?? $validated['front_x'],
                    'y' => $validated['back_y'] ?? $validated['front_y'],
                    'w' => $validated['back_w'] ?? $validated['front_w'],
                    'h' => $validated['back_h'] ?? $validated['front_h'],
                ],
                $validated['color'],
                'custom-product-back/'.uniqid('custom-', true).'.png'
            );
        }

        CustomProduct::create([
            'user_id' => Auth::id(),
            'price' => 35,
            'color' => $validated['color'],
            'size' => $validated['size'],
            'quantity' => $validated['quantity'],
            'custom_image_front' => basename($frontDesignPath),
            'custom_image_back' => $backDesignPath ? basename($backDesignPath) : null,
            'custom_product_image' => $frontMockupPath,
            'custom_product_image_2' => $backMockupPath ?? $frontMockupPath,
        ]);

        session()->flash('message', 'Custom product saved for demo review.');
        return redirect()->route('cart.index');
    }
}
