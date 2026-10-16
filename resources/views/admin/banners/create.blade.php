@extends('admin.layouts.new_app')

@section('content')
    <style>
        /* ---------- Banner upload ---------- */
        .banner-dropzone {
            border: 2px dashed var(--a-border-strong, #D9DDE5);
            border-radius: var(--a-radius, 12px);
            background: var(--a-primary-soft, #e6f5f1);
            padding: 34px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s ease, background-color .2s ease;
        }

        .banner-dropzone:hover,
        .banner-dropzone.dz-drag-hover {
            border-color: var(--a-primary, #0da487);
            background: var(--a-primary-soft, #e6f5f1);
        }

        .banner-dropzone .dz-message {
            margin: 0;
        }

        .banner-dropzone.dz-started .dz-message {
            display: none;
        }

        .banner-dropzone .banner-dropzone-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: var(--a-surface, #fff);
            color: var(--a-primary, #0da487);
            font-size: 28px;
            margin-bottom: 12px;
            box-shadow: var(--a-shadow-sm, 0 2px 8px rgba(31, 41, 55, .05));
        }

        .banner-dropzone .banner-dropzone-title {
            margin: 0 0 4px;
            font-weight: 600;
            font-size: 15px;
            color: var(--a-text, #1b2430);
        }

        .banner-dropzone .banner-dropzone-hint {
            margin: 0;
            font-size: 13px;
            color: var(--a-muted, #64748b);
        }

        .banner-dropzone .banner-dropzone-browse {
            color: var(--a-primary, #0da487);
            font-weight: 600;
        }

        .banner-preview {
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid var(--a-border, #e6eaf0);
            border-radius: var(--a-radius, 12px);
            padding: 12px;
            background: var(--a-surface, #fff);
        }

        .banner-preview-media {
            flex: none;
            width: 168px;
            height: 74px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--a-border, #e6eaf0);
            background: var(--a-bg, #f4f6f9);
        }

        .banner-preview-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .banner-preview-info {
            flex: 1;
            min-width: 0;
        }

        .banner-preview-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--a-text, #1b2430);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 6px;
        }

        .banner-progress .progress-bar {
            background-color: var(--a-primary, #0da487);
            transition: width .2s ease;
        }

        .banner-list-img {
            width: 100%;
            max-width: 320px;
            border-radius: 8px;
            border: 1px solid var(--a-border, #e6eaf0);
        }

        @media (max-width: 575.98px) {
            .banner-preview-media {
                width: 110px;
                height: 62px;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Banners</li>
                </ul>
                <h4 class="a-page-title">Banners</h4>
                <p class="a-page-desc">Manage the homepage banner. Only one banner can be active at a time.</p>
            </div>
            <div class="a-actions">
                <button type="button" class="btn btn-theme" data-bs-toggle="modal" data-bs-target="#bannerModal">
                    <i class="ri-add-line"></i> Create Banner
                </button>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Banner List</h5>
                <span class="a-cell-sub">Activating a banner automatically deactivates the others.</span>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="banners">
                    <thead>
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th>Image</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:120px;">Option</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Banner Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Create Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="image_id" id="image_id">

                    <div id="banner-dropzone" class="banner-dropzone">
                        <div class="dz-message needsclick">
                            <span class="banner-dropzone-icon"><i class="ri-upload-cloud-2-line"></i></span>
                            <p class="banner-dropzone-title">Drag &amp; drop your banner here</p>
                            <p class="banner-dropzone-hint">
                                or <span class="banner-dropzone-browse">browse files</span> ·
                                JPG, PNG, GIF, WEBP · max 5 MB
                            </p>
                        </div>
                    </div>

                    <div id="banner-upload-error" class="text-danger small mt-2 d-none"></div>

                    <div id="banner-preview" class="banner-preview mt-3 d-none">
                        <div class="banner-preview-media">
                            <img id="banner-preview-img" src="" alt="Banner preview">
                        </div>
                        <div class="banner-preview-info">
                            <div class="banner-preview-name" id="banner-preview-name">banner.jpg</div>
                            <span class="a-badge a-badge-primary"><span class="dot"></span>Selected</span>
                        </div>
                        <button type="button" class="a-action-btn a-danger" id="banner-remove" title="Remove image">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div id="banner-progress-wrap" class="banner-progress mt-3 d-none">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted" id="banner-progress-label">Uploading…</span>
                            <span class="text-muted" id="banner-progress-text">0%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div id="banner-progress-bar" class="progress-bar" role="progressbar"
                                aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-theme" id="banner-save">
                        <i class="ri-save-3-line"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        var bannerDropzone = null;

        function notify(icon, title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: icon, title: title, text: text || '', timer: 2200, showConfirmButton: false });
            } else {
                alert(title + (text ? '\n' + text : ''));
            }
        }

        function setBannerProgress(percent) {
            if (percent === null) {
                $("#banner-progress-wrap").addClass('d-none');
                $("#banner-progress-bar").css('width', '0%').attr('aria-valuenow', 0);
                $("#banner-progress-text").text('0%');
                return;
            }

            percent = Math.max(0, Math.min(100, Math.round(percent)));
            $("#banner-progress-wrap").removeClass('d-none');
            $("#banner-progress-bar").css('width', percent + '%').attr('aria-valuenow', percent);
            $("#banner-progress-text").text(percent + '%');
        }

        function bannerModalError(message) {
            setBannerProgress(null);
            $("#banner-upload-error").removeClass('d-none').text(message);
            $("#banner-save").prop('disabled', false).html('<i class="ri-save-3-line"></i> Save');
        }

        function resetBannerModal() {
            $("#image_id").val('');
            $("#banner-upload-error").addClass('d-none').text('');
            $("#banner-preview").addClass('d-none');
            $("#banner-preview-img").attr('src', '');
            $("#banner-preview-name").text('');
            $("#banner-progress-label").text('Uploading…');
            setBannerProgress(null);
            $("#banner-save").prop('disabled', false).html('<i class="ri-save-3-line"></i> Save');
            if (bannerDropzone) {
                bannerDropzone.removeAllFiles(true);
            }
        }

        function createBannerRecord() {
            $.ajax({
                url: '{{ route('banner.create') }}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    image_id: $("#image_id").val(),
                },
                dataType: 'json',
                success: function(response) {
                    if (response["status"] === true) {
                        var modalEl = document.getElementById('bannerModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }

                        notify('success', 'Banner created', 'The banner was added as inactive. Activate it from the list.');

                        setTimeout(function() {
                            window.location.href = "{{ route('banner.index') }}";
                        }, 2200);
                    } else {
                        var errors = response['errors'] || {};
                        var first = Object.values(errors)[0];
                        bannerModalError(first || 'Could not create the banner. Please try again.');
                    }
                },
                error: function(jqXHR) {
                    console.log("AJAX error:", jqXHR.responseText);
                    bannerModalError('Something went wrong. Please try again.');
                }
            });
        }

        $("#banner-save").on('click', function() {
            // Already uploaded (e.g. re-saved) — just create the record.
            if ($("#image_id").val()) {
                $("#banner-save").prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');
                setBannerProgress(100);
                $("#banner-progress-label").text('Saving…');
                createBannerRecord();
                return;
            }

            if (!bannerDropzone || bannerDropzone.getQueuedFiles().length === 0) {
                $("#banner-upload-error").removeClass('d-none').text('Please choose an image before saving.');
                return;
            }

            $("#banner-save").prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span>Uploading…');
            $("#banner-progress-label").text('Uploading…');
            setBannerProgress(0);
            bannerDropzone.processQueue();
        });

        $("#banner-remove").on('click', function() {
            if (bannerDropzone) {
                bannerDropzone.removeAllFiles(true);
            }
            $("#image_id").val('');
            $("#banner-preview").addClass('d-none');
            $("#banner-preview-img").attr('src', '');
            setBannerProgress(null);
        });

        $("#bannerModal").on('hidden.bs.modal', function() {
            resetBannerModal();
        });

        Dropzone.autoDiscover = false;
        bannerDropzone = new Dropzone("#banner-dropzone", {
            url: "{{ route('temp-images.create') }}",
            autoProcessQueue: false,
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: false,
            acceptedFiles: "image/jpeg,image/png,image/gif,image/webp",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                this.on('addedfile', function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }

                    $("#banner-upload-error").addClass('d-none').text('');
                    $("#banner-preview-img").attr('src', URL.createObjectURL(file));
                    $("#banner-preview-name").text(file.name);
                    $("#banner-preview").removeClass('d-none');
                    setBannerProgress(null);
                });

                this.on('uploadprogress', function(file, progress) {
                    setBannerProgress(progress);
                });

                this.on('success', function(file, response) {
                    var data = response;
                    if (typeof data === 'string') {
                        try {
                            data = JSON.parse(data);
                        } catch (e) {
                            data = {};
                        }
                    }

                    if (data && data.status && data.image_id) {
                        $("#image_id").val(data.image_id);
                        setBannerProgress(100);
                        $("#banner-progress-label").text('Saving…');
                        createBannerRecord();
                    } else {
                        bannerModalError((data && data.message) ? data.message :
                            'Upload failed. Please try again.');
                    }
                });

                this.on('error', function(file, message) {
                    bannerModalError(typeof message === 'string' && message ? message :
                        'Upload failed. Please try again.');
                });
            }
        });

        function deletebanner(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete this banner?',
                    text: 'This action cannot be undone.',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#DC2626',
                    cancelButtonColor: '#6B7280'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    doDeleteBanner(id);
                });
            } else if (confirm('Delete this banner?')) {
                doDeleteBanner(id);
            }
        }

        function doDeleteBanner(id) {
            $.ajax({
                url: '{{ route('banner.destroy', 'ID') }}'.replace("ID", id),
                type: 'delete',
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
    </script>

    <script>
        const banners = <?= json_encode(
            $banners
                ->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'image' => asset('uploads/banners/' . $banner->image),
                        'active' => $banner->status == 1,
                    ];
                })
                ->toArray(),
        ) ?>;

        const tbody = document.querySelector('#banners tbody');

        function renderTable() {
            tbody.innerHTML = '';

            if (!banners.length) {
                tbody.innerHTML =
                    '<tr><td colspan="4"><div class="a-empty"><div class="a-empty-icon"><i class="ri-image-line"></i></div><h5>No banners yet</h5><p>Upload your first banner.</p></div></td></tr>';
                return;
            }

            banners.forEach(function(banner) {
                const badge = banner.active ?
                    '<span class="a-badge a-badge-success"><span class="dot"></span>Active</span>' :
                    '<span class="a-badge a-badge-neutral"><span class="dot"></span>Inactive</span>';

                const toggleTitle = banner.active ? 'Deactivate' : 'Activate';
                const toggleIcon = banner.active ? 'ri-toggle-fill' : 'ri-toggle-line';
                const toggleClass = banner.active ? 'a-action-btn' : 'a-action-btn a-view';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${banner.id}</td>
                    <td><img src="${banner.image}" alt="Banner ${banner.id}" class="banner-list-img"></td>
                    <td>${badge}</td>
                    <td>
                        <div class="a-actions-cell">
                            <button type="button" class="${toggleClass}" title="${toggleTitle}"
                                onclick="toggleStatus(${banner.id})">
                                <i class="${toggleIcon}"></i>
                            </button>
                            <button type="button" class="a-action-btn a-danger" title="Delete"
                                onclick="deletebanner(${banner.id})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function toggleStatus(id) {
            const banner = banners.find(b => b.id === id);
            if (!banner) return;

            fetch("<?= route('banners.toggleStatus') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "<?= csrf_token() ?>",
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({ id }),
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if (!data.success) {
                        notify('error', 'Oops', data.message || 'Could not update status.');
                        return;
                    }

                    // Only one banner can be active at a time.
                    banners.forEach(b => b.active = false);
                    if (data.active) {
                        banner.active = true;
                    }
                    renderTable();
                })
                .catch(function() {
                    notify('error', 'Network error', 'Could not update banner status.');
                });
        }

        renderTable();
    </script>
@endsection
