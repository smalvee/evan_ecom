@extends('front.layouts.app')

@section('content')
    <!-- Top Header -->
    <section style="background:#FC8934;color:#fff;" class="py-3">
        <div class="container text-center">
            <h1 class="fw-bold mb-1">Refund Policy</h1>
            {{-- <p class="lead mb-0">Learn about our refund and return policies</p> --}}
        </div>
    </section>

    <!-- Refund Policy Content -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                  {!! $about_us->refund_policy !!}

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
