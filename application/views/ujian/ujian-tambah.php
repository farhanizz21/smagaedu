<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('ujian')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah Ujian</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Buat ujian baru untuk siswa</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form method="post" action="<?= base_url('ujian/tambah');?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span
                            class="text-red-500">*</span></label>
                    <select name="namaMapel"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option disabled selected>Pilih Mata Pelajaran</option>
                        <?php 
                        foreach($mapel as $val){
                        ?>
                        <option value="<?= $val->uuid; ?>" <?= set_select('namaMapel', $val->uuid) ;?>>
                            <?= $val->nama; ?>
                        </option>
                        <?php 
                        }
                        ?>
                    </select>
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('namaMapel')) ? '' : 'hidden' ?>">
                        <?= form_error('namaMapel') ?>
                    </div>
                </div>

                <!-- Nama Ujian -->
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

                <!-- Tanggal Mulai -->
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

                <!-- Tanggal Selesai -->
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

            <!-- Buttons -->
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit" name="action" value="simpan"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
                <button type="submit" name="action" value="simpan_detail"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-green-600 shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Simpan dan Tambah Soal
                </button>
                <a href="<?= base_url('ujian')?>"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>