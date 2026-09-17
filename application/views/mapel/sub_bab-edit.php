<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Edit Bab']); ?>
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
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Edit Bab</h1>
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
        <?= form_open_multipart('sub_bab/edit/' . $bab->uuid); ?>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Sub Bab <span
                        class="text-red-500">*</span></label>
                <input type="text" name="judul" id="judul"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                    placeholder="Masukkan Judul Sub Bab" value="<?= set_value('judul', $bab->judul); ?>">
                <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('judul')) ? '' : 'hidden' ?>">
                    <?= form_error('judul') ?>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <div id="editor" style="height: 200px;"><?= set_value('deskripsi', $bab->deskripsi, FALSE); ?></div>
                <input type="hidden" name="deskripsi" id="deskripsi"
                    value="<?= set_value('deskripsi', $bab->deskripsi, FALSE); ?>">
                <small class="text-gray-400 text-xs mt-1 block">Gunakan toolbar di atas untuk formatting teks</small>
            </div>

            <!-- Dokumentasi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dokumentasi</label>
                <?php if (!empty($bab->dokumentasi)): ?>
                <p class="text-xs text-gray-400 mb-2">File saat ini:
                    <a href="<?= base_url('uploads/dokumentasi/' . $bab->dokumentasi) ?>" target="_blank"
                        class="text-blue-600 hover:underline"><?= $bab->dokumentasi ?></a>.
                    Kosongkan jika tidak ingin mengubah file.
                </p>
                <?php endif; ?>
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
                        placeholder="https://docs.google.com/..."
                        value="<?= set_value('dokumentasi_link', $bab->dokumentasi_link); ?>">
                    <small class="text-gray-400 text-xs">Contoh: Google Drive, Google Docs, atau link lain</small>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan
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
    placeholder: 'Masukkan deskripsi bab...'
});

// Update hidden input before form submit
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('deskripsi').value = quill.root.innerHTML;
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>