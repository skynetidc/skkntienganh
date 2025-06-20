<?php

namespace PayOs\Http\Controllers;

use PayOs\Http\Requests\PayOsPaymentIPNRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use PayOs\Http\Requests\PayOsPaymentCallbackRequest;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Routing\Controller;
use Binjuhor\PayOs\Services\Gateways\PayOsPaymentService;

class PayOSController extends Controller
{
    /**
     * Get callback from PayOs
     *
     * @param PayOsPaymentCallbackRequest $request
     * @param PayOsPaymentService $payOsPaymentService
     * @param BaseHttpResponse $response
     * @return void
     */
    public function getCallback(
        PayOsPaymentCallbackRequest $request,
        PayOsPaymentService         $payOsPaymentService,
        BaseHttpResponse            $response
    ) {
        $status = $payOsPaymentService->getPaymentStatus($request);
        $token = null;

        if (! $status) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed!'));
        }

        if(setting('payment_payos_mode') == 0) {
            $payOsPaymentService->afterMakePayment($request->input());
        }

        if(setting('payment_payos_mode') == 1) {
            $token = $payOsPaymentService->getToken($request->input());
        }

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL($token))
            ->setMessage(__('Checkout successfully!'));
    }

    /**
     * Get IPN from PayOs
     *
     * @param PayOsPaymentIPNRequest $request
     * @param PayOsPaymentService $payOsPaymentService
     * @return void
     */
    public function getIPN(
        PayOsPaymentIPNRequest $request,
        PayOsPaymentService $payOsPaymentService
    ) {
//        if($request->has('vnp_SecureHash')) {
//            return response()->json($payOsPaymentService->storeData($request->input()));
//        }

        return response()->json([
            'RspCode' => '99',
            'Message' => 'Invalid Parameters'
        ]);
    }
}
