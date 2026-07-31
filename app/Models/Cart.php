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
}
