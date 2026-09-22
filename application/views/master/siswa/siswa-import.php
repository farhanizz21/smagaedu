<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Import Data Siswa']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'siswa']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('siswa')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Import Data Siswa</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Tambahkan banyak data siswa sekaligus menggunakan file Excel</p>
        </div>
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
            <strong class="font-semibold">Beberapa data siswa gagal diimport:</strong>
            <div class="mt-1"><?= $this->session->userdata('import_errors'); ?></div>
        </div>
        <?php $this->session->unset_userdata('import_errors'); ?>
    </div>
    <?php endif; ?>

    <!-- Format Excel -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">1. Unduh Format Excel</h3>
                <p class="text-sm text-gray-500 mt-1">Isi data siswa sesuai kolom di bawah ini</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('siswa/format_excel'); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Download Format Excel
                </a>
                <a href="<?= base_url('siswa'); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Data Siswa
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Kolom</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-24">Wajib</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Keterangan</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Contoh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">NIS</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Ya</span></td>
                        <td class="px-4 py-3 text-gray-600">10 digit angka dan belum terdaftar pada data siswa</td>
                        <td class="px-4 py-3 text-gray-500">2024000001</td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">Nama Lengkap</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Ya</span></td>
                        <td class="px-4 py-3 text-gray-600">Nama lengkap siswa</td>
                        <td class="px-4 py-3 text-gray-500">Budi Santoso</td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">Username</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Tidak</span></td>
                        <td class="px-4 py-3 text-gray-600">Bila dikosongkan, dibuat otomatis dari nama dan ditambah angka bila sudah dipakai</td>
                        <td class="px-4 py-3 text-gray-500">budi.santoso</td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">Tanggal Lahir</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Ya</span></td>
                        <td class="px-4 py-3 text-gray-600">Format <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">YYYY-MM-DD</code>
                            atau <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">DD/MM/YYYY</code></td>
                        <td class="px-4 py-3 text-gray-500">2008-05-03</td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">Jenis Kelamin</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Ya</span></td>
                        <td class="px-4 py-3 text-gray-600"><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">L</code>
                            untuk Laki-laki atau <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded">P</code> untuk Perempuan</td>
                        <td class="px-4 py-3 text-gray-500">L</td>
                    </tr>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">Kelas</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">Ya</span></td>
                        <td class="px-4 py-3 text-gray-600">Nama kelas harus sama dengan data master kelas</td>
                        <td class="px-4 py-3 text-gray-500">XII IPA 1</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ul class="mt-6 space-y-1.5 text-sm text-gray-500 list-disc list-inside">
            <li>Hapus baris contoh pada file format sebelum diisi dengan data siswa.</li>
            <li>Satu baris mewakili satu siswa. Jangan mengubah atau menghapus baris header.</li>
            <li>Password awal siswa hasil import adalah <strong>edu12345</strong>, sama seperti tambah siswa manual.</li>
            <li>Format file <strong>.xlsx</strong> atau <strong>.xls</strong>, maksimal 5 MB.</li>
        </ul>
    </div>
    <!-- Upload -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-8">
        <h3 class="text-lg font-bold text-gray-900">2. Upload File Excel</h3>
        <p class="text-sm text-gray-500 mt-1 mb-6">Pilih file yang sudah diisi, lalu klik Import Data Siswa</p>

        <form method="post" action="<?= base_url('siswa/import'); ?>" enctype="multipart/form-data"
            class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-1 w-full">
                <label for="file_excel"
                    class="flex items-center justify-center w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-blue-400 transition-colors cursor-pointer bg-gray-50 hover:bg-blue-50/50">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6 text-emerald-600 flex-shrink-0"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700" id="file_excel_label">Pilih file Excel (.xlsx / .xls)</p>
                            <p class="text-xs text-gray-400">Maksimal 5 MB</p>
                        </div>
                    </div>
                    <input type="file" name="file_excel" id="file_excel" accept=".xlsx,.xls" class="hidden">
                </label>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all whitespace-nowrap">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import Data Siswa
            </button>
        </form>
    </div>

    <!-- Daftar Kelas -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <h3 class="text-lg font-bold text-gray-900">Daftar Kelas Tersedia</h3>
        <p class="text-sm text-gray-500 mt-1 mb-4">Nilai kolom Kelas pada file Excel harus sama dengan salah satu nama
            kelas berikut</p>

        <?php if(!empty($daftar_kelas)): ?>
        <div class="flex flex-wrap gap-2">
            <?php foreach($daftar_kelas as $k): ?>
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100">
                <?= $k->nama; ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 flex-shrink-0"></i>
            Belum ada data kelas. Tambahkan kelas terlebih dahulu pada menu
            <a href="<?= base_url('kelas'); ?>" class="font-semibold underline">Data Kelas</a>.
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('file_excel').addEventListener('change', function() {
    var label = document.getElementById('file_excel_label');
    label.textContent = this.files.length > 0 ? this.files[0].name : 'Pilih file Excel (.xlsx / .xls)';
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
