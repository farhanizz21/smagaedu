<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Soal Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-rose-500 to-red-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('ujian')?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Tambah Soal Ujian</h1>
                    <p class="text-rose-100 text-sm mt-1">Ujian:
                        <span class="font-semibold"><?= $ujian->nama; ?></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
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

    <?php if ($this->session->userdata('import_errors')): ?>
    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-start gap-2">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5"></i>
        <div>
            <strong class="font-semibold">Beberapa soal gagal diimport:</strong>
            <div class="mt-1"><?= $this->session->userdata('import_errors'); ?></div>
        </div>
        <?php $this->session->unset_userdata('import_errors'); ?>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-8">
        <form method="post" action="<?= base_url('ujian/tambah_soal/' . $ujian->uuid); ?>">
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

                <!-- Jenis Soal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Soal <span
                            class="text-red-500">*</span></label>
                    <select name="jenis_soal" id="jenis_soal"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option value="pilihan_ganda"
                            <?= set_value('jenis_soal') == 'pilihan_ganda' ? 'selected' : '' ?>>Pilihan Ganda</option>
                        <option value="pilihan_ganda_kompleks"
                            <?= set_value('jenis_soal') == 'pilihan_ganda_kompleks' ? 'selected' : '' ?>>Pilihan Ganda
                            Kompleks (Multiple Jawaban)</option>
                        <option value="menjodohkan" <?= set_value('jenis_soal') == 'menjodohkan' ? 'selected' : '' ?>>
                            Menjodohkan</option>
                        <option value="benar_salah" <?= set_value('jenis_soal') == 'benar_salah' ? 'selected' : '' ?>>
                            Benar atau Salah</option>
                        <option value="essay" <?= set_value('jenis_soal') == 'essay' ? 'selected' : '' ?>>Essay (Isian
                            Panjang / Upload File)</option>
                    </select>
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('jenis_soal')) ? '' : 'hidden' ?>">
                        <?= form_error('jenis_soal') ?>
                    </div>
                </div>

                <!-- Pilihan Ganda (Single Answer) -->
                <div id="field_pilihan_ganda" class="field-jenis-soal">
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
                    <div class="grid md:grid-cols-2 gap-6 mt-4">
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
                </div>

                <!-- Pilihan Ganda Kompleks (Multiple Answer) -->
                <div id="field_pilihan_ganda_kompleks" class="field-jenis-soal hidden">
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
                    <div class="grid md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban Benar (pilih
                                banyak)</label>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="jawaban_benar[]" value="A"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> A
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="jawaban_benar[]" value="B"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> B
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="jawaban_benar[]" value="C"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> C
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="jawaban_benar[]" value="D"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> D
                                </label>
                            </div>
                            <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_benar') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Benar atau Salah -->
                <div id="field_benar_salah" class="field-jenis-soal hidden">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban Benar <span
                                    class="text-red-500">*</span></label>
                            <select name="jawaban_benar"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                                <option disabled selected>Pilih Jawaban Benar</option>
                                <option value="benar" <?= set_select('jawaban_benar', 'benar'); ?>>Benar</option>
                                <option value="salah" <?= set_select('jawaban_benar', 'salah'); ?>>Salah</option>
                            </select>
                            <div class="text-red-500 text-xs mt-1"><?= form_error('jawaban_benar') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Menjodohkan -->
                <div id="field_menjodohkan" class="field-jenis-soal hidden">
                    <div class="space-y-4">
                        <div id="jodohkan_pairs_wrapper">
                            <div class="jodohkan-pair grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Soal / Kunci <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="jodohkan_pairs[0][kunci]" rows="2"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                                        placeholder="Masukkan soal/kunci..."><?= set_value('jodohkan_pairs[0][kunci]'); ?></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="jodohkan_pairs[0][jawaban]" rows="2"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                                        placeholder="Masukkan jawaban..."></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add_jodohkan_pair"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pasangan
                        </button>
                    </div>
                </div>

                <!-- Essay -->
                <div id="field_essay" class="field-jenis-soal hidden">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Jawaban Essay</label>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="jenis_jawaban_essay" value="teks"
                                        class="border-gray-300 text-blue-600 focus:ring-blue-500"> Isian Teks (Panjang)
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="jenis_jawaban_essay" value="file"
                                        class="border-gray-300 text-blue-600 focus:ring-blue-500"> Upload File
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Soal
                    </button>
                    <a href="<?= base_url('ujian')?>"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Import / Export Excel -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Import / Export Soal Excel</h3>
                <p class="text-sm text-gray-500 mt-1">Tambahkan banyak soal sekaligus menggunakan file Excel</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('ujian/download_template_soal'); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Download Format Excel
                </a>
                <?php if(!empty($soal)): ?>
                <a href="<?= base_url('ujian/export_soal/' . $ujian->uuid); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-blue-600 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-all">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    Export Soal ke Excel
                </a>
                <?php endif; ?>
            </div>
        </div>

        <form method="post" action="<?= base_url('ujian/import_soal/' . $ujian->uuid); ?>" enctype="multipart/form-data"
            class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <input type="hidden" name="ujian_uuid" value="<?= $ujian->uuid ?>">
            <div class="flex-1 w-full">
                <label for="file_excel"
                    class="flex items-center justify-center w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-blue-400 transition-colors cursor-pointer bg-gray-50 hover:bg-blue-50/50">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6 text-emerald-600 flex-shrink-0"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700" id="file_excel_label">Pilih file Excel (.xlsx /
                                .xls)</p>
                            <p class="text-xs text-gray-400">Maksimal 5 MB</p>
                        </div>
                    </div>
                    <input type="file" name="file_excel" id="file_excel" accept=".xlsx,.xls" class="hidden">
                </label>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all whitespace-nowrap">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import Soal
            </button>
        </form>
    </div>

    <!-- Daftar Soal -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h3 class="text-lg font-bold text-gray-900">Daftar Soal</h3>
            <div class="flex items-center gap-2">
                <?php if(!empty($soal)): ?>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
                    <?= count($soal) ?> soal
                </span>
                <button id="bulkDeleteBtn"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors hidden">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus Terpilih
                </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!empty($soal)): ?>
        <!-- Stats Bar -->
        <div class="flex flex-wrap gap-2 mb-4" id="soalStats">
            <?php
            $type_counts = [];
            $jenis_labels = [
                'pilihan_ganda' => 'PG',
                'pilihan_ganda_kompleks' => 'PG Kompleks',
                'menjodohkan' => 'Menjodohkan',
                'benar_salah' => 'Benar/Salah',
                'essay' => 'Essay'
            ];
            $type_colors = [
                'pilihan_ganda' => 'bg-blue-100 text-blue-700 border-blue-200',
                'pilihan_ganda_kompleks' => 'bg-purple-100 text-purple-700 border-purple-200',
                'menjodohkan' => 'bg-amber-100 text-amber-700 border-amber-200',
                'benar_salah' => 'bg-green-100 text-green-700 border-green-200',
                'essay' => 'bg-gray-100 text-gray-700 border-gray-200'
            ];
            foreach ($soal as $s) {
                $type_counts[$s->jenis_soal] = ($type_counts[$s->jenis_soal] ?? 0) + 1;
            }
            ?>
            <?php foreach ($type_counts as $type => $count): ?>
            <span
                class="text-xs px-2.5 py-1 rounded-full border <?= $type_colors[$type] ?? 'bg-gray-100 text-gray-700 border-gray-200' ?>">
                <?= $jenis_labels[$type] ?? $type ?> (<?= $count ?>)
            </span>
            <?php endforeach; ?>
        </div>

        <!-- Search & Filter -->
        <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="soalSearch" placeholder="Cari soal..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white placeholder:text-gray-400">
            </div>
            <select id="soalTypeFilter"
                class="px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white text-gray-700">
                <option value="">Semua Jenis</option>
                <option value="pilihan_ganda">Pilihan Ganda</option>
                <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks</option>
                <option value="menjodohkan">Menjodohkan</option>
                <option value="benar_salah">Benar atau Salah</option>
                <option value="essay">Essay</option>
            </select>
        </div>

        <!-- Soal List -->
        <div class="space-y-3" id="soalList">
            <?php $no = 1; ?>
            <?php foreach($soal as $s):
                $jenis_label = $jenis_labels[$s->jenis_soal] ?? ($s->jenis_soal ?? '-');
                $type_color = $type_colors[$s->jenis_soal] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                $type_icon = [
                    'pilihan_ganda' => 'list',
                    'pilihan_ganda_kompleks' => 'list-checks',
                    'menjodohkan' => 'arrow-right-left',
                    'benar_salah' => 'check-circle',
                    'essay' => 'file-text'
                ][$s->jenis_soal] ?? 'help-circle';

                $jawaban_benar_display = $s->jawaban_benar ?? '-';
                if ($s->jawaban_benar) {
                    $decoded = json_decode($s->jawaban_benar, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $jawaban_benar_display = implode(', ', $decoded);
                    }
                }

                $soal_truncated = mb_strlen($s->soal) > 80 ? mb_substr($s->soal, 0, 80) . '...' : $s->soal;
            ?>
            <div class="soal-item border border-gray-200 rounded-xl overflow-hidden transition-all"
                data-jenis="<?= $s->jenis_soal ?>" data-soal="<?= htmlspecialchars(strtolower($s->soal)) ?>">
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50/50 transition-colors accordion-header cursor-pointer"
                    onclick="toggleAccordion(this)">
                    <label class="flex items-center" onclick="event.stopPropagation()">
                        <input type="checkbox"
                            class="soal-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                            data-uuid="<?= $s->uuid ?>">
                    </label>
                    <span
                        class="flex-shrink-0 w-7 h-7 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold flex items-center justify-center">
                        <?= $no ?>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <i data-lucide="<?= $type_icon ?>" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                            <span
                                class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($soal_truncated) ?></span>
                        </div>

                        <?php if (in_array($s->jenis_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks'])): ?>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <?php if ($s->jawaban_a): ?>
                            <span class="text-xs text-gray-500">A. <?= htmlspecialchars($s->jawaban_a) ?></span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">A. (kosong)</span>
                            <?php endif; ?>
                            <?php if ($s->jawaban_b): ?>
                            <span class="text-xs text-gray-500">B. <?= htmlspecialchars($s->jawaban_b) ?></span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">B. (kosong)</span>
                            <?php endif; ?>
                            <?php if ($s->jawaban_c): ?>
                            <span class="text-xs text-gray-500">C. <?= htmlspecialchars($s->jawaban_c) ?></span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">C. (kosong)</span>
                            <?php endif; ?>
                            <?php if ($s->jawaban_d): ?>
                            <span class="text-xs text-gray-500">D. <?= htmlspecialchars($s->jawaban_d) ?></span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">D. (kosong)</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($s->jenis_soal == 'menjodohkan'): ?>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <?php 
                            $jodohkan_pairs = isset($s->jodohkan_pairs) ? $s->jodohkan_pairs : [];
                            if (!empty($jodohkan_pairs)): 
                                $pair_count = count($jodohkan_pairs);
                            ?>
                            <span class="text-xs text-gray-500"><?= $pair_count ?> pasangan</span>
                            <?php else: ?>
                            <?php if ($s->jawaban_a): ?>
                            <span class="text-xs text-gray-500">Kunci: <?= htmlspecialchars($s->jawaban_a) ?></span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">Kunci: (kosong)</span>
                            <?php endif; ?>
                            <?php if ($s->jawaban_b): ?>
                            <span class="text-xs text-gray-500">Jawaban: <?= htmlspecialchars($s->jawaban_b) ?></span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">Jawaban: (kosong)</span>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <span
                        class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full border <?= $type_color ?>"><?= $jenis_label ?></span>
                    <div class="flex items-center gap-1 flex-shrink-0" onclick="event.stopPropagation()">
                        <button type="button"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                            title="Edit Soal" onclick="openEditModal('<?= $s->uuid ?>')">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <a href="<?= base_url('ujian/hapus_soal/'.$s->uuid.'?ujian_uuid='.$ujian->uuid) ?>"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')" title="Hapus Soal">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                        <button type="button"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                            title="Toggle Detail">
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200"></i>
                        </button>
                    </div>
                </div>
                <div class="accordion-content hidden border-t border-gray-100 bg-white">
                    <div class="px-4 py-3 space-y-3">
                        <!-- <div class="flex items-start gap-2">
                            <span class="text-xs font-semibold text-gray-500 mt-0.5">Soal:</span>
                            <p class="text-sm text-gray-700 leading-relaxed"><?= htmlspecialchars($s->soal) ?></p>
                        </div> -->

                        <?php if (in_array($s->jenis_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks'])): ?>
                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-gray-500">Pilihan Jawaban:</span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <?php
                                $jawaban_list = [
                                    'A' => $s->jawaban_a,
                                    'B' => $s->jawaban_b,
                                    'C' => $s->jawaban_c,
                                    'D' => $s->jawaban_d
                                ];
                                $correct_answers = [];
                                if ($s->jawaban_benar) {
                                    $decoded = json_decode($s->jawaban_benar, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $correct_answers = array_map(function($x) { return strtoupper(trim($x)); }, $decoded);
                                    } else {
                                        $correct_answers = [strtoupper(trim($s->jawaban_benar))];
                                    }
                                }
                                foreach ($jawaban_list as $letter => $text):
                                    $is_correct = in_array($letter, $correct_answers);
                                ?>
                                <div
                                    class="flex items-center gap-2 text-sm <?= $is_correct ? 'bg-green-50 -mx-1 px-1 py-0.5 rounded border border-green-200' : '' ?>">
                                    <span
                                        class="inline-flex items-center justify-center w-5 h-5 rounded bg-gray-100 text-gray-600 text-xs font-bold flex-shrink-0"><?= $letter ?></span>
                                    <span class="<?= $is_correct ? 'text-green-700 font-medium' : 'text-gray-700' ?>">
                                        <?= $text ? htmlspecialchars($text) : '<span class="text-gray-400 italic">Belum diisi</span>' ?>
                                    </span>
                                    <?php if ($is_correct): ?>
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-green-600 flex-shrink-0"></i>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($s->jenis_soal == 'menjodohkan'): ?>
                        <div class="space-y-1.5">
                            <span class="text-xs font-semibold text-gray-500">Pasangan:</span>
                            <?php 
                            $jodohkan_pairs = isset($s->jodohkan_pairs) ? $s->jodohkan_pairs : [];
                            if (!empty($jodohkan_pairs)): 
                            ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <?php foreach ($jodohkan_pairs as $pair): ?>
                                <div class="text-sm text-gray-700">
                                    <span class="text-xs font-semibold text-gray-500">Kunci: </span>
                                    <?= htmlspecialchars($pair->kunci) ?>
                                </div>
                                <div class="text-sm text-gray-700">
                                    <span class="text-xs font-semibold text-gray-500">Jawaban: </span>
                                    <?= htmlspecialchars($pair->jawaban) ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div class="text-sm text-gray-700">
                                    <span class="text-xs font-semibold text-gray-500">Kunci: </span>
                                    <?= $s->jawaban_a ? htmlspecialchars($s->jawaban_a) : '<span class="text-gray-400 italic">Belum diisi</span>' ?>
                                </div>
                                <div class="text-sm text-gray-700">
                                    <span class="text-xs font-semibold text-gray-500">Jawaban: </span>
                                    <?= $s->jawaban_b ? htmlspecialchars($s->jawaban_b) : '<span class="text-gray-400 italic">Belum diisi</span>' ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500">Jawaban Benar:</span>
                            <span class="text-sm text-gray-700"><?= htmlspecialchars($jawaban_benar_display) ?></span>
                        </div> -->
                    </div>
                </div>
            </div>
            <?php $no++; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12 text-gray-400">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                <i data-lucide="file-text" class="w-8 h-8 text-gray-300"></i>
            </div>
            <p class="text-sm font-medium text-gray-500">Belum ada soal</p>
            <p class="text-xs text-gray-400 mt-1">Tambahkan soal pertama menggunakan form di atas</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Soal Modal -->
<div id="editSoalModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-900">Edit Soal</h3>
            <button type="button" onclick="closeEditModal()"
                class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editSoalForm" method="post" action="<?= base_url('ujian/edit_soal') ?>" class="p-6 space-y-4">
            <input type="hidden" name="soal_uuid" id="editSoalUuid">
            <input type="hidden" name="ujian_uuid" value="<?= $ujian->uuid ?>">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Soal <span
                        class="text-red-500">*</span></label>
                <textarea name="soal" id="editSoalText" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                    placeholder="Masukkan soal..."></textarea>
                <div class="text-red-500 text-xs mt-1" id="editSoalError"></div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Soal <span
                        class="text-red-500">*</span></label>
                <select name="jenis_soal" id="editSoalJenis"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                    <option value="pilihan_ganda">Pilihan Ganda</option>
                    <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks</option>
                    <option value="menjodohkan">Menjodohkan</option>
                    <option value="benar_salah">Benar atau Salah</option>
                    <option value="essay">Essay</option>
                </select>
            </div>

            <div id="editFieldPilihanGanda" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban A</label>
                        <input type="text" name="jawaban_a" id="editJawabanA"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban A">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban B</label>
                        <input type="text" name="jawaban_b" id="editJawabanB"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban B">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban C</label>
                        <input type="text" name="jawaban_c" id="editJawabanC"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban C">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban D</label>
                        <input type="text" name="jawaban_d" id="editJawabanD"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Jawaban D">
                    </div>
                </div>
            </div>

            <div id="editFieldMenjodohkan" class="space-y-3 hidden">
                <div id="edit_jodohkan_pairs_wrapper">
                    <div class="edit-jodohkan-pair grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Soal / Kunci</label>
                            <textarea name="jodohkan_pairs[0][kunci]" rows="2"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                                placeholder="Soal / Kunci..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban</label>
                            <textarea name="jodohkan_pairs[0][jawaban]" rows="2"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                                placeholder="Jawaban..."></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" id="edit_add_jodohkan_pair"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pasangan
                </button>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban Benar</label>
                <input type="text" name="jawaban_benar" id="editSoalJawaban"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                    placeholder="Contoh: A atau A, B">
                <p class="text-xs text-gray-400 mt-1">Untuk PG ketik A/B/C/D. Untuk PG Kompleks ketik A, B (pisahkan
                    koma). Untuk Menjodohkan/Essay biarkan kosong.</p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAccordion(header) {
    var content = header.nextElementSibling;
    var icon = header.querySelector('i[data-lucide="chevron-down"]');

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
    }
}

function openEditModal(uuid) {
    var modal = document.getElementById('editSoalModal');
    modal.classList.remove('hidden');
    document.getElementById('editSoalUuid').value = uuid;

    fetch('<?= base_url('ujian/get_soal/') ?>' + uuid)
        .then(function(res) {
            return res.json();
        })
        .then(function(data) {
            if (data.status === 'success') {
                document.getElementById('editSoalText').value = data.data.soal;
                document.getElementById('editSoalJenis').value = data.data.jenis_soal;

                document.getElementById('editJawabanA').value = data.data.jawaban_a || '';
                document.getElementById('editJawabanB').value = data.data.jawaban_b || '';
                document.getElementById('editJawabanC').value = data.data.jawaban_c || '';
                document.getElementById('editJawabanD').value = data.data.jawaban_d || '';

                var jawaban = data.data.jawaban_benar;
                if (jawaban && jawaban !== 'null') {
                    try {
                        var parsed = JSON.parse(jawaban);
                        if (Array.isArray(parsed)) {
                            document.getElementById('editSoalJawaban').value = parsed.join(', ');
                        } else {
                            document.getElementById('editSoalJawaban').value = jawaban;
                        }
                    } catch (e) {
                        document.getElementById('editSoalJawaban').value = jawaban;
                    }
                } else {
                    document.getElementById('editSoalJawaban').value = '';
                }

                var editJodohkanWrapper = document.getElementById('edit_jodohkan_pairs_wrapper');
                editJodohkanWrapper.innerHTML = '';
                var pairs = data.data.jodohkan_pairs || [];
                if (pairs.length === 0 && data.data.jenis_soal === 'menjodohkan') {
                    pairs = [{
                        kunci: data.data.jawaban_a || '',
                        jawaban: data.data.jawaban_b || ''
                    }];
                }
                pairs.forEach(function(pair, index) {
                    addEditJodohkanPair(pair.kunci || '', pair.jawaban || '');
                });
                if (pairs.length === 0) {
                    addEditJodohkanPair('', '');
                }

                toggleEditFields();
                lucide.createIcons();
            }
        });
}

function addEditJodohkanPair(kunci, jawaban) {
    var wrapper = document.getElementById('edit_jodohkan_pairs_wrapper');
    var index = wrapper.children.length;
    var div = document.createElement('div');
    div.className = 'edit-jodohkan-pair grid grid-cols-1 md:grid-cols-2 gap-3 mb-3';
    div.innerHTML = '<div>' +
        '<label class="block text-sm font-semibold text-gray-700 mb-1.5">Soal / Kunci</label>' +
        '<textarea name="jodohkan_pairs[' + index +
        '][kunci]" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400" placeholder="Soal / Kunci...">' +
        (kunci || '') + '</textarea>' +
        '</div>' +
        '<div class="flex flex-col justify-end">' +
        '<label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban</label>' +
        '<div class="flex gap-2">' +
        '<textarea name="jodohkan_pairs[' + index +
        '][jawaban]" rows="2" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400" placeholder="Jawaban...">' +
        (jawaban || '') + '</textarea>' +
        '<button type="button" class="remove-edit-jodohkan-pair p-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors h-fit" title="Hapus pasangan">' +
        '<i data-lucide="trash-2" class="w-4 h-4"></i>' +
        '</button>' +
        '</div>' +
        '</div>';
    wrapper.appendChild(div);
    lucide.createIcons();
}

function addJodohkanPair() {
    var wrapper = document.getElementById('jodohkan_pairs_wrapper');
    var index = wrapper.children.length;
    var div = document.createElement('div');
    div.className = 'jodohkan-pair grid grid-cols-1 md:grid-cols-2 gap-4 mb-3';
    div.innerHTML = '<div>' +
        '<label class="block text-sm font-semibold text-gray-700 mb-1.5">Soal / Kunci <span class="text-red-500">*</span></label>' +
        '<textarea name="jodohkan_pairs[' + index +
        '][kunci]" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400" placeholder="Masukkan soal/kunci..."></textarea>' +
        '</div>' +
        '<div class="flex flex-col justify-end">' +
        '<label class="block text-sm font-semibold text-gray-700 mb-1.5">Jawaban <span class="text-red-500">*</span></label>' +
        '<div class="flex gap-2">' +
        '<textarea name="jodohkan_pairs[' + index +
        '][jawaban]" rows="2" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400" placeholder="Masukkan jawaban..."></textarea>' +
        '<button type="button" class="remove-jodohkan-pair p-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors h-fit" title="Hapus pasangan">' +
        '<i data-lucide="trash-2" class="w-4 h-4"></i>' +
        '</button>' +
        '</div>' +
        '</div>';
    wrapper.appendChild(div);
    lucide.createIcons();
}

function toggleEditFields() {
    var jenis = document.getElementById('editSoalJenis').value;
    var pgField = document.getElementById('editFieldPilihanGanda');
    var jodohkanField = document.getElementById('editFieldMenjodohkan');
    var jawabanBenarField = document.getElementById('editSoalJawaban').closest('div');

    pgField.classList.add('hidden');
    jodohkanField.classList.add('hidden');
    jawabanBenarField.classList.add('hidden');

    pgField.querySelectorAll('input, textarea').forEach(function(el) {
        el.disabled = true;
    });
    jodohkanField.querySelectorAll('input, textarea').forEach(function(el) {
        el.disabled = true;
    });
    jawabanBenarField.querySelectorAll('input, textarea').forEach(function(el) {
        el.disabled = true;
    });

    if (jenis === 'pilihan_ganda' || jenis === 'pilihan_ganda_kompleks') {
        pgField.classList.remove('hidden');
        jawabanBenarField.classList.remove('hidden');
        pgField.querySelectorAll('input, textarea').forEach(function(el) {
            el.disabled = false;
        });
        jawabanBenarField.querySelectorAll('input, textarea').forEach(function(el) {
            el.disabled = false;
        });
    } else if (jenis === 'menjodohkan') {
        jodohkanField.classList.remove('hidden');
        jodohkanField.querySelectorAll('input, textarea').forEach(function(el) {
            el.disabled = false;
        });
    }
}

function closeEditModal() {
    document.getElementById('editSoalModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    var jenisSoal = document.getElementById('jenis_soal');
    var fields = document.querySelectorAll('.field-jenis-soal');

    function toggleFields() {
        var selected = jenisSoal.value;
        fields.forEach(function(field) {
            field.classList.add('hidden');
            field.querySelectorAll('input, select, textarea').forEach(function(el) {
                el.disabled = true;
            });
        });
        var activeField = document.getElementById('field_' + selected);
        if (activeField) {
            activeField.classList.remove('hidden');
            activeField.querySelectorAll('input, select, textarea').forEach(function(el) {
                el.disabled = false;
            });
        }
    }

    jenisSoal.addEventListener('change', toggleFields);
    toggleFields();

    var editJenis = document.getElementById('editSoalJenis');
    if (editJenis) {
        editJenis.addEventListener('change', toggleEditFields);
    }

    lucide.createIcons();

    var searchInput = document.getElementById('soalSearch');
    var typeFilter = document.getElementById('soalTypeFilter');
    var soalItems = document.querySelectorAll('.soal-item');
    var bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    var checkboxes = document.querySelectorAll('.soal-checkbox');
    var selectedCount = 0;

    function filterSoal() {
        var query = searchInput.value.toLowerCase();
        var type = typeFilter.value;

        soalItems.forEach(function(item) {
            var jenis = item.getAttribute('data-jenis');
            var soalText = item.getAttribute('data-soal') || '';

            var matchesSearch = !query || soalText.indexOf(query) !== -1;
            var matchesType = !type || jenis === type;

            if (matchesSearch && matchesType) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterSoal);
    typeFilter.addEventListener('change', filterSoal);

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            selectedCount = document.querySelectorAll('.soal-checkbox:checked').length;
            if (bulkDeleteBtn) {
                bulkDeleteBtn.classList.toggle('hidden', selectedCount === 0);
            }
        });
    });

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            var checked = document.querySelectorAll('.soal-checkbox:checked');
            if (checked.length === 0) return;

            if (confirm('Hapus ' + checked.length + ' soal terpilih?')) {
                var uuids = [];
                checked.forEach(function(cb) {
                    uuids.push(cb.getAttribute('data-uuid'));
                });

                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('ujian/bulk_hapus_soal') ?>';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'soal_uuids';
                input.value = JSON.stringify(uuids);
                form.appendChild(input);

                var ujianInput = document.createElement('input');
                ujianInput.type = 'hidden';
                ujianInput.name = 'ujian_uuid';
                ujianInput.value = '<?= $ujian->uuid ?>';
                form.appendChild(ujianInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    var addJodohkanBtn = document.getElementById('add_jodohkan_pair');
    if (addJodohkanBtn) {
        addJodohkanBtn.addEventListener('click', addJodohkanPair);
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-jodohkan-pair')) {
            var pair = e.target.closest('.jodohkan-pair');
            if (pair) {
                pair.remove();
                reindexJodohkanPairs('jodohkan_pairs_wrapper', 'jodohkan_pairs');
            }
        }
        if (e.target.closest('.remove-edit-jodohkan-pair')) {
            var pair = e.target.closest('.edit-jodohkan-pair');
            if (pair) {
                pair.remove();
                reindexJodohkanPairs('edit_jodohkan_pairs_wrapper', 'jodohkan_pairs');
            }
        }
    });

    function reindexJodohkanPairs(wrapperId, namePrefix) {
        var wrapper = document.getElementById(wrapperId);
        var pairs = wrapper.querySelectorAll('.' + (wrapperId === 'jodohkan_pairs_wrapper' ? 'jodohkan-pair' :
            'edit-jodohkan-pair'));
        pairs.forEach(function(pair, index) {
            pair.querySelectorAll('textarea').forEach(function(textarea) {
                var name = textarea.getAttribute('name');
                if (name) {
                    var newName = name.replace(/jodohkan_pairs\[\d+\]/, 'jodohkan_pairs[' +
                        index + ']');
                    textarea.setAttribute('name', newName);
                }
            });
        });
    }

    var editAddJodohkanBtn = document.getElementById('edit_add_jodohkan_pair');
    if (editAddJodohkanBtn) {
        editAddJodohkanBtn.addEventListener('click', function() {
            addEditJodohkanPair('', '');
        });
    }

    var fileExcel = document.getElementById('file_excel');
    if (fileExcel) {
        fileExcel.addEventListener('change', function() {
            var label = document.getElementById('file_excel_label');
            if (this.files && this.files.length > 0) {
                label.textContent = this.files[0].name;
            } else {
                label.textContent = 'Pilih file Excel (.xlsx / .xls)';
            }
        });
    }
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>