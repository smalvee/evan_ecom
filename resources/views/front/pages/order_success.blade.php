@extends('front.layouts.new_app')

@section('content')
    <style>
        .order-success-wrap {
            background: #f6f7f9;
            padding: 48px 16px 64px;
        }

        .order-success-card {
            max-width: 620px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #eceef2;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
            padding: 44px 34px 40px;
            text-align: center;
        }

        .order-success-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--theme-color-rgb, 217, 159, 70), .14);
            color: var(--theme-color, #d99f46);
        }

        .order-success-icon svg {
            width: 44px;
            height: 44px;
            stroke-width: 2.5;
        }

        .order-success-title {
            font-size: 28px;
            font-weight: 800;
            color: #1b2430;
            margin: 0 0 8px;
            line-height: 1.2;
        }

        .order-success-text {
            color: #6b7280;
            font-size: 15px;
            margin: 0 auto 26px;
            max-width: 420px;
        }

        .order-id-box {
            background: #faf7f2;
            border: 1px dashed rgba(var(--theme-color-rgb, 217, 159, 70), .55);
            border-radius: 12px;
            padding: 18px 16px;
            margin-bottom: 28px;
        }

        .order-id-label {
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #8a8f98;
            font-weight: 700;
        }

        .order-id-value {
            font-size: 24px;
            font-weight: 800;
            color: #1b2430;
            margin-top: 4px;
            word-break: break-word;
        }

        .order-success-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .order-success-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 176px;
            padding: 12px 22px;
            border-radius: 10px;
            font-weight: 600;
            line-height: 1.2;
        }

        .order-success-actions svg {
            width: 16px;
            height: 16px;
        }

        .btn-order-primary {
            background: var(--theme-color, #d99f46);
            border: 1px solid var(--theme-color, #d99f46);
            color: #fff;
        }

        .btn-order-primary:hover,
        .btn-order-primary:focus {
            background: var(--theme-color, #d99f46);
            border-color: var(--theme-color, #d99f46);
            color: #fff;
            filter: brightness(.94);
        }

        .btn-order-outline {
            background: #fff;
            border: 1px solid #dfe3e8;
            color: #1b2430;
        }

        .btn-order-outline:hover,
        .btn-order-outline:focus {
            border-color: var(--theme-color, #d99f46);
            color: var(--theme-color, #d99f46);
            background: #fff;
        }

        @media (max-width: 575.98px) {
            .order-success-wrap {
                padding: 28px 12px 44px;
            }

            .order-success-card {
                padding: 30px 18px 28px;
                border-radius: 14px;
            }

            .order-success-icon {
                width: 72px;
                height: 72px;
                margin-bottom: 18px;
            }

            .order-success-icon svg {
                width: 36px;
                height: 36px;
            }

            .order-success-title {
                font-size: 22px;
            }

            .order-id-value {
                font-size: 20px;
            }

            .order-success-actions .btn {
                width: 100%;
                min-width: 0;
            }
        }
    </style>

    <section class="order-success-wrap">
        <div class="order-success-card">
            <div class="order-success-icon" aria-hidden="true">
                <i data-feather="check"></i>
            </div>

            <h1 class="order-success-title">Order Success</h1>
            <p class="order-success-text">
                Your order has been placed successfully. We'll start processing it right away.
            </p>

            <div class="order-id-box">
                <div class="order-id-label">Order ID</div>
                <div class="order-id-value">{{ $id }}</div>
            </div>

            <div class="order-success-actions">
                <a href="{{ route('front.invoice', $id) }}" class="btn btn-order-primary">
                    <i data-feather="file-text"></i>
                    <span>View Invoice</span>
                </a>
                <a href="{{ route('front.home') }}" class="btn btn-order-outline">
                    <i data-feather="arrow-left"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
@endsection
