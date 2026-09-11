@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Units</li>
                </ul>
                <h4 class="a-page-title">Units</h4>
                <p class="a-page-desc">Manage your product units.</p>
            </div>
            <div class="a-actions">
                <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="ri-add-line"></i> Add Unit
                </button>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Units</h5>
                <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="ri-add-line"></i> Add Unit
                </button>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Short Name</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $unit)
                            <tr>
                                <td>
                                    <span class="a-cell-main">{{ $unit->name }}</span>
                                </td>
                                <td>{{ $unit->s_name }}</td>
                                <td>
                                    <div class="a-actions-cell">
                                        <a href="#" data-id="{{ $unit->id }}"
                                            data-name="{{ $unit->name }}" data-sname="{{ $unit->s_name }}"
                                            data-url="{{ route('units.update', $unit->id) }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            class="a-action-btn a-view" title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="deleteUnit({{ $unit->id }})"
                                            class="a-action-btn a-danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addModalLabel">Add Unit</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="" method="POST" id="createUnits" name="createUnits">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="name" name="name" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="s_name" class="form-label">Short Name</label>
                                        <input type="text" id="s_name" name="s_name" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-theme">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="editModalLabel">Edit Unit</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form action="" method="POST" id="updateUnits" name="updateUnits">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="edit_name" class="form-label">Name</label>
                                        <input type="text" id="edit_name" name="edit_name" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_s_name" class="form-label">Short Name</label>
                                        <input type="text" id="edit_s_name" name="edit_s_name" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>
                                    <input type="hidden" id="edit_id" name="edit_id">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-theme">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#createUnits").submit(function(event) {
            event.preventDefault();

            var element = $(this);
            $.ajax({
                url: '{{ route('units.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response["status"] == true) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Units Added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('units.index') }}";
                        }, 2000);


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


    {{-- for view modal data  --}}

    <script>
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-bs-target="#editModal"]')) {

                let btn = e.target.closest('[data-bs-target="#editModal"]');

                let id = btn.dataset.id;
                let name = btn.dataset.name;
                let sname = btn.dataset.sname;
                let url = btn.dataset.url; // Laravel-generated URL ✔️

                document.getElementById('edit_name').value = name;
                document.getElementById('edit_s_name').value = sname;
                document.getElementById('edit_id').value = id;

                document.getElementById('updateUnits').action = url; // 100% correct ✔️
            }
        });
    </script>

    {{-- delete  --}}

    <script>
        function deleteUnit(id) {

            var url = '{{ route('units.delete', 'ID') }}';
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
                            window.location.href = "{{ route('units.index') }}";
                        }
                    }
                })
            }
        }
    </script>

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
@endsection
