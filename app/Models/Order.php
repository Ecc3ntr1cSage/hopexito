<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductOrder;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $casts = ['id' => 'string'];
    protected $fillable = [
        'id',
        'email',
        'name',
        'description',
        'delivery',
        'status',
        'amount',
        'tracking_number',
        'paid',
        'paid_at',
        'address',
        'postcode',
        'state',
        'phone'
    ];
    
    public function user(){
        return $this->hasOne(User::class, 'email', 'email');
    } 

    public function productOrder(){
        return $this->hasMany(ProductOrder::class, 'billplz_id', 'id');
    }
}
