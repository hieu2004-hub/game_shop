<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckExpiredWarranties; // Import lệnh của bạn

class Kernel extends ConsoleKernel
{
    /**
     * Các lệnh Artisan được cung cấp bởi ứng dụng của bạn.
     *
     * @var array
     */
    protected $commands = [
        CheckExpiredWarranties::class, // Đăng ký lệnh của bạn ở đây
    ];

    /**
     * Định nghĩa lịch trình lệnh của ứng dụng.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Chạy lệnh 'warranty:check-expired' mỗi ngày vào lúc 1:00 sáng
        $schedule->command('warranty:check-expired')->dailyAt('01:00');

        // Bạn cũng có thể thêm ghi log cho đầu ra của lệnh (tùy chọn)
        // $schedule->command('warranty:check-expired')
        //          ->dailyAt('01:00')
        //          ->sendOutputTo(storage_path('logs/warranty_check.log'));
    }

    /**
     * Đăng ký các lệnh cho ứng dụng.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
