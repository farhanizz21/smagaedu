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
                                <?= $bg->bab_count ?> bab • <?= $bg->ujian_count ?> ujian
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
                                <?= $wg->bab_count ?> bab • <?= $wg->ujian_count ?> ujian
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
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            Rank</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Username</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
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
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
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
                                <span class="font-medium text-gray-900"><?= $val->nama; ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->username; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <?php if (!empty($val->mapel_nama)) : ?>
                                <?php foreach ($val->mapel_nama as $nama_mapel) : ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    <?= $nama_mapel ?>
                                </span>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <span class="text-gray-400 text-xs">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($val->jadwal_count > 0): ?>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                <?= $val->jadwal_count ?> file
                            </span>
                            <?php else: ?>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <i data-lucide="x" class="w-3 h-3"></i>
                                Belum
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($val->mapel_updated): ?>
                            <div class="flex flex-col items-center gap-1">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Sudah Update
                                </span>
                                <div class="flex flex-wrap justify-center gap-1 mt-1">
                                    <?php if ($val->materi_count > 0): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100"
                                        title="Materi">
                                        <i data-lucide="file-text"
                                            class="w-2.5 h-2.5 mr-0.5"></i><?= $val->materi_count ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($val->bab_count > 0): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-amber-50 text-amber-600 border border-amber-100"
                                        title="Bab / Sub Bab">
                                        <i data-lucide="book" class="w-2.5 h-2.5 mr-0.5"></i><?= $val->bab_count ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($val->sub_materi_count > 0): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-purple-50 text-purple-600 border border-purple-100"
                                        title="Sub Materi">
                                        <i data-lucide="layers"
                                            class="w-2.5 h-2.5 mr-0.5"></i><?= $val->sub_materi_count ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($val->ujian_count > 0): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-cyan-50 text-cyan-600 border border-cyan-100"
                                        title="Ujian">
                                        <i data-lucide="clipboard-list"
                                            class="w-2.5 h-2.5 mr-0.5"></i><?= $val->ujian_count ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($val->last_update): ?>
                                <span class="text-[10px] text-gray-400">
                                    <i data-lucide="clock" class="w-2.5 h-2.5 inline"></i>
                                    <?= date('d M Y H:i', strtotime($val->last_update)) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="flex flex-col items-center gap-1">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-200">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                    Belum Update
                                </span>
                                <span class="text-[10px] text-gray-400">Belum ada materi/bab/ujian</span>
                            </div>
                            <?php endif; ?>
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
                    <?php 
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($guru)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada data guru</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>