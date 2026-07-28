<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Nilai Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('ujian/tambah_kelas/'.$ujian->uuid)?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Nilai Ujian</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Ujian: <span
                    class="text-blue-600 font-semibold"><?= $ujian->nama; ?></span></p>
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

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Detail Ujian -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 table-shadow">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Detail Ujian</h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Nama Ujian</span>
                        <span class="text-sm font-semibold text-gray-900"><?= $ujian->nama; ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Mata Pelajaran</span>
                        <span class="text-sm font-semibold text-gray-900"><?= $mapel; ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Nama Siswa</span>
                        <span class="text-sm font-semibold text-gray-900"><?= $siswa->nama; ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600">Guru Pengajar</span>
                        <span class="text-sm font-semibold text-gray-900"><?= $guru; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai -->
        <div>
            <div class="bg-white rounded-2xl border border-gray-200 p-6 table-shadow text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Nilai Ujian</h3>
                <div class="text-5xl font-extrabold text-blue-600 my-4"><?= isset($nilai) ? $nilai : '-'; ?></div>
                <p class="text-sm text-gray-500">Skor yang diperoleh</p>
                <p class="text-xs text-gray-400 mt-1">Maksimal 10</p>
            </div>
        </div>
    </div>

    <!-- Soal & Jawaban -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Soal & Jawaban</h3>
        <form method="post" action="<?= base_url('ujian/tambah_nilai/'.$ujian->uuid .'/'.$siswa->uuid); ?>">
            <div class="space-y-6">
                <?php $no = 1; foreach ($soal as $d): ?>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <div class="flex items-start gap-2 mb-3">
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex-shrink-0"><?= $no++; ?></span>
                                <p class="text-sm font-medium text-gray-900"><?= $d->soal; ?></p>
                            </div>
                            <div class="ml-9">
                                <p class="text-sm font-semibold text-gray-700 mb-1">Jawaban:</p>
                                <div class="text-sm text-gray-800 bg-white rounded-lg p-3 border border-gray-200">
                                    <?php if (isset($jawaban[$d->uuid][0]->jawaban_siswa)): ?>
                                        <?= $jawaban[$d->uuid][0]->jawaban_teks ?? $jawaban[$d->uuid][0]->jawaban_siswa; ?>
                                    <?php else: ?>
                                        <span class="text-red-600">Tidak dijawab</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <?php if (isset($jawaban[$d->uuid][0]->jawaban_siswa)): ?>
                            <div class="bg-white rounded-xl p-4 border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nilai <span
                                        class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Isi dengan 0 - 10</p>
                                <input type="number" name="nilai[<?= $d->uuid; ?>]"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                                    min="0" max="10" value="<?= $jawaban[$d->uuid][0]->nilai;?>" required>
                            </div>
                            <?php else: ?>
                            <div class="bg-red-50 rounded-xl p-4 border border-red-200 text-center">
                                <p class="text-sm font-medium text-red-700">Tidak Dijawab</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="flex items-center justify-center gap-3 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Nilai
                    </button>
                    <a href="<?= base_url('ujian/tambah_kelas/'.$ujian->uuid)?>"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>