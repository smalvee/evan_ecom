<style>
    .courier-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 7px 0;
        font-size: 13px;
        border-bottom: 1px dashed var(--a-border);
    }

    .courier-meta-row:last-child {
        border-bottom: 0;
    }

    .courier-meta-row span {
        color: var(--a-muted);
    }

    .courier-meta-row strong {
        color: var(--a-text);
        text-align: right;
        word-break: break-word;
    }

    .courier-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        column-gap: 20px;
    }

    .courier-sim {
        padding: 12px;
        border: 1px dashed var(--a-border-strong, var(--a-border));
        border-radius: var(--a-radius-sm);
        background: #fafbfc;
    }

    .courier-sim-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--a-muted);
        margin-bottom: 8px;
    }

    .courier-history-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 8px 0;
        border-top: 1px solid var(--a-border);
        font-size: 12px;
    }

    .courier-history-msg {
        flex: 1;
        color: var(--a-text);
        word-break: break-word;
    }

    .courier-history-time {
        color: var(--a-muted);
        white-space: nowrap;
    }

    @media (max-width: 575.98px) {
        .courier-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    (function() {
        'use strict';

        var card = document.getElementById('courierCard');
        if (!card) return;

        var alertBox = document.getElementById('courierAlert');
        var isLive = card.dataset.mode === 'live';
        var token = '{{ csrf_token() }}';

        function showAlert(success, message) {
            if (!alertBox) return;
            alertBox.hidden = false;
            alertBox.className = 'alert ' + (success ? 'alert-success' : 'alert-danger') + ' mb-3';
            alertBox.textContent = (success ? '✓ ' : '✕ ') + (message || '');
        }

        function busy(btn, on, text) {
            if (!btn) return;
            if (on) {
                btn.dataset.original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> ' + text;
            } else {
                btn.disabled = false;
                if (btn.dataset.original) btn.innerHTML = btn.dataset.original;
            }
        }

        function post(url, data, btn, text) {
            busy(btn, true, text);
            return jQuery.ajax({
                url: url,
                type: 'POST',
                data: data || {},
                dataType: 'json'
            }).always(function() {
                busy(btn, false);
            });
        }

        function reloadSoon() {
            setTimeout(function() {
                window.location.reload();
            }, 1200);
        }

        function failureMessage(xhr) {
            return (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
        }

        /* ---------- Send to courier ---------- */
        var sendBtn = document.getElementById('courierSendBtn');
        if (sendBtn) {
            sendBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Send order to courier?',
                    text: 'A ' + (isLive ? 'LIVE' : 'test') + ' consignment will be created for this order.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0da487',
                    confirmButtonText: 'Yes, send',
                    cancelButtonText: 'Cancel'
                }).then(function(res) {
                    if (!res.isConfirmed) return;

                    post(card.dataset.createUrl, {
                        _token: token
                    }, sendBtn, 'Sending…').done(function(r) {
                        showAlert(!!r.success, r.message);
                        if (r.success) reloadSoon();
                    }).fail(function(xhr) {
                        showAlert(false, failureMessage(xhr));
                    });
                });
            });
        }

        /* ---------- Refresh status ---------- */
        var refreshBtn = document.getElementById('courierRefreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                post(card.dataset.statusUrl, {
                    _token: token
                }, refreshBtn, 'Refreshing…').done(function(r) {
                    showAlert(!!r.success, r.message);
                    if (r.success) reloadSoon();
                }).fail(function(xhr) {
                    showAlert(false, failureMessage(xhr));
                });
            });
        }

        /* ---------- Cancel shipment ---------- */
        var cancelBtn = document.getElementById('courierCancelBtn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Cancel courier shipment?',
                    text: 'This cancels the courier consignment only. The ecommerce order is not changed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Cancel shipment',
                    cancelButtonText: 'Keep'
                }).then(function(res) {
                    if (!res.isConfirmed) return;

                    post(card.dataset.cancelUrl, {
                        _token: token
                    }, cancelBtn, 'Cancelling…').done(function(r) {
                        showAlert(!!r.success, r.message);
                        if (r.success) reloadSoon();
                    }).fail(function(xhr) {
                        showAlert(false, failureMessage(xhr));
                    });
                });
            });
        }

        /* ---------- Test-mode simulation ---------- */
        jQuery('.courier-sim-btn').on('click', function() {
            var btn = this;
            var status = btn.dataset.status;

            Swal.fire({
                title: 'Simulate "' + status + '"?',
                text: 'Test-mode only. This updates the courier status and history.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0da487',
                confirmButtonText: 'Simulate',
                cancelButtonText: 'Cancel'
            }).then(function(res) {
                if (!res.isConfirmed) return;

                post(card.dataset.simulateUrl, {
                    _token: token,
                    status: status
                }, btn, 'Working…').done(function(r) {
                    showAlert(!!r.success, r.message);
                    if (r.success) reloadSoon();
                }).fail(function(xhr) {
                    showAlert(false, failureMessage(xhr));
                });
            });
        });
    })();
</script>
