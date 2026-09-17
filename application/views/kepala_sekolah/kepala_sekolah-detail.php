<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Detail Guru - Kepala Sekolah']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'kepala_sekolah']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Back Button & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('kepala_sekolah')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Detail Guru</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Informasi lengkap guru dan kelengkapan pembelajaran</p>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-8">
        <div class="flex flex-col sm:flex-row items-start gap-6">
            <div
                class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-3xl font-bold flex-shrink-0 shadow-lg">
                <?= strtoupper(substr($guru->nama, 0, 1)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-gray-900"><?= $guru->nama ?></h2>
                <p class="text-sm text-gray-500">@<?= $guru->username ?></p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <?php if ($guru->jenis_kelamin): ?>
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                        <i data-lucide="<?= $guru->jenis_kelamin == 'L' ? 'mars' : 'venus' ?>" class="w-3 h-3"></i>
                        <?= $guru->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($mapel_list)): ?>
                    <?php foreach ($mapel_list as $m): ?>
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                        <i data-lucide="book" class="w-3 h-3"></i>
                        <?= $m->nama ?>
                    </span>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Mapel Status Section -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="refresh-cw" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Status Update Mata Pelajaran</h3>
                    <p class="text-xs text-gray-500">Kelengkapan pembaruan materi, bab, sub bab, dan ujian pada menu
                        mata pelajaran</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if ($guru->mapel_updated): ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div
                    class="rounded-xl bg-blue-50 border border-blue-100 p-4 text-center hover:shadow-md transition-all">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 mx-auto mb-2"></i>
                    <p class="text-2xl font-bold text-gray-900"><?= $guru->materi_count ?></p>
                    <p class="text-xs text-gray-500 mt-1">Materi</p>
                </div>
                <div
                    class="rounded-xl bg-amber-50 border border-amber-100 p-4 text-center hover:shadow-md transition-all">
                    <i data-lucide="book" class="w-6 h-6 text-amber-600 mx-auto mb-2"></i>
                    <p class="text-2xl font-bold text-gray-900"><?= $guru->bab_count ?></p>
                    <p class="text-xs text-gray-500 mt-1">Bab / Sub Bab</p>
                </div>
                <div
                    class="rounded-xl bg-purple-50 border border-purple-100 p-4 text-center hover:shadow-md transition-all">
                    <i data-lucide="layers" class="w-6 h-6 text-purple-600 mx-auto mb-2"></i>
                    <p class="text-2xl font-bold text-gray-900"><?= $guru->sub_materi_count ?></p>
                    <p class="text-xs text-gray-500 mt-1">Sub Materi</p>
                </div>
                <div
                    class="rounded-xl bg-cyan-50 border border-cyan-100 p-4 text-center hover:shadow-md transition-all">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-cyan-600 mx-auto mb-2"></i>
                    <p class="text-2xl font-bold text-gray-900"><?= $guru->ujian_count ?></p>
                    <p class="text-xs text-gray-500 mt-1">Ujian</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-4">
                <span
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                    Sudah melakukan update
                </span>
                <?php if ($guru->last_update): ?>
                <span class="text-xs text-gray-500">
                    <i data-lucide="clock" class="w-3.5 h-3.5 inline text-gray-400"></i>
                    Terakhir update: <?= date('d M Y H:i', strtotime($guru->last_update)) ?> WIB
                </span>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-red-50 border border-red-200 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-7 h-7 text-red-500"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-sm">Belum melakukan update pada menu mata pelajaran</p>
                    <p class="text-xs text-gray-500 mt-0.5">Guru belum menambahkan materi, bab/sub bab, atau ujian
                        untuk mata pelajaran yang diampu.</p>
                </div>
                <span
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-200 flex-shrink-0">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    Belum Update
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Column -->
        <div class="space-y-8">
            <!-- Jadwal Mengajar Section -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Jadwal Mengajar</h3>
                            <p class="text-xs text-gray-500">Upload jadwal berupa gambar dari menu profil guru</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($jadwal)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($jadwal as $j): ?>
                        <div
                            class="group relative rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all">
                            <a href="<?= base_url('uploads/jadwal/'.$j->file_gambar) ?>" target="_blank">
                                <img src="<?= base_url('uploads/jadwal/'.$j->file_gambar) ?>" alt="Jadwal"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors">
                                </div>
                            </a>
                            <div class="p-3 bg-white">
                                <p class="text-xs text-gray-500">
                                    <i data-lucide="clock" class="w-3 h-3 inline"></i>
                                    <?= date('d M Y H:i', strtotime($j->created_at)) ?>
                                </p>
                                <?php if ($j->deskripsi): ?>
                                <p class="text-sm font-medium text-gray-700 mt-1"><?= $j->deskripsi ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada jadwal yang diupload</p>
                        <p class="text-xs text-gray-400 mt-1">Guru dapat mengupload jadwal dari menu profil</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mata Pelajaran Section -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i data-lucide="book-open" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Mata Pelajaran</h3>
                            <p class="text-xs text-gray-500"><?= count($mapel_list) ?> mata pelajaran yang diampu</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($mapel_list)): ?>
                    <div class="space-y-3">
                        <?php foreach ($mapel_list as $m): ?>
                        <div
                            class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors">
                            <div
                                class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                <?= strtoupper(substr($m->nama, 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm"><?= $m->nama ?></p>
                                <p class="text-xs text-gray-400">UUID: <?= substr($m->uuid, 0, 8) ?>...</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="book-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Tidak ada mata pelajaran</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kelas Section -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Kelas yang Diajar</h3>
                            <p class="text-xs text-gray-500"><?= count($kelas) ?> kelas tersedia</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($kelas)): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($kelas as $k): ?>
                        <span
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium bg-gradient-to-br from-green-50 to-emerald-50 text-green-700 border border-green-200 hover:shadow-md transition-all">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <?= $k->nama ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="school" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada data kelas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Perangkat Pembelajaran Section -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-violet-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                            <i data-lucide="folder-open" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Perangkat Pembelajaran</h3>
                            <p class="text-xs text-gray-500">Modul dan ATP yang telah dibuat</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($perangkat_list)): ?>
                    <div class="space-y-4">
                        <?php foreach ($perangkat_list as $mapel_uuid => $data): ?>
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i data-lucide="book" class="w-4 h-4 text-blue-500"></i>
                                <?= $data['mapel']->nama ?>
                            </h4>
                            <div class="space-y-2 ml-6">
                                <?php foreach ($data['perangkat'] as $p): ?>
                                <div
                                    class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-purple-50 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-8 h-8 rounded-lg <?= $p->jenis_file == 'modul' ? 'bg-blue-100' : 'bg-emerald-100' ?> flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="file-text"
                                                class="w-4 h-4 <?= $p->jenis_file == 'modul' ? 'text-blue-600' : 'text-emerald-600' ?>"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate"><?= $p->nama_file ?>
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium <?= $p->jenis_file == 'modul' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' ?>">
                                                    <?= strtoupper($p->jenis_file) ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if ($p->file): ?>
                                    <a href="<?= base_url('uploads/perangkat/'.$p->file) ?>" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-blue-600 hover:bg-blue-100 transition-colors flex-shrink-0 ml-2">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        Lihat
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="folder-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada perangkat pembelajaran</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Proyek Section -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50 to-pink-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                            <i data-lucide="briefcase" class="w-5 h-5 text-rose-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Project / Tugas</h3>
                            <p class="text-xs text-gray-500"><?= count($proyek) ?> project telah dibuat</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($proyek)): ?>
                    <div class="space-y-3">
                        <?php foreach ($proyek as $p): ?>
                        <div
                            class="p-4 rounded-xl bg-gray-50 hover:bg-rose-50 transition-colors border border-gray-100">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-gray-900 text-sm"><?= $p->judul ?></h4>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                                            <?= $p->mapel_nama ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        <i data-lucide="calendar" class="w-3 h-3 inline"></i>
                                        <?= date('d M Y', strtotime($p->tgl_mulai)) ?> -
                                        <?= date('d M Y', strtotime($p->tgl_selesai)) ?>
                                    </p>
                                    <?php if ($p->deskripsi): ?>
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-2"><?= $p->deskripsi ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php if ($p->file): ?>
                                <a href="<?= base_url('uploads/proyek/'.$p->file) ?>" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 hover:bg-rose-100 transition-colors flex-shrink-0">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="briefcase-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada project/tugas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ujian Section -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-sky-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center">
                            <i data-lucide="clipboard-list" class="w-5 h-5 text-cyan-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Ujian / Penilaian</h3>
                            <p class="text-xs text-gray-500"><?= count($ujian) ?> ujian telah dibuat</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($ujian)): ?>
                    <div class="space-y-3">
                        <?php foreach ($ujian as $u): ?>
                        <div
                            class="p-4 rounded-xl bg-gray-50 hover:bg-cyan-50 transition-colors border border-gray-100">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-gray-900 text-sm"><?= $u->nama ?></h4>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                                            <?= $u->mapel_nama ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        <i data-lucide="clock" class="w-3 h-3 inline"></i>
                                        <?= $u->tgl_mulai_formatted ?> - <?= $u->tgl_selesai_formatted ?>
                                    </p>
                                </div>
                                <a href="<?= base_url('kepala_sekolah/detail_ujian/'.$u->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-cyan-600 hover:bg-cyan-100 transition-colors flex-shrink-0">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    Lihat
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="clipboard-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Belum ada ujian/penilaian</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>