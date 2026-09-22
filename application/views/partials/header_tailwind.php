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
    </style>
</head>

<body class="w-full min-h-screen bg-gray-50">