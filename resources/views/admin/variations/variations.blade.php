@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Variations</li>
                </ul>
                <h4 class="a-page-title">Variations</h4>
                <p class="a-page-desc">Manage your product variations and their values.</p>
            </div>
            <div class="a-actions">
                <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="ri-add-line"></i> Add Variation
                </button>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>All Variations</h5>
                <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="ri-add-line"></i> Add Variation
                </button>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Variations</th>
                            <th>Values</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variations as $variation)
                            <tr>
                                <td>
                                    <span class="a-cell-main">{{ $variation->variations }}</span>
                                </td>
                                <td>
                                    {{ $variation->values->pluck('value')->join(', ') }}
                                </td>
                                <td>
                                    <div class="a-actions-cell">
                                        <a href="#" data-id="{{ $variation->id }}"
                                            data-name="{{ $variation->variations }}"
                                            data-values="{{ $variation->values->pluck('value')->join('|') }}"
                                            data-url="{{ route('variations.update', $variation->id) }}"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            class="a-action-btn a-view" title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                            onclick="deleteVariation({{ $variation->id }})"
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
                                <h1 class="modal-title fs-5" id="addModalLabel">Add Variation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="" method="POST" id="createvariation" name="createvariation">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="variations" class="form-label">Variation Name</label>
                                        <input type="text" id="variations" name="variations" class="form-control">
                                        <p class="invalid-feedback"></p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Add Variation Values</label>

                                        <div id="inputContainer">
                                            <!-- FIRST (main) input field – only + button -->
                                            <div class="input-group mb-2">
                                                <input type="text" name="values[]" class="form-control"
                                                    placeholder="Enter value">
                                                <p class="invalid-feedback"></p>
                                                <button type="button" class="btn btn-success addBtn">+</button>
                                            </div>
                                        </div>
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
                <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Edit Variation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form action="" method="POST" id="editVariationForm">
                                @csrf

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Variation Name</label>
                                        <input type="text" id="edit_variation_name" name="variations"
                                            class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Edit Variation Values</label>
                                        <div id="inputEditContainer"></div>
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

                <form id="deleteVariationForm" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        let count = 1;

        document.addEventListener("click", function(e) {

            // ADD new input field
            if (e.target.classList.contains("addBtn")) {
                let container = document.getElementById("inputContainer");

                let newField = document.createElement("div");
                newField.classList.add("input-group", "mb-2");

                newField.innerHTML = `
            <input type="text" id="values_${count}" name="values[]" class="form-control" placeholder="Enter value" required>
            <button type="button" class="btn btn-danger removeBtn">-</button>
            <div class="invalid-feedback"></div>
        `;

                container.appendChild(newField);
                count++;
            }

            // REMOVE input field
            if (e.target.classList.contains("removeBtn")) {
                e.target.parentElement.remove();
            }

        });

        // AJAX form submit
        $("#createvariation").submit(function(event) {
            event.preventDefault();

            var element = $(this);

            // Clear old validation
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            $.ajax({
                url: '{{ route('variation.store') }}',
                type: 'post',
                data: element.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Variations added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function() {
                            window.location.href = "{{ route('variation.index') }}";
                        }, 2000);

                    } else {
                        var errors = response.errors || {};

                        $.each(errors, function(key, value) {
                            // Laravel returns array input names as values. Use regex to select correct input
                            $('input[name="' + key + '[]"]').each(function() {
                                $(this).addClass('is-invalid');
                                $(this).next('.invalid-feedback').html(value[0]);
                            });
                        });
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });
    </script>

    <script>
        // When clicking the edit button
        document.addEventListener("click", function(e) {
            let btn = e.target.closest("[data-bs-target='#editModal']");
            if (!btn) return;

            let id = btn.getAttribute("data-id");
            let name = btn.getAttribute("data-name");
            let values = btn.getAttribute("data-values").split("|");
            let url = btn.getAttribute("data-url");

            // Set form action URL
            document.getElementById("editVariationForm").action = url;

            // Set variation name
            document.getElementById("edit_variation_name").value = name;

            // Fill value inputs
            let container = document.getElementById("inputEditContainer");
            container.innerHTML = "";

            values.forEach((val, index) => {
                container.innerHTML += `
            <div class="input-group mb-2">
                <input type="text" name="values[]" class="form-control" value="${val}">
                ${index === 0
                    ? `<button type="button" class="btn btn-success addEditBtn">+</button>`
                    : `<button type="button" class="btn btn-danger removeBtn">-</button>`
                }
            </div>
        `;
            });
        });

        // Dynamic add/remove button logic
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("addEditBtn")) {
                let container = document.getElementById("inputEditContainer");
                container.innerHTML += `
            <div class="input-group mb-2">
                <input type="text" name="values[]" class="form-control" placeholder="Enter value">
                <button type="button" class="btn btn-danger removeBtn">-</button>
            </div>
        `;
            }

            if (e.target.classList.contains("removeBtn")) {
                e.target.parentElement.remove();
            }
        });
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

    {{-- delete  --}}

    <script>
        function deleteVariation(id) {
            if (confirm("Are you sure you want to delete this variation?")) {
                let form = document.getElementById("deleteVariationForm");
                form.action = "/admin/variations/" + id;
                form.submit();
            }
        }
    </script>
@endsection
