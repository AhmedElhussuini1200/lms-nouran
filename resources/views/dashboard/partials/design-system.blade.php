@push('styles')
<style>
    /* ===== LMS Design System (مستوحى من نفس لغة التصميم: سكاشن + كروت أرقام + داكن) ===== */
    :root {
        --brand: {{ brand('primary_color', '#1b84ff') }};
        --brand-soft: color-mix(in srgb, {{ brand('primary_color', '#1b84ff') }} 12%, transparent);
    }
    [data-bs-theme="dark"] {
        --brand-soft: color-mix(in srgb, {{ brand('primary_color', '#1b84ff') }} 20%, transparent);
    }

    .sec { margin-bottom: 2rem; }
    .sec-head {
        display: flex; align-items: center; gap: .85rem;
        padding-bottom: .8rem; margin-bottom: 1.2rem;
        border-bottom: 1px solid var(--bs-border-color);
    }
    .sec-head__bar { width: 4px; align-self: stretch; min-height: 2.2rem; border-radius: 2px; background: var(--brand); flex: none; }
    .sec-head__title { font-size: 1.05rem; font-weight: 700; margin: 0; }
    .sec-head__desc { font-size: .8rem; margin: .15rem 0 0; color: var(--bs-gray-600); }
    .sec-head__aside { margin-inline-start: auto; }

    .stat {
        display: flex; align-items: center; gap: .9rem; height: 100%;
        background: var(--bs-card-bg); border: 1px solid var(--bs-border-color);
        border-radius: .75rem; padding: 1rem 1.1rem;
        box-shadow: 0 18px 45px -30px rgba(15,29,24,.5);
        transition: border-color .15s ease, transform .15s ease;
    }
    a.stat, a.stat * { color: inherit !important; text-decoration: none; }
    a.stat:hover { border-color: var(--brand); transform: translateY(-2px); }
    .stat__icon {
        width: 2.75rem; height: 2.75rem; border-radius: .6rem; flex: none;
        display: grid; place-items: center; background: var(--brand-soft);
    }
    .stat__icon i { font-size: 1.3rem; color: var(--brand) !important; }
    .stat__value { font-size: 1.6rem; font-weight: 700; line-height: 1; font-variant-numeric: tabular-nums; }
    .stat__label { display: block; margin-top: .25rem; font-size: .8rem; color: var(--bs-gray-600); }

    .rec {
        display: flex; align-items: center; gap: .7rem;
        padding: .65rem .25rem; border-radius: .45rem; text-decoration: none; color: inherit !important;
    }
    .rec + .rec { border-top: 1px dashed var(--bs-border-color); }
    .rec:hover { background: var(--brand-soft); }
    .rec__icon { font-size: 1.25rem; flex: none; }
    .rec__txt { font-size: .85rem; font-weight: 600; }
    .rec__lvl { margin-inline-start: auto; flex: none; }

    .mastery { margin-bottom: .7rem; }
    .mastery__top { display: flex; justify-content: space-between; font-size: .8rem; margin-bottom: .25rem; }
    .mastery__bar { height: 7px; border-radius: 999px; background: var(--bs-border-color); overflow: hidden; }
    .mastery__bar > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--brand), #17c653); }

    .risk-high { --risk: #f1416c; }
    .risk-medium { --risk: #f6c343; }
    .risk-low { --risk: #17c653; }
    .risk-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--risk); display: inline-block; }

    .hero-lms {
        background: linear-gradient(120deg, var(--brand) 0%, var(--brand) 55%, #17c653 130%);
        border-radius: .75rem; color: #fff;
    }
    .hero-lms .btn-light { border: 0; }
</style>
@endpush
