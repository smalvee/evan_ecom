@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Suppliers</h5>
                            <form class="d-inline-flex">
                                <a href="{{ route('supply.create') }}" class="align-items-center btn btn-theme d-flex">
                                    <i data-feather="plus"></i>Add New
                                </a>
                            </form>
                        </div>

                        <div class="table-responsive table-product">
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

                                    @foreach ($suppliers as $supplier)
                                    <tr>
                                       

                                        <td>
                                            <div class="user-name" style="text-align: center">
                                                <span>{{ $supplier->name }}</span>
                                                
                                            </div>
                                        </td>

                                        <td>{{ $supplier->phone }}</td>

                                        <td>{{ $supplier->email }}</td>
                                        <td>{{ $supplier->address }}</td>

                                        <td>
                                            <ul>
                                                {{-- <li>
                                                    <a href="order-detail.html">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </li> --}}

                                                <li>
                                                    <a href="{{ route('supply.edit', $supplier->id) }}">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#exampleModalToggle">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
