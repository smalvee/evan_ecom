@extends('admin.layouts.new_app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="title-header option-title">
                        <h5>Suppliers</h5>
                    </div>

                    <div class="table-responsive table-product">
                        <table class="table all-package theme-table" id="table_id">
                            <thead>
                                <tr>
                                    <th>Placement</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $placements = [
                                        1 => 'Home Page Add - 01 (1600 x 138)',
                                        2 => 'Home Page Add - 02 (376 x 231)',
                                        3 => 'Home Page Add - 03 (376 x 231)',
                                        4 => 'Home Page Add - 04 (376 x 231)',
                                        5 => 'Home Page Add - 05 (376 x 231)',
                                        6 => 'Product Details Page Add - 06 (375 x 586)',
                                        7 => 'Home Page Bottom (1588 x 408)'
                                    ];
                                @endphp

                                @foreach($placements as $key => $placement)
                                <tr>
                                    <td class="text-center"><span>{{ $placement }}</span></td>
                                    <td><span>{{ $add_infos->{'name_0'.$key} }}</span></td>
                                    <td>
                                        <img style="width: 200px" src="{{ asset('uploads/add/' . $add_infos->{'image_0'.$key}) }}" alt="">
                                    </td>
                                    <td class="text-center">
                                        <button data-bs-toggle="modal" data-bs-target="#addModal_{{ $key }}" class="btn btn-primary">Change</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Modals for all ads --}}
                        @foreach($placements as $key => $placement)
                        <div class="modal fade" id="addModal_{{ $key }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModalLabel_{{ $key }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="addModalLabel_{{ $key }}">{{ $placement }}</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="" method="POST" id="add_{{ $key }}" name="add_{{ $key }}">
                                        <div class="modal-body">
                                            <div>
                                                <label for="name_{{ $key }}">Name</label>
                                                <input type="text" id="name_0{{ $key }}" name="name_0{{ $key }}" class="form-control">
                                                <p class="invalid-feedback"></p>
                                            </div>

                                             <div>
                                                <label for="url_{{ $key }}">Target Url</label>
                                                <input type="text" id="url_0{{ $key }}" name="url_0{{ $key }}" class="form-control">
                                                <p class="invalid-feedback"></p>
                                            </div>

                                            <div class="mt-3">
                                                <label for="product_image_{{ $key }}">Image</label>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="product_image_{{ $key }}" accept="image/*">
                                                    <button type="button" class="btn btn-success" id="upload_btn_{{ $key }}">Upload</button>
                                                </div>

                                                <div id="image_preview_{{ $key }}" class="row g-2 mt-3"></div>
                                                <div id="product-gallery_{{ $key }}" class="row g-3 mt-3"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary d-none" id="submit_btn_{{ $key }}">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const totalAds = 7;

    for (let i = 1; i <= totalAds; i++) {
        const productInput = document.getElementById(`product_image_${i}`);
        const uploadBtn = document.getElementById(`upload_btn_${i}`);
        const previewDiv = document.getElementById(`image_preview_${i}`);
        const galleryDiv = document.getElementById(`product-gallery_${i}`);
        const submitBtn = document.getElementById(`submit_btn_${i}`);

        if (!productInput) continue;

        // Image preview
        productInput.addEventListener('change', function() {
            previewDiv.innerHTML = '';
            [...this.files].forEach(file => {
                if (!file.type.startsWith('image/')) return;
                previewDiv.innerHTML += `
                    <div class="col-6 col-md-3">
                        <img src="${URL.createObjectURL(file)}" class="img-thumbnail" style="height:150px;object-fit:cover">
                    </div>
                `;
            });
        });

        // Upload images
        uploadBtn.addEventListener('click', async function() {
            if (!productInput.files.length) return alert('Please select images first');

            uploadBtn.disabled = true;
            const originalText = uploadBtn.innerText;
            uploadBtn.innerText = 'Uploading...';

            for (let file of productInput.files) {
                const formData = new FormData();
                formData.append('image', file);

                try {
                    const res = await fetch("{{ route('temp-images.create') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    });

                    const data = await res.json();

                    if (data.status) {
                        galleryDiv.insertAdjacentHTML('beforeend', `
                            <div class="col-md-3" id="image-row-${data.image_id}">
                                <div class="card shadow-sm">
                                    <input type="hidden" name="image_id" value="${data.image_id}">
                                    <img src="${data.ImagePath}" class="card-img-top" style="height:150px;object-fit:cover">
                                    <div class="card-body text-center">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteImage(${data.image_id})">Delete</button>
                                    </div>
                                </div>
                            </div>
                        `);
                        submitBtn.classList.remove('d-none');
                    }
                } catch (err) { console.error('Upload failed:', err); }
            }

            previewDiv.innerHTML = '';
            productInput.value = '';
            uploadBtn.disabled = false;
            uploadBtn.innerText = originalText;
        });

        // Form submission
        $(`#add_${i}`).submit(function(event) {
            event.preventDefault();
            let element = $(this);
            $.ajax({
                url: `{{ url('admin/add-store-${i}') }}`,
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $(`#add_${i} button[type=submit]`).prop('disabled', true);

                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Add created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.href = "{{ route('advertise.index') }}", 2000);
                        $(`#name_${i}`).removeClass('is-invalid').siblings('.invalid-feedback').html('');
                    } else {
                        $(`#add_${i} button[type=submit]`).prop('disabled', false);
                        let errors = response.errors;
                        if (errors[`name_${i}`]) {
                            $(`#name_${i}`).addClass('is-invalid').siblings('.invalid-feedback').html(errors[`name_${i}`]);
                        } else {
                            $(`#name_${i}`).removeClass('is-invalid').siblings('.invalid-feedback').html('');
                        }
                    }
                },
                error: function() { console.log("Something went wrong"); }
            });
        });
    }
});

// Delete image function (global)
function deleteImage(imageId) {
    const row = document.getElementById(`image-row-${imageId}`);
    if (row) row.remove();

    fetch(`/admin/delete-temp-image/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    });

    document.querySelectorAll('.card').length === 0 && document.querySelectorAll('[id^="submit_btn_"]').forEach(btn => btn.classList.add('d-none'));
}
</script>
@endsection
