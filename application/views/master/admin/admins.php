<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Data Admin']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'admins']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Data Admin</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola data administrator SMARTEDU</p>
        </div>
        <?php if(is_superadmin()): ?>
        <a href="<?= base_url('admin/admins_tambah')?>"
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

    <!-- Info Card untuk Role Admin -->
    <?php if(!is_superadmin()): ?>
    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm flex items-center gap-2">
        <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0"></i>
        Anda login sebagai Admin. Hanya dapat melihat data. Hubungi Superadmin untuk perubahan data.
    </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Username</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Email</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Dibuat oleh</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-28">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
                        foreach($admins as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-center"><?= $no ; ?></td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $val->nama; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->username; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->email ?: '-'; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->creator_nama ?: '-'; ?></td>
                        <td class="px-6 py-4">
                            <?php if(is_superadmin()): ?>
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?=base_url('admin/admins_edit/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    Edit
                                </a>
                                <a href="<?=base_url('auth/reset_password/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-100 transition-colors"
                                    onclick="return confirm('Reset password untuk <?= $val->nama; ?>? Password baru akan menjadi: admin12345')">
                                    <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                    Reset Password
                                </a>
                                <?php if($this->session->userdata('uuid') != $val->uuid): ?>
                                <a href="<?=base_url('admin/admins_hapus/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data <?= $val->nama; ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    Hapus
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="flex items-center justify-center">
                                <span class="text-xs text-gray-400 italic">View Only</span>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($admins)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada data admin</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>