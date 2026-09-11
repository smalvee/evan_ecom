@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Sub Categories</li>
                </ul>
                <h4 class="a-page-title">Sub Categories</h4>
                <p class="a-page-desc">Manage your product sub categories.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('sub-categories.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add New</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Sub Categories</h5>
                <a href="{{ route('sub-categories.create') }}" class="btn btn-theme btn-sm"><i class="ri-add-line"></i> Add New</a>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subcategories as $subcategory)
                            <tr>
                                <td class="a-cell-main">{{ $subcategory->name }}</td>
                                <td>{{ $subcategory->slug }}</td>
                                <td>{{ $subcategory->categoryName }}</td>
                                <td>
                                    @if ($subcategory->status == 1)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Active</span>
                                    @else
                                        <span class="a-badge a-badge-secondary"><span class="dot"></span>Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="a-actions-cell">
                                        <a href="{{ route('sub-categories.edit', $subcategory->id) }}" class="a-action-btn a-view" title="Edit"><i class="ri-pencil-line"></i></a>
                                        <a href=""
                                            onclick="deleteSubCategory({{ $subcategory->id }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#exampleModalToggle"
                                            class="a-action-btn a-danger" title="Delete"><i class="ri-delete-bin-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No records found</h5>
                                        <p>No sub categories have been added yet.</p>
                                        <a href="{{ route('sub-categories.create') }}" class="btn btn-theme btn-sm"><i class="ri-add-line"></i> Add New</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($subcategories->hasPages())
                <div class="a-card-footer">
                    {{ $subcategories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        function deleteSubCategory(id) {
            var url = '{{ route('sub-categories.delete', 'ID') }}';
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
                            window.location.href = "{{ route('sub-categories.index') }}";
                        }
                    }
                })
            }
        }
    </script>
@endsection
