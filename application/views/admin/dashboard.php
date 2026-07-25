<div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Admin Dashboard - Superadmin Only</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card Role -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-purple-100 rounded-xl">
                    <i data-lucide="shield" class="w-8 h-8 text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Kelola Role</h3>
            </div>
            <p class="text-gray-600 mb-4">Atur role dan permission untuk semua user</p>
            <a href="<?= base_url('admin/manage_roles') ?>"
                class="inline-block px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Kelola Role
            </a>
        </div>

        <!-- Card Permission -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-green-100 rounded-xl">
                    <i data-lucide="key" class="w-8 h-8 text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Kelola Permission</h3>
            </div>
            <p class="text-gray-600 mb-4">Atur hak akses untuk setiap role</p>
            <a href="<?= base_url('admin/manage_permissions') ?>"
                class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Kelola Permission
            </a>
        </div>

        <!-- Card Settings -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i data-lucide="settings" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Pengaturan Sistem</h3>
            </div>
            <p class="text-gray-600 mb-4">Konfigurasi aplikasi</p>
            <a href="<?= base_url('admin/settings') ?>"
                class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Pengaturan
            </a>
        </div>
    </div>

    <!-- Info Role -->
    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Role Anda</h3>
        <div class="space-y-2">
            <p><span class="font-medium">Nama:</span> <?= current_user()->nama ?? '-'; ?></p>
            <p><span class="font-medium">Username:</span> <?= current_user()->username ?? '-'; ?></p>
            <p><span class="font-medium">Role:</span> <span
                    class="px-2 py-1 bg-purple-100 text-purple-800 rounded"><?= user_role(); ?></span></p>
        </div>
    </div>
</div>