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

    <?php
        $has_filter = !empty($filters['q']) || !empty($filters['mapel']) || !empty($filters['jenis_kelamin']) || !empty($filters['admin_uuid']);
    ?>

    <!-- Filter & Pencarian -->
    <form method="get" action="<?= base_url('guru') ?>"
        class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 table-shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">
            <!-- Pencarian -->
            <div class="xl:col-span-4 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="q" value="<?= html_escape($filters['q']) ?>"
                    placeholder="Cari nama, username, atau NIP..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400">
            </div>

            <!-- Filter Mata Pelajaran -->
            <div class="xl:col-span-2">
                <select name="mapel" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                    <option value="">Semua Mapel</option>
                    <?php foreach($daftar_mapel as $m): ?>
                    <option value="<?= $m->uuid ?>" <?= ($filters['mapel'] === $m->uuid) ? 'selected' : '' ?>>
                        <?= $m->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if(is_superadmin()): ?>
            <div class="xl:col-span-2">
                <select name="admin_uuid" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                    <option value="">Semua Admin</option>
                    <option value="__unassigned__" <?= ($filters['admin_uuid'] === '__unassigned__') ? 'selected' : '' ?>>Belum ditugaskan</option>
                    <?php foreach($admins as $admin): ?>
                    <option value="<?= html_escape($admin->uuid) ?>" <?= ($filters['admin_uuid'] === $admin->uuid) ? 'selected' : '' ?>><?= html_escape($admin->nama) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Filter Jenis Kelamin -->
            <div class="xl:col-span-2">
                <select name="jenis_kelamin" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                    <option value="">Semua Gender</option>
                    <option value="L" <?= ($filters['jenis_kelamin'] === 'L') ? 'selected' : '' ?>>Laki-Laki</option>
                    <option value="P" <?= ($filters['jenis_kelamin'] === 'P') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <!-- Jumlah per halaman -->
            <div class="xl:col-span-2">
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
            <a href="<?= base_url('guru') ?>"
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
    <form id="bulkDeleteForm" method="POST" action="<?= base_url('guru/bulk_hapus') ?>"></form>
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <!-- Bulk action bar -->
        <div id="bulkActionBar" class="hidden items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <span id="selectedCount" class="text-sm font-medium text-gray-700">0 data dipilih</span>
            </div>
            <div>
                <?php if(is_superadmin()): ?>
                <button type="button" id="bulkAssignAdminBtn"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-all text-sm">
                    <i data-lucide="user-cog" class="w-4 h-4"></i> Tugaskan Admin
                </button>
                <?php endif; ?>
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
                        <th class="text-left px-4 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-56">
                            Admin / Pembuat</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Mata Pelajaran & Kelas</th>
                        <th
                            class="text-center px-4 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider <?= is_superadmin() ? 'w-40' : 'w-28' ?>">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = isset($start_no) ? $start_no : 1;
                        foreach($guru as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors" data-uuid="<?= $val->uuid ?>">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" form="bulkDeleteForm" name="guru_uuids[]" value="<?= $val->uuid ?>"
                                class="guru-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $val->nama; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->username; ?></td>
                        <td class="px-4 py-4">
                            <span class="block max-w-56 truncate text-gray-600" title="<?= html_escape($val->admin_nama ?? 'Belum ditugaskan'); ?>">
                                <?= html_escape($val->admin_nama ?? 'Belum ditugaskan'); ?>
                            </span>
                        </td>
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
                            <div class="flex items-center justify-center gap-1.5">
                                <?php if(is_superadmin()): ?>
                                <button type="button" data-assign-admin
                                    data-guru-uuid="<?= html_escape($val->uuid); ?>"
                                    data-guru-name="<?= html_escape($val->nama); ?>"
                                    data-admin-uuid="<?= html_escape($val->created_by ?? ''); ?>"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100"
                                    title="Atur admin pengelola" aria-label="Atur admin pengelola untuk <?= html_escape($val->nama); ?>">
                                    <i data-lucide="user-cog" class="h-4 w-4"></i>
                                </button>
                                <?php endif; ?>
                                <a href="<?=base_url('guru/edit/'.$val->uuid)?>"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors"
                                    title="Edit guru" aria-label="Edit <?= html_escape($val->nama); ?>">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <a href="<?=base_url('auth/reset_password/'.$val->uuid)?>"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100 transition-colors"
                                    title="Reset password" aria-label="Reset password <?= html_escape($val->nama); ?>"
                                    onclick="return confirm('Reset password untuk <?= html_escape($val->nama); ?>? Password baru akan menjadi: edu12345')">
                                    <i data-lucide="key-round" class="w-4 h-4"></i>
                                </a>
                                <a href="<?=base_url('guru/hapus/'.$val->uuid)?>"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                                    title="Hapus guru" aria-label="Hapus <?= html_escape($val->nama); ?>"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data <?= html_escape($val->nama); ?>?')">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
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
            <p class="text-sm">
                <?= $has_filter ? 'Tidak ada data guru yang cocok dengan pencarian/filter.' : 'Belum ada data guru' ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

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

<?php if(is_superadmin()): ?>
<dialog id="bulkAssignAdminDialog" class="w-[min(28rem,calc(100%-2rem))] rounded-xl border border-gray-200 p-0 shadow-2xl backdrop:bg-gray-900/40">
    <?= form_open('guru/bulk_assign_admin', array('id' => 'bulkAssignAdminForm')); ?>
    <div id="bulkAssignGuruUuids"></div>
    <div class="border-b border-gray-100 px-5 py-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Tugaskan Admin</h2>
                <p id="bulkAssignGuruCount" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <button type="button" id="closeBulkAssignAdmin" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100" title="Tutup" aria-label="Tutup dialog">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>
    <div class="px-5 py-5">
        <label for="bulkAssignAdminUuid" class="mb-2 block text-sm font-medium text-gray-700">Pilih admin</label>
        <select name="admin_uuid" id="bulkAssignAdminUuid" class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <option value="">Belum ditugaskan</option>
            <?php foreach($admins as $admin): ?>
            <option value="<?= html_escape($admin->uuid); ?>"><?= html_escape($admin->nama . ' (' . $admin->username . ')'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-4">
        <button type="button" id="cancelBulkAssignAdmin" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i data-lucide="save" class="h-4 w-4"></i> Simpan
        </button>
    </div>
    <?= form_close(); ?>
</dialog>
<dialog id="assignAdminDialog" class="w-[min(28rem,calc(100%-2rem))] rounded-xl border border-gray-200 p-0 shadow-2xl backdrop:bg-gray-900/40">
    <?= form_open('guru/assign_admin', array('id' => 'assignAdminForm')); ?>
    <input type="hidden" name="guru_uuid" id="assignGuruUuid">
    <div class="border-b border-gray-100 px-5 py-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Admin Pengelola</h2>
                <p id="assignGuruName" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <button type="button" id="closeAssignAdmin" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100" title="Tutup" aria-label="Tutup dialog">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>
    <div class="px-5 py-5">
        <label for="assignAdminUuid" class="mb-2 block text-sm font-medium text-gray-700">Pilih admin</label>
        <select name="admin_uuid" id="assignAdminUuid" class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <option value="">Belum ditugaskan</option>
            <?php foreach($admins as $admin): ?>
            <option value="<?= html_escape($admin->uuid); ?>"><?= html_escape($admin->nama . ' (' . $admin->username . ')'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-4">
        <button type="button" id="cancelAssignAdmin" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i data-lucide="save" class="h-4 w-4"></i> Simpan
        </button>
    </div>
    <?= form_close(); ?>
</dialog>
<?php endif; ?>

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
    var assignAdminDialog = document.getElementById('assignAdminDialog');
    var bulkAssignAdminDialog = document.getElementById('bulkAssignAdminDialog');

    var bulkAssignAdminBtn = document.getElementById('bulkAssignAdminBtn');
    if (bulkAssignAdminBtn) {
        bulkAssignAdminBtn.addEventListener('click', function () {
            var selected = getSelected();
            if (selected.length === 0) return;
            var target = document.getElementById('bulkAssignGuruUuids');
            target.innerHTML = '';
            selected.forEach(function (checkbox) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'guru_uuids[]';
                input.value = checkbox.value;
                target.appendChild(input);
            });
            document.getElementById('bulkAssignGuruCount').textContent = selected.length + ' guru dipilih';
            bulkAssignAdminDialog.showModal();
        });
    }

    ['closeBulkAssignAdmin', 'cancelBulkAssignAdmin'].forEach(function (id) {
        var button = document.getElementById(id);
        if (button) {
            button.addEventListener('click', function () {
                bulkAssignAdminDialog.close();
            });
        }
    });

    document.querySelectorAll('[data-assign-admin]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('assignGuruUuid').value = button.dataset.guruUuid;
            document.getElementById('assignGuruName').textContent = button.dataset.guruName;
            document.getElementById('assignAdminUuid').value = button.dataset.adminUuid;
            assignAdminDialog.showModal();
        });
    });

    ['closeAssignAdmin', 'cancelAssignAdmin'].forEach(function (id) {
        var button = document.getElementById(id);
        if (button) {
            button.addEventListener('click', function () {
                assignAdminDialog.close();
            });
        }
    });

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