<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Product extends Model 
{

    use HasFactory;
    
    protected $table = 'products';
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'product_type',
        'visibility',
        'tags',
        'price',
        'commission_rate',
        'status',
        'sold',
        'preview',
        'preview_color',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission_rate' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Product $product): void {
            $product->deleteGeneratedMedia();
        });
    }

    protected $appends = [
        'shopname',
        'category',
        'color',
        'product_image',
        'product_image_2',
        'product_card_image',
        'product_card_hover_image',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    private function previewVariant(): ?ProductVariant
    {
        return $this->variants->firstWhere('color', $this->preview_color) ?? $this->variants->first();
    }

    public function getProductImageAttribute(): ?string
    {
        return $this->previewVariant()?->image_front_url;
    }

    public function getProductImage2Attribute(): ?string
    {
        return $this->previewVariant()?->image_back_url;
    }

    public function getProductCardImageAttribute(): ?string
    {
        $variant = $this->previewVariant();

        if (! $variant) {
            return null;
        }

        return (int) $this->preview === 1
            ? ($variant->image_back_url ?: $variant->image_front_url)
            : $variant->image_front_url;
    }

    public function getProductCardHoverImageAttribute(): ?string
    {
        $variant = $this->previewVariant();

        if (! $variant) {
            return null;
        }

        return (int) $this->preview === 1
            ? $variant->image_front_url
            : $variant->image_back_url;
    }

    public function getShopnameAttribute(): ?string
    {
        return $this->owner?->name;
    }

    public function getCategoryAttribute(): string
    {
        return config('catalog.types.'.$this->product_type.'.label', ucfirst((string) $this->product_type));
    }

    public function getColorAttribute(): string
    {
        return $this->variants->pluck('color')->implode(',');
    }

    public function getCommissionAttribute(): float
    {
        return round((float) $this->price * (float) $this->commission_rate, 2);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', '!=', 2)->public();
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user && (int) $this->user_id === (int) $user->id;
    }

    public function canBeViewedBy(?User $user): bool
    {
        return $this->visibility === 'public' || $this->isOwnedBy($user);
    }

    public function productOrder()
    {
        return $this->hasMany(ProductOrder::class, 'product_id', 'id')->orderByDesc('created_at');
    }

    public function deleteGeneratedMedia(): void
    {
        $paths = $this->variants()
            ->get(['image_front_path', 'image_back_path'])
            ->flatMap(fn (ProductVariant $variant): Collection => collect([
                $variant->image_front_path,
                $variant->image_back_path,
            ]))
            ->filter(fn (?string $path): bool => filled($path) && ! str_starts_with($path, 'mockups/'))
            ->unique()
            ->values();

        if ($paths->isNotEmpty()) {
            Storage::disk('public')->delete($paths->all());
        }

        Storage::disk('public')->deleteDirectory('products/'.$this->getKey());
    }
    public function productCart(){
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
