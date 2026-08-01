<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $appends = ['shopname', 'category', 'color', 'product_image', 'product_image_2'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getProductImageAttribute(): ?string
    {
        $variant = $this->variants->firstWhere('color', $this->preview_color) ?? $this->variants->first();

        return $variant?->image_front_url;
    }

    public function getProductImage2Attribute(): ?string
    {
        $variant = $this->variants->firstWhere('color', $this->preview_color) ?? $this->variants->first();

        return $variant?->image_back_url;
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
    public function productCart(){
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
