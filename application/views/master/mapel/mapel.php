<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Data Mata Pelajaran']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'mapel']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Data Mata Pelajaran</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola data mata pelajaran di SMAGAEDU</p>
        </div>
        <?php if(has_role(['admin', 'superadmin'])): ?>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('mapel/import')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition-all text-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Import Excel
            </a>
            <a href="<?= base_url('mapel/tambah')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Data
            </a>
        </div>
        <?php else: ?>
        <a href="<?= base_url('mapel/tambah')?>"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Data
        </a>
        <?php endif; ?>
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

    <?php
        $has_filter = !empty($filters['q']);
    ?>

    <!-- Filter & Pencarian -->
    <form method="get" action="<?= base_url('mapel') ?>"
        class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 table-shadow">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <!-- Pencarian -->
            <div class="md:col-span-9 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="q" value="<?= html_escape($filters['q']) ?>"
                    placeholder="Cari nama mata pelajaran..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400">
            </div>

            <!-- Jumlah per halaman -->
            <div class="md:col-span-3">
                <select name="per_page" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                    <?php foreach($per_page_options as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($per_page == $opt) ? 'selected' : '' ?>><?= $opt ?> / halaman</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 mt-3">
            <?php if($has_filter): ?>
            <span class="mr-auto text-xs text-gray-400 hidden sm:inline">Filter aktif</span>
            <?php endif; ?>
            <a href="<?= base_url('mapel') ?>"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                Reset
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="search" class="w-4 h-4"></i>
                Cari
            </button>
        </div>
    </form>

    <!-- Table Card -->
    <form id="bulkDeleteForm" method="POST" action="<?= base_url('mapel/bulk_hapus') ?>">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <?php if(has_role(['admin', 'superadmin'])): ?>
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
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            <?php if(has_role(['admin', 'superadmin'])): ?>
                            <input type="checkbox" id="checkboxAll"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            <?php else: ?>
                            No.
                            <?php endif; ?>
                        </th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Mata Pelajaran</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-28">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = isset($start_no) ? $start_no : 1;
                        foreach($mapel as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors" data-uuid="<?= $val->uuid ?>">
                        <td class="px-6 py-4 text-center">
                            <?php if(has_role(['admin', 'superadmin'])): ?>
                            <input type="checkbox" name="mapel_uuids[]" value="<?= $val->uuid ?>"
                                class="mapel-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            <?php else: ?>
                            <span class="text-gray-500"><?= $no; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $val->nama; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?=base_url('mapel/edit/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    Edit
                                </a>
                                <a href="<?=base_url('mapel/hapus/'.$val->uuid)?>"
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
        <?php if(empty($mapel)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">
                <?= $has_filter ? 'Tidak ada mata pelajaran yang cocok dengan pencarian/filter.' : 'Belum ada data mata pelajaran' ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
    </form>

    <!-- Info & Pagination -->
    <?php if($total_rows > 0): ?>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
        <p class="text-sm text-gray-500">
            Menampilkan
            <span class="font-semibold text-gray-700"><?= $start_no ?></span>&ndash;<span
                class="font-semibold text-gray-700"><?= $end_no ?></span>
            dari <span class="font-semibold text-gray-700"><?= $total_rows ?></span> data
        </p>
        <?php if(!empty($pagination_links)): ?>
        <div><?= $pagination_links ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>

<?php if(has_role(['admin', 'superadmin'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var checkboxAll = document.getElementById('checkboxAll');
    var checkboxes = document.querySelectorAll('.mapel-checkbox');
    var bulkActionBar = document.getElementById('bulkActionBar');
    var selectedCountEl = document.getElementById('selectedCount');
    var bulkHapusBtn = document.getElementById('bulkHapusBtn');
    var bulkDeleteForm = document.getElementById('bulkDeleteForm');

    if (!bulkDeleteForm) return;

    function getSelected() {
        return Array.from(document.querySelectorAll('.mapel-checkbox:checked'));
    }

    function updateBulkState() {
        var selected = getSelected();
        if (selectedCountEl) {
            selectedCountEl.textContent = selected.length + ' data dipilih';
        }
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
            if (confirm('Hapus ' + selected.length + ' mata pelajaran terpilih? Data yang sudah dihapus tidak dapat dikembalikan.')) {
                bulkDeleteForm.submit();
            }
        });
    }

    updateBulkState();
});
</script>
<?php endif; ?>