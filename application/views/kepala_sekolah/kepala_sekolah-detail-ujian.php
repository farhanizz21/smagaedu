<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Detail Ujian - Kepala Sekolah']); ?>
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
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Detail Ujian</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Informasi lengkap ujian dan peserta</p>
        </div>
    </div>

    <!-- Info Ujian Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-8">
        <div class="flex flex-col md:flex-row md:items-start gap-6">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0 shadow-lg">
                <i data-lucide="clipboard-list" class="w-8 h-8"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-xl font-bold text-gray-900"><?= $ujian->nama ?></h2>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                        <?= $ujian->mapel_nama ?>
                    </span>
                    <?php if (!empty($ujian->jenis_penilaian)): ?>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                        <?= $ujian->jenis_penilaian ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-cyan-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="user" class="w-4 h-4 text-cyan-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Guru Pengampu</p>
                            <p class="text-sm font-semibold text-gray-800"><?= $guru ? $guru->nama : '-' ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Tanggal Mulai</p>
                            <p class="text-sm font-semibold text-gray-800"><?= $ujian->tgl_mulai_formatted ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="calendar-clock" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Tanggal Selesai</p>
                            <p class="text-sm font-semibold text-gray-800"><?= $ujian->tgl_selesai_formatted ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Daftar Soal Section -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-sky-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center">
                        <i data-lucide="file-question" class="w-5 h-5 text-cyan-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Daftar Soal</h3>
                        <p class="text-xs text-gray-500"><?= count($soal) ?> soal dalam ujian ini</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <?php if (!empty($soal)): ?>
                <div class="space-y-3">
                    <?php $no = 1; foreach ($soal as $s): ?>
                    <div class="p-4 rounded-xl bg-gray-50 hover:bg-cyan-50 transition-colors border border-gray-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold flex-shrink-0">
                                        <?= $no ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                        <?php 
                                        $jenis_class = [
                                            'pilihan_ganda' => 'bg-blue-50 text-blue-600',
                                            'pilihan_ganda_kompleks' => 'bg-indigo-50 text-indigo-600',
                                            'benar_salah' => 'bg-green-50 text-green-600',
                                            'essay' => 'bg-amber-50 text-amber-600',
                                            'menjodohkan' => 'bg-purple-50 text-purple-600'
                                        ];
                                        echo $jenis_class[$s->jenis_soal] ?? 'bg-gray-50 text-gray-600';
                                        ?>">
                                        <?= str_replace('_', ' ', ucwords($s->jenis_soal)) ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-800 font-medium"><?= $s->soal ?></p>
                                <?php if ($s->jenis_soal === 'menjodohkan' && !empty($s->jodohkan_pairs)): ?>
                                <div class="mt-2 space-y-1">
                                    <?php foreach ($s->jodohkan_pairs as $pair): ?>
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <span class="font-semibold text-gray-700"><?= $pair->kunci ?></span>
                                        <i data-lucide="arrow-right" class="w-3 h-3 text-gray-400"></i>
                                        <span><?= $pair->jawaban ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php $no++; endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada soal pada ujian ini</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Daftar Peserta Section -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Peserta Ujian</h3>
                        <p class="text-xs text-gray-500"><?= count($peserta) ?> siswa terdaftar</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th
                                class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-10">
                                No.</th>
                            <th
                                class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                Nama Siswa</th>
                            <th
                                class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                Kelas</th>
                            <th
                                class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-24">
                                Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $no = 1; foreach ($peserta as $p): ?>
                        <tr class="table-row-hover transition-colors">
                            <td class="px-4 py-3 text-gray-500 text-center"><?= $no ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($p->nama) ?></td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= !empty($p->kelas_nama) ? htmlspecialchars($p->kelas_nama) : '-' ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if (!empty($p->nilai_ujian)): ?>
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold 
                                    <?= ($p->nilai_ujian >= 70) ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' ?>">
                                    <?= $p->nilai_ujian ?>
                                </span>
                                <?php else: ?>
                                <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php $no++; endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($peserta)): ?>
                <div class="text-center py-12 text-gray-400">
                    <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada peserta ujian</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>