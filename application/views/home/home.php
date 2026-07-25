<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/Logo_Smartedu.svg'); ?>">
    <title>SMARTEDU - Learning Management System</title>

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

    .nav-dropdown:hover .nav-dropdown-menu {
        display: block;
    }

    .hero-gradient {
        background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 50%, #FEF3C7 100%);
    }

    .dash-shadow {
        box-shadow: 0 25px 60px -12px rgba(37, 99, 235, 0.15);
    }
    </style>
</head>

<body class="w-full min-h-screen">

    <!-- Navbar -->
    <nav class="w-full sticky top-0 z-50 border-b border-gray-100"
        style="backdrop-filter:blur(16px);background:rgba(255,255,255,0.85)">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="<?= base_url('home') ?>" class="flex items-center gap-2">
                    <img src="<?= base_url('assets/img/Logo_Smartedu.svg') ?>" alt="Smartedu" class="h-8 w-8">
                    <h1 class="text-xl font-extrabold tracking-tight text-blue-600">SMARTEDU</h1>
                </a>
                <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                    <a href="<?= base_url('home') ?>"
                        class="px-3 py-2 rounded-lg bg-blue-50 text-blue-600 font-semibold">Home</a>
                    <a href="<?= base_url('panduan') ?>"
                        class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Panduan</a>
                    <a href="<?= base_url('materi') ?>" class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Mata
                        Pelajaran</a>
                    <a href="<?= base_url('proyek') ?>"
                        class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Proyek</a>
                    <a href="<?= base_url('ujian') ?>"
                        class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Ujian</a>
                    <a href="<?= base_url('perangkat') ?>"
                        class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Perangkat</a>
                    <a href="<?= base_url('kalender') ?>"
                        class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Kalender</a>
                    <?php if(in_array(user_role(), ['superadmin', 'admin'])) { ?>
                    <div class="nav-dropdown relative">
                        <a
                            class="px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 cursor-pointer flex items-center gap-1">Master
                            Data ▾</a>
                        <div
                            class="nav-dropdown-menu hidden absolute top-full left-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 py-2 min-w-[160px]">
                            <a href="<?= base_url('guru') ?>"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Data Guru</a>
                            <a href="<?= base_url('siswa') ?>"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Data Siswa</a>
                            <a href="<?= base_url('mapel') ?>"
                                class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Data Mata Pelajaran</a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="nav-dropdown relative">
                    <span class="text-sm font-medium text-gray-700 hidden sm:block cursor-pointer">Hai,
                        <?= $this->session->userdata('nama'); ?></span>
                    <div
                        class="nav-dropdown-menu hidden absolute top-full right-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 py-2 min-w-[140px]">
                        <a href="<?= base_url('logout') ?>"
                            class="block px-4 py-2 text-sm text-red-500 hover:bg-gray-50">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient w-full">
        <div class="max-w-7xl mx-auto px-6 py-16 md:py-24 grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h1 class="font-extrabold text-5xl md:text-6xl leading-tight tracking-tight text-gray-900">SMARTEDU</h1>
                <p class="text-lg leading-relaxed max-w-lg text-gray-600">
                    Platform Learning Management System untuk mendukung pembelajaran berbasis proyek secara efektif,
                    kolaboratif, dan terstruktur.
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="<?= base_url('materi') ?>"
                        class="px-6 py-3 rounded-2xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:shadow-xl transition-all">Mulai
                        Belajar</a>
                    <a href="<?= base_url('panduan') ?>"
                        class="px-6 py-3 rounded-2xl font-semibold border-2 border-blue-600 text-blue-600 bg-white hover:bg-gray-50 transition-all">Pelajari
                        Panduan</a>
                </div>
                <div class="flex flex-wrap gap-3 pt-4">
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-600">✓ Project Based
                        Learning</span>
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-600">✓
                        Terintegrasi</span>
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-600">✓ Mudah
                        Digunakan</span>
                </div>
            </div>
            <div class="relative">
                <div class="dash-shadow rounded-3xl overflow-hidden border border-gray-200 bg-white p-1">
                    <div class="bg-gray-50 rounded-t-2xl px-4 py-2.5 flex items-center gap-2 border-b border-gray-100">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="flex-1 mx-4">
                            <div
                                class="bg-white rounded-lg px-3 py-1 text-xs text-gray-400 border border-gray-200 max-w-xs">
                                smartedu.app/dashboard</div>
                        </div>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-3 bg-gradient-to-br from-slate-50 to-blue-50/30">
                        <div class="col-span-1 space-y-2">
                            <div class="bg-blue-600 text-white rounded-xl p-3 text-xs font-semibold">📊 Dashboard</div>
                            <div class="bg-white rounded-xl p-2.5 text-xs text-gray-500 border border-gray-100">📚
                                Materi</div>
                            <div class="bg-white rounded-xl p-2.5 text-xs text-gray-500 border border-gray-100">📂
                                Proyek</div>
                            <div class="bg-white rounded-xl p-2.5 text-xs text-gray-500 border border-gray-100">📅
                                Kalender</div>
                            <div class="bg-white rounded-xl p-2.5 text-xs text-gray-500 border border-gray-100">📝 Ujian
                            </div>
                        </div>
                        <div class="col-span-2 space-y-3">
                            <div class="glass-card rounded-xl p-3">
                                <div class="text-xs font-semibold text-gray-700 mb-2">Progress Belajar</div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width:72%"></div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1">72% selesai</div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="glass-card rounded-xl p-2.5 text-center">
                                    <div class="text-lg font-bold text-blue-600">24</div>
                                    <div class="text-[10px] text-gray-500">Assignment</div>
                                </div>
                                <div class="glass-card rounded-xl p-2.5 text-center">
                                    <div class="text-lg font-bold text-amber-500">8</div>
                                    <div class="text-[10px] text-gray-500">Jadwal Hari Ini</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-white rounded-xl p-2.5 border border-gray-100">
                                    <div class="text-[10px] font-medium text-gray-700">Matematika</div>
                                    <div class="text-[9px] text-gray-400 mt-0.5">Bab 4 - Aljabar</div>
                                    <div class="mt-1.5 w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-green-500 h-1.5 rounded-full" style="width:60%"></div>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl p-2.5 border border-gray-100">
                                    <div class="text-[10px] font-medium text-gray-700">Fisika</div>
                                    <div class="text-[9px] text-gray-400 mt-0.5">Proyek Energi</div>
                                    <div class="mt-1.5 w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-amber-400 h-1.5 rounded-full" style="width:40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="w-full py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-center font-bold text-3xl mb-4 text-gray-900">Fitur Unggulan</h2>
            <p class="text-center max-w-xl mx-auto mb-12 text-gray-600">Semua yang Anda butuhkan untuk mengelola
                pembelajaran digital dalam satu platform.</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card rounded-3xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl mb-4">📚
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900">Manajemen Materi</h3>
                    <p class="text-sm text-gray-600">Kelola dan distribusikan materi pembelajaran secara terstruktur.
                    </p>
                </div>
                <div class="glass-card rounded-3xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-2xl mb-4">📝
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900">Ujian Online</h3>
                    <p class="text-sm text-gray-600">Buat, kelola, dan koreksi ujian secara otomatis.</p>
                </div>
                <div class="glass-card rounded-3xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-2xl mb-4">📂
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900">Project Management</h3>
                    <p class="text-sm text-gray-600">Pantau progres proyek siswa secara real-time.</p>
                </div>
                <div class="glass-card rounded-3xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center text-2xl mb-4">📅
                    </div>
                    <h3 class="font-semibold mb-2 text-gray-900">Kalender Akademik</h3>
                    <p class="text-sm text-gray-600">Jadwalkan kegiatan akademik dengan mudah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="w-full py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-4xl font-extrabold text-blue-600">500+</div>
                    <div class="text-sm mt-1 text-gray-600">Materi</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-extrabold text-amber-500">120</div>
                    <div class="text-sm mt-1 text-gray-600">Proyek</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-extrabold text-blue-600">1.200</div>
                    <div class="text-sm mt-1 text-gray-600">Siswa</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-extrabold text-amber-500">45</div>
                    <div class="text-sm mt-1 text-gray-600">Guru</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full py-8 border-t border-gray-100 bg-gray-50">
        <p class="text-center text-sm text-gray-400">© <?= date('Y'); ?> SMARTEDU. All rights reserved.</p>
    </footer>

    <script>
    lucide.createIcons();
    </script>

</body>

</html>