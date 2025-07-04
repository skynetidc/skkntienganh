<div class="container bb-product-detail" id="bb-product-detail">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
            @include(EcommerceHelper::viewPath('includes.product-gallery'))
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
            <div class="bb-product-page-content">
                <h2 class="product-title mb-2">{{ $product->name }}</h2>
                @if (EcommerceHelper::isReviewEnabled())
                    @include(EcommerceHelper::viewPath('includes.rating'))
                @endif

                @include(EcommerceHelper::viewPath('includes.product-price'))

                {!! apply_filters('ecommerce_before_product_description', null, $product) !!}
                {!! apply_filters('ecommerce_after_product_description', null, $product) !!}
                <div class="text-warning"></div>
                <form class="single-variation-wrap" data-bb-toggle="product-form" action="{{ route('public.cart.add-to-cart') }}" method="post">
                    @csrf
                    <div class="row product-filters">
                        @if ($product->variations()->count() > 0)
                            {!! render_product_swatches($product, [
                                'selected' => $selectedAttrs,
                            ]) !!}
                        @endif
                    </div>

                    {!! render_product_options($product) !!}

                    {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null) !!}
                    <input
                        id="hidden-product-is_out_of_stock"
                        name="product_is_out_of_stock"
                        type="hidden"
                        value="{{ $product->isOutOfStock() }}"
                    />
                    <input
                        id="hidden-product-id"
                        name="id"
                        type="hidden"
                        value="{{ $product->id }}"
                    />

                    <div class="d-flex gap-4 mb-3">
                        @include(EcommerceHelper::viewPath('includes.product-quantity'))
                        <button
                            type="submit"
                            name="add-to-cart"
                            class="bb-product-details-add-to-cart-btn btn btn-primary bb-btn-product-actions-icon"
                            @disabled($product->isOutOfStock())
                            data-bb-toggle="add-to-cart-in-form"
                            {!! EcommerceHelper::jsAttributes('add-to-cart-in-form', $product) !!}
                        >
                            <x-core::icon name="ti ti-shopping-cart"/>
                            {{ __('Add To Cart') }}
                        </button>
                    </div>
                    <div class="d-flex gap-4 mb-3">
                        <div class="cart-checkout-proceed mt-3">
                            <a href="{{ route('public.checkout.information', OrderHelper::getOrderSessionToken()) }}" data-bb-toggle="cart-checkout" class="cart-checkout-btn w-100 btn btn-primary">
                                {{ __('Proceed to Checkout') }}
                            </a>
                        </div>
                    </div>
                </form>
                <div class="bb-product-meta">
                    <span>
                        <span id="is-out-of-stock">{{ !$product->isOutOfStock() ? __('In stock') : __('Out of stock') }}</span>
                    </span>
                    @if (!$product->categories->isEmpty())
                        <span>{{ __('Categories') }} :
                        @foreach ($product->categories as $category)
                            <a href="{{ $category->url }}"> {{ $category->name }}
                                @if (!$loop->last), @endif
                            </a>
                        @endforeach
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
