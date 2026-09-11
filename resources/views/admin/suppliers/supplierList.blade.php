@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Suppliers</li>
                </ul>
                <h4 class="a-page-title">Suppliers</h4>
                <p class="a-page-desc">Manage your suppliers.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('supply.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add Supplier</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Suppliers</h5>
                <a href="{{ route('supply.create') }}" class="btn btn-theme btn-sm"><i class="ri-add-line"></i> Add Supplier</a>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Option</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="a-cell-main">{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td>
                                    <div class="a-actions-cell">
                                        <a href="{{ route('supply.edit', $supplier->id) }}" class="a-action-btn a-view" title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <a href="javascript:void(0)" data-bs-toggle="modal"
                                            data-bs-target="#exampleModalToggle" class="a-action-btn a-danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No records found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
