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
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'home' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Home</a>
                <a href="<?= base_url('panduan') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'panduan' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Panduan</a>
                <a href="<?= base_url('materi') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'materi' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Materi</a>
                <a href="<?= base_url('proyek') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'proyek' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Proyek</a>
                <a href="<?= base_url('ujian') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'ujian' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Ujian</a>
                <a href="<?= base_url('kalender') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'kalender' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Kalender</a>
                <?php if($this->session->userdata('role') == 1) { ?>
                <div class="nav-dropdown relative">
                    <span
                        class="px-3 py-2 rounded-lg <?= in_array($active_nav, ['guru','siswa','mapel']) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?> cursor-pointer flex items-center gap-1 select-none">Master
                        Data ▾</span>
                    <div
                        class="nav-dropdown-menu hidden absolute top-full left-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 py-2 min-w-[160px]">
                        <a href="<?= base_url('guru') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'guru' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Guru</a>
                        <a href="<?= base_url('siswa') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'siswa' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Siswa</a>
                        <a href="<?= base_url('mapel') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'mapel' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Mata Pelajaran</a>
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
                <span class="text-sm font-medium text-gray-700 hidden sm:block cursor-pointer select-none">Hai,
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
</write_to_file>