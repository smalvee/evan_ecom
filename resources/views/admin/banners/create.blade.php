@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Site Banner</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="" method="post" name="createBanner" id="createBanner">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Banner
                                    <span style="color: red">(Please choose the Dimensions of 1100 x 480 pixels)</span>
                                </h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop file here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="row" id="product-gallery"></div>
                        <input type="hidden" name="image_id" id="image_id">
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>

            <!-- Table will appear here -->
            <div id="banner-table" class="mt-5">
                <table class="table table-bordered" id="banners">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- rows will be appended dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        // ✅ Form submit
        $("#createBanner").submit(function(event) {
            event.preventDefault();

            // Must have an image
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
                        // ✅ Show saved banners in table
                        window.location.href = "{{ route('banner.index') }}";

                        // renderBannerTable(response.data);

                        // Reset form after save
                        $("#product-gallery").empty();
                        $("#image_id").val('');
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

        // ✅ Dropzone for single image
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
                    // clear previous image
                    $("#product-gallery").html('');
                    $("#image_id").val(response.image_id);

                    let html = `
                    <div class="col-md-3" id="image-row-${response.image_id}">
                        <div class="card">
                            <img class="card-img-top" src="${response.ImagePath}" alt="image">
                            <div class="card-body">                        
                                <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
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
                            window.location.href = "{{ route('banner.index') }}";
                        }
                    }
                })
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
                            'status' => $banner->status == 1 ? 'Active' : 'Inactive', // convert 1/0
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
                <td><img src="${banner.image}" alt="Banner ${banner.id}" width="600"></td>
                <td id="status-${banner.id}" class="${banner.status === 'Active' ? 'text-success' : 'text-danger'}">
                ${banner.status === 'Active' ? '✅ ' : ''}${banner.status}
                </td>

            </td>
                <td>
                    <button class="btn btn-sm ${banner.status === 'Active' ? 'btn-primary' : 'btn-primary'}" 
                        onclick="toggleStatus(${banner.id})">
                        ${banner.status === 'Active' ? 'Make Deactivate' : 'Make Activate'}
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deletebanner(${banner.id})">
                    Delete
                </button>
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
                    body: JSON.stringify({
                        id
                    }),
                });

                // Update local state
                banners.forEach(b => b.status = 'Inactive'); // deactivate all
                banner.status = 'Active';

                renderTable();
            } catch (err) {
                console.error('Failed to update banner status', err);
            }
        }

        renderTable();
    </script>
@endsection
