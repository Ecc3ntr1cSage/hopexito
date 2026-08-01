<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $casts = ['id' => 'string'];
    protected $fillable = [
        'id',
        'product_id',
        'email',
        'title',
        'quantity',
        'price',
        'subtotal',
        'weight',
        'size',
        'color',
    ];

    public function cartProduct(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getDisplayImageAttribute(): ?string
    {
        $product = $this->cartProduct;
        $variant = $product?->variants->firstWhere('color', $this->color);

        if (! $variant) {
            return $product?->product_card_image;
        }

        return (int) $product->preview === 1
            ? ($variant->image_back_url ?: $variant->image_front_url)
            : $variant->image_front_url;
    }
}
