@extends('admin.layouts.new_app')

@section('content')
<div class="container-fluid">
    <div class="a-page-head">
        <div class="a-page-head-text">
            <ul class="a-breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="is-active">Advertisements</li>
            </ul>
            <h4 class="a-page-title">Advertisements</h4>
            <p class="a-page-desc">Manage advertisement slots across your store.</p>
        </div>
    </div>

    @php
        $placements = [
            1 => 'Home Page Add - 01 (1600 x 138)',
            2 => 'Home Page Add - 02 (376 x 231)',
            3 => 'Home Page Add - 03 (376 x 231)',
            4 => 'Home Page Add - 04 (376 x 231)',
            5 => 'Home Page Add - 05 (376 x 231)',
            6 => 'Product Details Page Add - 06 (375 x 586)',
            7 => 'Home Page Bottom (1588 x 408)',
        ];
    @endphp

    <div class="row g-3">
        @foreach($placements as $key => $placement)
        @php $active = $add_infos->isSlotActive($key); @endphp
        <div class="col-sm-6 col-xl-4 col-xxl-3">
            <div class="a-card h-100">
                <div class="a-card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if (!empty($add_infos->{'image_0'.$key}))
                            <img id="image-display-{{ $key }}" src="{{ asset('uploads/add/' . $add_infos->{'image_0'.$key}) }}"
                                alt="" style="width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid var(--a-border);">
                        @else
                            <div id="image-display-{{ $key }}"
                                style="width:64px;height:64px;flex:none;border-radius:8px;background:var(--a-bg);display:flex;align-items:center;justify-content:center;color:var(--a-muted-2);font-size:22px;">
                                <i class="ri-image-line"></i>
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <h6 class="a-cell-main mb-1">{{ $placement }}</h6>
                            <div id="name-display-{{ $key }}" class="a-cell-sub text-truncate">{{ $add_infos->{'name_0'.$key} ?: 'No title' }}</div>
                        </div>
                    </div>
                    <div class="a-cell-sub mb-3 text-truncate">
                        {{ $add_infos->{'url_0'.$key} ?: 'No target URL set' }}
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                        style="border-top: 1px solid var(--a-border);">
                        <button data-bs-toggle="modal" data-bs-target="#addModal_{{ $key }}"
                            class="btn btn-theme btn-sm"><i class="ri-edit-line"></i> Change</button>
                        <div class="d-flex align-items-center gap-2">
                            <span class="a-badge {{ $active ? 'a-badge-success' : 'a-badge-secondary' }}"
                                id="status-badge-{{ $key }}">
                                <span class="dot"></span>{{ $active ? 'Active' : 'Inactive' }}
                            </span>
                            <div class="form-check form-switch mb-0" title="Toggle active / inactive">
                                <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                    id="status-toggle-{{ $key }}" data-slot="{{ $key }}"
                                    {{ $active ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Modals for all ads --}}
