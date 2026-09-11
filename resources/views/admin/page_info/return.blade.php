@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Create Product</h1> --}}
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')


            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Return Policy</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="return_policy_form" name="return_policy_form">
                                <div class="mb-3">
                                    <label for="return_policy" class="form-label"></label>
                                    <textarea name="return_policy" id="return_policy" class="form-control summernote" rows="10"
                                        placeholder="Enterrefund policy...">{{ $about_us->return_policy ?? 'Enter who we are...' }}</textarea>

                                </div>
                                <button type="submit" class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#return_policy_form").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $.ajax({
                url: '{{ route('admin.store_return') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    window.location.href = "{{ route('admin.display.return') }}";
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
