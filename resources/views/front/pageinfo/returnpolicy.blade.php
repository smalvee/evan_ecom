@extends('front.layouts.app')

@section('content')
    <!-- Top Header -->
    <section style="background:#FC8934;color:#fff;" class="py-3">
        <div class="container text-center">
            <h1 class="fw-bold mb-1">Return Policy</h1>
            {{-- <p class="lead mb-0">Understand how to return products and get assistance</p> --}}
        </div>
    </section>

    <!-- Return Policy Content -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                  {!! $about_us->return_policy !!}

                </div>
            </div>
        </div>
    </section>

<br>
<br>
<br>
@endsection

@section('customJs')
@endsection
