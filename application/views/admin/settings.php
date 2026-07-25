<div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Pengaturan Sistem</h1>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <p class="text-gray-600">Halaman pengaturan sistem untuk superadmin.</p>
        <p class="mt-4 text-sm text-gray-500">Fitur ini hanya bisa diakses oleh superadmin.</p>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="<?= base_url('admin/manage_roles') ?>"
                class="block p-4 rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="shield" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800">Kelola Role</div>
                        <div class="text-xs text-gray-500">Tambah / edit role</div>
                    </div>
                </div>
            </a>
            <a href="<?= base_url('admin/manage_permissions') ?>"
                class="block p-4 rounded-xl border border-gray-200 hover:border-emerald-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="key" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800">Kelola Permission</div>
                        <div class="text-xs text-gray-500">Assign permission per role</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>