@foreach($placements as $key => $placement)
<div class="modal fade" id="addModal_{{ $key }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addModalLabel_{{ $key }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel_{{ $key }}">{{ $placement }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="add_{{ $key }}" name="add_{{ $key }}">
                <input type="hidden" name="slot" value="{{ $key }}">
                <div class="modal-body">
                    @if (!empty($add_infos->{'image_0'.$key}))
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="{{ asset('uploads/add/' . $add_infos->{'image_0'.$key}) }}"
                                    class="img-thumbnail" style="max-height: 150px;" alt="">
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="product_image_{{ $key }}" class="form-label">Image <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="product_image_{{ $key }}" accept="image/*">
                            <button type="button" class="btn btn-success" id="upload_btn_{{ $key }}">Upload</button>
                        </div>
                        <div id="image_preview_{{ $key }}" class="row g-2 mt-2"></div>
                        <div id="product-gallery_{{ $key }}" class="row g-2 mt-2"></div>
                        <input type="hidden" name="image_id" id="image_id_{{ $key }}">
                        <p class="invalid-feedback d-block" id="image_error_{{ $key }}"></p>
                    </div>

                    <div class="mb-3">
                        <label for="name_0{{ $key }}" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" id="name_0{{ $key }}" name="name_0{{ $key }}" class="form-control"
                            value="{{ $add_infos->{'name_0'.$key} ?? '' }}">
                        <p class="invalid-feedback"></p>
                    </div>

                    <div class="mb-3">
                        <label for="url_0{{ $key }}" class="form-label">Target URL <span class="text-danger">*</span></label>
                        <input type="text" id="url_0{{ $key }}" name="url_0{{ $key }}" class="form-control"
                            value="{{ $add_infos->{'url_0'.$key} ?? '' }}" placeholder="https://example.com">
                        <p class="invalid-feedback"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
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
        const imageIdInput = document.getElementById(`image_id_${i}`);
        const imageError = document.getElementById(`image_error_${i}`);

        if (!productInput) continue;

        // Image preview before upload
        productInput.addEventListener('change', function() {
            previewDiv.innerHTML = '';
            [...this.files].forEach(file => {
                if (!file.type.startsWith('image/')) return;
                previewDiv.innerHTML += `
                    <div class="col-6 col-md-3">
                        <img src="${URL.createObjectURL(file)}" class="img-thumbnail" style="height:120px;object-fit:cover">
                    </div>
                `;
            });
        });

        // Upload image to temp storage
        uploadBtn.addEventListener('click', async function() {
            if (!productInput.files.length) return alert('Please select an image first');

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
                        galleryDiv.innerHTML = `
                            <div class="col-12" id="image-row-${data.image_id}">
                                <div class="card shadow-sm">
                                    <img src="${data.ImagePath}" class="card-img-top" style="height:150px;object-fit:cover">
                                    <div class="card-body text-center">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeImage(${i}, ${data.image_id})">Delete</button>
                                    </div>
                                </div>
                            </div>`;
                        imageIdInput.value = data.image_id;
                        imageError.innerHTML = '';
                    } else {
                        alert('Image upload failed');
                    }
                } catch (err) {
                    console.error('Upload failed:', err);
                }
            }

            previewDiv.innerHTML = '';
            productInput.value = '';
            uploadBtn.disabled = false;
            uploadBtn.innerText = originalText;
        });

        // Save advertisement via AJAX
        $(`#add_${i}`).submit(function(event) {
            event.preventDefault();
            let element = $(this);

            $.ajax({
                url: `{{ route('advertisements.update', 'SLOT') }}`.replace('SLOT', i),
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $(`#addModal_${i}`).modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.href = "{{ route('advertise.index') }}";
                        }, 1500);
                    } else {
                        let errors = response.errors || {};

                        $(`#add_${i} .form-control`).removeClass('is-invalid');
                        $(`#add_${i} .invalid-feedback`).html('');

                        $.each(errors, function(key, value) {
                            let message = Array.isArray(value) ? value[0] : value;

                            if (key === 'image_id') {
                                imageError.innerHTML = message;
                                return;
                            }

                            let input = $(`#add_${i} [name="${key}"]`);
                            if (input.length) {
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').html(message);
                            }
                        });
                    }
                },
                error: function() {
                    console.log('Something went wrong');
                }
            });
        });
    }
});

function removeImage(slot, imageId) {
    document.getElementById(`product-gallery_${slot}`).innerHTML = '';
    document.getElementById(`image_id_${slot}`).value = '';

    fetch(`/admin/delete-temp-image/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    });
}

// Active / inactive toggle per advertisement slot
document.querySelectorAll('.status-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const checkbox = this;
        const slot = this.dataset.slot;

        $.ajax({
            url: `{{ route('advertisements.toggleStatus', 'SLOT') }}`.replace('SLOT', slot),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    const badge = document.getElementById(`status-badge-${slot}`);
                    badge.className = 'a-badge ' + (response.active ? 'a-badge-success' :
                        'a-badge-secondary');
                    badge.innerHTML = '<span class="dot"></span>' + (response.active ? 'Active' :
                        'Inactive');

                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        timer: 1200,
                        showConfirmButton: false
                    });
                } else {
                    checkbox.checked = !checkbox.checked;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Unable to update status.'
                    });
                }
            },
            error: function() {
                checkbox.checked = !checkbox.checked;
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong'
                });
            }
        });
    });
});
</script>
@endsection
