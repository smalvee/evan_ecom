@extends('admin.layouts.new_app')

@section('content')
<?php 

use App\Models\Supplier;
use App\Models\Purchase;

?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title d-sm-flex d-block">
                            <h5>Purchase List</h5>
                            <div class="right-options">
                                <form action="" method="GET">
                                    <div style="position: relative; display: inline-block;">
                                        <input type="text" class=""
                                            style="border-radius: 5px; border-color: #028e84; padding-right: 35px;"
                                            placeholder="search" value="{{ Request::get('keyword') }}" name="keyword">
                                        <button type="submit"
                                            style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>

                                </form>
                                <br>
                                <ul>
                                    {{-- <li>
                                        <a href="javascript:void(0)">import</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">Export</a>
                                    </li>
                                    <li> --}}
                                    <a class="btn btn-solid" href="{{ route('return.index') }}">Add Return</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table all-package theme-table table-product" id="table_id">
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
                                        {{-- @dd($products) --}}

                                        @if (!empty($purchase_return))
                                            @foreach ($purchase_return as $return_info)
                                            @php

                                            $purchase_info = Purchase::where('id', $return_info->purchase_id)->first();
                                            $supplier_info = Supplier::where('id', $purchase_info->supplier_id)->first();
                                         
                                            @endphp
                                                <tr>
                                                    <td>{{ $return_info->id}}</td>
                                                    

                                                    <td>{{ $supplier_info->name }}</td>

                                                  
                                                    <td>{{ $return_info->return_amount }}</td>                                                   
                                                    <td>{{ $return_info->created_at->format('d-m-Y') }}</td>
                                           

                                                    <td>
                                                        <ul>
                                                            <li><a href="{{ route('purchase.return_view', $return_info->id) }}">
                                                                <i class="ri-eye-line"></i>

                                                            </a>
                                                        </li>

                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                {{ $purchase_return->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
