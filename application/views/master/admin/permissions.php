<div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Permission</h1>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Permission per Role</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permission</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $current_role = null;
                    foreach ($permissions as $perm): 
                        if ($current_role != $perm->role_nama):
                            $current_role = $perm->role_nama;
                    ?>
                    <tr class="bg-gray-50">
                        <td colspan="2" class="px-6 py-3 font-semibold text-gray-700"><?= ucfirst($perm->role_nama); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500"></td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <span
                                class="px-2 py-1 bg-green-100 text-green-800 rounded"><?= $perm->permission_nama; ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-700 font-medium">Keterangan:</p>
            <ul class="mt-2 text-sm text-blue-600 space-y-1">
                <li>• superadmin: Memiliki semua permission</li>
                <li>• admin: manage_users, manage_mapel, manage_materi, manage_ujian, manage_proyek</li>
                <li>• guru: manage_materi, manage_ujian, manage_proyek</li>
                <li>• siswa: view_reports</li>
            </ul>
        </div>
    </div>
</div>