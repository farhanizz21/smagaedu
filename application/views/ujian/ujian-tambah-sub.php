<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Ujian Sub Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-3xl mx-auto px-6 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('bab/index/' . $materi->uuid) ?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah Ujian Sub Bab</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">
                Materi: <span class="text-blue-600 font-semibold"><?= $materi->judul ?></span> →
                Bab: <span class="text-blue-600 font-semibold"><?= $bab->judul ?></span> →
                Sub Bab: <span class="text-blue-600 font-semibold"><?= $sub->judul ?></span>
            </p>
        </div>
    </div>

    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <?= form_open('ujian/tambah_sub/' . $sub->uuid); ?>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span
                        class="text-red-500">*</span></label>
                <select name="namaMapel" id="namaMapel"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach($mapel_list as $val): ?>
                    <option value="<?= $val->uuid ?>" <?= set_select('namaMapel', $val->uuid) ?>>
                        <?= $val->nama ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('namaMapel')) ? '' : 'hidden' ?>">
                    <?= form_error('namaMapel') ?>
                </div>
            </div>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_mulai" id="tgl_mulai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        value="<?= set_value('tgl_mulai'); ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('tgl_mulai')) ? '' : 'hidden' ?>">
                        <?= form_error('tgl_mulai') ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_selesai" id="tgl_selesai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        value="<?= set_value('tgl_selesai'); ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('tgl_selesai')) ? '' : 'hidden' ?>">
                        <?= form_error('tgl_selesai') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit" name="action" value="simpan"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan
            </button>
            <button type="submit" name="action" value="simpan_detail"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-green-600 shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="list" class="w-4 h-4"></i> Simpan & Tambah Soal
            </button>
            <a href="<?= base_url('bab/index/' . $materi->uuid) ?>"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                <i data-lucide="x" class="w-4 h-4"></i> Batal
            </a>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>