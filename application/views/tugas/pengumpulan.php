<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Pengumpulan Tugas']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'tugas']); ?>
<?php endif; ?>

<div class="max-w-6xl mx-auto px-6 py-8">
    <div class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <a href="<?= base_url('tugas') ?>" class="inline-flex items-center gap-2 text-sm text-violet-100 hover:text-white mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>Kembali ke daftar tugas
            </a>
            <h1 class="text-2xl md:text-3xl font-extrabold">Pengumpulan Tugas</h1>
            <p class="text-violet-100 mt-2"><?= html_escape($tugas->judul) ?> · <?= html_escape($tugas->mapel_nama ?? '') ?></p>
            <p class="text-sm text-violet-100 mt-1"><?= count($submissions) ?> siswa telah menyimpan jawaban.</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
    </div>

    <?php if (empty($submissions)): ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <i data-lucide="inbox" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
        <p class="font-semibold text-gray-700">Belum ada jawaban yang dikumpulkan.</p>
        <p class="text-sm text-gray-500 mt-1">Jawaban siswa akan tampil di halaman ini setelah disimpan.</p>
    </div>
    <?php else: ?>
        <?php foreach ($submissions as $submission): ?>
        <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div>
                    <h2 class="font-bold text-gray-900"><?= html_escape($submission['nama'] ?: 'Nama siswa tidak tersedia') ?></h2>
                    <p class="text-xs text-gray-500 mt-1">NIS: <?= html_escape($submission['nis'] ?? '-') ?> · Kelas: <?= html_escape($submission['kelas_nama'] ?? '-') ?></p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-violet-700 bg-violet-50 border border-violet-200 rounded-lg px-3 py-1.5">
                    <i data-lucide="clock-3" class="w-3.5 h-3.5"></i>
                    Terakhir disimpan <?= html_escape(date('d M Y H:i', strtotime($submission['terakhir_diubah']))) ?>
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                <?php foreach ($submission['jawaban'] as $index => $answer): ?>
                <div class="px-6 py-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-violet-600 mb-2">Soal <?= $index + 1 ?> · <?= html_escape(str_replace('_', ' ', $answer->jenis_soal)) ?></p>
                    <div class="prose prose-sm max-w-none text-gray-800 mb-3"><?= $answer->soal ?></div>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 text-sm text-gray-700">
                        <span class="font-semibold text-gray-900">Jawaban:</span>
                        <?php if ($answer->jenis_soal === 'essay' && $answer->jenis_jawaban_essay === 'file'): ?>
                            <?php $filename = basename($answer->jawaban_siswa); ?>
                            <a href="<?= base_url('uploads/jawaban_tugas/' . rawurlencode($filename)) ?>" target="_blank" rel="noopener" class="ml-1 text-blue-600 underline"><?= html_escape($filename) ?></a>
                        <?php else: ?>
                            <?php
                            $decoded_answer = json_decode($answer->jawaban_siswa, TRUE);
                            $display_answer = json_last_error() === JSON_ERROR_NONE && is_array($decoded_answer)
                                ? implode(', ', array_map('strval', $decoded_answer))
                                : $answer->jawaban_siswa;
                            ?>
                            <?= nl2br(html_escape($display_answer)) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
