@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Shipping Charges</li>
                </ul>
                <h4 class="a-page-title">Shipping Charges</h4>
                <p class="a-page-desc">Manage delivery charges by district.</p>
            </div>
        </div>

        <div class="a-card mb-3">
            <div class="a-card-head">
                <h5>Add Shipping Charge</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="shippingForm">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="district" class="form-label a-required">District</label>
                                <select name="district" id="district" class="form-select">
                                    <option value="">-- Select District --</option>
                                    @foreach (config('districts.list') as $district)
                                        <option value="{{ $district }}">{{ $district }}</option>
                                    @endforeach
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="amount" class="form-label a-required">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control"
                                    placeholder="Amount">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-theme w-100">Create</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Shipping Charges</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>District</th>
                            <th>Amount</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($shippingCharge->isNotEmpty())
                            @foreach ($shippingCharge as $shipping)
                                <tr>
                                    <td>{{ $shipping->id }}</td>
                                    <td><span class="a-cell-main">{{ $shipping->district }}</span></td>
                                    <td>৳ {{ $shipping->amount }}</td>
                                    <td>
                                        <div class="a-actions-cell">
                                            <a href="{{ route('shipping.edit', $shipping->id) }}"
                                                class="a-action-btn"><i class="ri-pencil-line"></i></a>
                                            <a href="javascript:void(0)" class="a-action-btn a-danger"
                                                onclick="deleteShipping({{ $shipping->id }})"><i
                                                    class="ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No shipping charges found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#shippingForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('shipping.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Shipping charge added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('shipping.create') }}";
                        }, 1500);
                    } else {
                        var errors = response['errors'] || {};

                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');

                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).siblings('.invalid-feedback').html(value);
                        });
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });

        function deleteShipping(id) {
            var url = '{{ route('shipping.delete', 'ID') }}';
            var newUrl = url.replace("ID", id);

            if (confirm("Are you sure to delete")) {
                $.ajax({
                    url: newUrl,
                    type: 'delete',
                    data: {},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response["status"] == true) {
                            window.location.href = "{{ route('shipping.create') }}";
                        }
                    }
                });
            }
        }
    </script>
@endsection
