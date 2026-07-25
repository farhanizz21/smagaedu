<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Pengerjaan Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Pengerjaan Ujian</h1>
            <p class="text-gray-500 mt-1 text-sm">Ujian: <span
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

    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                <span>Jawab semua soal di bawah ini dengan teliti. Pastikan Anda tidak berpindah tab selama ujian
                    berlangsung.</span>
            </div>
        </div>

        <form method="post" action="<?= base_url('ujian/pengerjaan/'.$ujian->uuid); ?>">
            <div class="space-y-6">
                <?php $no = 1; foreach ($soal as $s): ?>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="flex items-start gap-3 mb-4">
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex-shrink-0"><?= $no++; ?></span>
                        <p class="text-base font-medium text-gray-900"><?= $s->soal; ?></p>
                    </div>
                    <div class="ml-11 space-y-3">
                        <label
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-white cursor-pointer transition-all">
                            <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="A"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" required>
                            <span class="text-sm text-gray-700"><?= $s->jawaban_a; ?></span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-white cursor-pointer transition-all">
                            <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="B"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700"><?= $s->jawaban_b; ?></span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-white cursor-pointer transition-all">
                            <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="C"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700"><?= $s->jawaban_c; ?></span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-white cursor-pointer transition-all">
                            <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="D"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700"><?= $s->jawaban_d; ?></span>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="flex items-center justify-center gap-3 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Kirim Jawaban
                    </button>
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