@extends('front.layouts.new_app')

@section('content')
    <!-- Breadcrumb Section Start -->
    <section class="breadcrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-contain">
                        <h2>Return & Refund</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">
                                        <i class="fa-solid fa-house"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Return & Refund</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Fresh Vegetable Section Start -->
    <section class="fresh-vegetable-section section-lg-space">
        <div class="container-fluid-lg">
            <div>
                <h2>Return Policy</h2>
            </div>
            <hr>




            <div class="col-md-12">
                <div class="fresh-contain p-center">
                    <div>
                        <div class="delivery-list">
                            <div class="rich-content">{!! \App\Support\HtmlSanitizer::clean($about_us->return_policy) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="fresh-vegetable-section section-lg-space">
        <div class="container-fluid-lg">
            <div>
                <h2>Refund Policy</h2>
            </div>
            <hr>
            <div class="col-md-12">
                <div class="fresh-contain p-center">
                    <div>
                        <div class="delivery-list">
                            <div class="rich-content">{!! \App\Support\HtmlSanitizer::clean($about_us->return_policy) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <br>


    <section class="fresh-vegetable-section section-lg-space">
        <div class="container-fluid-lg">




            <div class="col-md-12">
                <div class="fresh-contain p-center">
                    <div>
                        <div class="delivery-list">
                            <div class="rich-content">{!! \App\Support\HtmlSanitizer::clean($about_us->refund_policy) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- Fresh Vegetable Section End -->
@endsection

@section('customJs')
@endsection
