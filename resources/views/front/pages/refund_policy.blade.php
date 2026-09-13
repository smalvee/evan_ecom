@extends('front.layouts.new_app')

@section('content')
    <section class="breadcrumb-section pt-0">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-contain">
                        <h2>Refund Policy</h2>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('front.home') }}"><i class="fa-solid fa-house"></i></a>
                                </li>
                                <li class="breadcrumb-item active">Refund Policy</li>
                            </ol>
                        </nav>
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
                            <div class="rich-content">{!! \App\Support\HtmlSanitizer::clean($about_us->refund_policy) !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
@endsection
