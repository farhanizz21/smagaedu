<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Data Guru']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'guru']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Data Guru</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola data guru pengajar di SMAGAEDU</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('guru/import')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition-all text-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Import Excel
            </a>
            <a href="<?= base_url('guru/tambah')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Data
            </a>
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
            <strong class="font-semibold">Beberapa data guru gagal diimport:</strong>
            <div class="mt-1"><?= $this->session->userdata('import_errors'); ?></div>
        </div>
        <?php $this->session->unset_userdata('import_errors'); ?>
    </div>
    <?php endif; ?>

    <!-- Table Card -->
    <form id="bulkDeleteForm" method="POST" action="<?= base_url('guru/bulk_hapus') ?>">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <!-- Bulk action bar -->
        <div id="bulkActionBar" class="hidden items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <span id="selectedCount" class="text-sm font-medium text-gray-700">0 data dipilih</span>
            </div>
            <div>
                <button type="button" id="bulkHapusBtn"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-white bg-red-600 hover:bg-red-700 transition-all text-sm disabled:opacity-50"
                    disabled>
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Terpilih
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            <input type="checkbox" id="checkboxAll"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Username</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Mata Pelajaran & Kelas</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-28">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
                        foreach($guru as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors" data-uuid="<?= $val->uuid ?>">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" name="guru_uuids[]" value="<?= $val->uuid ?>"
                                class="guru-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $val->nama; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->username; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <?php if (!empty($val->mapel_nama)) : ?>
                                <?php foreach ($val->mapel_data as $i => $mapel_obj) : ?>
                                <div class="flex flex-col gap-0.5">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        <?= $mapel_obj->nama ?>
                                    </span>
                                    <?php if (isset($val->kelas_per_mapel[$mapel_obj->uuid]) && !empty($val->kelas_per_mapel[$mapel_obj->uuid])): ?>
                                    <div class="flex flex-wrap gap-0.5 ml-1">
                                        <?php foreach ($val->kelas_per_mapel[$mapel_obj->uuid] as $kelas_obj): ?>
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-50 text-green-600 border border-green-100">
                                            <?= $kelas_obj->nama ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <span class="text-gray-400 text-xs">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?=base_url('guru/edit/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    Edit
                                </a>
                                <a href="<?=base_url('auth/reset_password/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition-colors"
                                    onclick="return confirm('Reset password untuk <?= $val->nama; ?>? Password baru akan menjadi: edu12345')">
                                    <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                    Reset Password
                                </a>
                                <a href="<?=base_url('guru/hapus/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data <?= $val->nama; ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($guru)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada data guru</p>
        </div>
        <?php endif; ?>
    </div>
    </form>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var checkboxAll = document.getElementById('checkboxAll');
    var checkboxes = document.querySelectorAll('.guru-checkbox');
    var bulkActionBar = document.getElementById('bulkActionBar');
    var selectedCountEl = document.getElementById('selectedCount');
    var bulkHapusBtn = document.getElementById('bulkHapusBtn');
    var bulkDeleteForm = document.getElementById('bulkDeleteForm');

    function getSelected() {
        return Array.from(document.querySelectorAll('.guru-checkbox:checked'));
    }

    function updateBulkState() {
        var selected = getSelected();
        selectedCountEl.textContent = selected.length + ' data dipilih';
        if (selected.length > 0) {
            bulkActionBar.classList.remove('hidden');
            bulkHapusBtn.disabled = false;
        } else {
            bulkActionBar.classList.add('hidden');
            bulkHapusBtn.disabled = true;
        }
    }

    if (checkboxAll) {
        checkboxAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) {
                cb.checked = checkboxAll.checked;
            });
            updateBulkState();
        });
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', updateBulkState);
    });

    if (bulkHapusBtn) {
        bulkHapusBtn.addEventListener('click', function () {
            var selected = getSelected();
            if (selected.length === 0) return;
            if (confirm('Hapus ' + selected.length + ' guru terpilih? Data yang sudah dihapus tidak dapat dikembalikan.')) {
                bulkDeleteForm.submit();
            }
        });
    }

    updateBulkState();
});
</script>