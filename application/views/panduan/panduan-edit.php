<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Edit Panduan']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'panduan']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('panduan')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Edit Data Panduan</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Perbarui informasi panduan yang sudah ada</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form method="post" enctype="multipart/form-data" action="<?= base_url('panduan/edit/'.$panduan->uuid);?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Judul Panduan" value="<?= $panduan->judul; ?>">
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
                        placeholder="Masukkan berkas Panduan">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('berkas')) ? '' : 'hidden' ?>">
                        <?= form_error('berkas') ?>
                    </div>
                    <p class="text-gray-400 text-xs mt-1">Biarkan kosong jika tidak ingin mengubah file</p>
                </div>

                <!-- Tujuan (Multi Select) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ditujukan Kepada <span
                            class="text-red-500">*</span></label>
                    <select name="tujuan[]" class="multiple-tujuan w-full" multiple="multiple" style="width:100%">
                        <?php $selected_tujuan = json_decode($panduan->tujuan); ?>
                        <option value="1"
                            <?= is_array($selected_tujuan) && in_array('1', $selected_tujuan) ? 'selected' : ''; ?>>Guru
                        </option>
                        <option value="2"
                            <?= is_array($selected_tujuan) && in_array('2', $selected_tujuan) ? 'selected' : ''; ?>>
                            Siswa</option>
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