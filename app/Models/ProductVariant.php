<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'image_front_path',
        'image_back_path',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageFrontUrlAttribute(): string
    {
        return str_starts_with($this->image_front_path, 'mockups/')
            ? asset($this->image_front_path)
            : asset('storage/'.$this->image_front_path);
    }

    public function getImageBackUrlAttribute(): ?string
    {
        if (! $this->image_back_path) {
            return null;
        }
        return str_starts_with($this->image_back_path, 'mockups/')
            ? asset($this->image_back_path)
            : asset('storage/'.$this->image_back_path);
    }
}
