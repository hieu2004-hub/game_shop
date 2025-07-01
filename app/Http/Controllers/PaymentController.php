<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductWarehouseStock;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Hàm thực thi một yêu cầu POST bằng cURL, đã thêm kiểm tra lỗi.
     */
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($data)]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        // Bỏ xác minh SSL (chỉ dành cho môi trường local/test, không dùng cho production)
        // Điều này rất hữu ích nếu máy local của bạn có vấn đề về chứng chỉ SSL.
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        // Kiểm tra nếu có lỗi cURL xảy ra
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            // Trả về một mảng lỗi để HomeController có thể xử lý
            return ['curl_error' => $error_msg];
        }

        curl_close($ch);
        return $result;
    }

    public function createMomoPaymentRequest($momoOrderId, $amount, $internalOrderId)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        // Lấy thông tin từ .env hoặc hard-code để chắc chắn
        $partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
        $accessKey = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
        $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');

        $requestId = Str::random(20);
        $safeOrderInfo = "ThanhToanDonHang" . $internalOrderId;
        $ipnUrl = route('momo.callback');
        $redirectUrl = route('momo.callback');
        $extraData = "";
        $requestType = "payWithATM";
        $amountStr = (string) $amount;

        // --- BẮT ĐẦU PHẦN QUAN TRỌNG ---
        // XÂY DỰNG RAW HASH THỦ CÔNG

        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amountStr .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $momoOrderId .
            "&orderInfo=" . $safeOrderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        // Tạo chữ ký
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        // Mảng dữ liệu gửi đi
        $requestData = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'Game Shop',
            'storeId' => 'GameShopStore',
            'requestId' => $requestId,
            'amount' => $amountStr,
            'orderId' => $momoOrderId,
            'orderInfo' => $safeOrderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        // Ghi log lại để so sánh lần cuối
        Log::info('MANUAL Raw hash string:', ['rawHash' => $rawHash]);
        Log::info('MANUAL Sending data to Momo:', $requestData);

        $resultJson = $this->execPostRequest($endpoint, json_encode($requestData));

        if (is_array($resultJson) && isset($resultJson['curl_error'])) {
            Log::error('cURL Error when calling Momo API: ', $resultJson);
            return $resultJson;
        }

        return json_decode($resultJson, true);
    }

    /**
     * Xử lý callback từ Momo sau khi người dùng thanh toán.
     */
    public function handleMomoCallback(Request $request)
    {
        Log::info('Momo Callback Received:', $request->all());

        // --- BƯỚC 1: XÁC THỰC CHỮ KÝ ---
        $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $signature = $request->input('signature');
        $rawHash = "accessKey=" . env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j') .
            "&amount=" . $request->input('amount') .
            "&extraData=" . $request->input('extraData') .
            "&message=" . $request->input('message') .
            "&orderId=" . $request->input('orderId') .
            "&orderInfo=" . $request->input('orderInfo') .
            "&orderType=" . $request->input('orderType') .
            "&partnerCode=" . $request->input('partnerCode') .
            "&payType=" . $request->input('payType') .
            "&requestId=" . $request->input('requestId') .
            "&responseTime=" . $request->input('responseTime') .
            "&resultCode=" . $request->input('resultCode') .
            "&transId=" . $request->input('transId');

        $expectedSignature = hash_hmac("sha256", $rawHash, $secretKey);

        if ($signature !== $expectedSignature) {
            Log::error('Momo Callback: Invalid signature');
            return redirect()->route('checkout')->with('error', 'Giao dịch không hợp lệ. Chữ ký không khớp.');
        }

        // --- BƯỚC 2: TÌM ĐƠN HÀNG VÀ KIỂM TRA ---
        $resultCode = $request->input('resultCode');
        $momoOrderId = $request->input('orderId');
        $order = Order::with('orderItems')->where('momo_order_id', $momoOrderId)->first();

        if (!$order) {
            return redirect()->route('checkout')->with('error', 'Không tìm thấy đơn hàng tương ứng.');
        }
        if ($order->status !== 'Chờ Thanh Toán') {
            return redirect()->route($order->payment_status === 'Đã thanh toán' ? 'thankYou' : 'checkout');
        }

        // --- BƯỚC 3: XỬ LÝ KẾT QUẢ ---
        if ($resultCode == '0') {
            // --- THANH TOÁN THÀNH CÔNG ---
            DB::transaction(function () use ($request, $order) {
                $order->status = 'Chờ Xử Lý';
                $order->payment_status = 'Đã thanh toán';
                $order->momo_trans_id = $request->input('transId');
                $order->save();

                $productIdsInOrder = $order->orderItems->pluck('productID')->toArray();
                Cart::where('userID', $order->userID)->whereIn('productID', $productIdsInOrder)->delete();
            });

            Log::info("Momo Payment Success for Order ID: " . $order->id);
            return redirect()->route('thankYou');
        } else {
            // --- THANH TOÁN THẤT BẠI HOẶC BỊ HỦY ---
            Log::warning("Momo Payment Failed/Cancelled for Order ID: " . $order->id . ". ResultCode: " . $resultCode);

            DB::transaction(function () use ($order) {
                // 1. Hoàn trả lại số lượng về kho
                foreach ($order->orderItems as $item) {
                    ProductWarehouseStock::where('product_id', $item->productID)
                        ->where('warehouse_id', $item->warehouse_id_at_sale)
                        ->where('batch_identifier', $item->batch_identifier_at_sale)
                        ->increment('quantity', $item->quantity);
                }

                // 2. XÓA HOÀN TOÀN ĐƠN HÀNG
                // Vì đã thiết lập "onDelete('cascade')" trong migration,
                // việc xóa order sẽ tự động xóa các order_items và product_instances liên quan.
                $order->delete();
                Log::info("Order ID {$order->id} and related items have been deleted due to payment failure.");
            });

            return redirect()->route('checkout')->with('error', 'Thanh toán đã bị hủy hoặc thất bại. Vui lòng thử lại.');
        }
    }
}
