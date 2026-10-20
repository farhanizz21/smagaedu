<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Data Guru - Kepala Sekolah']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Data Guru</h1>
            <p class="text-gray-500 mt-1 text-sm">Pantau data guru, jadwal mengajar, dan kelengkapan perangkat
                pembelajaran</p>
        </div>
    </div>

    <?php if(!empty($admin_filter_enabled)): ?>
    <form method="get" action="<?= base_url('kepala_sekolah') ?>" class="flex flex-col sm:flex-row gap-3 mb-6">
        <select name="admin_uuid" onchange="this.form.submit()"
            class="w-full sm:max-w-xs px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
            <option value="">Semua Admin</option>
            <option value="__unassigned__" <?= ($filters['admin_uuid'] === '__unassigned__') ? 'selected' : '' ?>>Belum
                ditugaskan</option>
            <?php foreach($admins as $admin): ?>
            <option value="<?= html_escape($admin->uuid) ?>"
                <?= ($filters['admin_uuid'] === $admin->uuid) ? 'selected' : '' ?>><?= html_escape($admin->nama) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <a href="<?= base_url('kepala_sekolah') ?>"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50">
            <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset filter
        </a>
    </form>
    <?php endif; ?>

    <?php if ($this->session->userdata('success_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0"></i>
        <?= $this->session->userdata('success_msg'); ?>
        <?php $this->session->unset_userdata('success_msg'); ?>
    </div>
    <?php endif; ?>

    <?php if ($this->session->userdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->userdata('error_msg'); ?>
        <?php $this->session->unset_userdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 table-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= count($guru) ?></p>
                    <p class="text-sm text-gray-500">Total Guru</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 table-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <div>
                    <?php 
                    $guru_with_jadwal = 0;
                    foreach ($guru as $g) {
                        if ($g->jadwal_count > 0) $guru_with_jadwal++;
                    }
                    ?>
                    <p class="text-2xl font-bold text-gray-900"><?= $guru_with_jadwal ?></p>
                    <p class="text-sm text-gray-500">Upload Jadwal</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 table-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i data-lucide="book-open" class="w-6 h-6 text-amber-600"></i>
                </div>
                <div>
                    <?php 
                    $total_mapel = 0;
                    foreach ($guru as $g) {
                        $total_mapel += count($g->mapel_nama ?? []);
                    }
                    ?>
                    <p class="text-2xl font-bold text-gray-900"><?= $total_mapel ?></p>
                    <p class="text-sm text-gray-500">Total Mapel Diampu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 table-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-check" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <?php 
                    $guru_updated = 0;
                    foreach ($guru as $g) {
                        if ($g->mapel_updated) $guru_updated++;
                    }
                    ?>
                    <p class="text-2xl font-bold text-gray-900"><?= $guru_updated ?>/<?= count($guru) ?></p>
                    <p class="text-sm text-gray-500">Sudah Update Mapel</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Guru Terbaik -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-yellow-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i data-lucide="trophy" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Guru Terbaik</h3>
                        <p class="text-xs text-gray-500">Peringkat guru paling rajin upload & update mapel</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (!empty($best_guru)): ?>
                <div class="space-y-3">
                    <?php foreach ($best_guru as $i => $bg): ?>
                    <div
                        class="flex items-center gap-4 p-3 rounded-xl <?= $i == 0 ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 hover:bg-amber-50 transition-colors' ?>">
                        <div class="relative flex-shrink-0">
                            <div
                                class="w-11 h-11 rounded-full <?= $i == 0 ? 'bg-gradient-to-br from-amber-400 to-yellow-600' : ($i == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500' : 'bg-gradient-to-br from-orange-300 to-orange-500') ?> flex items-center justify-center text-white font-bold text-sm">
                                <?= strtoupper(substr($bg->nama, 0, 1)) ?>
                            </div>
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 rounded-full <?= $i == 0 ? 'bg-amber-500' : ($i == 1 ? 'bg-gray-400' : 'bg-orange-400') ?> flex items-center justify-center text-white text-[10px] font-bold">
                                <?= $i + 1 ?>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate"><?= $bg->nama ?></p>
                            <p class="text-xs text-gray-500">
                                <?= $bg->materi_count ?> pekan • <?= $bg->bab_count ?> pertemuan •
                                <?= $bg->ujian_count ?>
                                ujian
                                <?php if ($bg->last_update): ?>
                                • Update <?= date('d M Y', strtotime($bg->last_update)) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <?php if ($bg->last_update): ?>
                            <p class="text-xs font-semibold <?= $i == 0 ? 'text-amber-600' : 'text-gray-700' ?>">
                                <?= date('d M Y', strtotime($bg->last_update)) ?>
                            </p>
                            <p class="text-[10px] text-gray-400">update terakhir</p>
                            <?php else: ?>
                            <p class="text-xs font-semibold text-gray-400">Belum update</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <i data-lucide="trophy" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada data guru</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Guru Perlu Perhatian -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-rose-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Perlu Perhatian</h3>
                        <p class="text-xs text-gray-500">Guru yang jarang / belum update mapel</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (!empty($worst_guru)): ?>
                <div class="space-y-3">
                    <?php foreach ($worst_guru as $i => $wg): ?>
                    <div
                        class="flex items-center gap-4 p-3 rounded-xl <?= $wg->total_content == 0 ? 'bg-red-50 border border-red-200' : 'bg-gray-50 hover:bg-red-50 transition-colors' ?>">
                        <div class="relative flex-shrink-0">
                            <div
                                class="w-11 h-11 rounded-full bg-gradient-to-br from-red-300 to-red-500 flex items-center justify-center text-white font-bold text-sm">
                                <?= strtoupper(substr($wg->nama, 0, 1)) ?>
                            </div>
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 flex items-center justify-center text-white text-[10px] font-bold">
                                <?= $wg->rank ?>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate"><?= $wg->nama ?></p>
                            <p class="text-xs text-gray-500">
                                <?php if ($wg->total_content == 0): ?>
                                <span class="text-red-500 font-medium">Belum ada konten sama sekali</span>
                                <?php else: ?>
                                <?= $wg->materi_count ?> pekan • <?= $wg->bab_count ?> pertemuan •
                                <?= $wg->ujian_count ?> ujian
                                <?php if ($wg->last_update): ?>
                                • Terakhir <?= date('d M Y', strtotime($wg->last_update)) ?>
                                <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <?php if ($wg->last_update): ?>
                            <p class="text-xs font-semibold text-red-500">
                                <?= date('d M Y', strtotime($wg->last_update)) ?>
                            </p>
                            <p class="text-[10px] text-gray-400">update terakhir</p>
                            <?php else: ?>
                            <p class="text-xs font-semibold text-gray-400">Belum update</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">Semua guru sudah aktif</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div id="guruList" class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="border-b border-gray-100 p-5">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                <label class="relative md:col-span-2">
                    <span class="sr-only">Cari guru</span>
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
                    <input type="search" class="search w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        placeholder="Cari nama, mata pelajaran, atau admin...">
                </label>
                <label>
                    <span class="sr-only">Filter mata pelajaran</span>
                    <select id="guruMapelFilter" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Mata Pelajaran</option>
                        <?php
                        $mapel_filter_options = [];
                        foreach ($guru as $guru_item) {
                            foreach ($guru_item->mapel_nama ?? [] as $nama_mapel) {
                                $mapel_filter_options[$nama_mapel] = $nama_mapel;
                            }
                        }
                        natcasesort($mapel_filter_options);
                        foreach ($mapel_filter_options as $nama_mapel):
                        ?>
                        <option value="<?= html_escape($nama_mapel) ?>"><?= html_escape($nama_mapel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span class="sr-only">Filter status update mapel</span>
                    <select id="guruStatusFilter" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Status Update</option>
                        <option value="updated">Sudah ada update</option>
                        <option value="not-updated">Belum ada update</option>
                    </select>
                </label>
                <label>
                    <span class="sr-only">Jumlah guru per halaman</span>
                    <select id="guruPageSize" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="10">10 guru per halaman</option>
                        <option value="25">25 guru per halaman</option>
                        <option value="50">50 guru per halaman</option>
                    </select>
                </label>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            Rank</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama</th>
                        <th
                            class="text-left px-4 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-56">
                            Admin / Pembuat</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider min-w-56">
                            Mata Pelajaran</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Jadwal</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Update Mapel</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Update Terakhir</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-28">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="guru-items divide-y divide-gray-100">
                    <?php 
                        foreach($guru as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-center">
                            <?php if ($val->rank == 1): ?>
                            <span
                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-600 font-bold text-xs">
                                <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                            </span>
                            <?php elseif ($val->rank == 2): ?>
                            <span
                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 text-gray-600 font-bold text-xs">
                                <i data-lucide="medal" class="w-3.5 h-3.5"></i>
                            </span>
                            <?php elseif ($val->rank == 3): ?>
                            <span
                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 font-bold text-xs">
                                <i data-lucide="medal" class="w-3.5 h-3.5"></i>
                            </span>
                            <?php else: ?>
                            <span
                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-500 font-bold text-xs">
                                <?= $val->rank ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    <?= strtoupper(substr($val->nama, 0, 1)) ?>
                                </div>
                                <span class="name font-medium text-gray-900"><?= html_escape($val->nama); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="admin block max-w-56 truncate text-gray-600"
                                title="<?= html_escape($val->admin_nama ?? 'Belum ditugaskan'); ?>">
                                <?= html_escape($val->admin_nama ?? 'Belum ditugaskan'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex max-w-sm flex-wrap gap-1.5">
                                <?php if (!empty($val->mapel_nama)) : ?>
                                <?php foreach ($val->mapel_nama as $nama_mapel) : ?>
                                <span
                                    class="inline-flex items-center rounded-lg border border-blue-100 bg-blue-50 px-2.5 py-1 text-xs font-medium leading-4 text-blue-700">
                                    <?= html_escape($nama_mapel) ?>
                                </span>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <span class="text-xs text-gray-400">Belum ada mata pelajaran</span>
                                <?php endif; ?>
                            </div>
                            <span class="mapel sr-only"><?= html_escape(implode(' ', $val->mapel_nama ?? [])) ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="jadwal_status sr-only"><?= $val->jadwal_count > 0 ? 'Ada jadwal' : 'Belum ada jadwal' ?></span>
                            <?php if ($val->jadwal_count > 0): ?>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                <?= $val->jadwal_count ?> file jadwal
                            </span>
                            <?php else: ?>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">
                                <i data-lucide="x" class="w-3 h-3"></i>
                                Belum ada jadwal
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="update_status sr-only"><?= $val->mapel_updated ? 'updated' : 'not-updated' ?></span>
                            <div class="flex min-w-44 flex-col items-start gap-2">
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-md border border-blue-100 bg-blue-50 px-2 py-1 text-[11px] font-medium text-blue-700"
                                        title="Pekan">
                                        <i data-lucide="calendar" class="h-3 w-3"></i>
                                        Pekan: <?= (int) $val->materi_count ?>
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-md border border-amber-100 bg-amber-50 px-2 py-1 text-[11px] font-medium text-amber-700"
                                        title="Pertemuan">
                                        <i data-lucide="clock" class="h-3 w-3"></i>
                                        Pertemuan: <?= (int) $val->bab_count ?>
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-md border border-cyan-100 bg-cyan-50 px-2 py-1 text-[11px] font-medium text-cyan-700"
                                        title="Ujian">
                                        <i data-lucide="clipboard-list" class="h-3 w-3"></i>
                                        Ujian: <?= (int) $val->ujian_count ?>
                                    </span>
                                </div>
                                <?php if ($val->last_update): ?>
                                <span class="text-[11px] text-gray-500">
                                    <i data-lucide="clock" class="w-2.5 h-2.5 inline"></i>
                                    Update terakhir: <?= date('d M Y H:i', strtotime($val->last_update)) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($val->last_update): ?>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <?= date('d M Y H:i', strtotime($val->last_update)) ?>
                            </span>
                            <?php else: ?>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-500">
                                <i data-lucide="x" class="w-3 h-3"></i>
                                Belum
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <a href="<?=base_url('kepala_sekolah/detail/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-sm">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id="guruListEmpty" class="hidden px-6 py-10 text-center text-sm text-gray-500">
            Tidak ada guru yang sesuai dengan pencarian atau filter.
        </div>
        <?php if (!empty($guru)): ?>
        <div class="flex flex-col gap-4 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p id="guruListSummary" class="text-sm text-gray-500" aria-live="polite"></p>
            <ul class="pagination flex flex-wrap items-center justify-center gap-2"></ul>
        </div>
        <?php endif; ?>
        <?php if(empty($guru)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada data guru</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($guru)): ?>
<style>
#guruList .pagination li {
    display: inline-block;
}

#guruList .pagination li a {
    display: inline-flex;
    min-width: 36px;
    height: 36px;
    align-items: center;
    justify-content: center;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0 10px;
    color: #374151;
    font-size: 0.8125rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
}

#guruList .pagination li.active a {
    border-color: #2563eb;
    background: #2563eb;
    color: #fff;
}

#guruList .pagination li.disabled a {
    color: #cbd5e1;
    pointer-events: none;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof List === 'undefined') {
        return;
    }

    var guruList = new List('guruList', {
        listClass: 'guru-items',
        valueNames: ['name', 'mapel', 'admin', 'update_status', 'jadwal_status'],
        page: 10,
        pagination: {
            item: "<li><a class='page'></a></li>"
        }
    });
    var mapelFilter = document.getElementById('guruMapelFilter');
    var statusFilter = document.getElementById('guruStatusFilter');
    var pageSize = document.getElementById('guruPageSize');
    var summary = document.getElementById('guruListSummary');
    var emptyState = document.getElementById('guruListEmpty');

    var applyFilters = function() {
        var selectedMapel = mapelFilter.value.toLowerCase();
        var selectedStatus = statusFilter.value;

        guruList.filter(function(item) {
            var values = item.values();
            var matchesMapel = !selectedMapel || values.mapel.toLowerCase().includes(selectedMapel);
            var matchesStatus = !selectedStatus || values.update_status === selectedStatus;
            return matchesMapel && matchesStatus;
        });
    };

    mapelFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    pageSize.addEventListener('change', function() {
        guruList.show(1, Number(pageSize.value));
    });
    guruList.on('updated', function() {
        var visibleCount = guruList.visibleItems.length;
        var totalCount = guruList.matchingItems.length;
        summary.textContent = 'Menampilkan ' + visibleCount + ' dari ' + totalCount + ' guru';
        emptyState.classList.toggle('hidden', totalCount > 0);
    });
    guruList.update();
});
</script>
<?php endif; ?>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>