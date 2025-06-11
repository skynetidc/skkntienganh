<?php

use Binjuhor\VNPay\Http\Controllers\PayOSController;

Route::group(['controller' => PayOSController::class, 'middleware' => ['web', 'core']], function () {
    Route::get('payment/payos/callback', 'getCallback')->name('payments.vnpay.callback');
    Route::get('payment/payos/ipn', 'getIPN')->name('payments.vnpay.ipn');
});
