<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    // Hàm handleMomoCallback giữ nguyên như cũ, không cần thay đổi
    public function handleMomoCallback(Request $request)
    {
        Log::info('Momo Callback Received:', $request->all());

        $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $signature = $request->input('signature');

        // Lấy chính xác các trường mà Momo gửi về để tạo chữ ký
        $partnerCode = $request->input('partnerCode');
        $orderId = $request->input('orderId');
        $requestId = $request->input('requestId');
        $amount = $request->input('amount');
        $orderInfo = $request->input('orderInfo');
        $orderType = $request->input('orderType');
        $transId = $request->input('transId');
        $resultCode = $request->input('resultCode');
        $message = $request->input('message');
        $payType = $request->input('payType');
        $responseTime = $request->input('responseTime');
        $extraData = $request->input('extraData'); // Dù là null/rỗng cũng phải có

        // Xây dựng chuỗi rawHash THEO ĐÚNG THỨ TỰ ALPHABET của các key Momo gửi về
        $rawHash = "accessKey=" . env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j') .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&message=" . $message .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType .
            "&partnerCode=" . $partnerCode .
            "&payType=" . $payType .
            "&requestId=" . $requestId .
            "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode .
            "&transId=" . $transId;

        // Tạo chữ ký dự kiến
        $expectedSignature = hash_hmac("sha256", $rawHash, $secretKey);

        Log::info('CALLBACK Raw Hash String for Verification:', ['string' => $rawHash]);
        Log::info('CALLBACK Signature from Momo:', ['signature' => $signature]);
        Log::info('CALLBACK Expected Signature on our side:', ['signature' => $expectedSignature]);

        if ($signature !== $expectedSignature) {
            Log::error('Momo Callback: Invalid signature');
            // Thử lại với một rawHash khác mà không có accessKey (một số tài liệu cũ yêu cầu vậy)
            // Đây là bước dự phòng cuối cùng
            $rawHashV2 = "amount=" . $amount .
                "&extraData=" . $extraData .
                "&message=" . $message .
                "&orderId=" . $orderId .
                "&orderInfo=" . $orderInfo .
                "&orderType=" . $orderType .
                "&partnerCode=" . $partnerCode .
                "&payType=" . $payType .
                "&requestId=" . $requestId .
                "&responseTime=" . $responseTime .
                "&resultCode=" . $resultCode .
                "&transId=" . $transId;
            $expectedSignatureV2 = hash_hmac("sha256", $rawHashV2, $secretKey);
            if ($signature !== $expectedSignatureV2) {
                return redirect()->route('checkout')->with('error', 'Giao dịch không hợp lệ. Chữ ký không khớp.');
            }
        }

        $resultCode = $request->input('resultCode');
        $momoOrderId = $request->input('orderId');

        $order = Order::where('momo_order_id', $momoOrderId)->first();

        if (!$order) {
            Log::error("Momo Callback: Order not found with momo_order_id: " . $momoOrderId);
            return; // Dừng lại nếu không tìm thấy đơn hàng
        }

        // Nếu một trong hai chữ ký khớp, xử lý kết quả
        if ($resultCode == '0') {
            $order = Order::where('momo_order_id', $orderId)->first();
            if ($order && $order->status == 'Chờ Thanh Toán') {
                $order->status = 'Chờ Xử Lý';
                $order->payment_status = 'Đã thanh toán'; // *** SỬA Ở ĐÂY ***
                $order->momo_trans_id = $transId;
                $order->save();
            }
            return redirect()->route('thankYou');
        } else {
            $order = Order::where('momo_order_id', $orderId)->first();
            if ($order && $order->status == 'Chờ Thanh Toán') {
                $order->status = 'Thanh toán thất bại';
                $order->payment_status = 'Thanh toán thất bại'; // *** SỬA Ở ĐÂY ***
                $order->save();
            }
            return redirect()->route('checkout')->with('error', 'Thanh toán thất bại: ' . $message);
        }
    }
}
