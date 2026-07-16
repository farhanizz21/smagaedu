<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Materi']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('materi') ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Daftar Materi</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Mata Pelajaran:
                <span class="text-blue-600 font-semibold"><?= $mapel->nama ?></span>
            </p>
        </div>
        <?php if($pengampu == true || !empty($is_admin)): ?>
        <a href="<?= base_url('materi/tambah/' . $mapel->uuid) ?>"
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

    <!-- Card Grid -->
    <?php if(!empty($materi)): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach($materi as $val): ?>
        <div
            class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-blue-300 transition-all flex flex-col">
            <a href="<?= base_url('bab/index/' . $val->uuid) ?>" class="block group flex-1">
                <?php if (!empty($val->thumbnail)): ?>
                <img src="<?= base_url('uploads/thumbnail/' . $val->thumbnail) ?>" class="w-full h-44 object-cover">
                <?php else: ?>
                <div class="w-full h-44 bg-gray-100 flex items-center justify-center">
                    <span class="text-gray-400 text-sm">No Image Available</span>
                </div>
                <?php endif; ?>
                <div class="p-5 flex flex-col gap-3 flex-1">
                    <h5
                        class="font-semibold text-gray-900 text-base leading-snug group-hover:text-blue-600 transition-colors">
                        <?= $val->judul ?></h5>
                    <p class="text-sm text-gray-500 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <?= $val->nama ?>
                    </p>
                    <div class="mt-auto flex items-center justify-between gap-2 pt-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 group-hover:bg-blue-100 transition-colors">
                            <i data-lucide="book-open" class="w-3.5 h-3.5"></i> Bab
                        </span>
                    </div>
                </div>
            </a>
            <?php
                $current_uuid = $this->session->userdata('uuid');
                $can_manage = !empty($is_admin) || $val->created_by == $current_uuid;
            ?>
            <?php if($can_manage): ?>
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
                <a href="<?= base_url('materi/edit/' . $val->uuid) ?>"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                </a>
                <a href="<?= base_url('materi/hapus/' . $val->uuid) ?>"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus materi <?= $val->judul; ?>?')">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-gray-200 text-center py-16 text-gray-400">
        <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
        <p class="text-sm">Belum ada data materi</p>
    </div>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>