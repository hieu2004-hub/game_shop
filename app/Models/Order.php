<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'userID',
        'status',
        'delivery_method', // THÊM DÒNG NÀY
        'payment_method',  // THÊM DÒNG NÀY
        'payment_status', // Đảm bảo cột này có trong fillable
        'momo_order_id', // Thêm dòng này
        'momo_trans_id', // Thêm dòng này
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'orderID');
    }
}
