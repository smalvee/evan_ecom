@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Purchases</li>
                </ul>
                <h4 class="a-page-title">Purchase List</h4>
                <p class="a-page-desc">Manage your purchase orders.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('purchase.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add Purchase</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Purchases</h5>
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
                            <th>Purchase ID</th>
                            <th>Supplier Name</th>
                            <th>Purchase Amount</th>
                            <th>Purchase Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (!empty($purchase))
                            @foreach ($purchase as $purchase_info)
                                <tr>
                                    <td class="a-cell-main">{{ $purchase_info->id }}</td>
                                    <td>{{ $purchase_info->supplier->name ?? '—' }}</td>
                                    <td>{{ $purchase_info->total }}</td>
                                    <td>{{ $purchase_info->date }}</td>
                                    <td>
                                        <div class="a-actions-cell">
                                            <a href="{{ route('purchase.edit', $purchase_info->id) }}" class="a-action-btn a-view" title="Edit"><i class="ri-pencil-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if (!empty($purchase) && $purchase->hasPages())
                <div class="a-card-footer">
                    {{ $purchase->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
@endsection
