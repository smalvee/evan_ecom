<style>
    /* ---------- Layout ---------- */
    .pf-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 16px;
        align-items: start;
    }

    @media (max-width: 1199.98px) {
        .pf-grid {
            grid-template-columns: 1fr;
        }
    }

    .pf-side {
        position: sticky;
        top: 80px;
    }

    @media (max-width: 1199.98px) {
        .pf-side {
            position: static;
        }
    }

    .pf-stack > .a-card {
        margin-bottom: 16px;
    }

    /* ---------- Section header ---------- */
    .pf-head {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .pf-head .pf-ico {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: var(--a-primary-soft, #e6f6f3);
        color: var(--a-primary);
        font-size: 18px;
    }

    .pf-head h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--a-text);
    }

    .pf-head p {
        margin: 0;
        font-size: 12px;
        color: var(--a-muted);
    }

    /* ---------- Product type selector ---------- */
    .pf-types {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 575.98px) {
        .pf-types {
            grid-template-columns: 1fr;
        }
    }

    .pf-type-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .pf-type-card {
        display: block;
        padding: 12px 14px;
        border: 1px solid var(--a-border);
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pf-type-card strong {
        display: block;
        font-size: 14px;
        color: var(--a-text);
    }

    .pf-type-card small {
        color: var(--a-muted);
    }

    .pf-type-input:checked + .pf-type-card {
        border-color: var(--a-primary);
        box-shadow: 0 0 0 3px rgba(13, 164, 135, .12);
    }

    .pf-type-input:focus-visible + .pf-type-card {
        outline: 2px solid var(--a-primary);
        outline-offset: 2px;
    }

    /* ---------- Variation builder ---------- */
    .pf-vrow {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.7fr) auto;
        gap: 8px;
        align-items: start;
        margin-bottom: 8px;
    }

    @media (max-width: 575.98px) {
        .pf-vrow {
            grid-template-columns: 1fr;
        }
    }

    /* ---------- Generated variants table ---------- */
    .pf-variant-table th,
    .pf-variant-table td {
        vertical-align: middle;
    }

    .pf-variant-name {
        font-weight: 600;
        color: var(--a-text);
    }

    .pf-variant-meta {
        font-size: 12px;
        color: var(--a-muted);
    }

    .pf-variant-table .variant-sku-input {
        min-width: 170px;
    }

    /* ---------- Sticky save bar ---------- */
    .pf-savebar {
        position: sticky;
        bottom: 0;
        z-index: 1020;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        padding: 12px 16px;
        background: #fff;
        border: 1px solid var(--a-border);
        border-radius: 12px;
        box-shadow: 0 -4px 16px rgba(15, 23, 42, .06);
    }

    .pf-dirty {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--a-muted);
    }

    .pf-dirty .pf-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
    }

    .pf-dirty.is-dirty .pf-dot {
        background: var(--a-warning);
    }

    @media (max-width: 575.98px) {
        .pf-savebar {
            flex-direction: column;
            align-items: stretch;
        }

        .pf-savebar .btn {
            width: 100%;
        }
    }
</style>
