@if ($sliders->isNotEmpty())
    <section class="slider__area shortcode-simple-slider shortcode-simple-slider-style-2" style="max-height: 500px">
    <div class="swiper-container slider_baner__active slider_baner_home6">
        <div class="swiper-wrapper">
            @foreach($sliders as $slider)
                <div class="swiper-slide slider__single">
                    <div class="slider__bg" @if($image = $slider->image) data-background="{{ RvMedia::getImageUrl($image) }}" @endif></div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="box-button-slider-bottom">
        <div class="container">
            <div class="testimonial__nav-four">
                <div class="testimonial-two-button-prev button-swiper-prev"><i class="flaticon-right-arrow"></i></div>
                <div class="testimonial-two-button-next button-swiper-next"><i class="flaticon-right-arrow"></i></div>
            </div>
        </div>
    </div>
</section>
@endif
