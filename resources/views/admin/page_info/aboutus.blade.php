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
                            <h5 class="mb-0">Who We Are</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="who_we_are" name="who_we_are">
                                <div class="mb-3">
                                    <label for="who_we_are" class="form-label"></label>
                                    
                                    <textarea name="who_we_are" id="who_we_are" class="form-control summernote" rows="10"
                                        placeholder="Enter who we are...">{{ $about_us->who_we_are ?? 'Enter who we are...' }}</textarea>

                                </div>
                                <button type="submit" class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Our Mission</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="our_mission" name="our_mission">
                                <div class="mb-3">
                                    <label for="our_mission" class="form-label"></label>
                                    <textarea name="our_mission" id="our_mission" class="form-control summernote" rows="10"
                                        placeholder="Enter our_mission...">{{ $about_us->our_mission ?? 'Enter our_mission...' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Our Mission</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="our_mission" name="our_mission">
                                <div class="mb-3">
                                    <label for="our_mission" class="form-label"></label>
                                    <textarea name="our_mission" id="our_mission" class="form-control summernote" rows="10"
                                        placeholder="Enter our_mission...">{{ $about_us->our_mission ?? 'Enter our_mission...' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Our Vision</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="our_vission" name="our_vission">
                                <div class="mb-3">
                                    <label for="our_vission" class="form-label"></label>
                                    <textarea name="our_vission" id="our_vission" class="form-control summernote" rows="10"
                                        placeholder="Enter our_vission...">{{ $about_us->our_vision ?? 'Enter our_vission...' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}




        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#who_we_are").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $.ajax({
                url: '{{ route('admin.store_who_we_are') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    window.location.href = "{{ route('admin.display.aboutus') }}";
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });

        $("#our_mission").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $.ajax({
                url: '{{ route('admin.store_our_mission') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    window.location.href = "{{ route('admin.display.aboutus') }}";
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });

        $("#our_vission").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $.ajax({
                url: '{{ route('admin.store_our_vission') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    window.location.href = "{{ route('admin.display.aboutus') }}";
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            })
        });
    </script>
@endsection
