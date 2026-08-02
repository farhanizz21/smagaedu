<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Proyek']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'proyek']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('proyek')?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Tambah Proyek</h1>
                    <p class="text-violet-100 text-sm mt-1">Buat proyek pembelajaran baru</p>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
    </div>

    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form class="user" method="post" enctype="multipart/form-data" action="<?= base_url('proyek/tambah');?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Judul proyek" value="<?= set_value('judul'); ?>">
                    <div class="text-red-500 text-xs mt-1"><?= form_error('judul') ?></div>
                </div>

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
                    <div class="text-red-500 text-xs mt-1"><?= form_error('namaMapel') ?></div>
                </div>
            </div>

            <!-- File Upload -->
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File Proyek <span
                        class="text-red-500">*</span></label>
                <input type="file" name="berkas" id="berkas"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                    placeholder="Masukkan materi Materi" value="<?= set_value('berkas'); ?>">
                <p class="text-xs text-gray-500 mt-1">File dapat berupa dokumen, foto. Maksimal 50 Mb</p>
                <div class="text-red-500 text-xs mt-1"><?= form_error('berkas') ?></div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="6"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                    placeholder="Masukkan deskripsi proyek..."><?= set_value('deskripsi'); ?></textarea>
                <div class="text-red-500 text-xs mt-1"><?= form_error('deskripsi') ?></div>
            </div>

            <!-- Tanggal -->
            <div class="grid md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_mulai" id="tgl_mulai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        value="<?= set_value('tgl_mulai'); ?>">
                    <div class="text-red-500 text-xs mt-1"><?= form_error('tgl_mulai') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_selesai" id="tgl_selesai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        value="<?= set_value('tgl_selesai'); ?>">
                    <div class="text-red-500 text-xs mt-1"><?= form_error('tgl_selesai') ?></div>
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
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan dan Detail
                </button>
                <a href="<?= base_url('proyek')?>"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#deskripsi'))
    .catch(error => {
        console.error(error);
    });
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>