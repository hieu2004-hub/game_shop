<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderID',
        'productID',
        'quantity',
        'price',
        'import_price_at_sale',
        'batch_identifier_at_sale',
        'warehouse_id_at_sale',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'orderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }
    // ĐỊNH NGHĨA MỐI QUAN HỆ 'warehouse' TẠI ĐÂY
    public function warehouse()
    {
        // Mối quan hệ này sẽ trỏ đến bảng 'warehouses' thông qua cột 'warehouse_id_at_sale'
        return $this->belongsTo(Warehouse::class, 'warehouse_id_at_sale');
    }

    // Accessor để tính lợi nhuận cho từng mặt hàng (nếu bạn đã thêm các cột giá nhập)
    public function getProfitAttribute()
    {
        // Kiểm tra xem import_price_at_sale có tồn tại không
        // Nếu không, có thể fallback về giá nhập trung bình từ product
        $cost = $this->import_price_at_sale ?? ($this->product ? $this->product->average_import_price : 0);
        return ($this->price - $cost) * $this->quantity;
    }
    // THÊM MỐI QUAN HỆ NÀY
    public function productInstances()
    {
        return $this->hasMany(ProductInstance::class, 'order_item_id');
    }
}
