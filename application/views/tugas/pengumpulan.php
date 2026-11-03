<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Pengumpulan Tugas']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'tugas']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div
        class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <a href="<?= base_url('tugas') ?>"
                class="inline-flex items-center gap-2 text-sm text-violet-100 hover:text-white mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>Kembali ke daftar tugas
            </a>
            <h1 class="text-2xl md:text-3xl font-extrabold">Pengumpulan Tugas</h1>
            <p class="text-violet-100 mt-2"><?= html_escape($tugas->judul) ?> ·
                <?= html_escape($tugas->mapel_nama ?? '') ?></p>
            <p class="text-sm text-violet-100 mt-1"><?= (int) $total_rows ?> siswa ditemukan pada daftar pengumpulan.
            </p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
    </div>

    <?php $has_filter = $filters['q'] !== '' || $filters['kelas'] !== '' || $filters['status'] !== ''; ?>
    <form method="get" action="<?= base_url('tugas/pengumpulan/' . rawurlencode($tugas->uuid)) ?>"
        class="bg-white rounded-2xl border border-gray-200 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3">
            <div class="relative sm:col-span-2 xl:col-span-5">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" name="q" value="<?= html_escape($filters['q']) ?>"
                    placeholder="Cari nama siswa atau NIS"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none text-sm">
            </div>
            <div class="xl:col-span-3">
                <select name="kelas" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 bg-white text-sm">
                    <option value="">Semua kelas</option>
                    <?php foreach ($daftar_kelas as $kelas): ?>
                    <option value="<?= html_escape($kelas->uuid) ?>"
                        <?= $filters['kelas'] === $kelas->uuid ? 'selected' : '' ?>>
                        <?= html_escape($kelas->nama) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="xl:col-span-2">
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 bg-white text-sm">
                    <option value="">Semua status</option>
                    <option value="tepat_waktu" <?= $filters['status'] === 'tepat_waktu' ? 'selected' : '' ?>>Tepat
                        waktu</option>
                    <option value="terlambat" <?= $filters['status'] === 'terlambat' ? 'selected' : '' ?>>Terlambat
                    </option>
                </select>
            </div>
            <div class="xl:col-span-2">
                <select name="per_page" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 bg-white text-sm">
                    <?php foreach ($per_page_options as $option): ?>
                    <option value="<?= $option ?>" <?= $per_page === $option ? 'selected' : '' ?>><?= $option ?> /
                        halaman</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 mt-3">
            <?php if ($has_filter): ?>
            <a href="<?= base_url('tugas/pengumpulan/' . rawurlencode($tugas->uuid)) ?>"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
            </a>
            <?php endif; ?>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700">
                <i data-lucide="filter" class="w-4 h-4"></i> Terapkan filter
            </button>
        </div>
    </form>

    <?php if ($total_rows > 0): ?>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 text-sm">
        <p class="text-gray-500">Menampilkan <span
                class="font-semibold text-gray-700"><?= $start_no ?>&ndash;<?= $end_no ?></span>
            dari <span class="font-semibold text-gray-700"><?= $total_rows ?></span> siswa</p>
        <?php if ($has_filter): ?><p class="text-violet-700 font-medium">Filter aktif</p><?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($submissions)): ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
        <i data-lucide="inbox" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
        <p class="font-semibold text-gray-700">
            <?= $has_filter ? 'Tidak ada jawaban yang sesuai filter.' : 'Belum ada jawaban yang dikumpulkan.' ?></p>
        <p class="text-sm text-gray-500 mt-1">
            <?= $has_filter ? 'Coba ubah atau reset filter pencarian.' : 'Jawaban siswa akan tampil di halaman ini setelah disimpan.' ?>
        </p>
    </div>
    <?php else: ?>
    <?php
            $first_submission = reset($submissions);
            $question_number = 0;
            $is_answer_correct = function ($answer) {
                if (in_array($answer->jenis_soal, array('pilihan_ganda', 'pilihan_ganda_kompleks'), TRUE)
                    && $answer->jawaban_benar !== NULL && $answer->jawaban_benar !== '') {
                    if ($answer->jenis_soal === 'pilihan_ganda_kompleks') {
                        $selected = json_decode($answer->jawaban_siswa, TRUE);
                        $correct = json_decode($answer->jawaban_benar, TRUE);
                        if (is_array($selected) && is_array($correct)) {
                            sort($selected);
                            sort($correct);
                            return $selected === $correct;
                        }
                    } else {
                        return strtolower(trim($answer->jawaban_siswa))
                            === strtolower(trim($answer->jawaban_benar));
                    }
                } elseif ($answer->jenis_soal === 'benar_salah'
                    && $answer->jawaban_benar !== NULL && $answer->jawaban_benar !== '') {
                    return strtolower(trim($answer->jawaban_siswa))
                        === strtolower(trim($answer->jawaban_benar));
                } elseif ($answer->jenis_soal === 'menjodohkan') {
                    $matching = json_decode($answer->jawaban_siswa, TRUE);
                    $pairs = $answer->jodohkan_pairs ?? array();
                    if (is_array($matching) && !empty($pairs) && count($matching) === count($pairs)) {
                        foreach ($pairs as $pair_index => $pair) {
                            $expected_letter = chr(ord('A') + $pair_index);
                            if (strtoupper((string) ($matching[$pair_index + 1] ?? '')) !== $expected_letter) {
                                return FALSE;
                            }
                        }
                        return TRUE;
                    }
                }
                return NULL;
            };
        ?>
    <?php foreach ($first_submission['jawaban'] as $question_uuid => $question): ?>
    <?php
            $question_number++;
            $answered_count = 0;
            $correct_count = 0;
            $wrong_count = 0;
            foreach ($submissions as $submission) {
                $submitted_answer = $submission['jawaban'][$question_uuid] ?? NULL;
                if ($submitted_answer && trim((string) $submitted_answer->jawaban_siswa) !== '') {
                    $answered_count++;
                    $is_correct = $is_answer_correct($submitted_answer);
                    if ($is_correct === TRUE) {
                        $correct_count++;
                    } elseif ($is_correct === FALSE) {
                        $wrong_count++;
                    }
                }
            }
            $question_summary = html_entity_decode(
                strip_tags(preg_replace('/<br\s*\/?>|<\/(p|div|li|h[1-6])>/i', ' ', $question->soal)),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );
            $question_summary = trim(preg_replace('/\s+/', ' ', $question_summary));
            if (function_exists('mb_strlen') && mb_strlen($question_summary, 'UTF-8') > 110) {
                $question_summary = mb_substr($question_summary, 0, 110, 'UTF-8') . '…';
            } elseif (strlen($question_summary) > 110) {
                $question_summary = substr($question_summary, 0, 107) . '...';
            }
        ?>
    <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-5">
        <details class="group">
            <summary
                class="flex items-center justify-between gap-3 px-4 sm:px-5 py-3 bg-violet-50 cursor-pointer list-none [&::-webkit-details-marker]:hidden hover:bg-violet-100/70 transition-colors">
                <span class="min-w-0 flex flex-col sm:flex-row sm:items-center sm:gap-3">
                    <span class="text-sm font-bold text-violet-800">
                        Soal <?= $question_number ?>
                        <span class="text-violet-600 font-medium">· <?= html_escape(str_replace('_', ' ', $question->jenis_soal)) ?></span>
                    </span>
                    <span class="text-xs text-gray-600 mt-1 sm:mt-0">
                        <?= $answered_count ?> dari <?= count($submissions) ?> siswa menjawab
                    </span>
                    <span class="block min-w-0 max-w-full text-xs text-gray-600 mt-1 sm:mt-0 truncate">
                        <?= html_escape($question_summary) ?>
                    </span>
                    <?php if ($question->jenis_soal === 'essay'): ?>
                    <span class="text-xs font-medium text-amber-700 mt-1 sm:mt-0">Penilaian manual</span>
                    <?php else: ?>
                    <span class="flex items-center gap-2 mt-1 sm:mt-0">
                        <span class="text-xs font-medium text-emerald-700">Benar <?= $correct_count ?></span>
                        <span class="text-xs font-medium text-red-700">Salah <?= $wrong_count ?></span>
                    </span>
                    <?php endif; ?>
                </span>
                <i data-lucide="chevron-down"
                    class="w-5 h-5 text-violet-600 flex-shrink-0 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="px-4 sm:px-5 py-4 border-t border-violet-100">
                <h3 class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">Teks soal</h3>
                <div class="prose prose-sm max-w-none text-gray-800"><?= $question->soal ?></div>
                <?php if (in_array($question->jenis_soal, array('pilihan_ganda', 'pilihan_ganda_kompleks'), TRUE)): ?>
                <?php
                    $correct_options = json_decode((string) $question->jawaban_benar, TRUE);
                    $correct_options = is_array($correct_options) ? $correct_options : array($question->jawaban_benar);
                    ?>
                <div class="mt-4 space-y-2">
                    <?php foreach (array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E') as $key => $label): ?>
                    <?php if (!empty($question->{'jawaban_' . $key})): ?>
                    <?php $is_correct = in_array($label, $correct_options, TRUE); ?>
                    <div
                        class="flex items-start gap-2 text-sm <?= $is_correct ? 'text-emerald-800' : 'text-gray-700' ?>">
                        <?php if ($is_correct): ?>
                        <span
                            class="inline-flex items-center justify-center w-5 h-5 mt-0.5 rounded-full bg-emerald-100 text-emerald-700 flex-shrink-0"><i
                                data-lucide="check" class="w-3.5 h-3.5"></i></span>
                        <?php else: ?>
                        <span class="w-5 flex-shrink-0"></span>
                        <?php endif; ?>
                        <span
                            class="<?= $is_correct ? 'rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 -mt-0.5' : '' ?>"><strong><?= $label ?>.</strong>
                            <?= html_escape($question->{'jawaban_' . $key}) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php elseif ($question->jenis_soal === 'benar_salah'): ?>
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach (array('benar' => 'Benar', 'salah' => 'Salah') as $value => $option): ?>
                    <?php $is_correct = strtolower(trim((string) $question->jawaban_benar)) === $value; ?>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-sm <?= $is_correct ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-gray-200 bg-white text-gray-700' ?>">
                        <?php if ($is_correct): ?><i data-lucide="circle-check"
                            class="w-4 h-4 text-emerald-600"></i><?php endif; ?>
                        <?= $option ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php elseif ($question->jenis_soal === 'menjodohkan'): ?>
                <div class="mt-4 space-y-2">
                    <?php foreach ($question->jodohkan_pairs ?? array() as $pair): ?>
                    <div
                        class="flex items-start gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                        <i data-lucide="circle-check" class="w-4 h-4 mt-0.5 text-emerald-600 flex-shrink-0"></i>
                        <span><?= html_escape($pair->kunci) ?> <span class="text-emerald-600">→</span>
                            <strong><?= html_escape($pair->jawaban) ?></strong></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php elseif ($question->jenis_soal === 'essay'): ?>
                <p class="mt-3 inline-flex items-center gap-1.5 text-xs text-gray-500">
                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                    Jawaban berupa
                    <?= ($question->jenis_jawaban_essay ?? 'teks') === 'file' ? 'unggahan file' : 'teks' ?> dan dinilai
                    secara manual.
                </p>
                <?php endif; ?>
            </div>
            <div class="border-t border-gray-200 bg-gray-50/50 px-4 sm:px-5 py-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jawaban siswa</p>
            </div>
            <div class="divide-y divide-gray-100">
            <?php foreach ($submissions as $submission): ?>
            <?php $answer = $submission['jawaban'][$question_uuid] ?? NULL; ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 px-4 sm:px-5 py-4">
                <div class="lg:col-span-3 min-w-0">
                    <div class="flex items-start gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                            <?= html_escape(strtoupper(substr($submission['nama'] ?: 'S', 0, 1))) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-sm text-gray-900">
                                <?= html_escape($submission['nama'] ?: 'Nama siswa tidak tersedia') ?></p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                NIS <?= html_escape($submission['nis'] ?? '-') ?> ·
                                <?= html_escape($submission['kelas_nama'] ?? '-') ?>
                            </p>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                <?php if (!empty($submission['terlambat'])): ?>
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-700">
                                    <i data-lucide="triangle-alert" class="w-3 h-3"></i> Terlambat
                                </span>
                                <?php else: ?>
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                    <i data-lucide="circle-check" class="w-3 h-3"></i> Tepat waktu
                                </span>
                                <?php endif; ?>
                                <span
                                    class="text-[11px] text-gray-500"><?= html_escape(date('d M Y, H:i', strtotime($submission['terakhir_diubah']))) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-9">
                    <div
                        class="flex items-start justify-between gap-3 rounded-lg bg-gray-50 border border-gray-200 px-3.5 py-3 text-sm text-gray-800 break-words min-h-12">
                        <?php
                        $answer_correct = $answer ? $is_answer_correct($answer) : NULL;
                        ?>
                        <div class="min-w-0 flex-1">
                        <?php if (!$answer): ?>
                        <span class="italic text-gray-400">Siswa belum menjawab soal ini.</span>
                        <?php elseif ($answer->jenis_soal === 'essay' && $answer->jenis_jawaban_essay === 'file'): ?>
                        <?php $filename = basename($answer->jawaban_siswa); ?>
                        <a href="<?= base_url('uploads/jawaban_tugas/' . rawurlencode($filename)) ?>" target="_blank"
                            rel="noopener" class="inline-flex items-center gap-1 text-blue-700 underline break-all">
                            <i data-lucide="paperclip" class="w-4 h-4 flex-shrink-0"></i><?= html_escape($filename) ?>
                        </a>
                        <?php elseif (in_array($answer->jenis_soal, array('pilihan_ganda', 'pilihan_ganda_kompleks'), TRUE)): ?>
                        <?php
                                $decoded_answer = json_decode($answer->jawaban_siswa, TRUE);
                                $selected_options = json_last_error() === JSON_ERROR_NONE && is_array($decoded_answer)
                                    ? $decoded_answer
                                    : array($answer->jawaban_siswa);
                                $option_labels = array('a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E');
                                ?>
                        <div class="space-y-1">
                            <?php foreach ($option_labels as $key => $label): ?>
                            <?php if (in_array($label, $selected_options, TRUE)): ?>
                            <div><strong><?= $label ?>.</strong> <?= html_escape($answer->{'jawaban_' . $key} ?? '') ?>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php elseif ($answer->jenis_soal === 'menjodohkan'): ?>
                        <?php
                                $decoded_answer = json_decode($answer->jawaban_siswa, TRUE);
                                $matching_answers = json_last_error() === JSON_ERROR_NONE && is_array($decoded_answer) ? $decoded_answer : array();
                                $matching_letters = range('A', 'Z');
                                ?>
                        <div class="space-y-1">
                            <?php foreach ($answer->jodohkan_pairs ?? array() as $pair_index => $pair): ?>
                            <?php
                                        $selected = $matching_answers[$pair_index + 1] ?? '';
                                        $selected_index = array_search(strtoupper((string) $selected), $matching_letters, TRUE);
                                        ?>
                            <?php if ($selected !== ''): ?>
                            <div>
                                <span class="font-medium"><?= html_escape($pair->kunci) ?></span>
                                <span class="text-gray-500">→</span>
                                <?= $selected_index !== FALSE && isset($answer->jodohkan_pairs[$selected_index])
                                                ? html_escape($answer->jodohkan_pairs[$selected_index]->jawaban)
                                                : html_escape((string) $selected) ?>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php elseif ($answer->jenis_soal === 'benar_salah'): ?>
                        <?= strtolower(trim($answer->jawaban_siswa)) === 'benar' ? 'Benar' : (strtolower(trim($answer->jawaban_siswa)) === 'salah' ? 'Salah' : nl2br(html_escape($answer->jawaban_siswa))) ?>
                        <?php else: ?>
                        <?php
                                $decoded_answer = json_decode($answer->jawaban_siswa, TRUE);
                                $display_answer = json_last_error() === JSON_ERROR_NONE && is_array($decoded_answer)
                                    ? implode(', ', array_map('strval', $decoded_answer))
                                    : $answer->jawaban_siswa;
                                ?>
                        <?= $display_answer !== '' ? nl2br(html_escape($display_answer)) : '<span class="italic text-gray-400">Tidak ada jawaban tertulis.</span>' ?>
                        <?php endif; ?>
                        </div>
                        <?php if ($answer_correct !== NULL): ?>
                        <?php if ($answer_correct): ?>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold flex-shrink-0">
                            <i data-lucide="circle-check" class="w-4 h-4"></i> Benar
                        </span>
                        <?php else: ?>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold flex-shrink-0">
                            <i data-lucide="circle-x" class="w-4 h-4"></i> Salah
                        </span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </details>
    </section>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($pagination_links)): ?>
    <div class="mt-6"><?= $pagination_links ?></div>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>