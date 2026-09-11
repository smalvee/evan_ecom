@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Edit Shipping Charge</h5>
                            <a href="{{ route('shipping.create') }}" class="align-items-center btn btn-theme d-flex">
                                <i data-feather="arrow-left"></i>Back
                            </a>
                        </div>

                        <form action="" method="POST" id="shippingForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="district">District</label>
                                        <select name="district" id="district" class="form-control">
                                            <option value="">-- Select District --</option>
                                            @foreach (config('districts.list') as $district)
                                                <option value="{{ $district }}" {{ !empty($shippingCharge->district) && $shippingCharge->district == $district ? 'selected' : '' }}>{{ $district }}</option>
                                            @endforeach
                                        </select>
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amount">Amount</label>
                                        <input value="{{ !empty($shippingCharge->amount) ? $shippingCharge->amount : '' }}"
                                            type="number" name="amount" id="amount" class="form-control"
                                            placeholder="Amount">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="pb-3 pt-2">
                                <button type="submit" class="btn btn-theme">Update</button>
                                <a href="{{ route('shipping.create') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>
                        </form>
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
                url: '{{ route('shipping.update', $shippingCharge->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Shipping charge updated successfully!',
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
    </script>
@endsection
