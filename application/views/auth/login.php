<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/Logo_Smartedu.svg'); ?>">
    <title>Login - SMARTEDU</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>

    <style>
    body {
        font-family: 'DM Sans', sans-serif;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .glass-card-solid {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .hero-gradient {
        background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 50%, #FEF3C7 100%);
    }

    .dash-shadow {
        box-shadow: 0 25px 60px -12px rgba(37, 99, 235, 0.15);
    }

    .input-field {
        transition: all 0.2s ease;
    }

    .input-field:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    </style>
</head>

<body class="w-full min-h-screen hero-gradient flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        <!-- Login Card -->
        <div class="glass-card-solid rounded-3xl dash-shadow p-8">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <img src="<?= base_url('assets/img/Logo_Smartedu.svg') ?>" alt="Smartedu"
                    class="h-14 w-14 mx-auto mb-4">
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">SMARTEDU</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk ke akun Anda</p>
            </div>

            <!-- Error Alert -->
            <?php if($this->session->flashdata('error_msg')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
                <p class="text-sm font-medium text-red-700"><?= $this->session->flashdata('error_msg') ?></p>
            </div>
            <?php endif ?>

            <!-- Validation Errors -->
            <?php if($this->session->flashdata('validation_errors')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
                <div class="text-sm font-medium text-red-700">
                    <?= $this->session->flashdata('validation_errors') ?>
                </div>
            </div>
            <?php endif ?>

            <!-- Login Form -->
            <form action="" method="post" class="space-y-5">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" name="username" id="username"
                            class="input-field w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-gray-200 bg-white/80 text-sm font-medium text-gray-900 placeholder:text-gray-400 outline-none <?= form_error('username') ? 'border-red-300 bg-red-50' : '' ?>"
                            value="<?= set_value('username'); ?>" placeholder="Masukkan username">
                    </div>
                    <?php if(form_error('username')): ?>
                    <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <?= form_error('username') ?>
                    </p>
                    <?php endif ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password"
                            class="input-field w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-gray-200 bg-white/80 text-sm font-medium text-gray-900 placeholder:text-gray-400 outline-none <?= form_error('password') ? 'border-red-300 bg-red-50' : '' ?>"
                            value="<?= set_value('password'); ?>" placeholder="Masukkan password">
                    </div>
                    <?php if(form_error('password')): ?>
                    <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <?= form_error('password') ?>
                    </p>
                    <?php endif ?>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 rounded-2xl font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-lg shadow-blue-200 hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-400 mt-8">© <?= date('Y'); ?> SMARTEDU. All rights reserved.</p>

    </div>

    <!-- Vanta Background Effect (optional, retained for compatibility) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
    <script>
    VANTA.NET({
        el: "body",
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        scale: 1.00,
        scaleMobile: 1.00,
        color: 0x3b82f6,
        backgroundColor: 0xeff6ff
    })

    lucide.createIcons();
    </script>

</body>

</html>