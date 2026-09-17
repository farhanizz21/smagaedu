<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Ganti Password']); ?>
<?php $this->load->view('partials/navbar'); ?>
<?php endif; ?>

<div class="max-w-2xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Ganti Password</h1>
        <p class="text-gray-500 mt-1 text-sm">Ubah password akun Anda</p>
    </div>

    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('success_msg'); ?>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-8">
        <form action="" method="post" class="space-y-6">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Password Lama
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="current_password" id="current_password"
                        class="input-field w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-gray-200 bg-white/80 text-sm font-medium text-gray-900 placeholder:text-gray-400 outline-none <?= form_error('current_password') ? 'border-red-300 bg-red-50' : '' ?>"
                        placeholder="Masukkan password lama">
                </div>
                <?php if(form_error('current_password')): ?>
                <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                    <?= form_error('current_password') ?>
                </p>
                <?php endif ?>
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Password Baru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="new_password" id="new_password"
                        class="input-field w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-gray-200 bg-white/80 text-sm font-medium text-gray-900 placeholder:text-gray-400 outline-none <?= form_error('new_password') ? 'border-red-300 bg-red-50' : '' ?>"
                        placeholder="Masukkan password baru">
                </div>
                <?php if(form_error('new_password')): ?>
                <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                    <?= form_error('new_password') ?>
                </p>
                <?php endif ?>
                <p class="mt-1.5 text-xs text-gray-500">Password minimal 6 karakter</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="input-field w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-gray-200 bg-white/80 text-sm font-medium text-gray-900 placeholder:text-gray-400 outline-none <?= form_error('confirm_password') ? 'border-red-300 bg-red-50' : '' ?>"
                        placeholder="Ulangi password baru">
                </div>
                <?php if(form_error('confirm_password')): ?>
                <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                    <?= form_error('confirm_password') ?>
                </p>
                <?php endif ?>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 rounded-2xl font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-lg shadow-blue-200 hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                Ubah Password
            </button>
        </form>
    </div>
</div>