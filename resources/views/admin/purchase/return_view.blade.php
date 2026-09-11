@extends('admin.layouts.new_app')

@section('content')
    <?php
    
    use App\Models\ProductVariant;
    
    ?>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: #fff;
        }

        .delete-btn {
            cursor: pointer;
            color: red;
            font-weight: bold;
        }

        .form-control {
            padding: 8px 0px;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-10 m-auto">
                <div class="card">
                    <form method="post" id="updatePurchase" name="updatePurchase">
                        @csrf

                        <div class="card-body">
                            <h5>Edit Purchase</h5>

                          

                            {{-- Date --}}
                            {{-- <div class="mb-3">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control"
                                    value="{{ $purchase_info->date }}">
                            </div> --}}

                            {{-- TABLE --}}
                            <table id="purchaseTable">
                                <thead>
                                    <tr>
                                        <th>Sku</th>
                                        <th>Product</th>
                                        <th>qty</th>
                                        <th>Unit Cost</th>
                                      

                                    </tr>
                                </thead>
                                <tbody>



                                    {{-- Existing Items --}}
                                    @foreach ($purchase_return_list as $item)
                                        @php
                                            $variant_info = ProductVariant::where('id', $item->variant_id)
                                                ->with('product')
                                                ->first();

                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $variant_info->sku }}
                                            </td>
                                            <td>{{ $variant_info->product->name }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ $item->unit_cost }}</td>
                                           

                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            {{-- <div class="row mt-3">
                                <div class="col-md-4 offset-md-8">
                                    <label><strong>Total Purchase Amount</strong></label>
                                    <input type="number" id="total_purchase" name="total_purchase"
                                        value="{{ $purchase_info->total }}" class="form-control" readonly>
                                </div>
                            </div> --}}

                            <a href="{{ route('purchase.return_list') }}"><button type="button" class="btn btn-primary mt-4">
                                Back
                            </button></a>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')



@endsection
