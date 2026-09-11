@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('purchase.index') }}">Purchases</a></li>
                    <li class="is-active">Returns</li>
                </ul>
                <h4 class="a-page-title">Purchase Returns</h4>
                <p class="a-page-desc">Manage your purchase returns.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('return.index') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add Return</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Returns</h5>
                <form action="" method="GET" class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control" style="width: 220px;" placeholder="Search"
                        value="{{ Request::get('keyword') }}" name="keyword">
                    <button type="submit" class="btn btn-outline-secondary btn-icon" title="Search">
                        <i class="ri-search-line"></i>
                    </button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Return ID</th>
                            <th>Supplier Name</th>
                            <th>Return Amount</th>
                            <th>Return Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (!empty($purchase_return))
                            @foreach ($purchase_return as $return_info)
                                <tr>
                                    <td class="a-cell-main">{{ $return_info->id }}</td>
                                    <td>{{ $return_info->purchase->supplier->name ?? '—' }}</td>
                                    <td>{{ $return_info->return_amount }}</td>
                                    <td>{{ $return_info->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="a-actions-cell">
                                            <a href="{{ route('purchase.return_view', $return_info->id) }}" class="a-action-btn a-view" title="View">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if (!empty($purchase_return) && $purchase_return->hasPages())
                <div class="a-card-footer">
                    {{ $purchase_return->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
@endsection
