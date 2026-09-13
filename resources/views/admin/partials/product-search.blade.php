{{--
    Shared searchable product autocomplete.
    Used by the Create Order and Create Purchase pages so both share the exact
    same look, filtering (name + SKU) and keyboard behaviour.

    Usage (JS):
        const ac = ProductAutocomplete.create({
            input: inputEl,
            results: resultsEl,
            items: PRODUCTS,          // [{ id, name, sku, price, stock, allow_pre_order, active }]
            onSelect: function (product) { ... },
        });
--}}
<style>
    .pos-search {
        position: relative;
    }

    .pos-search-field {
        position: relative;
    }

    .pos-search-field > i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--a-muted);
        font-size: 20px;
        pointer-events: none;
    }

    .pos-search-field .form-control {
        height: 56px;
        padding-left: 46px;
        font-size: 16px;
    }

    /* Compact variant used inside table rows. */
    .pos-search-sm .pos-search-field .form-control {
        height: 40px;
        padding-left: 38px;
        font-size: 14px;
    }

    .pos-search-sm .pos-search-field > i {
        left: 12px;
        font-size: 16px;
    }

    .pos-search-results {
        position: fixed;
        z-index: 1080;
        max-height: 320px;
        overflow-y: auto;
        padding: 6px;
        background: #fff;
        border: 1px solid var(--a-border);
        border-radius: var(--a-radius-sm);
        box-shadow: 0 12px 30px rgba(15, 23, 42, .12);
    }

    .pos-result {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        padding: 8px 10px;
        border: 0;
        border-radius: var(--a-radius-sm);
        background: transparent;
        text-align: left;
        cursor: pointer;
    }

    .pos-result:hover,
    .pos-result.is-active {
        background: #f1f7f6;
    }

    .pos-result-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .pos-result-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--a-text);
    }

    .pos-result-sku {
        font-size: 12px;
        color: var(--a-muted);
    }

    .pos-result-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .pos-result-price {
        font-weight: 700;
        color: var(--a-primary);
        white-space: nowrap;
    }

    .pos-no-result {
        padding: 12px;
        text-align: center;
        font-size: 13px;
        color: var(--a-muted);
    }

    .pos-badge {
        display: inline-flex;
        align-items: center;
        padding: 1px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.7;
        white-space: nowrap;
    }

    .pos-badge-stock {
        background: #ecfdf5;
        color: #047857;
    }

    .pos-badge-out {
        background: #fef2f2;
        color: #b91c1c;
    }

    .pos-badge-preorder {
        background: #fffbeb;
        color: #b45309;
    }

    .pos-badge-inactive {
        background: #f1f5f9;
        color: #64748b;
    }
</style>

