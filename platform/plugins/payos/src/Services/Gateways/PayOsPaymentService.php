<?php

namespace Binjuhor\PayOs\Services\Gateways;

use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use PayOS\PayOS;

class PayOsPaymentService
{
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected PayOS $payOS;

    public function __construct()
    {
        $this->partnerCode = env("PAYOS_CLIENT_ID");
        $this->accessKey = env("PAYOS_API_KEY");
        $this->secretKey = env("PAYOS_CHECKSUM_KEY");


        $this->payOS = new PayOS(
            env("PAYOS_CLIENT_ID"),
            env("PAYOS_API_KEY"),
            env("PAYOS_CHECKSUM_KEY")
        );
    }

    public function makePayment(array $data)
    {
        error_log($data['orderCode']);
        try {
            $response = $this->payOS->createPaymentLink($data);
            return $response['checkoutUrl'];
        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    public function afterMakePayment(Request $request)
    {
    }

    public function execPostRequest($url, $data): bool|string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function storedData(array $data): void
    {
        $chargeId = $data['orderId'];
        $order = Order::find($data['extraData']);

        if($order !== NULL) {
            $customer = $order->user;
            do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                'amount' => $data['amount'],
                'currency' => 'VND',
                'charge_id' => $chargeId,
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'customer_type' => get_class($customer),
                'payment_channel' => MOMO_PAYMENT_METHOD_NAME,
                'status' => $data['resultCode'] == '0' ? PaymentStatusEnum::COMPLETED : PaymentStatusEnum::PENDING,
            ]);
        }
    }

    public function getPaymentStatus($request): string
    {
        $accessKey = $this->accessKey;
        $secretKey = $this->secretKey;
        $partnerCode = $request->partnerCode;
        $orderId = $request->orderId;
        $requestId = $request->requestId;
        $amount = $request->amount;
        $orderInfo = $request->orderInfo;
        $orderType = $request->orderType;
        $transId = $request->transId;
        $resultCode = $request->resultCode;
        $message = $request->message;
        $payType = $request->payType;
        $responseTime = $request->responseTime;
        $extraData = $request->extraData;
        $m2signature = $request->signature; //MoMo signate

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&message=" . $message . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType . "&partnerCode=" . $partnerCode . "&payType=" . $payType . "&requestId=" . $requestId . "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode . "&transId=" . $transId;

        $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);

        if ($m2signature == $partnerSignature) {
            if ($resultCode == '0') {
                $result = 'success';
            } else {
                $result = 'error';
            }
        } else {
            $result = 'hacked';
        }

        return $result;
    }

    public function supportedCurrencyCodes(): array
    {
        return ['VND'];
    }

    /**
     * This function run on production for IPN
     * Check more here: https://sandbox.momoment.vn/apis/docs/huong-dan-tich-hop/#code-ipn-url
     *
     * @param $request
     * @return string[]
     */
    public function afterPayment( $request ): array
    {
        $paymentStatus = $this->getPaymentStatus($request);
        switch ($paymentStatus) {
            case 'success':
                $response['message'] = 'Capture Payment Success';
                break;
            case 'error':
                $response['message'] = 'Capture Payment Error';
                break;
            case 'hacked':
                $response['message'] = 'This transaction could be hacked, please check your signature and returned signature';
                break;
        }

        return $response;
    }

    public function getToken(array $data)
    {
        $order = Order::find($data['extraData']);
        return $order->token;
    }
}
