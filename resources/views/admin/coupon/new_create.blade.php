@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('coupon.index') }}">Coupons</a></li>
                    <li class="is-active">Create</li>
                </ul>
                <h4 class="a-page-title">Create Coupon</h4>
                <p class="a-page-desc">Add a new discount coupon.</p>
            </div>
        </div>

        <form action="" method="POST" id="discountForm">
            <div class="a-card">
                <div class="a-card-head">
                    <h5>Coupon Details</h5>
                </div>
                <div class="a-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label a-required">Code</label>
                                <input type="text" name="code" id="code" class="form-control"
                                    placeholder="code">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="name">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses" class="form-label">Max Uses</label>
                                <input type="number" name="max_uses" id="max_uses" class="form-control"
                                    placeholder="max_uses">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses_user" class="form-label">Max Uses User</label>
                                <input type="number" name="max_uses_user" id="max_uses_user" class="form-control"
                                    placeholder="max_uses_user">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label a-required">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="fixed">Fixed</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label a-required">Discount Amount</label>
                                <input type="number" name="discount_amount" id="discount_amount" class="form-control"
                                    placeholder="discount_amount">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_amount" class="form-label">Min Amount</label>
                                <input type="number" name="min_amount" id="min_amount" class="form-control"
                                    placeholder="min_amount">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label a-required">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Block</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="starts_at" class="form-label">Starts At</label>
                                <input type="text" name="starts_at" id="starts_at" class="form-control"
                                    placeholder="starts_at">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">Expires At</label>
                                <input type="text" name="expires_at" id="expires_at" class="form-control"
                                    placeholder="expires_at">
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="a-card-footer text-end">
                    <a href="{{ route('coupon.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-theme">Create</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function() {
            $('#starts_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });

            $('#expires_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });

        $("#discountForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('coupon.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        window.location.href = "{{ route('coupon.index') }}";

                        $("#code").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                        $("#discount_amount").removeClass('is-invalid')
                            .siblings('.invalid-feedback').html('');

                    } else {
                        var errors = response['errors'];
                        if (errors['code']) {
                            $("#code").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['code']);
                        } else {
                            $("#code").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                        if (errors['discount_amount']) {
                            $("#discount_amount").addClass('is-invalid')
                                .siblings('.invalid-feedback').html(errors['discount_amount']);
                        } else {
                            $("#discount_amount").removeClass('is-invalid')
                                .siblings('.invalid-feedback').html('');
                        }

                    }



                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
