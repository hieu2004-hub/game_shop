<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductWarehouseStock;
use App\Models\ProductInstance;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'productName',
        'productPrice',
        'productImage',
        'productBrand', // Đảm bảo các trường khác cũng có
        'productCategory',
        'productDescription',
        'is_warrantable', // Đảm bảo trường này có
        'default_warranty_months' // ĐẢM BẢO TRƯỜNG NÀY CÓ Ở ĐÂY
    ];

    public function reduceStock($quantity)
    {
        if ($this->stockQuantity >= $quantity) {
            $this->stockQuantity -= $quantity;
            $this->save();
            return true; // Stock reduced successfully
        }
        return false; // Not enough stock
    }

    public function increaseStock($quantity)
    {
        $this->stockQuantity += $quantity;
        $this->save();
        return true; // Stock increased successfully
    }

    // Mối quan hệ với số lượng tồn kho theo kho và lô
    public function warehouseStocks()
    {
        return $this->hasMany(ProductWarehouseStock::class);
    }

    // Accessor để lấy tổng số lượng tồn kho của sản phẩm trên tất cả các kho
    public function getTotalStockQuantityAttribute()
    {
        return $this->warehouseStocks->sum('quantity');
    }

    // Accessor để lấy giá nhập trung bình (đơn giản)
    // Đây là một cách đơn giản, không phải là FIFO/LIFO chuẩn
    public function getAverageImportPriceAttribute()
    {
        $totalCost = 0;
        $totalQuantity = 0;

        foreach ($this->warehouseStocks as $stock) {
            $totalCost += $stock->import_price * $stock->quantity;
            $totalQuantity += $stock->quantity;
        }

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }

    public function productInstances()
    {
        return $this->hasMany(ProductInstance::class);
    }
}