<script>
    window.ProductAutocomplete = (function() {
        'use strict';

        const instances = [];

        // Close every open dropdown when clicking outside a search component.
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.pos-search')) {
                instances.forEach(function(instance) {
                    instance.close();
                });
            }
        });

        // Keep open dropdowns aligned while the page/table scrolls or resizes.
        function repositionAll() {
            instances.forEach(function(instance) {
                instance.reposition();
            });
        }

        window.addEventListener('scroll', repositionAll, true);
        window.addEventListener('resize', repositionAll);

        function money(value) {
            return (Math.round((parseFloat(value) || 0) * 100) / 100).toFixed(2);
        }

        function escapeHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function productStatus(product) {
            if (product.active === false) {
                return {
                    label: 'Inactive',
                    className: 'pos-badge-inactive'
                };
            }

            if (product.stock !== null && product.stock !== undefined && product.stock <= 0) {
                return product.allow_pre_order ? {
                    label: 'Pre-order',
                    className: 'pos-badge-preorder'
                } : {
                    label: 'Out of stock',
                    className: 'pos-badge-out'
                };
            }

            if (product.stock !== null && product.stock !== undefined) {
                return {
                    label: 'Stock: ' + product.stock,
                    className: 'pos-badge-stock'
                };
            }

            return null;
        }

        function badgeHtml(product) {
            const status = productStatus(product);
            return status ? '<span class="pos-badge ' + status.className + '">' + status.label + '</span>' : '';
        }

        function create(options) {
            const input = options.input;
            const results = options.results;
            const items = options.items || [];
            const onSelect = options.onSelect || function() {};
            const maxEmpty = options.maxEmpty || 8;
            const maxResults = options.maxResults || 20;
            const minWidth = options.minWidth || 280;

            let current = [];
            let activeIndex = -1;
            let suppress = false;

            function search(query) {
                const q = (query || '').trim().toLowerCase();

                if (!q) {
                    return items.slice(0, maxEmpty);
                }

                return items.filter(function(product) {
                    return String(product.name).toLowerCase().includes(q) ||
                        String(product.sku).toLowerCase().includes(q);
                }).slice(0, maxResults);
            }

            function position() {
                const rect = input.getBoundingClientRect();
                const width = Math.min(Math.max(rect.width, minWidth), window.innerWidth - 16);
                const left = Math.max(8, Math.min(rect.left, window.innerWidth - width - 8));

                results.style.top = (rect.bottom + 6) + 'px';
                results.style.left = left + 'px';
                results.style.width = width + 'px';
            }

            function render(list) {
                current = list;
                activeIndex = list.length ? 0 : -1;

                if (!list.length) {
                    results.innerHTML = '<div class="pos-no-result">No products found</div>';
                } else {
                    results.innerHTML = list.map(function(product, index) {
                        return '<button type="button" class="pos-result ' + (index === activeIndex ?
                                'is-active' : '') + '" data-index="' + index + '">' +
                            '<span class="pos-result-main">' +
                            '<span class="pos-result-name">' + escapeHtml(product.name) + '</span>' +
                            '<span class="pos-result-sku">SKU: ' + escapeHtml(product.sku) + '</span>' +
                            '</span>' +
                            '<span class="pos-result-side">' + badgeHtml(product) +
                            '<span class="pos-result-price">৳ ' + money(product.price) + '</span>' +
                            '</span>' +
                            '</button>';
                    }).join('');
                }

                position();
                results.hidden = false;
            }

            function close() {
                results.hidden = true;
                results.innerHTML = '';
                current = [];
                activeIndex = -1;
            }

            function reposition() {
                if (!results.hidden) position();
            }

            function highlight() {
                results.querySelectorAll('.pos-result').forEach(function(el, index) {
                    el.classList.toggle('is-active', index === activeIndex);
                });
            }

            function choose(index) {
                const product = current[index];
                if (product) onSelect(product);
            }

            input.addEventListener('input', function() {
                if (suppress) return;
                render(search(this.value));
            });

            input.addEventListener('focus', function() {
                render(search(this.value));
            });

            input.addEventListener('keydown', function(e) {
                if (results.hidden) {
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        render(search(this.value));
                        e.preventDefault();
                    }
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, current.length - 1);
                    highlight();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, 0);
                    highlight();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    choose(activeIndex);
                } else if (e.key === 'Escape') {
                    close();
                }
            });

            results.addEventListener('mousedown', function(e) {
                const btn = e.target.closest('.pos-result');
                if (!btn) return;

                e.preventDefault();
                choose(parseInt(btn.dataset.index, 10));
            });

            const instance = {
                close: close,
                reposition: reposition,
                open: function() {
                    render(search(input.value));
                },
                setValue: function(value) {
                    suppress = true;
                    input.value = value;
                    suppress = false;
                },
                focus: function() {
                    input.focus();
                },
                destroy: function() {
                    close();
                    const index = instances.indexOf(instance);
                    if (index !== -1) instances.splice(index, 1);
                }
            };

            instances.push(instance);

            return instance;
        }

        return {
            create: create,
            money: money,
            escapeHtml: escapeHtml,
            badgeHtml: badgeHtml
        };
    })();
</script>
