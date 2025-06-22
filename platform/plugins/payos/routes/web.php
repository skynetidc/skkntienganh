<?php

use Binjuhor\PayOs\Http\Controllers\PayOSController;

Route::group(['controller' => PayOSController::class, 'middleware' => ['web', 'core']], function () {
    Route::get('payment/payos/callback', 'getCallback')->name('payments.payos.callback');
    Route::get('payment/payos/ipn', 'getIPN')->name('payments.payos.ipn');
});
