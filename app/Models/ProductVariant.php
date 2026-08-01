<?php

namespace App\Models;

use App\Support\MockupAssets;
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
        return $this->imageUrl($this->image_front_path);
    }

    public function getImageBackUrlAttribute(): ?string
    {
        if (! $this->image_back_path) {
            return null;
        }
        return $this->imageUrl($this->image_back_path);
    }

    private function imageUrl(?string $path): string
    {
        if (! $path || ! str_starts_with($path, 'mockups/')) {
            return asset('storage/'.$path);
        }

        if (is_file(public_path($path))) {
            return asset($path);
        }

        $migratedPath = MockupAssets::migrateLegacyPath($path);

        return $migratedPath && is_file(public_path($migratedPath))
            ? asset($migratedPath)
            : asset($path);
    }
}
