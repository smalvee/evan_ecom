@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Site Banner</h5>
                        </div>

                        <form action="" method="post" name="createBanner" id="createBanner">
                            @csrf
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="mb-3">Banner
                                                <span class="text-danger small">(Recommended dimensions 1100 x 480 pixels)</span>
                                            </h5>
                                            <div id="image" class="dropzone dz-clickable"
                                                style="border: 2px dashed #6c757d; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer;">
                                                <div class="dz-message needsclick">Drop file here or click to upload.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3" id="product-gallery"></div>
                                    <input type="hidden" name="image_id" id="image_id">
                                </div>
                            </div>

                            <div class="pt-3">
                                <button type="submit" class="btn btn-theme">Create</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Banner List</h5>
                        </div>
                        <div class="table-responsive category-table">
                            <table class="table all-package theme-table" id="banners">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $("#createBanner").submit(function(event) {
            event.preventDefault();

            if ($("#image_id").val() === '') {
                alert("Please upload an image before saving.");
                return false;
            }

            var element = $(this);
            $("button[type=submit]").prop('disabled', true);

            $.ajax({
                url: '{{ route('banner.create') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Banner created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('banner.index') }}";
                        }, 1500);
                    } else {
                        var errors = response['errors'] || {};
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').html('');
                        $.each(errors, function(key, value) {
                            var input = $('#' + key);
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').html(value);
                        });
                    }
                },
                error: function(jqXHR, exception) {
                    console.log("AJAX error:", jqXHR.responseText);
                    alert("Something went wrong. Check console for details.");
                }
            });
        });

        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#image", {
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                if (response && response.ImagePath) {
                    $("#product-gallery").html('');
                    $("#image_id").val(response.image_id);

                    let html = `
                    <div class="col-6 col-md-3" id="image-row-${response.image_id}">
                        <div class="card shadow-sm">
                            <img class="card-img-top" src="${response.ImagePath}" alt="image" style="height:150px;object-fit:cover;">
                            <div class="card-body text-center">
                                <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </div>
                    </div>`;
                    $("#product-gallery").append(html);
                }
            },
            complete: function(file) {
                this.removeFile(file);
            }
        });

        function deleteImage(id) {
            $("#product-gallery").empty();
            $("#image_id").val('');
        }

        function deletebanner(id) {
            var url = '{{ route('banner.destroy', 'ID') }}';
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
                            window.location.href = "{{ route('banner.index') }}";
                        }
                    }
                });
            }
        }
    </script>

    <script>
        const banners = <?= json_encode(
            $banners
                ->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'image' => asset('uploads/banners/' . $banner->image),
                        'status' => $banner->status == 1 ? 'Active' : 'Inactive',
                    ];
                })
                ->toArray(),
        ) ?>;

        const tbody = document.querySelector('#banners tbody');

        function renderTable() {
            tbody.innerHTML = '';
            banners.forEach(banner => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${banner.id}</td>
                <td><img src="${banner.image}" alt="Banner ${banner.id}" class="img-fluid" style="max-width:300px;"></td>
                <td id="status-${banner.id}" class="${banner.status === 'Active' ? 'text-success' : 'text-danger'}">
                    ${banner.status}
                </td>
                <td>
                    <ul>
                        <li>
                            <a href="javascript:void(0)" onclick="toggleStatus(${banner.id})">
                                <i class="ri-toggle-line"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="deletebanner(${banner.id})">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </li>
                    </ul>
                </td>
            `;
                tbody.appendChild(tr);
            });
        }

        async function toggleStatus(id) {
            const banner = banners.find(b => b.id === id);
            if (!banner) return;

            try {
                await fetch("<?= route('banners.toggleStatus') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>',
                    },
                    body: JSON.stringify({ id }),
                });

                banners.forEach(b => b.status = 'Inactive');
                banner.status = 'Active';

                renderTable();
            } catch (err) {
                console.error('Failed to update banner status', err);
            }
        }

        renderTable();
    </script>
@endsection
