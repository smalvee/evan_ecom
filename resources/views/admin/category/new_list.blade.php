@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Categories</li>
                </ul>
                <h4 class="a-page-title">Categories</h4>
                <p class="a-page-desc">Manage your product categories.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('categories.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add New</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Categories</h5>
                <a href="{{ route('categories.create') }}" class="btn btn-theme btn-sm"><i class="ri-add-line"></i> Add New</a>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Image</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="a-cell-main">{{ $category->name }}</td>
                                <td>
                                    <div class="table-image">
                                        <img src="{{ asset('uploads/category/thumb/' . $category->image) }}"
                                            class="img-fluid" alt="">
                                    </div>
                                </td>
                                <td>{{ $category->slug }}</td>
                                <td>
                                    @if ($category->status == 1)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Active</span>
                                    @else
                                        <span class="a-badge a-badge-secondary"><span class="dot"></span>Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="a-actions-cell">
                                        <a href="{{ route('categories.edit', $category->id) }}" class="a-action-btn a-view" title="Edit"><i class="ri-pencil-line"></i></a>
                                        <a href="javascript:void(0)" onclick="deleteCategory({{ $category->id }})" class="a-action-btn a-danger" title="Delete"><i class="ri-delete-bin-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No records found</h5>
                                        <p>No categories have been added yet.</p>
                                        <a href="{{ route('categories.create') }}" class="btn btn-theme btn-sm"><i class="ri-add-line"></i> Add New</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($categories->hasPages())
                <div class="a-card-footer">
                    {{ $categories->links() }}
                </div>
            @endif
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
