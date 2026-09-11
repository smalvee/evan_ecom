@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Refund Policy</li>
                </ul>
                <h4 class="a-page-title">Refund Policy</h4>
                <p class="a-page-desc">Manage the Refund Policy page content.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Refund Policy Content</h5>
                    </div>
                    <div class="a-card-body">
                        <form action="" method="POST" id="save_refund" name="save_refund">
                            @csrf
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                    placeholder="Description">{{ $about_us->refund_policy ?? 'Enter who we are...' }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-theme">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#save_refund").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            // $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('admin.store_refund') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {

                    // $("button[type=submit]").prop('disabled', false);


                    if (response["status"] == true) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Product created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        location.reload();


                    } else {
                        var errors = response['errors'] || {};

                        // Clear old validation states
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');

                        // Loop through errors and display them
                        $.each(errors, function(key, value) {
                            var input = $('#' + key); // Correct ID selector
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').html(
                                value); // Works if feedback div is right after input
                        });

                    }

                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
