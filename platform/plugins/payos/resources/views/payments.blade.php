<ul>
    @foreach($payments->payments as $payment)
        <li>
            @include('plugins/payos::detail', compact('payment'))
        </li>
    @endforeach
</ul>
