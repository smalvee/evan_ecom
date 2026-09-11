@extends('front.layouts.new_app')

@section('content')

    <!-- Breadcrumb Section Start -->
    <section class="breadcrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-contain">
                        <h2>About Us</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.html">
                                        <i class="fa-solid fa-house"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">About Us</li>
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
           
                

                <div class="col-md-12">
                    <div class="fresh-contain p-center">
                        <div>    
                            <div class="delivery-list">
                               {!! $about_us->who_we_are !!}                                
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
