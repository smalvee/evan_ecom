@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>All Category</h5>
                            <form class="d-inline-flex">
                                <a href="{{ route('categories.create') }}" class="align-items-center btn btn-theme d-flex">
                                    <i data-feather="plus-square"></i>Add New
                                </a>
                            </form>
                        </div>

                        <div class="table-responsive category-table">
                            <div>
                                <table class="table all-package theme-table" id="table_id">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Product Image</th>
                                            <th>Slug</th>
                                            <th>status</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @if (!empty($categories))
                                            @foreach ($categories as $category)
                                                <tr>
                                                    <td>{{ $category->name }}</td>
                                                    <td>
                                                        <div class="table-image">
                                                            <img src="{{ asset('uploads/category/thumb/' . $category->image) }}"
                                                                class="img-fluid" alt="">

                                                        </div>
                                                    </td>

                                                    <td>{{ $category->slug }}</td>
                                                    <td>{{ $category->status }}</td>

                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <a href="{{ route('categories.edit', $category->id) }}">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="javascript:void(0)" onclick="deleteCategory({{ $category->id }})">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <td colspan="5">Records Not Found</td>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        function deleteCategory(id) {

            var url = '{{ route('categories.delete', 'ID') }}';
            var newUrl = url.replace("ID", id)

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

                             Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Category Deleted successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                           window.location.href = "{{ route('categories.index') }}";
                        }, 2000);
                            
                        }
                    }
                })
            }


        }
    </script>
@endsection
