<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductInstance; // Import Model ProductInstance
use Carbon\Carbon; // Import Carbon để làm việc với ngày tháng

class CheckExpiredWarranties extends Command
{
    /**
     * Tên và chữ ký của lệnh console.
     *
     * @var string
     */
    protected $signature = 'warranty:check-expired';

    /**
     * Mô tả lệnh console.
     *
     * @var string
     */
    protected $description = 'Kiểm tra các bảo hành sản phẩm đã hết hạn và cập nhật trạng thái của chúng.';

    /**
     * Thực thi lệnh console.
     */
    public function handle()
    {
        $this->info('Đang kiểm tra các bảo hành đã hết hạn...');

        // Tìm tất cả các bản ghi ProductInstance có trạng thái 'active'
        // và ngày hết hạn bảo hành đã qua (nhỏ hơn hoặc bằng thời điểm hiện tại)
        $expiredInstances = ProductInstance::where('warranty_status', 'active')
            ->where('warranty_end_date', '<=', Carbon::now())
            ->get();

        $count = $expiredInstances->count();

        if ($count > 0) {
            $this->info("Tìm thấy {$count} bảo hành đã hết hạn. Đang cập nhật trạng thái...");

            // Duyệt qua từng bản ghi và cập nhật trạng thái
            foreach ($expiredInstances as $instance) {
                $instance->warranty_status = 'expired';
                $instance->save();
            }
            $this->info('Đã cập nhật thành công các bảo hành hết hạn.');
        } else {
            $this->info('Không tìm thấy bảo hành nào đã hết hạn.');
        }

        return Command::SUCCESS;
    }
}
