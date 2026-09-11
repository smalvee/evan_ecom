@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Users</li>
                </ul>
                <h4 class="a-page-title">Users</h4>
                <p class="a-page-desc">Manage your customers and admins.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('users.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Add User</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Users</h5>
                <div class="d-flex align-items-center gap-2">
                    <form action="" method="GET" class="d-inline-flex">
                        <div class="input-group" style="width: auto;">
                            <input type="text" class="form-control" placeholder="Search"
                                value="{{ Request::get('keyword') }}" name="keyword">
                            <button type="submit" class="btn btn-outline-secondary"><i class="ri-search-line"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('users.create') }}" class="btn btn-theme btn-sm"><i class="ri-add-line"></i> Add User</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-semibold"
                                            style="width:40px;height:40px;background:var(--a-primary);">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="a-cell-main">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->role == 2 ? 'Admin' : 'Customer' }}</td>
                                <td>
                                    @if ($user->status == 1)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Active</span>
                                    @else
                                        <span class="a-badge a-badge-secondary"><span class="dot"></span>Blocked</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->name != 'Administrator')
                                        <div class="a-actions-cell">
                                            <a href="{{ route('users.edit', $user->id) }}" class="a-action-btn a-view" title="Edit">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="deleteUser({{ $user->id }})"
                                                class="a-action-btn a-danger" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">Super Admin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No records found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="a-card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        function deleteUser(id) {
            var url = '{{ route('users.delete', 'ID') }}';
            var newUrl = url.replace("ID", id);

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
                            window.location.href = "{{ route('users.index') }}";
                        }
                    }
                });
            }
        }
    </script>
@endsection
