<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_item_id',
        'user_id',
        'serial_number',
        'purchase_date',
        'warranty_duration_months',
        'warranty_start_date',
        'warranty_end_date',
        'warranty_status',
        'notes',
        'proof_of_purchase_path',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'purchase_date',
        'warranty_start_date',
        'warranty_end_date',
        'created_at',
        'updated_at',
    ];

    // Mối quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Mối quan hệ với User (chủ sở hữu)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor để kiểm tra trạng thái bảo hành còn hạn
    public function getIsWarrantyActiveAttribute()
    {
        // Chỉ kiểm tra nếu có ngày bắt đầu và ngày kết thúc bảo hành
        if ($this->warranty_start_date && $this->warranty_end_date) {
            // Đảm bảo warranty_end_date là đối tượng Carbon.
            // Nếu vì lý do nào đó nó vẫn là chuỗi, hãy cố gắng parse lại.
            $warrantyEndDate = $this->warranty_end_date instanceof Carbon ? $this->warranty_end_date : Carbon::parse($this->warranty_end_date);
            if (!$warrantyEndDate instanceof Carbon) {
                try {
                    $warrantyEndDate = Carbon::parse($warrantyEndDate);
                } catch (\Exception $e) {
                    // Log lỗi hoặc xử lý nếu chuỗi ngày tháng không hợp lệ
                    \Log::error("Could not parse warranty_end_date: " . $this->warranty_end_date . " for ProductInstance ID: " . $this->id);
                    return false; // Không thể xác định trạng thái nếu ngày tháng không hợp lệ
                }
            }

            return $this->warranty_status === 'active' && $warrantyEndDate->isFuture();
        }
        return false;
    }
}
