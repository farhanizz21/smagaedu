<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/Logo_Smartedu.svg'); ?>">
    <title>SMAGAEDU - <?= $title ?? 'Master Data Guru' ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>

    <!-- jQuery (for select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap CSS (for modals) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        font-family: 'DM Sans', sans-serif;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .nav-dropdown:hover .nav-dropdown-menu {
        display: block;
    }

    /* Dukungan tap (touchscreen) — tanpa mengubah perilaku hover desktop */
    .nav-dropdown.open .nav-dropdown-menu {
        display: block;
    }

    /* Tap target lebih besar hanya di layar kecil, desktop (>767px) tetap sama */
    @media (max-width: 767.98px) {
        .nav-dropdown-menu a {
            min-height: 44px;
            padding-top: 12px;
            padding-bottom: 12px;
        }
    }

    .table-shadow {
        box-shadow: 0 25px 60px -12px rgba(37, 99, 235, 0.15);
    }

    /* Override Select2 to match Tailwind */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db !important;
        border-radius: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
        min-height: 42px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 0.5rem !important;
        color: #1d4ed8 !important;
        font-size: 0.75rem !important;
        padding: 2px 8px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #1d4ed8 !important;
        margin-right: 4px !important;
    }

        /* Custom table styling */
    .table-row-hover:hover {
                background-color: #f8fafc;
    }

    /* =================================================--------------
       Typography untuk konten Quill (.prose)
       Tailwind via CDN di sini belum memuat plugin @tailwindcss/typography,
       sehingga kelas prose / prose-sm tidak berfungsi secara otomatis.
       Akibatnya heading <h1>-<h3> dari editor Quill tidak tampil/khas.
       CSS ini memastikan heading & elemen lain diekspor/di-render semantik.
    --------------------------------------------------------------- */
    .prose :where(h1, h2, h3, h4, h5, h6) {
        font-weight: 700;
        line-height: 1.2;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
        letter-spacing: -0.01em;
    }
    .prose h1 { font-size: 2.125rem; }   /* 34px */
    .prose h2 { font-size: 1.625rem; }   /* 26px */
    .prose h3 { font-size: 1.3125rem; }  /* 21px */
    .prose h4 { font-size: 1.0625rem; }
    .prose h5 { font-size: 0.95rem; }
    .prose h6 { font-size: 0.875rem; }
    .prose :where(p) { margin: 0.65rem 0; }
    .prose :where(ul, ol) { margin: 0.65rem 0; padding-left: 1.5rem; }
    .prose :where(li) { margin: 0.15rem 0; }
    .prose :where(blockquote) {
        border-left: 4px solid #e5e7eb; margin: 1rem 0; padding: 0 1rem; color: #4b5563;
    }
    .prose :where(a) { color: #2563eb; text-decoration: underline; }
    .prose :where(img) { max-width: 100%; height: auto; display: block; margin: 0.65rem 0; }
    .prose :where(pre, code) { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
    </style>
</head>

<body class="w-full min-h-screen bg-gray-50">