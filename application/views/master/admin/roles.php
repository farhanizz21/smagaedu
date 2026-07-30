<div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Role</h1>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Role</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($roles as $role): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900"><?= $role->id; ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <span
                                class="px-2 py-1 bg-blue-100 text-blue-800 rounded"><?= ucfirst($role->nama); ?></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= $role->deskripsi; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>