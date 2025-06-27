<?php

namespace Binjuhor\PayOs\Http\Controllers;

use Binjuhor\PayOs\Http\Requests\PayOsPaymentIPNRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Binjuhor\PayOs\Http\Requests\PayOsPaymentCallbackRequest;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;
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
        Request $request,
        PayOsPaymentService         $payOsPaymentService,
        BaseHttpResponse            $response
    ) {
        $status = $payOsPaymentService->getPaymentStatus($request);
        $code = $request->query('code');
        $id = $request->query('id');
        $orderCode = $request->query('orderCode');
        $cancel = $request->query('cancel');

        if ($status == 'CANCELLED') {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage('Cancel payment!');
        }

        $inputData = array(
            "id" => $id,
            "code" => $code,
            "status" => $status,
            "cancel" => $cancel,
            "orderCode" => $orderCode
        );

        if(setting('payment_payos_mode') == 1) {
            $payOsPaymentService->afterMakePayment($inputData);
        }

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL($id))
            ->setMessage(__('Checkout successfully!'));
    }

    /**
     * Get IPN from PayOs
     *
     * @param PayOsPaymentIPNRequest $request
     * @param PayOsPaymentService $payOsPaymentService
     * @return void
     */
    public function return(
        Request $request,
        PayOsPaymentService         $payOsPaymentService,
        BaseHttpResponse            $response
    ) {
        $status = $payOsPaymentService->getPaymentStatus($request);
        $code = $request->query('code');
        $id = $request->query('id');
        $orderCode = $request->query('orderCode');
        $cancel = $request->query('cancel');

        if ($status == 'CANCELLED') {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage('Cancel payment!');
        }

        $inputData = array(
            "id" => $id,
            "code" => $code,
            "status" => $status,
            "cancel" => $cancel,
            "orderCode" => $orderCode
        );

        $payOsPaymentService->afterMakePayment($inputData);

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL($id))
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
        if($request->has('vnp_SecureHash')) {
            return response()->json($payOsPaymentService->storedData($request->input()));
        }

        return response()->json([
            'RspCode' => '99',
            'Message' => 'Invalid Parameters'
        ]);
    }
}
