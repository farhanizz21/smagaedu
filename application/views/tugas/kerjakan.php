<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Kerjakan Tugas']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'tugas']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 md:p-8 mb-8 text-white">
        <a href="<?= base_url('tugas') ?>" class="inline-flex items-center gap-2 text-sm text-violet-100 hover:text-white mb-4"><i data-lucide="arrow-left" class="w-4 h-4"></i>Kembali ke daftar tugas</a>
        <h1 class="text-2xl md:text-3xl font-extrabold"><?= html_escape($tugas->judul) ?></h1>
        <p class="text-violet-100 mt-2"><?= html_escape($tugas->mapel_nama ?? '') ?> · Batas: <?= html_escape(date('d M Y H:i', strtotime($tugas->tgl_selesai))) ?></p>
        <?php if (!empty($read_only)): ?>
        <p class="inline-flex items-center gap-1.5 text-sm text-violet-100 mt-3"><i data-lucide="lock-keyhole" class="w-4 h-4"></i>Jawaban sudah dikumpulkan dan hanya dapat dilihat.</p>
        <?php endif; ?>
    </div>

    <?php if ($this->session->userdata('success_msg')): ?>
    <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm"><?= html_escape($this->session->userdata('success_msg')); $this->session->unset_userdata('success_msg'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->userdata('error_msg')): ?>
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm"><?= html_escape($this->session->userdata('error_msg')); $this->session->unset_userdata('error_msg'); ?></div>
    <?php endif; ?>

    <?php if (empty($read_only)): ?>
    <form method="post" enctype="multipart/form-data" action="<?= base_url('tugas/kerjakan/' . $tugas->uuid) ?>" class="space-y-5">
        <?php foreach ($soal as $number => $question): $answer = $answers[$question->uuid] ?? ''; ?>
        <section class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="text-xs font-semibold uppercase text-violet-600 mb-3">Soal <?= $number + 1 ?> · <?= html_escape(str_replace('_', ' ', $question->jenis_soal)) ?></div>
            <div class="prose max-w-none text-gray-800 mb-5"><?= $question->soal ?></div>
            <?php if (in_array($question->jenis_soal, array('pilihan_ganda', 'pilihan_ganda_kompleks'), TRUE)): ?>
                <?php foreach (array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E') as $key => $label): ?>
                    <?php if (!empty($question->{'jawaban_' . $key})): ?>
                    <label class="flex items-start gap-3 p-3 mb-2 rounded-xl border border-gray-200 hover:bg-violet-50">
                        <input type="<?= $question->jenis_soal === 'pilihan_ganda' ? 'radio' : 'checkbox' ?>" name="jawaban[<?= html_escape($question->uuid) ?>]<?= $question->jenis_soal === 'pilihan_ganda_kompleks' ? '[]' : '' ?>" value="<?= $label ?>" <?= (is_array($answer) ? in_array($label, $answer, TRUE) : $answer === $label) ? 'checked' : '' ?>>
                        <span><strong><?= $label ?>.</strong> <?= html_escape($question->{'jawaban_' . $key}) ?></span>
                    </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php elseif ($question->jenis_soal === 'menjodohkan'): ?>
                <?php $letters = range('A', 'Z'); ?>
                <div class="space-y-3">
                    <?php foreach ($question->jodohkan_pairs as $index => $pair): ?>
                    <label class="grid sm:grid-cols-[1fr_1fr] gap-3 items-center text-sm">
                        <span><?= $index + 1 ?>. <?= html_escape($pair->kunci) ?></span>
                        <select name="jawaban[<?= html_escape($question->uuid) ?>][<?= $index + 1 ?>]" class="rounded-xl border border-gray-300 p-2">
                            <option value="">Pilih pasangan</option>
                            <?php foreach ($question->jodohkan_pairs as $pair_index => $option): $letter = $letters[$pair_index] ?? (string) ($pair_index + 1); ?>
                            <option value="<?= $letter ?>" <?= (is_array($answer) && ($answer[$index + 1] ?? '') === $letter) ? 'selected' : '' ?>><?= $letter ?> — <?= html_escape($option->jawaban) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($question->jenis_soal === 'benar_salah'): ?>
                <?php foreach (array('benar' => 'Benar', 'salah' => 'Salah') as $value => $label): ?>
                <label class="inline-flex items-center gap-2 mr-6 text-sm"><input type="radio" name="jawaban[<?= html_escape($question->uuid) ?>]" value="<?= $value ?>" <?= $answer === $value ? 'checked' : '' ?>><?= $label ?></label>
                <?php endforeach; ?>
            <?php elseif ($question->jenis_soal === 'essay' && ($question->jenis_jawaban_essay ?? 'teks') === 'file'): ?>
                <?php if (!empty($answer)): ?><a class="block text-sm text-blue-600 underline mb-2" target="_blank" href="<?= base_url('uploads/jawaban_tugas/' . rawurlencode($answer)) ?>">Lihat file jawaban sebelumnya</a><?php endif; ?>
                <input type="file" name="jawaban_file[<?= html_escape($question->uuid) ?>]" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" class="w-full rounded-xl border border-gray-300 p-3 text-sm">
                <p class="text-xs text-gray-500 mt-2">PDF, dokumen Office, atau gambar; maksimal 10 MB.</p>
            <?php else: ?>
                <textarea name="jawaban[<?= html_escape($question->uuid) ?>]" rows="5" class="w-full rounded-xl border border-gray-300 p-3 text-sm" placeholder="Tulis jawaban Anda"><?= html_escape(is_array($answer) ? implode(', ', $answer) : $answer) ?></textarea>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>
        <?php if (empty($soal)): ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center text-gray-500">Soal untuk tugas ini belum tersedia.</div>
        <?php else: ?>
        <div class="flex justify-end"><button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white bg-violet-600 hover:bg-violet-700"><i data-lucide="save" class="w-4 h-4"></i>Simpan Jawaban</button></div>
        <?php endif; ?>
    </form>
    <?php else: ?>
        <?php $displayed_answers = 0; ?>
        <?php foreach ($soal as $number => $question): ?>
            <?php
            $answer = $answers[$question->uuid] ?? NULL;
            if ($answer === NULL || $answer === '' || $answer === array()) {
                continue;
            }
            $displayed_answers++;
            ?>
        <section class="bg-white rounded-2xl border border-gray-200 p-6 mb-5">
            <div class="text-xs font-semibold uppercase text-violet-600 mb-3">Soal <?= $number + 1 ?> · <?= html_escape(str_replace('_', ' ', $question->jenis_soal)) ?></div>
            <div class="prose max-w-none text-gray-800 mb-5"><?= $question->soal ?></div>
            <div class="rounded-xl bg-violet-50 border border-violet-100 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-700 mb-2">Jawaban dikumpulkan</p>
                <?php if ($question->jenis_soal === 'essay' && ($question->jenis_jawaban_essay ?? 'teks') === 'file'): ?>
                    <?php $filename = basename((string) $answer); ?>
                    <a class="inline-flex items-center gap-2 text-sm text-blue-700 underline" target="_blank" rel="noopener" href="<?= base_url('uploads/jawaban_tugas/' . rawurlencode($filename)) ?>"><i data-lucide="paperclip" class="w-4 h-4"></i><?= html_escape($filename) ?></a>
                <?php elseif (in_array($question->jenis_soal, array('pilihan_ganda', 'pilihan_ganda_kompleks'), TRUE)): ?>
                    <?php $selected_options = is_array($answer) ? $answer : array($answer); ?>
                    <div class="space-y-2">
                    <?php foreach (array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E') as $key => $label): ?>
                        <?php if (in_array($label, $selected_options, TRUE)): ?>
                        <div class="text-sm text-gray-800"><strong><?= $label ?>.</strong> <?= html_escape($question->{'jawaban_' . $key} ?? '') ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                <?php elseif ($question->jenis_soal === 'menjodohkan' && is_array($answer)): ?>
                    <div class="space-y-2">
                    <?php foreach ($question->jodohkan_pairs as $pair_index => $pair): ?>
                        <?php $selected = $answer[$pair_index + 1] ?? ''; ?>
                        <?php if ($selected !== ''): ?>
                            <?php $selected_index = ord(strtoupper((string) $selected)) - ord('A'); ?>
                            <div class="text-sm text-gray-800">
                                <span class="font-medium"><?= html_escape($pair->kunci) ?></span>
                                <span class="text-gray-500">→</span>
                                <?= isset($question->jodohkan_pairs[$selected_index]) ? html_escape($question->jodohkan_pairs[$selected_index]->jawaban) : html_escape((string) $selected) ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-sm text-gray-800"><?= nl2br(html_escape(is_array($answer) ? implode(', ', $answer) : (string) $answer)) ?></div>
                <?php endif; ?>
            </div>
        </section>
        <?php endforeach; ?>
        <?php if ($displayed_answers === 0): ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center text-gray-500">Tidak ada jawaban tersimpan untuk soal yang tersedia.</div>
        <?php endif; ?>
        <div class="flex justify-end">
            <a href="<?= base_url('tugas') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white bg-violet-600 hover:bg-violet-700"><i data-lucide="arrow-left" class="w-4 h-4"></i>Kembali ke Daftar Tugas</a>
        </div>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
