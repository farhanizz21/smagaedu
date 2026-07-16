<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Sub Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="<?= base_url('bab/index/' . $materi->uuid) ?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">Materi: <?= $materi->judul ?></a>
                <span class="text-gray-400">→</span>
                <span class="text-gray-900 font-medium">Bab: <?= $bab->judul ?></span>
                <span class="text-gray-400">→</span>
                <span class="text-blue-600 font-semibold">Sub Bab</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= base_url('bab/index/' . $materi->uuid) ?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Sub Bab</h1>
            </div>
        </div>
        <?php if($can_manage): ?>
        <a href="<?= base_url('sub_materi/tambah/' . $bab->uuid) ?>"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Sub Bab
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

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Judul Sub Bab</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            File</th>
                        <?php if($can_manage): ?>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-32">
                            Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $no = 1; foreach($sub as $val): ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-center"><?= $no++; ?></td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $val->judul ?></td>
                        <td class="px-6 py-4">
                            <?php if(!empty($val->berkas)): ?>
                            <a href="<?= base_url('uploads/sub_materi/' . $val->berkas) ?>" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat
                            </a>
                            <?php else: ?>
                            <span class="text-gray-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                        <?php if($can_manage): ?>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('sub_materi/edit/' . $val->uuid) ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </a>
                                <a href="<?= base_url('sub_materi/hapus/' . $val->uuid) ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus sub bab <?= $val->judul; ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($sub)): ?>
                    <tr>
                        <td colspan="<?= $can_manage ? 4 : 3 ?>" class="text-center py-12 text-gray-400">
                            <i data-lucide="layers" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                            <p class="text-sm">Belum ada sub bab</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>