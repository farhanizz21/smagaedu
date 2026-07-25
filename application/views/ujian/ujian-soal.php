<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Soal Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('ujian/tambah_soal/' . $ujian->uuid)?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah Soal Ujian</h1>
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

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form method="post" action="<?= base_url('ujian/edit_soal'); ?>">
            <input type="hidden" name="ujian_uuid" value="<?= $ujian->uuid ?>">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Soal <span
                            class="text-red-500">*</span></label>
                    <textarea name="soal" rows="4"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan soal ujian..."><?= set_value('soal'); ?></textarea>
                    <div class="text-red-500 text-xs mt-1"><?= form_error('soal') ?></div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban A <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jawaban_a"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban A" value="<?= set_value('jawaban_a'); ?>">
                        <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_a') ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban B <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jawaban_b"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban B" value="<?= set_value('jawaban_b'); ?>">
                        <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_b') ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban C <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jawaban_c"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban C" value="<?= set_value('jawaban_c'); ?>">
                        <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_c') ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban D <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jawaban_d"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban D" value="<?= set_value('jawaban_d'); ?>">
                        <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_d') ?></div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban Benar <span
                                class="text-red-500">*</span></label>
                        <select name="jawaban_benar"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                            <option disabled selected>Pilih Jawaban Benar</option>
                            <option value="A" <?= set_select('jawaban_benar', 'A'); ?>>A</option>
                            <option value="B" <?= set_select('jawaban_benar', 'B'); ?>>B</option>
                            <option value="C" <?= set_select('jawaban_benar', 'C'); ?>>C</option>
                            <option value="D" <?= set_select('jawaban_benar', 'D'); ?>>D</option>
                        </select>
                        <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_benar') ?></div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Soal
                    </button>
                    <a href="<?= base_url('ujian/tambah_soal/' . $ujian->uuid)?>"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>

        <!-- List Soal -->
        <div class="mt-10 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Soal</h3>
            <div class="space-y-3">
                <?php if(!empty($soal)): ?>
                <?php foreach($soal as $s): ?>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 mb-1"><?= $s->soal ?></p>
                            <p class="text-xs text-gray-500">Jawaban benar: <span
                                    class="font-semibold text-blue-600"><?= $s->jawaban_benar ?></span></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors"
                                onclick="editSoal('<?= $s->uuid ?>')">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                            </button>
                            <a href="<?= base_url('ujian/hapus_soal/'.$s->uuid.'?ujian_uuid='.$ujian->uuid) ?>"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada soal</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function editSoal(uuid) {
    // Implementasi edit soal (bisa menggunakan modal atau redirect)
    alert('Fitur edit soal akan segera tersedia');
}
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>