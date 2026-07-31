<?php

namespace App\Models;

use GuzzleHttp\Handler\Proxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCollection extends Model
{
    use HasFactory;

    protected $table = 'products_collection';
    protected $casts = ['id' => 'string'];
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'title',
        'collection_image'
    ];

    public function product(){
        return $this->hasMany(Product::class, 'collection_id','id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
