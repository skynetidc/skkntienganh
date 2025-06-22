@php $payPalStatus = setting('payment_payos_status'); @endphp
<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col"><i class="fa fa-theme-payments"></i></td>
        <td style="width: 20%;">
            <img class="filter-black" src="{{ url('vendor/core/plugins/payos/images/payos.svg') }}" alt="payos">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://payos.vn" target="_blank">payos</a>
                    <p>{{ trans('plugins/payos::payos.payos_description') }}</p>
                </li>
            </ul>
        </td>
    </tr>
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-start" style="margin-top: 5px;">
                <div class="payment-name-label-group  @if ($payPalStatus== 0) hidden @endif">
                    <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span> <label class="ws-nm inline-display method-name-label">{{ setting('payment_payos_name') }}</label>
                </div>
            </div>
            <div class="float-end">
                <a class="btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger @if ($payPalStatus == 0) hidden @endif">{{ trans('plugins/payment::payment.edit') }}</a>
                <a class="btn btn-secondary toggle-payment-item save-payment-item-btn-trigger @if ($payPalStatus == 1) hidden @endif">{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="payos-online-payment payment-content-item hidden">
        <td class="border-left" colspan="3">
            {!! Form::open() !!}
            {!! Form::hidden('type', PAYOS_PAYMENT_METHOD_NAME, ['class' => 'payment_type']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <ul>
                        <li>
                            <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => 'payos']) }}</label>
                        </li>
                        <li class="payment-note">
                            <p>{{ trans('plugins/payment::payment.configuration_requirement', ['name' => 'payos']) }}:</p>
                            <ul class="m-md-l" style="list-style-type:decimal">
                                <li style="list-style-type:decimal">
                                    <a href="https://doitac.payos.vn/login" target="_blank">
                                        {{ trans('plugins/payment::payment.service_registration', ['name' => 'payos']) }}
                                    </a>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{ trans('plugins/payos::payos.after_service_registration_msg', ['name' => 'payos']) }}</p>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{ trans('plugins/payos::payos.enter_client_id_and_secret') }}</p>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{!! trans('plugins/payos::payos.send_ipn_url_to_payos', ['url' => route('payments.payos.ipn')]) !!}</p>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="well bg-white">
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payos_name">{{ trans('plugins/payment::payment.method_name') }}</label>
                            <input type="text" class="next-input input-name" name="payment_payos_name" id="payos_name" data-counter="400" value="{{ setting('payment_payos_name', trans('plugins/payment::payment.pay_online_via', ['name' => 'payos'])) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payment_payos_description">{{ trans('core/base::forms.description') }}</label>
                            <textarea class="next-input" name="payment_payos_description" id="payment_payos_description">{{ get_payment_setting('description', 'payos', __('Bạn sẽ được chuyển hướng đến payos để hoàn tất thanh toán.')) }}</textarea>
                        </div>
                        <p class="payment-note">
                            {{ trans('plugins/payment::payment.please_provide_information') }} <a target="_blank" href="https://payos.vn">payos</a>:
                        </p>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payos_client_url">{{ trans('plugins/payos::payos.payos_url') }}</label>
                            <input type="text" class="next-input" name="payment_payos_client_url" id="payos_client_url" value="{{ setting('payment_payos_client_url') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payment_payos_tmncode">{{ trans('plugins/payos::payos.payos_tmncode') }}</label>
                            <input type="text" class="next-input" name="payment_payos_tmncode" id="payment_payos_tmncode" value="{{ setting('payment_payos_tmncode') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payment_payos_secret">{{ trans('plugins/payos::payos.payos_secret') }}</label>
                            <div class="input-option">
                                <input type="text" class="next-input" placeholder="••••••••" id="payment_payos_secret" name="payment_payos_secret" value="{{ setting('payment_payos_secret') }}">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payment_payos_mode">{{ trans('plugins/payos::payos.payos_mode') }}</label>
                            <div class="input-option">
                                <select name="payment_payos_mode" class="next-input">
                                    <option {{ setting('payment_payos_mode') == 0 ? 'selected' : ''}} value="0">{{ __('No') }}</option>
                                    <option {{ setting('payment_payos_mode') == 1 ? 'selected' : '' }} value="1">{{ __('Yes') }}</option>
                                </select>
                            </div>
                        </div>
                        {!! apply_filters(PAYMENT_METHOD_SETTINGS_CONTENT, null, 'payos') !!}
                    </div>
                </div>
            </div>
            <div class="col-12 bg-white text-end">
                <button class="btn btn-warning disable-payment-item @if ($payPalStatus == 0) hidden @endif" type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                <button class="btn btn-info save-payment-item btn-text-trigger-save @if ($payPalStatus == 1) hidden @endif" type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                <button class="btn btn-info save-payment-item btn-text-trigger-update @if ($payPalStatus == 0) hidden @endif" type="button">{{ trans('plugins/payment::payment.update') }}</button>
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    </tbody>
</table>
