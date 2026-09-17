<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Edit Admin']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'admins']); ?>
<?php endif; ?>

<div class="max-w-3xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <a href="<?= base_url('admin/admins') ?>"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Edit Admin</h1>
        <p class="text-gray-500 mt-1 text-sm">Mengubah data administrator SMARTEDU</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <form action="<?= base_url('admin/admins_edit/'.$admin->uuid) ?>" method="POST" class="p-6 space-y-6">
            <input type="hidden" name="uuid" value="<?= $admin->uuid ?>">

            <!-- Nama Lengkap -->
            <div>
                <label for="namaLengkap" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span
                        class="text-red-500">*</span></label>
                <input type="text" id="namaLengkap" name="namaLengkap"
                    value="<?= set_value('namaLengkap', $admin->nama) ?>"
                    class="w-full px-4 py-2.5 rounded-xl border <?= form_error('namaLengkap') ? 'border-red-300 bg-red-50' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
                    placeholder="Masukkan nama lengkap">
                <?= form_error('namaLengkap', '<p class="mt-1 text-xs text-red-500">', '</p>') ?>
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span
                        class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" value="<?= set_value('username', $admin->username) ?>"
                    class="w-full px-4 py-2.5 rounded-xl border <?= form_error('username') ? 'border-red-300 bg-red-50' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
                    placeholder="contoh: admin_baru (huruf kecil semua)">
                <?= form_error('username', '<p class="mt-1 text-xs text-red-500">', '</p>') ?>
                <p class="mt-1 text-xs text-gray-400">Hanya huruf kecil (a-z), tidak boleh ada spasi</p>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input type="email" id="email" name="email" value="<?= set_value('email', $admin->email) ?>"
                    class="w-full px-4 py-2.5 rounded-xl border <?= form_error('email') ? 'border-red-300 bg-red-50' : 'border-gray-300' ?> focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors"
                    placeholder="admin@sekolah.sch.id">
                <?= form_error('email', '<p class="mt-1 text-xs text-red-500">', '</p>') ?>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update
                </button>
                <a href="<?= base_url('admin/admins') ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>