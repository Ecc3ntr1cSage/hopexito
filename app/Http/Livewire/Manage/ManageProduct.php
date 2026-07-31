<?php

namespace App\Http\Livewire\Manage;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageProduct extends Component
{
    public $title, $tags, $search;
    // edit product by id
    public function editProduct($id)
    {
        $validatedData = $this->validate([
            'title' => 'required|string',
            'tags' => 'required|string',
            'visibility' => 'nullable|in:public,private',
        ]);

        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);

        $product->update([
            'title' => $this->title,
            'tags' => $this->tags,
        ]);

        session()->flash('message', 'Product Updated');
        return redirect()->route('product.manage');
    }
    // pin product to top by id
    public function pinProduct($id)
    {
        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $product->update(['status' => 3]);
  
        session()->flash('message', 'Product Pinned');
        return redirect()->route('product.manage');
    }
    // pin product to top by id
    public function unpinProduct($id)
    {
        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $product->update(['status' => 1]);

        session()->flash('message', 'Product Unpinned');
        return redirect()->route('product.manage');
    }
    // archive product by id
    public function archiveProduct($id)
    {
        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $product->status = 2;
        $product->save();

        session()->flash('message', 'Product Archived');
        return redirect()->route('product.manage');
    }
    // unarchive product by id
    public function unarchiveProduct($id)
    {
        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $product->status = 1;
        $product->save();

        session()->flash('message', 'Product Unarchived');
        return redirect()->route('product.manage');
    }
    // delete product by id
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $product->delete();
        session()->flash('message', 'Product Deleted');
        return redirect()->route('product.manage');
    }

    // return id(key) and boolean(value) to identify product in cart
    private function inCart()
    {
        $inCart = [];
        $products = Product::where('user_id', Auth::id())->get();
        foreach ($products as $product) {
            $value = false;
            $productIds = $product->productCart->pluck('id');
            foreach ($product->productCart as $item) {
                if ($productIds->contains($item->id)) {
                    $value = true;
                }
            }
            $inCart[$product->id] = $value;
        }
        return $inCart;
    }
    // forcefill product edit field
    public function forceFill($id)
    {
        $product = Product::findOrFail($id);
        $this->title = $product->title;
        $this->tags = $product->tags;
    }

    public function setVisibility($id, $visibility)
    {
        $product = Product::findOrFail($id);
        abort_unless($product->isOwnedBy(Auth::user()), 403);
        $this->validate(['visibility' => 'nullable|in:public,private']);
        abort_unless(in_array($visibility, ['public', 'private'], true), 422);
        $product->update(['visibility' => $visibility]);
        session()->flash('message', 'Visibility Updated');
        return redirect()->route('product.manage');
    }

    public function render()
    {
        $search = '%' . $this->search . '%';
        $products = Product::where('user_id', Auth::id())->where('status', '!=', 2)->where('title', 'like', $search)->orderBy('status', 'desc')->get();
        $archives = Product::where('user_id', Auth::id())->where('status', 2)->get();
        $noArchives = $archives->isEmpty();
        $inCart = $this->inCart();

        return view('livewire.manage.manage-product', compact('products','archives','noArchives','inCart'));
    }
}
