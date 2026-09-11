@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-8 m-auto">
                        <form action="" method="POST" id="save_aboutus" name="save_aboutus">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>About Us</h5>
                                    </div>
                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                        placeholder="Description">{{ $about_us->who_we_are ?? 'Enter who we are...' }}</textarea>
                                </div>
                            </div>


                            <button type="submit" class="btn btn-success mt-3">Save</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#save_aboutus").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            // $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('admin.store_who_we_are') }}',
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
