<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Detail Proyek']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'proyek']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('proyek')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Detail Proyek</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Mata Pelajaran: <span
                    class="text-blue-600 font-semibold"><?= $proyek->mapel ?></span></p>
        </div>
        <div class="flex items-center gap-2">
            <?php if($this->session->userdata('role') == 1 || $this->session->userdata('uuid') == $proyek->created_by ){?>
            <a href="<?= base_url('proyek/pilih_siswa/'.$proyek->uuid)?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-purple-600 shadow-lg shadow-purple-200 hover:bg-purple-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="users" class="w-4 h-4"></i>
                Data Siswa Proyek
            </a>
            <?php } ?>
            <button type="button"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm"
                data-bs-toggle="modal" data-bs-target="#pdfViewerModal">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Buka File
            </button>
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

    <!-- Info Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2"><?= $proyek->judul ?></h2>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span>Dibuat oleh : <?= $guru->nama ?></span>
                </div>
            </div>
        </div>
        <div class="mt-4 prose prose-sm max-w-none text-gray-600">
            <?= $proyek->deskripsi ?>
        </div>
    </div>

    <!-- Daftar Kelompok -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Kelompok</h3>
        <div class="space-y-3">
            <?php if(!empty($kelompok)): ?>
            <?php foreach($kelompok as $k): ?>
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900"><?= $k['kelompok'] ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?= count($k['anggota'] ?? []) ?> anggota</p>
                    </div>
                </div>
                <?php if(!empty($k['anggota'])): ?>
                <div class="mt-3 flex flex-wrap gap-2">
                    <?php foreach($k['anggota'] ?? [] as $a): ?>
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <?= $a['nama'] ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p class="text-sm">Belum ada kelompok</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($pengerjaan): ?>
    <!-- Kumpulkan Jawaban -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Kumpulkan Jawaban</h3>
        <form method="post" action="<?= base_url('proyek/kumpulkan/'.$proyek->uuid); ?>" enctype="multipart/form-data">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban Teks</label>
                    <textarea name="jawaban_text" rows="4"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        placeholder="Tulis jawaban Anda di sini..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File Jawaban</label>
                    <input type="file" name="jawaban_file"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                    <p class="text-xs text-gray-500 mt-1">Format: dokumen, foto, atau video. Maksimal 50 Mb</p>
                </div>
                <?php if($pengumpulan): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                    <i data-lucide="info" class="w-5 h-5 inline-block mr-2"></i>
                    Anda sudah mengumpulkan jawaban. Mengirim kembali akan mengganti jawaban sebelumnya.
                </div>
                <?php endif; ?>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Kumpulkan
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Daftar Jawaban -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Daftar Jawaban</h3>
        <div class="space-y-4">
            <?php if(!empty($jawaban)): ?>
            <?php foreach($jawaban as $j): ?>
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-700">
                                <?= substr($j->jawaban_siswa ?: $j->nama_siswa, 0, 1) ?>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900"><?= $j->nama_siswa ?></p>
                                <p class="text-xs text-gray-500">Kelompok: <?= $j->kelompok_nama ?></p>
                            </div>
                        </div>
                        <?php if($j->jawaban_text): ?>
                        <p class="text-sm text-gray-700 ml-10"><?= $j->jawaban_text ?></p>
                        <?php endif; ?>
                        <?php if($j->jawaban_file): ?>
                        <a href="<?= base_url('uploads/jawaban/'.$j->jawaban_file)?>" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors ml-10 mt-2">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Lihat File
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($this->session->userdata('role') == 1 || $this->session->userdata('uuid') == $proyek->created_by): ?>
                        <button
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors btn-beri-nilai"
                            data-uuid="<?= $j->uuid ?>">
                            <i data-lucide="star" class="w-3.5 h-3.5"></i>
                            <?= $j->nilai ? 'Edit Nilai' : 'Beri Nilai' ?>
                        </button>
                        <a href="<?= base_url('proyek/hapus_jawaban_proyek/'.$j->uuid)?>"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                            onclick="return confirm('Hapus jawaban ini?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p class="text-sm">Belum ada jawaban</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div class="modal fade" id="pdfViewerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $proyek->judul ?></h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <embed src="<?= base_url('uploads/proyek/'.$proyek->file) ?>" type="application/pdf" width="100%"
                        height="600px">
                </div>
            </div>
        </div>
    </div>

    <!-- Komentar -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Komentar</h3>
        <form method="post" action="<?= base_url('proyek/komentar_tambah/'.$proyek->uuid); ?>" class="mb-6">
            <div class="flex gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <textarea name="komentar" rows="2"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        placeholder="Tulis komentar..."></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all text-sm">
                            <i data-lucide="send" class="w-4 h-4"></i> Kirim
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div class="space-y-4">
            <?php if(!empty($komentar)): ?>
            <?php foreach($komentar as $kom): ?>
            <div class="flex gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-900"><?= $kom->pengomen ?></span>
                    </div>
                    <p class="text-sm text-gray-700"><?= $kom->komentar ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="text-center py-6 text-gray-400">
                <p class="text-sm">Belum ada komentar</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>