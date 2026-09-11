@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Shipping Charges</h5>
                        </div>

                        <form action="" method="POST" id="shippingForm">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="district">District</label>
                                        <select name="district" id="district" class="form-control">
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
                                        <label for="amount">Amount</label>
                                        <input type="number" name="amount" id="amount" class="form-control"
                                            placeholder="Amount">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3 d-flex align-items-end h-100">
                                        <button type="submit" class="btn btn-theme">Create</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive category-table">
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
                                                <td>{{ $shipping->district }}</td>
                                                <td>{{ $shipping->amount }}</td>
                                                <td>
                                                    <ul>
                                                        <li>
                                                            <a href="{{ route('shipping.edit', $shipping->id) }}">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="" onclick="deleteShipping({{ $shipping->id }})">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">Records Not Found</td>
                                        </tr>
                                    @endif
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
