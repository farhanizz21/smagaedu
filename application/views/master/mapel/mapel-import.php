<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Import Data Mata Pelajaran']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'mapel']); ?>
<?php endif; ?>

<div class="max-w-3xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('mapel')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Import Data Mata Pelajaran
                </h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Tambahkan banyak mata pelajaran sekaligus menggunakan file Excel</p>
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
            <strong class="font-semibold">Beberapa data mata pelajaran gagal diimport:</strong>
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
                <p class="text-sm text-gray-500 mt-1">Isi nama mata pelajaran sesuai kolom di bawah ini</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('mapel/format_excel'); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Download Format Excel
                </a>
                <a href="<?= base_url('mapel'); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    Data Mata Pelajaran
                </a>
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Kolom</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-20">
                            Wajib</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Keterangan</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Contoh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">Nama Mata Pelajaran</td>
                        <td class="px-4 py-3 text-center"><span
                                class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-600 border border-red-100">Ya</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">Nama mata pelajaran. Nama tidak boleh sama dengan data yang
                            sudah ada (termasuk antar baris dalam file), maksimal 100 karakter.</td>
                        <td class="px-4 py-3 text-gray-500">Koding</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ul class="mt-6 space-y-2 text-sm text-gray-600">
            <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5"></i>
                Hapus baris contoh (baris 2-4) sebelum mengisi data.</li>
            <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5"></i>
                Satu baris = satu mata pelajaran. Baris kosong akan dilewati saat import.</li>
            <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5"></i>
                Materi/bab tidak diisi dari file ini; kelola lewat menu masing-masing setelah mapel dibuat.</li>
            <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5"></i>
                Format file <strong>.xlsx</strong> atau <strong>.xls</strong>, maksimal 5 MB.</li>
        </ul>
    </div>

    <!-- Upload -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <h3 class="text-lg font-bold text-gray-900">2. Upload File Excel</h3>
        <p class="text-sm text-gray-500 mt-1 mb-6">Pilih file yang sudah diisi, lalu klik Import Data Mata Pelajaran
        </p>

        <form method="post" action="<?= base_url('mapel/import'); ?>" enctype="multipart/form-data"
            class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
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
                Import Data
            </button>
        </form>
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
