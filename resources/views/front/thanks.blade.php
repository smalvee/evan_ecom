@php
    use Gloudemans\Shoppingcart\Facades\Cart;
@endphp

@extends('front.layouts.app')

@section('content')
    <div
        style="max-width:700px; margin:50px auto; background:#fff; padding:40px; border-radius:20px; box-shadow:0 8px 25px rgba(0,0,0,0.1); text-align:center;">

        <!-- Success & Error Messages -->
        @if (Session::has('success'))
            <div
                style="margin-bottom:20px; padding:15px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:12px;">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div
                style="margin-bottom:20px; padding:15px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:12px;">
                {{ Session::get('error') }}
            </div>
        @endif

        <!-- Thank You Icon -->
        <div
            style="width:100px; height:100px; margin:0 auto 20px; background:#FC8934; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:45px; box-shadow:0 6px 15px rgba(252,137,52,0.5);">
            <i class="bi bi-check-lg"></i>
        </div>

        <!-- Heading -->
        <h1 style="color:#333; font-weight:700; margin-bottom:15px;">🎉 ধন্যবাদ!</h1>
        <p style="font-size:16px; color:#555; line-height:1.6; max-width:550px; margin:0 auto 20px;">
            আপনার অর্ডার সফলভাবে কনফার্ম হয়েছে। আমাদের টিম শীঘ্রই আপনার সাথে যোগাযোগ করবে।
        </p>

        <!-- Order ID -->

        <p style="font-size:16px; color:#444; margin-bottom:25px;">
            📦 আপনার অর্ডার আইডি:
            <span style="font-weight:700; color:#FC8934;">{{ $id }}</span>
        </p>


        <!-- Order Summary Button -->
        <a href="/"
            style="display:inline-block; padding:14px 30px; background:#FC8934; color:#fff; font-size:16px; font-weight:600; border-radius:12px; text-decoration:none; transition:0.3s;"
            onmouseover="this.style.background='#e57825'" onmouseout="this.style.background='#FC8934'">
            🏠 হোম পেজে ফিরে যান
        </a>

        <!-- Extra Suggestion -->
        <div style="margin-top:30px; padding:20px; background:#fff7f0; border:1px solid #ffd9b3; border-radius:15px;">
            <h5 style="margin-bottom:10px; color:#FC8934; font-weight:600;">👉 পরবর্তী ধাপ</h5>
            <p style="margin:0; color:#666; font-size:14px;">
                আপনার অর্ডারের স্ট্যাটাস দেখতে চাইলে, আপনার <strong>প্রোফাইল</strong> এ লগইন করুন।

                @if (!Auth::check())
                    অর্ডারের সময় ব্যাবহার করা মোবাইল নাম্বার ও ডিফল্ট পাসওয়ার্ড (১২৩৪৫৬) দিয়ে লগইন করতে পারবেন।
                @endif

                আমাদের সাথে কেনাকাটা করার জন্যে আপনাকে ধন্যবাদ। <br>
                <a style="margin-bottom:10px; color:#FC8934; font-weight:600;" href="{{ route('account.userLogin') }}">
                    লগইন করুন
                </a>
            </p>
        </div>

    </div>
@endsection

@section('customJs')
@endsection
