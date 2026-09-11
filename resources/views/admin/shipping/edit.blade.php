@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('shipping.create') }}">Shipping Charges</a></li>
                    <li class="is-active">Edit</li>
                </ul>
                <h4 class="a-page-title">Edit Shipping Charge</h4>
                <p class="a-page-desc">Update delivery charge details.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('shipping.create') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line"></i> Back
                </a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Shipping Charge Details</h5>
            </div>
            <div class="a-card-body">
                <form action="" method="POST" id="shippingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="district" class="form-label a-required">District</label>
                                <select name="district" id="district" class="form-select">
                                    <option value="">-- Select District --</option>
                                    @foreach (config('districts.list') as $district)
                                        <option value="{{ $district }}"
                                            {{ !empty($shippingCharge->district) && $shippingCharge->district == $district ? 'selected' : '' }}>
                                            {{ $district }}</option>
                                    @endforeach
                                </select>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label a-required">Amount</label>
                                <input value="{{ !empty($shippingCharge->amount) ? $shippingCharge->amount : '' }}"
                                    type="number" name="amount" id="amount" class="form-control"
                                    placeholder="Amount">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                    </div>

                    <div class="text-end pt-2">
                        <button type="submit" class="btn btn-theme">Update</button>
                        <a href="{{ route('shipping.create') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
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
