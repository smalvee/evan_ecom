<script>
    window.ProductForm = (function() {
        'use strict';

        const $ = window.jQuery;

        const state = {
            cfg: null,
            builder: [],
            variants: {},
            existingBySig: {},
            removed: new Set(),
            dirty: false,
            submitting: false,
        };

        /* ---------- utils ---------- */
        function escapeHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function variationById(id) {
            return (state.cfg.variations || []).find((v) => v.id === id);
        }

        function valueName(variationId, valueId) {
            const variation = variationById(variationId);
            if (!variation) return '';
            const value = variation.values.find((v) => v.id === valueId);
            return value ? value.value : '';
        }

        function signature(values) {
            return (values || []).map((v) => v.variation_id + ':' + v.value_id).sort().join('|');
        }

        function valuesLabel(values) {
            return (values || []).map((v) => (variationById(v.variation_id)?.name || '?') + ': ' + valueName(v.variation_id,
                v.value_id)).join(', ');
        }

        function variantTitle(variant) {
            if (!variant.values || !variant.values.length) return 'Default';
            return variant.values.map((v) => valueName(v.variation_id, v.value_id)).join(' / ');
        }

        function autoSku(values) {
            const base = (document.getElementById('product_sku')?.value || state.cfg.productSku || '').trim();
            const parts = [base].concat((values || []).map((v) => valueName(v.variation_id, v.value_id)));
            return parts.filter(Boolean).join('-');
        }

        function currentType() {
            const checked = document.querySelector('input[name="product_type"]:checked');
            return checked ? checked.value : '0';
        }

        function refreshSelect2(el) {
            if (!window.jQuery || !jQuery.fn.select2 || !el) return;
            if (jQuery(el).data('select2')) {
                jQuery(el).trigger('change.select2');
            } else {
                jQuery(el).select2({
                    width: '100%'
                });
            }
        }

        function initSelect2El(el, options) {
            if (!window.jQuery || !jQuery.fn.select2 || !el) return;
            if (jQuery(el).data('select2')) jQuery(el).select2('destroy');
            jQuery(el).select2(Object.assign({
                width: '100%'
            }, options || {}));
        }

        /* ---------- variation builder ---------- */
        function populateValues(select, variationId, selectedIds) {
            const variation = variationById(variationId);
            if (!variation) {
                select.innerHTML = '';
                return;
            }
            select.innerHTML = variation.values.map((v) =>
                `<option value="${v.id}" ${selectedIds.includes(v.id) ? 'selected' : ''}>${escapeHtml(v.value)}</option>`
            ).join('');
        }

        function renderBuilder() {
            const wrap = document.getElementById('variationBuilder');
            if (!wrap) return;

            wrap.innerHTML = '';

            state.builder.forEach(function(row, index) {
                const div = document.createElement('div');
                div.className = 'pf-vrow';
                div.innerHTML = `
                    <select class="form-select builder-variation" aria-label="Variation"></select>
                    <select class="form-select builder-values" multiple aria-label="Values"></select>
                    <button type="button" class="btn btn-outline-danger btn-sm builder-remove" title="Remove variation" aria-label="Remove variation"><i class="ri-delete-bin-line"></i></button>
                `;
                wrap.appendChild(div);

                const vSel = div.querySelector('.builder-variation');
                const valSel = div.querySelector('.builder-values');

                vSel.innerHTML = '<option value="">Select variation</option>' + (state.cfg.variations || []).map((v) =>
                    `<option value="${v.id}" ${v.id === row.variationId ? 'selected' : ''}>${escapeHtml(v.name)}</option>`
                ).join('');

                populateValues(valSel, row.variationId, row.valueIds);
                initSelect2El(vSel, {
                    placeholder: 'Select variation'
                });
                initSelect2El(valSel, {
                    placeholder: 'Select values',
                    closeOnSelect: false
                });

                $(vSel).on('change', function() {
                    const idx = Array.prototype.indexOf.call(wrap.children, div);
                    state.builder[idx].variationId = parseInt(this.value, 10) || 0;
                    state.builder[idx].valueIds = [];
                    populateValues(valSel, state.builder[idx].variationId, []);
                    initSelect2El(valSel, {
                        placeholder: 'Select values',
                        closeOnSelect: false
                    });
                    markDirty();
                    generateVariants();
                });

                $(valSel).on('change', function() {
                    const idx = Array.prototype.indexOf.call(wrap.children, div);
                    const selected = $(this).val();
                    state.builder[idx].valueIds = selected ? selected.map(Number) : [];
                    markDirty();
                    generateVariants();
                });

                div.querySelector('.builder-remove').addEventListener('click', function() {
                    const idx = Array.prototype.indexOf.call(wrap.children, div);
                    state.builder.splice(idx, 1);
                    markDirty();
                    renderBuilder();
                    generateVariants();
                });
            });

            const addBtn = document.getElementById('addVariationBtn');
            if (addBtn) addBtn.disabled = state.builder.length >= (state.cfg.variations || []).length;
        }

        function addVariation() {
            const used = state.builder.map((r) => r.variationId);
            const next = (state.cfg.variations || []).find((v) => !used.includes(v.id));

            if (!next) {
                Swal.fire({
                    icon: 'info',
                    title: 'All variations added'
                });
                return;
            }

            state.builder.push({
                variationId: next.id,
                valueIds: []
            });
            markDirty();
            renderBuilder();
            generateVariants();
        }

        /* ---------- generated variants ---------- */
        function generateVariants() {
            const rows = state.builder.filter((r) => r.variationId && r.valueIds.length);

            if (!rows.length) {
                state.variants = {};
                renderVariantTable();
                return;
            }

            let combos = [
                []
            ];
            rows.forEach(function(row) {
                const next = [];
                combos.forEach(function(combo) {
                    row.valueIds.forEach(function(valueId) {
                        next.push(combo.concat([{
                            variation_id: row.variationId,
                            value_id: valueId
                        }]));
                    });
                });
                combos = next;
            });

            const map = {};
            combos.forEach(function(values) {
                const sig = signature(values);
                if (state.removed.has(sig)) return;

                if (state.variants[sig]) {
                    map[sig] = state.variants[sig];
                    map[sig].values = values;
                } else if (state.existingBySig[sig]) {
                    map[sig] = Object.assign({}, state.existingBySig[sig], {
                        values: values
                    });
                } else {
                    map[sig] = {
                        id: null,
                        sku: autoSku(values),
                        allowPreOrder: false,
                        qty: null,
                        values: values
                    };
                }
            });

            state.variants = map;
            renderVariantTable();
        }

        function renderVariantTable() {
            const body = document.getElementById('variantTableBody');
            const wrap = document.getElementById('variantTableWrap');
            const empty = document.getElementById('variantEmpty');
            if (!body) return;

            body.innerHTML = '';
            const sigs = Object.keys(state.variants);

            const count = document.getElementById('variantCount');
            if (count) count.innerText = sigs.length;

            const summaryCount = document.getElementById('summaryVariantCount');
            if (summaryCount) summaryCount.innerText = sigs.length;

            if (!sigs.length) {
                if (wrap) wrap.hidden = true;
                if (empty) empty.hidden = false;
                return;
            }

            if (wrap) wrap.hidden = false;
            if (empty) empty.hidden = true;

            sigs.forEach(function(sig) {
                const variant = state.variants[sig];
                const encoded = encodeURIComponent(JSON.stringify(variant.values));
                const canDelete = !(variant.qty > 0);

                const stockBadge = (variant.qty === null || variant.qty === undefined) ?
                    '<span class="a-badge a-badge-neutral"><span class="dot"></span>No stock</span>' :
                    (variant.qty > 0 ?
                        '<span class="a-badge a-badge-success"><span class="dot"></span>Stock: ' + variant.qty + '</span>' :
                        '<span class="a-badge a-badge-danger"><span class="dot"></span>Out of stock</span>');

                const tr = document.createElement('tr');
                tr.className = 'variant-row';
                tr.dataset.signature = sig;
                tr.innerHTML = `
                    <td>
                        <div class="pf-variant-name">${escapeHtml(variantTitle(variant))}</div>
                        <div class="pf-variant-meta">Variation: ${escapeHtml(valuesLabel(variant.values))}</div>
                    </td>
                    <td>
                        <input type="text" class="form-control variant-sku-input" name="variation_sku[]" value="${escapeHtml(variant.sku)}" aria-label="Variant SKU">
                        <input type="hidden" name="variant_id[]" value="${variant.id || ''}">
                        <input type="hidden" name="variation_values[]" value="${encoded}">
                        <input type="hidden" name="allow_pre_order[]" class="variant-preorder-value" value="${variant.allowPreOrder ? '1' : '0'}">
                        <div class="invalid-feedback"></div>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input variant-preorder-toggle" ${variant.allowPreOrder ? 'checked' : ''} aria-label="Allow pre-order">
                        </div>
                    </td>
                    <td>${stockBadge}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm variant-remove" ${canDelete ? '' : 'disabled'} title="${canDelete ? 'Delete variant' : 'Cannot delete: variant has stock'}" aria-label="Delete variant">
                            <i class="ri-delete-bin-6-line"></i>
                        </button>
                    </td>
                `;
                body.appendChild(tr);
            });
        }

        function validateVariantSkus() {
            const inputs = Array.prototype.slice.call(document.querySelectorAll('.variant-sku-input'));
            const counts = {};

            inputs.forEach(function(input) {
                const value = input.value.trim().toLowerCase();
                counts[value] = (counts[value] || 0) + 1;
            });

            let valid = true;

            inputs.forEach(function(input) {
                const value = input.value.trim();
                const feedback = input.closest('td').querySelector('.invalid-feedback');

                if (!value) {
                    input.classList.add('is-invalid');
                    if (feedback) feedback.textContent = 'SKU is required.';
                    valid = false;
                } else if (counts[value.toLowerCase()] > 1) {
                    input.classList.add('is-invalid');
                    if (feedback) feedback.textContent = 'This SKU is already used by another variant.';
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                    if (feedback) feedback.textContent = '';
                }
            });

            return valid;
        }

        /* ---------- init sections ---------- */
        function initSelect2() {
            if (window.initSelect2) window.initSelect2('.js-select2');
        }

        function initSlug() {
            const name = document.getElementById('name');
            const slug = document.getElementById('slug');
            if (!name || !slug) return;

            name.addEventListener('input', function() {
                if (slug.dataset.manual === '1') return;
                slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            });

            $(name).on('change', function() {
                if (!this.value) return;
                $.ajax({
                    url: state.cfg.urls.getSlug,
                    type: 'get',
                    data: {
                        title: this.value
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) slug.value = response.slug;
                    }
                });
            });
        }

        function initCategory() {
            const category = document.getElementById('category');
            const subCategory = document.getElementById('sub_category');
            if (!category || !subCategory) return;

            if (!category.value) subCategory.disabled = true;

            $(category).on('change', function() {
                const categoryId = this.value;
                subCategory.disabled = true;
                subCategory.innerHTML = '<option value="">Loading…</option>';
                refreshSelect2(subCategory);

                $.ajax({
                    url: state.cfg.urls.subCategory,
                    type: 'get',
                    data: {
                        category_id: categoryId
                    },
                    dataType: 'json',
                    success: function(response) {
                        subCategory.innerHTML = '<option value="">Select a Sub Category</option>';
                        (response.subCategory || []).forEach(function(item) {
                            subCategory.insertAdjacentHTML('beforeend',
                                `<option value="${item.id}">${escapeHtml(item.name)}</option>`);
                        });
                        subCategory.disabled = false;
                        refreshSelect2(subCategory);
                    },
                    error: function() {
                        subCategory.innerHTML = '<option value="">Select a Sub Category</option>';
                        subCategory.disabled = false;
                        refreshSelect2(subCategory);
                    }
                });
            });
        }

        function initProductType() {
            document.querySelectorAll('input[name="product_type"]').forEach(function(radio) {
                radio.addEventListener('change', applyType);
            });
            applyType();
        }

        function applyType() {
            const type = currentType();
            const variations = document.getElementById('variationsSection');
            const variantsCard = document.getElementById('variantsCard');
            const singlePre = document.getElementById('singlePreorderWrap');
            const typeWarning = document.getElementById('typeChangeWarning');

            if (variations) variations.hidden = type !== '1';
            if (variantsCard) variantsCard.hidden = type !== '1';
            if (singlePre) singlePre.hidden = type !== '0';
            if (typeWarning) typeWarning.hidden = !(state.cfg.mode === 'edit' && type !== state.cfg.initialType);
        }

        function initVariationBuilder() {
            const addBtn = document.getElementById('addVariationBtn');
            if (addBtn) addBtn.addEventListener('click', addVariation);
        }

        function initVariantTableEvents() {
            const body = document.getElementById('variantTableBody');
            if (!body) return;

            body.addEventListener('input', function(e) {
                if (e.target.classList.contains('variant-sku-input')) {
                    const row = e.target.closest('tr');
                    const variant = state.variants[row.dataset.signature];
                    if (variant) variant.sku = e.target.value;
                    markDirty();
                    validateVariantSkus();
                }
            });

            body.addEventListener('change', function(e) {
                if (e.target.classList.contains('variant-preorder-toggle')) {
                    const row = e.target.closest('tr');
                    const variant = state.variants[row.dataset.signature];
                    if (variant) variant.allowPreOrder = e.target.checked;
                    row.querySelector('.variant-preorder-value').value = e.target.checked ? '1' : '0';
                    markDirty();
                }
            });

            body.addEventListener('click', function(e) {
                const btn = e.target.closest('.variant-remove');
                if (!btn) return;

                const row = btn.closest('tr');
                const sig = row.dataset.signature;
                const variant = state.variants[sig];

                if (variant && variant.qty > 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Cannot delete',
                        text: 'This variant has stock and cannot be deleted.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Delete this variant?',
                    text: 'It will be removed when you save.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Keep'
                }).then(function(res) {
                    if (!res.isConfirmed) return;
                    state.removed.add(sig);
                    delete state.variants[sig];
                    renderVariantTable();
                    markDirty();
                });
            });
        }

        /* ---------- dirty state ---------- */
        function markDirty() {
            if (state.dirty) return;
            state.dirty = true;
            updateDirtyUi();
        }

        function updateDirtyUi() {
            const text = document.getElementById('pfDirtyText');
            const bar = document.getElementById('productSaveBar');
            if (text) text.textContent = state.dirty ? 'Unsaved changes' : 'No unsaved changes';
            if (bar) bar.classList.toggle('is-dirty', state.dirty);
        }

        function initDirtyState() {
            const form = document.getElementById(state.cfg.formId);
            if (!form) return;

            form.addEventListener('input', function(e) {
                if (e.target.matches('input, select, textarea')) markDirty();
            });
            form.addEventListener('change', function(e) {
                if (e.target.matches('input, select, textarea')) markDirty();
            });

            window.addEventListener('beforeunload', function(e) {
                if (state.dirty && !state.submitting) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

            const cancel = document.getElementById('pfCancelBtn');
            if (cancel) {
                cancel.addEventListener('click', function() {
                    if (!state.dirty) {
                        window.location.href = state.cfg.urls.redirect;
                        return;
                    }
                    Swal.fire({
                        title: 'Discard changes?',
                        text: 'You have unsaved changes. Are you sure you want to leave?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Discard & leave',
                        cancelButtonText: 'Stay'
                    }).then(function(res) {
                        if (res.isConfirmed) {
                            state.submitting = true;
                            window.location.href = state.cfg.urls.redirect;
                        }
                    });
                });
            }
        }

        /* ---------- submit ---------- */
        function setLoading(loading) {
            const btn = document.getElementById('pfSaveBtn');
            if (!btn) return;
            if (loading) {
                btn.dataset.original = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';
            } else {
                btn.disabled = false;
                if (btn.dataset.original) btn.innerHTML = btn.dataset.original;
            }
        }

        function handleValidationErrors(errors) {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            let firstEl = null;
            let firstMessage = '';

            Object.keys(errors || {}).forEach(function(key) {
                const message = errors[key][0];
                if (!firstMessage) firstMessage = message;

                let el = document.getElementById(key);

                if (!el && /^variation_sku\.(\d+)$/.test(key)) {
                    el = document.querySelectorAll('.variant-sku-input')[parseInt(key.split('.')[1], 10)];
                }

                if (el) {
                    el.classList.add('is-invalid');
                    const feedback = el.parentElement.querySelector('.invalid-feedback');
                    if (feedback) feedback.textContent = message;
                    if (!firstEl) firstEl = el;
                }
            });

            if (firstEl) firstEl.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            Swal.fire({
                icon: 'error',
                title: 'Please fix the errors',
                text: firstMessage || 'Some fields need your attention.'
            });
        }

        function initSubmit() {
            const form = document.getElementById(state.cfg.formId);
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (state.submitting) return;

                if (currentType() === '1') {
                    if (!document.querySelectorAll('.variant-sku-input').length) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Add at least one variant',
                            text: 'Select variation values to generate variants.'
                        });
                        return;
                    }

                    if (!validateVariantSkus()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate or missing SKU',
                            text: 'Fix the highlighted variant SKUs before saving.'
                        });
                        return;
                    }
                }

                state.submitting = true;
                setLoading(true);

                $.ajax({
                    url: state.cfg.mode === 'edit' ? state.cfg.urls.update : state.cfg.urls.store,
                    type: 'POST',
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            state.dirty = false;
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                window.location.href = state.cfg.urls.redirect;
                            }, 1200);
                        } else {
                            state.submitting = false;
                            setLoading(false);
                            handleValidationErrors(response.errors || {});
                            if (response.message && !response.errors) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Unable to save',
                                    text: response.message
                                });
                            }
                        }
                    },
                    error: function() {
                        state.submitting = false;
                        setLoading(false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Unable to save',
                            text: 'Unable to save the product. No changes were applied. Please try again.'
                        });
                    }
                });
            });
        }

        /* ---------- init ---------- */
        function seedBuilderFromExisting() {
            const order = [];
            const sets = {};

            (state.cfg.existingVariants || []).forEach(function(variant) {
                (variant.values || []).forEach(function(item) {
                    if (!sets[item.variation_id]) {
                        sets[item.variation_id] = new Set();
                        order.push(item.variation_id);
                    }
                    sets[item.variation_id].add(item.value_id);
                });
            });

            state.builder = order.map(function(id) {
                return {
                    variationId: id,
                    valueIds: Array.from(sets[id])
                };
            });
        }

        function init(cfg) {
            state.cfg = cfg;
            state.builder = [];
            state.variants = {};
            state.existingBySig = {};
            state.removed = new Set();
            state.dirty = false;
            state.submitting = false;

            (cfg.existingVariants || []).forEach(function(variant) {
                state.existingBySig[signature(variant.values || [])] = {
                    id: variant.id,
                    sku: variant.sku,
                    allowPreOrder: variant.allow_pre_order,
                    qty: variant.qty,
                    values: variant.values || [],
                };
            });

            if (cfg.mode === 'edit') seedBuilderFromExisting();

            initSelect2();
            initSlug();
            initCategory();
            initProductType();
            initVariationBuilder();
            initVariantTableEvents();
            initDirtyState();
            initSubmit();

            renderBuilder();
            generateVariants();
            updateDirtyUi();
        }

        return {
            init: init
        };
    })();

    document.addEventListener('DOMContentLoaded', function() {
        if (window.PRODUCT_FORM_CONFIG) {
            window.ProductForm.init(window.PRODUCT_FORM_CONFIG);
        }
    });
</script>
