<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Nilai Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-rose-500 to-red-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('ujian/tambah_kelas/'.$ujian->uuid)?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Nilai Ujian</h1>
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
    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 md:p-8 table-shadow">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h3 class="text-lg font-bold text-gray-900">Soal &amp; Jawaban</h3>
            <span
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 bg-gray-100 border border-gray-200 rounded-full px-3 py-1">
                <i data-lucide="list-checks" class="w-3.5 h-3.5"></i>
                <?= count($soal); ?> Soal
            </span>
        </div>
        <form method="post" action="<?= base_url('ujian/tambah_nilai/'.$ujian->uuid .'/'.$siswa->uuid); ?>">
            <div class="space-y-5">
                <?php $no = 1; foreach ($soal as $d): ?>
                <div data-soal-card class="bg-gray-50 rounded-2xl p-4 sm:p-6 border border-gray-200">
                    <div class="grid md:grid-cols-3 gap-5 md:gap-6 items-start">
                        <!-- Pertanyaan & Jawaban -->
                        <div class="md:col-span-2 min-w-0">
                            <div class="flex items-start gap-3 mb-4">
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex-shrink-0 mt-0.5"><?= $no++; ?></span>
                                <div
                                    class="prose min-w-0 max-w-none flex-1 text-sm text-gray-900 font-medium leading-relaxed break-words [overflow-wrap:anywhere] [&>p:first-child]:mt-0 [&>p:last-child]:mb-0 [&_a]:break-words">
                                    <?= $d->soal; ?>
                                </div>
                            </div>

                            <div class="ml-0 sm:ml-10 mt-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jawaban
                                        Siswa</p>
                                    <?php if (isset($jawaban[$d->uuid][0]->jawaban_siswa)): ?>
                                    <span data-jumlah-karakter class="text-[11px] font-medium text-gray-400"></span>
                                    <?php endif; ?>
                                </div>
                                <div
                                    class="text-sm text-gray-800 bg-white rounded-xl p-3.5 sm:p-4 border border-gray-200 leading-relaxed break-words [overflow-wrap:anywhere] [&>p]:my-1 [&>p:first-child]:mt-0 [&>p:last-child]:mb-0 [&>ul]:my-2 [&>ol]:my-2 [&_a]:break-words">
                                    <?php if (isset($jawaban[$d->uuid][0]->jawaban_siswa)): ?>
                                    <div data-jawaban class="relative">
                                        <div data-jawaban-isi>
                                            <?= $jawaban[$d->uuid][0]->jawaban_teks ?? $jawaban[$d->uuid][0]->jawaban_siswa; ?>
                                        </div>
                                        <div data-jawaban-grad
                                            class="hidden pointer-events-none absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-white via-white/95 to-transparent">
                                        </div>
                                    </div>
                                    <button type="button" data-jawaban-toggle
                                        class="hidden mt-2.5 inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                        <span data-jawaban-label>Selengkapnya</span>
                                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform"></i>
                                    </button>
                                    <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 text-red-600 font-medium"><i
                                            data-lucide="circle-slash" class="w-4 h-4"></i> Tidak dijawab</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Nilai -->
                        <div class="min-w-0">
                            <?php if (isset($jawaban[$d->uuid][0]->jawaban_siswa)): ?>
                            <div class="bg-white rounded-xl p-4 border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nilai <span
                                        class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Isi dengan 0 - 10</p>
                                <?php
                                    $auto_nilai = in_array($d->jenis_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks', 'menjodohkan', 'benar_salah']) ? 10 : '';
                                    $nilai_value = !empty($jawaban[$d->uuid][0]->nilai) ? $jawaban[$d->uuid][0]->nilai : $auto_nilai;
                                ?>
                                <input type="number" name="nilai[<?= $d->uuid; ?>]"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                                    min="0" max="10" value="<?= $nilai_value;?>" required>
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

<script>
// Jawaban siswa yang panjang: tampilkan ringkas + tombol "Selengkapnya"
// supaya guru tetap bisa membaca seluruh jawaban tanpa halaman jadi terlalu panjang.
(function() {
    var BATAS = 176; // tinggi (px) sebelum jawaban dipotong

    document.querySelectorAll('[data-jawaban]').forEach(function(wrapper) {
        var isi = wrapper.querySelector('[data-jawaban-isi]');
        var grad = wrapper.querySelector('[data-jawaban-grad]');
        var tombol = wrapper.parentElement.querySelector('[data-jawaban-toggle]');
        if (!isi || !tombol) return;

        // Jumlah karakter jawaban (agar Guru tahu panjangnya jawaban)
        var card = wrapper.closest('[data-soal-card]');
        var counter = card ? card.querySelector('[data-jumlah-karakter]') : null;
        if (counter) {
            counter.textContent = (isi.textContent || '').trim().length + ' karakter';
        }

        // Ukur tinggi jawaban secara penuh
        isi.style.maxHeight = '';
        isi.style.overflow = 'visible';
        var tinggiPenuh = isi.scrollHeight;

        // Jawaban pendek -> tampil utuh, tanpa tombol
        if (tinggiPenuh <= BATAS + 48) return;

        isi.style.maxHeight = BATAS + 'px';
        isi.style.overflow = 'hidden';
        if (grad) grad.classList.remove('hidden');

        var label = tombol.querySelector('[data-jawaban-label]');
        var ikon = tombol.querySelector('svg');
        tombol.classList.remove('hidden');

        tombol.addEventListener('click', function() {
            var terbuka = isi.getAttribute('data-terbuka') === '1';
            if (terbuka) {
                isi.style.maxHeight = BATAS + 'px';
                isi.style.overflow = 'hidden';
                isi.removeAttribute('data-terbuka');
                if (label) label.textContent = 'Selengkapnya';
                if (ikon) ikon.style.transform = '';
                if (grad) grad.classList.remove('hidden');
            } else {
                isi.style.maxHeight = 'none';
                isi.style.overflow = 'visible';
                isi.setAttribute('data-terbuka', '1');
                if (label) label.textContent = 'Tampilkan lebih sedikit';
                if (ikon) ikon.style.transform = 'rotate(180deg)';
                if (grad) grad.classList.add('hidden');
            }
        });
    });
})();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>