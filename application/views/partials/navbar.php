<!-- Navbar -->
<?php
$CI =& get_instance();
$CI->load->model('periode_akademik_model');
$CI->load->model('chat_model');
$periode_akademik = $CI->periode_akademik_model->get_current();
$unread_chat_count = $CI->chat_model->get_unread_count($this->session->userdata('uuid'));
$CI->load->model('pengingat_model');
$role_reminders = $CI->pengingat_model->get_for_user(
    user_role(),
    $this->session->userdata('uuid'),
    $periode_akademik
);
?>

<nav class="w-full sticky top-0 z-50 border-b border-gray-100"
    style="backdrop-filter:blur(16px);background:rgba(255,255,255,0.85)">
    <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
        <div class="flex items-center gap-8">
            <a href="<?= base_url('home') ?>" class="flex items-center gap-2">
                <img src="<?= base_url('assets/img/Logo_Smartedu.svg') ?>" alt="Smartedu" class="h-8 w-8">
                <h1 class="text-xl font-extrabold tracking-tight text-blue-600">SMAGAEDU</h1>
            </a>
            <div class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="<?= base_url('home') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'home' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Home</a>
                <a href="<?= base_url('panduan') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'panduan' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Panduan</a>
                <a href="<?= base_url('mata_pelajaran') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'materi' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Mata
                    Pelajaran</a>
                <?php if(has_role(['guru','kepala_sekolah', 'admin', 'superadmin'])): ?>
                <a href="<?= base_url('perangkat') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'perangkat' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Perangkat</a>
                <?php endif; ?>
                <a href="<?= base_url('proyek') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'proyek' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Proyek</a>
                <a href="<?= base_url('ujian') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'ujian' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Ujian</a>
                <a href="<?= base_url('kalender') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'kalender' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Kalender</a>
                <?php if(has_role(['guru', 'superadmin'])): ?>
                <a href="<?= base_url('guru/jadwal') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'jadwal' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Jadwal</a>
                <?php endif; ?>
                <?php if(has_role(['kepala_sekolah', 'admin', 'superadmin'])): ?>
                <a href="<?= base_url('kepala_sekolah') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'kepala_sekolah' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                    Guru</a>
                <?php endif; ?>
                <?php if(has_role(['admin', 'superadmin'])): ?>
                <div class="nav-dropdown relative">
                    <span
                        class="nav-dropdown-trigger px-3 py-2 rounded-lg <?= in_array($active_nav, ['guru','siswa','mapel','kelas','settings']) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?> cursor-pointer flex items-center gap-1 select-none">Master
                        Data ▾</span>
                    <div
                        class="nav-dropdown-menu hidden absolute top-full left-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 py-2 min-w-[160px]">
                        <a href="<?= base_url('guru') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'guru' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Guru</a>
                        <a href="<?= base_url('siswa') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'siswa' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Siswa</a>
                        <a href="<?= base_url('kelas') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'kelas' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Kelas</a>
                        <a href="<?= base_url('mapel') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'mapel' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Mata Pelajaran</a>
                        <?php if(is_superadmin()): ?>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?= base_url('admin/kepala_sekolah') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'kepala_sekolah_admin' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Kepala Sekolah</a>
                        <?php endif; ?>
                        <?php if(has_role(['superadmin', 'admin'])): ?>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?= base_url('admin/admins') ?>"
                            class="block px-4 py-2 text-sm <?= $active_nav == 'admins' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                            Admin</a>
                        <?php endif; ?>
                        <?php if(is_superadmin()): ?>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?= base_url('admin/manage_roles') ?>"
                            class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Kelola Role</a>
                        <a href="<?= base_url('admin/manage_permissions') ?>"
                            class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Kelola Permission</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(is_superadmin()): ?>
                <a href="<?= base_url('admin/settings') ?>"
                    class="px-3 py-2 rounded-lg <?= $active_nav == 'settings' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Settings</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="nav-dropdown relative flex items-center gap-3">
                <span class="nav-dropdown-trigger flex items-center gap-3 cursor-pointer select-none" role="button"
                    tabindex="0" aria-label="Menu akun" aria-haspopup="true">
                    <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-700 hidden sm:block">Hai,
                        <?= $this->session->userdata('nama'); ?>
                        (<?= html_escape(implode(', ', (array) $this->session->userdata('roles'))); ?>)</span>
                </span>
                <div
                    class="nav-dropdown-menu hidden absolute top-full right-0 mt-1 bg-white rounded-xl shadow-lg border border-gray-100 py-2 min-w-[160px]">
                    <a href="<?= base_url('ganti_password') ?>"
                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Ganti Password</a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="<?= base_url('logout') ?>"
                        class="block px-4 py-2 text-sm text-red-500 hover:bg-gray-50">Logout</a>
                </div>
            </div>
            <!-- Tombol hamburger: hanya tampil di layar < md -->
            <button type="button" id="navbarToggle"
                class="md:hidden inline-flex items-center justify-center w-11 h-11 -mr-2 rounded-xl text-gray-600 hover:bg-gray-50 active:bg-gray-100"
                aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="navbarMobileMenu">
                <svg id="navbarIconOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="6" y2="6"></line>
                    <line x1="4" x2="20" y1="12" y2="12"></line>
                    <line x1="4" x2="20" y1="18" y2="18"></line>
                </svg>
                <svg id="navbarIconClose" class="hidden" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" x2="6" y1="6" y2="18"></line>
                    <line x1="6" x2="18" y1="6" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
    <!-- Menu mobile (portrait): hanya untuk layar < md -->
    <div id="navbarMobileMenu" class="hidden md:hidden border-t border-gray-100"
        style="background:rgba(255,255,255,0.98);backdrop-filter:blur(16px)">
        <div
            class="max-w-7xl mx-auto px-6 py-3 flex flex-col text-sm font-medium max-h-[calc(100vh-4rem)] overflow-y-auto">
            <a href="<?= base_url('home') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'home' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Home</a>
            <a href="<?= base_url('panduan') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'panduan' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Panduan</a>
            <a href="<?= base_url('mata_pelajaran') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'materi' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Mata
                Pelajaran</a>
            <?php if(has_role(['guru','kepala_sekolah', 'admin', 'superadmin'])): ?>
            <a href="<?= base_url('perangkat') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'perangkat' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Perangkat</a>
            <?php endif; ?>
            <a href="<?= base_url('proyek') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'proyek' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Proyek</a>
            <a href="<?= base_url('ujian') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'ujian' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Ujian</a>
            <a href="<?= base_url('kalender') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'kalender' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Kalender</a>
            <?php if(has_role(['guru', 'superadmin'])): ?>
            <a href="<?= base_url('guru/jadwal') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'jadwal' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Jadwal</a>
            <?php endif; ?>
            <?php if(has_role(['kepala_sekolah', 'admin', 'superadmin'])): ?>
            <a href="<?= base_url('kepala_sekolah') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'kepala_sekolah' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Guru</a>
            <?php endif; ?>
            <?php if(has_role(['admin', 'superadmin'])): ?>
            <div class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Master Data</div>
            <a href="<?= base_url('guru') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'guru' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Guru</a>
            <a href="<?= base_url('siswa') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'siswa' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Siswa</a>
            <a href="<?= base_url('kelas') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'kelas' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Kelas</a>
            <a href="<?= base_url('mapel') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'mapel' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Mata Pelajaran</a>
            <?php if(is_superadmin()): ?>
            <a href="<?= base_url('admin/kepala_sekolah') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'kepala_sekolah_admin' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Kepala Sekolah</a>
            <?php endif; ?>
            <?php if(has_role(['superadmin', 'admin'])): ?>
            <a href="<?= base_url('admin/admins') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'admins' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Data
                Admin</a>
            <?php endif; ?>
            <?php if(is_superadmin()): ?>
            <a href="<?= base_url('admin/manage_roles') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg text-gray-600 hover:bg-gray-50">Kelola Role</a>
            <a href="<?= base_url('admin/manage_permissions') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg text-gray-600 hover:bg-gray-50">Kelola
                Permission</a>
            <a href="<?= base_url('admin/settings') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg <?= ($active_nav ?? '') == 'settings' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' ?>">Settings</a>
            <?php endif; ?>
            <?php endif; ?>
            <div class="border-t border-gray-100 my-2"></div>
            <div class="px-3 py-1.5 text-xs text-gray-400">Hai,
                <?= $this->session->userdata('nama'); ?> (<?= user_role(); ?>)</div>
            <a href="<?= base_url('ganti_password') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg text-gray-600 hover:bg-gray-50">Ganti
                Password</a>
            <a href="<?= base_url('logout') ?>"
                class="flex items-center min-h-[44px] px-3 rounded-lg text-red-500 hover:bg-red-50">Logout</a>
        </div>
    </div>
