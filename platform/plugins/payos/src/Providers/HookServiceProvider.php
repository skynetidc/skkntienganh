<?php

namespace Binjuhor\PayOs\Providers;

use Botble\Payment\Enums\PaymentMethodEnum;
use Collective\Html\HtmlFacade as Html;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Botble\Payment\Facades\PaymentMethods;
use Binjuhor\PayOs\Services\Gateways\PayOsPaymentService;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerPayOsMethod'], 2, 2);

        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithPayOs'], 2, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 2);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['PAYOS'] = PAYOS_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYOS_PAYMENT_METHOD_NAME) {
                $value = 'PayOs';
            }

            return $value;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYOS_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 2, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == PAYOS_PAYMENT_METHOD_NAME) {
                $data = PayOsPaymentService::class;
            }

            return $data;
        }, 2, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . view('plugins/payos::settings')->render();
    }

    public function registerPayOsMethod(?string $html, array $data): string
    {
        PaymentMethods::method(PAYOS_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/payos::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithPayOs(array $data, Request $request): array
    {
        if ($request->input('payment_method') == PAYOS_PAYMENT_METHOD_NAME) {
            $currentCurrency = get_application_currency();

            $currencyModel = $currentCurrency->replicate();

            $payOSService = $this->app->make(PayOsPaymentService::class);

            $supportedCurrencies = $payOSService->supportedCurrencyCodes();

            $currency = strtoupper($currentCurrency->title);

            $notSupportCurrency = false;

            if (! in_array($currency, $supportedCurrencies)) {
                $notSupportCurrency = true;

                if (! $currencyModel->where('title', 'VND')->exists()) {
                    $data['error'] = true;
                    $data['message'] = __(":name doesn't support :currency. List of currencies supported by :name: :currencies.", [
                        'name' => 'PayOs',
                        'currency' => $currency,
                        'currencies' => implode(', ', $supportedCurrencies),
                    ]);

                    return $data;
                }
            }

            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

            if ($notSupportCurrency) {
                $usdCurrency = $currencyModel->where('title', 'VND')->first();

                $paymentData['currency'] = 'VND';
                if ($currentCurrency->is_default) {
                    $paymentData['amount'] = $paymentData['amount'] * $usdCurrency->exchange_rate;
                } else {
                    $paymentData['amount'] = format_price($paymentData['amount'], $currentCurrency, true);
                }
            }

            $paymentData['callback_url'] = route('payments.payos.callback');

            $YOUR_DOMAIN = $request->getSchemeAndHttpHost();
//            $data = [
//                "orderCode" => intval(substr(strval(microtime(true) * 10000), -6)),
//                "amount" => $paymentData['amount'],
//                "description" => "Thanh toán đơn hàng",
//                "returnUrl" => $YOUR_DOMAIN . "/payment/payos/callback",
//                "cancelUrl" => $YOUR_DOMAIN . "/payment/payos/callback"
//            ];
            $paymentData['orderCode'] = intval(substr(strval(microtime(true) * 10000), -6));
            $paymentData['description'] = "Thanh toán đơn hàng";
            $paymentData['returnUrl'] = $YOUR_DOMAIN . "/payment/payos/callback";
            $paymentData['cancelUrl'] = $YOUR_DOMAIN . "/payment/payos/callback";

            $response = $payOSService->makePayment($paymentData);

            if ($response) {
                $data['checkoutUrl'] = $response;
                $data['error'] = false;
                $data['message'] = '';
//                $data['message'] = $paymentData['checkout_token'];
            } else {
                $data['error'] = true;
                $data['message'] = __('Something went wrong. Please try again later.');
            }
            return $data;
        }

        return $data;
    }
}
