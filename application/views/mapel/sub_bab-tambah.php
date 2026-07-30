<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<!-- Quill Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

<div class="max-w-3xl mx-auto px-6 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('sub_bab/index/' . $materi->uuid) ?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah Sub Bab</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Materi: <span
                    class="text-blue-600 font-semibold"><?= $materi->judul ?></span></p>
        </div>
    </div>

    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <?= form_open_multipart('sub_bab/tambah/' . $materi->uuid); ?>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Sub Bab <span
                        class="text-red-500">*</span></label>
                <input type="text" name="judul" id="judul"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                    placeholder="Masukkan Judul Sub Bab" value="<?= set_value('judul'); ?>">
                <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('judul')) ? '' : 'hidden' ?>">
                    <?= form_error('judul') ?>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <div id="editor" style="height: 200px;"><?= set_value('deskripsi', '', FALSE); ?></div>
                <input type="hidden" name="deskripsi" id="deskripsi" value="<?= set_value('deskripsi', '', FALSE); ?>">
                <small class="text-gray-400 text-xs mt-1 block">Gunakan toolbar di atas untuk formatting teks</small>
            </div>

            <!-- Dokumentasi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dokumentasi</label>
                <input type="file" name="dokumentasi" id="dokumentasi"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                <small class="text-gray-400 text-xs">format file : PDF, DOCX, PPTX. Maximal 50Mb</small>
                <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('dokumentasi')) ? '' : 'hidden' ?>">
                    <?= form_error('dokumentasi') ?>
                </div>
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1.5">Atau masukkan link dokumentasi</label>
                    <input type="url" name="dokumentasi_link" id="dokumentasi_link"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="https://docs.google.com/..." value="<?= set_value('dokumentasi_link'); ?>">
                    <small class="text-gray-400 text-xs">Contoh: Google Drive, Google Docs, atau link lain</small>
                </div>
            </div>

            <!-- Tambah Ujian Section -->
            <?php if(has_role(['superadmin', 'admin', 'guru'])): ?>
            <div class="border-t border-gray-100 pt-6">
                <label class="flex items-center gap-3 cursor-pointer mb-4">
                    <input type="checkbox" name="create_ujian" id="create_ujian" value="1"
                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-200 transition-all"
                        <?= set_checkbox('create_ujian', '1'); ?>>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm font-semibold text-gray-700">Buat Ujian Sekaligus</span>
                    </div>
                </label>

                <div id="ujian-fields" class="space-y-4 hidden bg-blue-50/50 rounded-xl p-4 border border-blue-100">
                    <p class="text-xs text-blue-600 font-medium flex items-center gap-1.5">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                        Ujian akan otomatis terhubung dengan sub bab ini
                    </p>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Ujian <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="namaUjian" id="namaUjian"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                            placeholder="Masukkan Nama Ujian" value="<?= set_value('namaUjian'); ?>">
                        <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('namaUjian')) ? '' : 'hidden' ?>">
                            <?= form_error('namaUjian') ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Penilaian <span
                                class="text-red-500">*</span></label>
                        <select name="jenis_penilaian"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                            <option value="penilaian_harian"
                                <?= set_value('jenis_penilaian') == 'penilaian_harian' ? 'selected' : '' ?>>Penilaian
                                Harian
                            </option>
                            <option value="penilaian_tengah_semester"
                                <?= set_value('jenis_penilaian') == 'penilaian_tengah_semester' ? 'selected' : '' ?>>
                                Penilaian
                                Tengah Semester</option>
                            <option value="penilaian_akhir_semester"
                                <?= set_value('jenis_penilaian') == 'penilaian_akhir_semester' ? 'selected' : '' ?>>
                                Penilaian
                                Akhir Semester</option>
                        </select>
                        <div
                            class="text-red-500 text-xs mt-1 <?= !empty(form_error('jenis_penilaian')) ? '' : 'hidden' ?>">
                            <?= form_error('jenis_penilaian') ?>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="datetime-local" name="tgl_mulai" id="tgl_mulai"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                                value="<?= set_value('tgl_mulai'); ?>">
                            <div
                                class="text-red-500 text-xs mt-1 <?= !empty(form_error('tgl_mulai')) ? '' : 'hidden' ?>">
                                <?= form_error('tgl_mulai') ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai <span
                                    class="text-red-500">*</span></label>
                            <input type="datetime-local" name="tgl_selesai" id="tgl_selesai"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                                value="<?= set_value('tgl_selesai'); ?>">
                            <div
                                class="text-red-500 text-xs mt-1 <?= !empty(form_error('tgl_selesai')) ? '' : 'hidden' ?>">
                                <?= form_error('tgl_selesai') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit" name="action" value="simpan"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan
            </button>
            <button type="submit" name="action" value="simpan_detail" id="btn-simpan-soal"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-green-600 shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="list" class="w-4 h-4"></i> Simpan & Tambah Soal
            </button>
            <a href="<?= base_url('sub_bab/index/' . $materi->uuid) ?>"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                <i data-lucide="x" class="w-4 h-4"></i> Batal
            </a>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{
                'header': [1, 2, 3, false]
            }],
            ['bold', 'italic', 'underline', 'strike'],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            ['link', 'image'],
            ['clean']
        ]
    },
    placeholder: 'Masukkan deskripsi sub bab...'
});

// Update hidden input before form submit
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('deskripsi').value = quill.root.innerHTML;
});

// Toggle ujian fields visibility
var createUjianCheckbox = document.getElementById('create_ujian');
var ujianFields = document.getElementById('ujian-fields');
var btnSimpanSoal = document.getElementById('btn-simpan-soal');

function toggleUjianFields() {
    if (createUjianCheckbox.checked) {
        ujianFields.classList.remove('hidden');
        btnSimpanSoal.classList.remove('hidden');
    } else {
        ujianFields.classList.add('hidden');
        btnSimpanSoal.classList.add('hidden');
    }
}

createUjianCheckbox.addEventListener('change', toggleUjianFields);

// Initial state - hide simpan soal button if checkbox not checked
if (!createUjianCheckbox.checked) {
    btnSimpanSoal.classList.add('hidden');
}

// Re-run on page load (for form validation errors)
toggleUjianFields();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>