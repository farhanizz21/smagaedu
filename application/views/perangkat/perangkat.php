<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Perangkat Mata Pelajaran']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'perangkat']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Perangkat Mata Pelajaran</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola Modul dan ATP (Alur Tujuan Pembelajaran)</p>
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

    <!-- Card Grid -->
    <?php if(!empty($mapel)): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach($mapel as $val): ?>
        <a href="<?= base_url('perangkat/detail/' . $val->uuid) ?>"
            class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-blue-300 transition-all flex flex-col group">
            <div class="p-6 flex flex-col items-center text-center gap-4">
                <div
                    class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <i data-lucide="folder-open" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900 text-lg group-hover:text-blue-600 transition-colors">
                        <?= $val->nama ?>
                    </h5>
                    <p class="text-sm text-gray-500 mt-1">Kelola Modul & ATP</p>
                </div>
                <span
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200 group-hover:bg-blue-100 transition-colors">
                    <i data-lucide="eye" class="w-4 h-4"></i> Lihat Perangkat
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-gray-200 text-center py-16 text-gray-400">
        <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
        <p class="text-sm">Tidak ada mata pelajaran yang tersedia</p>
    </div>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>