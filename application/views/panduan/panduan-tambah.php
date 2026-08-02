<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Panduan']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'panduan']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('panduan')?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Tambah Data Panduan</h1>
                    <p class="text-amber-100 text-sm mt-1">Lengkapi formulir di bawah untuk menambahkan panduan baru</p>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form method="post" enctype="multipart/form-data" action="<?= base_url('panduan/tambah');?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Judul Panduan" value="<?= set_value('judul'); ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('judul')) ? '' : 'hidden' ?>">
                        <?= form_error('judul') ?>
                    </div>
                </div>

                <!-- Upload File -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File Panduan <span
                            class="text-red-500">*</span></label>
                    <input type="file" name="berkas" id="berkas"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        placeholder="Masukkan berkas Panduan" value="<?= set_value('berkas'); ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('berkas')) ? '' : 'hidden' ?>">
                        <?= form_error('berkas') ?>
                    </div>
                </div>

                <!-- Tujuan (Multi Select) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ditujukan Kepada <span
                            class="text-red-500">*</span></label>
                    <select name="tujuan[]" class="multiple-tujuan w-full" multiple="multiple" style="width:100%">
                        <option value="1" <?= set_select('tujuan', 1); ?>>Guru</option>
                        <option value="2" <?= set_select('tujuan', 2); ?>>Siswa</option>
                    </select>
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('tujuan')) ? '' : 'hidden' ?>">
                        <?= form_error('tujuan') ?>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
                <a href="<?= base_url('panduan')?>"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.multiple-tujuan').select2({
        placeholder: "Pilih Role Tujuan",
        allowClear: true,
        width: '100%'
    });
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>