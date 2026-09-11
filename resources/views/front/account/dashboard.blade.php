@extends('front.layouts.app')

@section('content')
    <section id="relatedProducts" class="py-5">
        <div class="container">

            <section style="background:#FC8934;color:#fff;" class="py-3">
                <div class="container text-center">
                    <h1 class="fw-bold mb-1">Dashboard</h1>
                </div>
            </section>

            <section style="background:#f9fafc;min-height:100vh;padding:40px 0;font-family:'Segoe UI',sans-serif;">
                <div class="container" style="max-width:1100px;margin:auto;padding:0 20px;">

                    <!-- Centered Navbar -->
                    <div class="card mb-4"
                        style="border:none;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
                        <div class="card-header"
                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;padding:15px 25px;text-align:center;">
                            <ul
                                style="list-style:none;margin:0;padding:0;display:inline-flex;justify-content:center;align-items:center;gap:30px;white-space:nowrap;">
                                <li><a href="{{ route('account.userDashboard') }}"
                                        style="color:#fff;text-decoration:none;font-weight:500;transition:0.3s;">Dashboard</a>
                                </li>
                                <li><a href="{{ route('account.profile') }}"
                                        style="color:#fff;text-decoration:none;font-weight:500;transition:0.3s;">Profile</a>
                                </li>
                                <li><a href="{{ route('account.logout') }}"
                                        style="color:#fff;text-decoration:none;font-weight:500;transition:0.3s;">Logout</a>
                                </li>
                            </ul>

                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="card mb-4"
                        style="border:none;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
                        <div class="card-header"
                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;padding:15px 25px;">
                            <h5 style="margin:0;font-weight:600;">Your Information</h5>
                        </div>
                        <div class="card-body" style="padding:25px;background:#fff;">
                            <div class="row" style="display:flex;flex-wrap:wrap;gap:15px;">
                                <div class="col" style="flex:1 1 45%;min-width:200px;">
                                    <p style="margin:0 0 10px;font-size:16px;"><strong>Name:</strong> {{ $user->name }}
                                    </p>
                                </div>
                                <div class="col" style="flex:1 1 45%;min-width:200px;">
                                    <p style="margin:0 0 10px;font-size:16px;"><strong>Email:</strong> {{ $user->email }}
                                    </p>
                                </div>
                                <div class="col" style="flex:1 1 45%;min-width:200px;">
                                    <p style="margin:0 0 10px;font-size:16px;"><strong>Phone:</strong>
                                        {{ $user->phone ?? '-' }}</p>
                                </div>
                                <div class="col" style="flex:1 1 45%;min-width:200px;">
                                    <p style="margin:0 0 10px;font-size:16px;"><strong>Registered On:</strong>
                                        {{ $user->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order History -->
                    <div class="card"
                        style="border:none;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">
                        <div class="card-header"
                            style="background:linear-gradient(90deg,#FC8934,#ff9f50);color:#fff;padding:15px 25px;">
                            <h5 style="margin:0;font-weight:600;">Your Order History</h5>
                        </div>
                        <div class="card-body" style="padding:25px;background:#fff;">
                            @if (!empty($orders))
                                <div class="table-responsive" style="overflow-x:auto;">
                                    <table class="table" style="width:100%;border-collapse:collapse;min-width:500px;">
                                        <thead>
                                            <tr style="background:#f8f9fa;text-align:left;">
                                                <th style="padding:10px;border-bottom:2px solid #eee;">Order ID</th>
                                                <th style="padding:10px;border-bottom:2px solid #eee;">Date</th>
                                                <th style="padding:10px;border-bottom:2px solid #eee;">Status</th>
                                                <th style="padding:10px;border-bottom:2px solid #eee;">Total</th>
                                                <th style="padding:10px;border-bottom:2px solid #eee;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr style="border-bottom:1px solid #f0f0f0;">
                                                    <td style="padding:10px;">#{{ $order->id }}</td>
                                                    <td style="padding:10px;">{{ $order->created_at->format('d M Y') }}</td>
                                                    <td style="padding:10px;">
                                                        @if ($order->status == 'pending')
                                                            <span
                                                                style="background:#ffeeba;color:#856404;padding:4px 10px;border-radius:6px;font-size:13px;">Pending</span>
                                                        @elseif($order->status == 'completed')
                                                            <span
                                                                style="background:#d4edda;color:#155724;padding:4px 10px;border-radius:6px;font-size:13px;">Completed</span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span
                                                                style="background:#f8d7da;color:#721c24;padding:4px 10px;border-radius:6px;font-size:13px;">Cancelled</span>
                                                        @else
                                                            <span
                                                                style="background:#e2e3e5;color:#383d41;padding:4px 10px;border-radius:6px;font-size:13px;">{{ ucfirst($order->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td style="padding:10px;">Tk:
                                                        {{ number_format($order->grand_total, 2) }}</td>
                                                    <td style="padding:10px;">
                                                        <a href="{{ route('account.orderDetails', $order->id) }}"
                                                            style="background:#FC8934;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;font-size:13px;transition:0.3s;display:inline-block;">
                                                            View Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p style="text-align:center;color:#777;">You have not placed any orders yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </section>





        </div>
    </section>
@endsection

@section('customJs')
    <!-- Optional JS can go here -->
    <!-- Simple inline responsive script -->
    <script>
        // Adjust navbar for mobile
        function adjustNav() {
            const nav = document.getElementById("navList");
            if (window.innerWidth < 600) {
                nav.style.flexDirection = "column";
                nav.style.alignItems = "flex-start";
                nav.style.gap = "10px";
            } else {
                nav.style.flexDirection = "row";
                nav.style.alignItems = "center";
                nav.style.gap = "20px";
            }
        }
        window.addEventListener('resize', adjustNav);
        window.addEventListener('load', adjustNav);
    </script>
@endsection