</nav>

<?php $CI->load->view('partials/chat_widget', array('unread_chat_count' => $unread_chat_count)); ?>

<?php if ($periode_akademik): ?>
<div class="border-b border-red-100 bg-red-50/70">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-6 py-2">
        <div class="flex min-w-0 items-center gap-2 text-sm text-red-800">
            <i data-lucide="calendar-days" class="h-4 w-4 shrink-0 text-red-600"></i>
            <span class="truncate text-red-600">Periode pembelajaran saat ini</span>
            <span class="hidden text-red-300 sm:inline">·</span>
            <span class="shrink-0 font-semibold">Pekan <?= html_escape($periode_akademik->pekan); ?></span>
            <span class="text-red-300">·</span>
            <span class="shrink-0 font-semibold">Semester <?= html_escape($periode_akademik->semester); ?></span>
        </div>
        <a href="<?= base_url('periode_akademik'); ?>"
            class="shrink-0 rounded-lg px-2.5 py-1 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100">
            Lihat
        </a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($role_reminders)): ?>
<section class="border-b-2 border-amber-300 bg-gradient-to-r from-amber-100 via-orange-50 to-amber-100 shadow-sm"
    aria-label="Pengingat pembelajaran">
    <div class="mx-auto max-w-7xl px-6 py-3">
        <div class="flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm">
                    <i data-lucide="bell-ring" class="h-5 w-5"></i>
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-sm font-extrabold text-amber-950">Pengingat untuk Anda</h2>
                        <span class="rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-bold text-white shadow-sm">
                            <?= count($role_reminders) ?> perlu ditindaklanjuti
                        </span>
                    </div>
                    <p class="hidden text-xs text-amber-900/80 sm:block">Jangan lewatkan materi, ujian, atau pembaruan penting.</p>
                </div>
            </div>
            <button type="button" id="reminderToggle"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-amber-400 bg-white px-3 py-2 text-xs font-bold text-amber-950 shadow-sm transition hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2"
                aria-expanded="true" aria-controls="reminderContent">
                <span id="reminderToggleLabel">Sembunyikan</span>
                <i data-lucide="chevron-up" id="reminderChevronUp" class="h-4 w-4"></i>
                <i data-lucide="chevron-down" id="reminderChevronDown" class="hidden h-4 w-4"></i>
            </button>
        </div>
        <div id="reminderContent" class="mt-3">
            <ul class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($role_reminders as $reminder): ?>
                <li class="flex items-start justify-between gap-3 rounded-xl border-l-4 <?= !empty($reminder['urgent']) ? 'border-l-red-500 border-y border-r border-y-amber-200 border-r-amber-200 bg-white' : 'border-l-orange-400 border-y border-r border-y-amber-200 border-r-amber-200 bg-white/90' ?> px-3 py-2.5 shadow-sm">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900"><?= html_escape($reminder['title']) ?></p>
                        <p class="mt-0.5 text-xs leading-relaxed text-gray-700"><?= html_escape($reminder['message']) ?></p>
                    </div>
                    <a href="<?= html_escape($reminder['url']) ?>"
                        class="shrink-0 rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2">
                        <?= html_escape($reminder['label']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
<?php endif; ?>


<script>
(function() {
    var reminderToggle = document.getElementById('reminderToggle');
    var reminderContent = document.getElementById('reminderContent');
    if (reminderToggle && reminderContent) {
        var reminderStorageKey = 'smagaedu-reminders-<?= html_escape($this->session->userdata('uuid')) ?>';
        var reminderLabel = document.getElementById('reminderToggleLabel');
        var reminderChevronUp = document.getElementById('reminderChevronUp');
        var reminderChevronDown = document.getElementById('reminderChevronDown');
        var setReminderExpanded = function(expanded) {
            reminderToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            reminderContent.classList.toggle('hidden', !expanded);
            if (reminderLabel) reminderLabel.textContent = expanded ? 'Sembunyikan' : 'Tampilkan';
            if (reminderChevronUp) reminderChevronUp.classList.toggle('hidden', !expanded);
            if (reminderChevronDown) reminderChevronDown.classList.toggle('hidden', expanded);
        };
        try {
            if (window.localStorage.getItem(reminderStorageKey) === 'collapsed') {
                setReminderExpanded(false);
            }
        } catch (error) {
            // Panel tetap dapat digunakan meski penyimpanan browser tidak tersedia.
        }
        reminderToggle.addEventListener('click', function() {
            var expanded = reminderToggle.getAttribute('aria-expanded') !== 'true';
            setReminderExpanded(expanded);
            try {
                window.localStorage.setItem(reminderStorageKey, expanded ? 'expanded' : 'collapsed');
            } catch (error) {
                // Perubahan tetap berlaku sampai halaman dimuat ulang.
            }
        });
    }

    var navbarToggle = document.getElementById('navbarToggle');
    var navbarMobileMenu = document.getElementById('navbarMobileMenu');
    if (navbarToggle && navbarMobileMenu) {
        var setIcons = function(open) {
            var iconOpen = document.getElementById('navbarIconOpen');
            var iconClose = document.getElementById('navbarIconClose');
            if (iconOpen) iconOpen.classList.toggle('hidden', open);
            if (iconClose) iconClose.classList.toggle('hidden', !open);
        };
        navbarToggle.addEventListener('click', function() {
            var isOpen = !navbarMobileMenu.classList.contains('hidden');
            navbarMobileMenu.classList.toggle('hidden', isOpen);
            navbarToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            setIcons(isOpen);
        });
        navbarMobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                navbarMobileMenu.classList.add('hidden');
                navbarToggle.setAttribute('aria-expanded', 'false');
                setIcons(false);
            });
        });
    }

    // Dropdown dapat di-tap di touchscreen; hover tetap berlaku di desktop
    document.querySelectorAll('.nav-dropdown-trigger').forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var dropdown = trigger.closest('.nav-dropdown');
            if (!dropdown) return;
            var wasOpen = dropdown.classList.contains('open');
            document.querySelectorAll('.nav-dropdown.open').forEach(function(d) {
                d.classList.remove('open');
            });
            if (!wasOpen) dropdown.classList.add('open');
        });
        trigger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                trigger.click();
            }
        });
    });
    document.addEventListener('click', function() {
        document.querySelectorAll('.nav-dropdown.open').forEach(function(d) {
            d.classList.remove('open');
        });
    });
})();
</script>