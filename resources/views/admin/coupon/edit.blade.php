@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('coupon.index') }}">Coupons</a></li>
                    <li class="is-active">Edit</li>
                </ul>
                <h4 class="a-page-title">Edit Coupon</h4>
                <p class="a-page-desc">Update this discount coupon.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('coupon.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line"></i>
                    Back</a>
            </div>
        </div>

        <form action="" method="POST" id="discountForm">
            @csrf
            @method('PUT')
            <div class="a-card">
                <div class="a-card-head">
                    <h5>Coupon Details</h5>
                </div>
                <div class="a-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label a-required">Code</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="code"
                                    value="{{ old('code', $coupon->code) }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="name"
                                    value="{{ old('name', $coupon->name) }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses" class="form-label">Max Uses</label>
                                <input type="number" name="max_uses" id="max_uses" class="form-control"
                                    placeholder="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses_user" class="form-label">Max Uses User</label>
                                <input type="number" name="max_uses_user" id="max_uses_user" class="form-control"
                                    placeholder="max_uses_user" value="{{ old('max_uses_user', $coupon->max_uses_user) }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label a-required">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percent
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label a-required">Discount Amount</label>
                                <input type="number" name="discount_amount" id="discount_amount" class="form-control"
                                    placeholder="discount_amount" value="{{ old('discount_amount', $coupon->discount_amount) }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_amount" class="form-label">Min Amount</label>
                                <input type="number" name="min_amount" id="min_amount" class="form-control"
                                    placeholder="min_amount" value="{{ old('min_amount', $coupon->min_amount) }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label a-required">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1" {{ $coupon->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $coupon->status == 0 ? 'selected' : '' }}>Block</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="starts_at" class="form-label">Starts At</label>
                                <input type="text" name="starts_at" id="starts_at" class="form-control"
                                    placeholder="starts_at"
                                    value="{{ old('starts_at', $coupon->starts_at ? \Carbon\Carbon::parse($coupon->starts_at)->format('Y-m-d H:i:s') : '') }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">Expires At</label>
                                <input type="text" name="expires_at" id="expires_at" class="form-control"
                                    placeholder="expires_at"
                                    value="{{ old('expires_at', $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d H:i:s') : '') }}">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="Enter description">{{ old('description', $coupon->description) }}</textarea>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="a-card-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="deleteCoupon()"><i
                            class="ri-delete-bin-line"></i> Delete</button>
                    <div>
                        <a href="{{ route('coupon.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-theme">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function() {
            $('#starts_at').datetimepicker({
                format: 'Y-m-d H:i:s',
            });

            $('#expires_at').datetimepicker({
                format: 'Y-m-d H:i:s',
            });
        });

        $("#discountForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('coupon.update', $coupon->id) }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('coupon.index') }}";
                        }, 1200);
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
                error: function() {
                    console.log("Something went wrong");
                }
            })
        });

        function deleteCoupon() {
            Swal.fire({
                title: 'Delete this coupon?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route('coupon.delete', $coupon->id) }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                window.location.href = "{{ route('coupon.index') }}";
                            }, 1000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong'
                        });
                    }
                });
            });
        }
    </script>
@endsection
