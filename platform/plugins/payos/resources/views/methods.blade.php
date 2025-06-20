@if (setting('payment_payos_status') == 1)
    <li class="list-group-item">
        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_payos"
               @if ($selecting == PAYOS_PAYMENT_METHOD_NAME) checked @endif
               value="payos" data-bs-toggle="collapse" data-bs-target=".payment_payos_wrap" data-toggle="collapse" data-target=".payment_payos_wrap" data-parent=".list_payment_method">
        <label for="payment_payos" class="text-start">{{ setting('payment_payos_name', trans('plugins/payos::payos.payment_via_payos')) }}</label>
        <div class="payment_payos_wrap payment_collapse_wrap collapse @if ($selecting == PAYOS_PAYMENT_METHOD_NAME) show @endif" style="padding: 15px 0;">
            <p>{!! BaseHelper::clean(setting('payment_payos_description')) !!}</p>
        </div>
    </li>
@endif
