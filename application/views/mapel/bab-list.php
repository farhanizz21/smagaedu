<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header with Gradient -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 p-8 md:p-10 mb-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/3 right-1/4 w-24 h-24 bg-yellow-400/10 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="<?= base_url('mata_pelajaran') ?>"
                            class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                        </a>
                        <div
                            class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <i data-lucide="book-marked" class="w-6 h-6 text-white"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Daftar Bab</h1>
                    </div>
                    <p class="text-teal-100 text-sm md:text-base ml-[104px]">Mata Pelajaran:
                        <span class="text-white font-semibold"><?= $mapel->nama ?></span>
                    </p>
                </div>
                <?php if($pengampu == true || $is_admin): ?>
                <a href="<?= base_url('bab/tambah/' . $mapel->uuid) ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-emerald-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Bab
                </a>
                <?php endif; ?>
            </div>
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
    <?php if(!empty($materi)): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
            $color_palettes = [
                ['from' => 'from-emerald-500', 'to' => 'to-green-500', 'bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'hover' => 'hover:border-emerald-300 hover:shadow-emerald-200/50', 'badge' => 'bg-emerald-100 text-emerald-700', 'btn' => 'from-emerald-500 to-green-500'],
                ['from' => 'from-violet-500', 'to' => 'to-purple-500', 'bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'hover' => 'hover:border-violet-300 hover:shadow-violet-200/50', 'badge' => 'bg-violet-100 text-violet-700', 'btn' => 'from-violet-500 to-purple-500'],
                ['from' => 'from-sky-500', 'to' => 'to-blue-500', 'bg' => 'bg-sky-50', 'icon' => 'text-sky-600', 'hover' => 'hover:border-sky-300 hover:shadow-sky-200/50', 'badge' => 'bg-sky-100 text-sky-700', 'btn' => 'from-sky-500 to-blue-500'],
                ['from' => 'from-rose-500', 'to' => 'to-pink-500', 'bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'hover' => 'hover:border-rose-300 hover:shadow-rose-200/50', 'badge' => 'bg-rose-100 text-rose-700', 'btn' => 'from-rose-500 to-pink-500'],
                ['from' => 'from-amber-500', 'to' => 'to-orange-500', 'bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'hover' => 'hover:border-amber-300 hover:shadow-amber-200/50', 'badge' => 'bg-amber-100 text-amber-700', 'btn' => 'from-amber-500 to-orange-500'],
                ['from' => 'from-cyan-500', 'to' => 'to-teal-500', 'bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'hover' => 'hover:border-cyan-300 hover:shadow-cyan-200/50', 'badge' => 'bg-cyan-100 text-cyan-700', 'btn' => 'from-cyan-500 to-teal-500'],
            ];
            $i = 0;
        ?>
        <?php foreach($materi as $val): ?>
        <?php 
            $palette = $color_palettes[$i % count($color_palettes)];
            $i++;
            $icons = ['book-open', 'book-marked', 'book-audio', 'file-text', 'notebook-text', 'scroll-text'];
            $icon = $icons[$i % count($icons)];
        ?>
        <div
            class="card group relative bg-white rounded-2xl border-2 border-gray-100 <?= $palette['hover'] ?> hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
            <!-- Colorful top accent bar -->
            <div class="h-2 w-full bg-gradient-to-r <?= $palette['from'] ?> <?= $palette['to'] ?>"></div>

            <a href="<?= base_url('sub_bab/index/' . $val->uuid) ?>" class="block group flex-1">
                <?php if (!empty($val->thumbnail)): ?>
                <div class="relative overflow-hidden">
                    <img src="<?= base_url('uploads/thumbnail/' . $val->thumbnail) ?>"
                        class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
                <?php else: ?>
                <div class="w-full h-44 <?= $palette['bg'] ?> flex items-center justify-center">
                    <i data-lucide="<?= $icon ?>" class="w-16 h-16 <?= $palette['icon'] ?> opacity-30"></i>
                </div>
                <?php endif; ?>
                <div class="p-5 flex flex-col gap-3 flex-1">
                    <h5
                        class="font-bold text-gray-900 text-base leading-snug group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r <?= $palette['from'] ?> <?= $palette['to'] ?> transition-all duration-300">
                        <?= $val->judul ?></h5>
                    <p class="text-sm text-gray-500 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <?= $val->nama ?>
                    </p>
                </div>
            </a>

            <!-- Action Buttons -->
            <div class="px-5 pb-4 flex items-center gap-2">
                <a href="<?= base_url('sub_bab/index/' . $val->uuid) ?>"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r <?= $palette['btn'] ?> shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                    <i data-lucide="layers" class="w-3.5 h-3.5"></i> Sub Bab
                </a>
                <?php
                    $current_uuid = $this->session->userdata('uuid');
                    $can_manage = $is_admin || $val->created_by == $current_uuid;
                ?>
                <?php if($can_manage): ?>
                <a href="<?= base_url('bab/edit/' . $val->uuid) ?>"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                </a>
                <a href="<?= base_url('mata_pelajaran/hapus/' . $val->uuid) ?>"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus bab <?= $val->judul; ?>?')">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                </a>
                <?php endif; ?>
            </div>

            <!-- Decorative dots -->
            <div class="absolute -bottom-6 -right-6 w-24 h-24 opacity-5">
                <div class="grid grid-cols-4 gap-2">
                    <?php for($d=0; $d<16; $d++): ?>
                    <div class="w-2 h-2 rounded-full bg-gray-800"></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border-2 border-gray-100 text-center py-16">
        <div
            class="w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="book-open" class="w-10 h-10 text-emerald-400"></i>
        </div>
        <p class="text-gray-400 font-medium">Belum ada data bab</p>
        <p class="text-gray-300 text-sm mt-1">Silakan tambah bab baru untuk mata pelajaran ini</p>
    </div>
    <?php endif; ?>
</div>

<style>
/* Smooth entrance animation for cards */
.card {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.card:nth-child(1) {
    animation-delay: 0.05s;
}

.card:nth-child(2) {
    animation-delay: 0.1s;
}

.card:nth-child(3) {
    animation-delay: 0.15s;
}

.card:nth-child(4) {
    animation-delay: 0.2s;
}

.card:nth-child(5) {
    animation-delay: 0.25s;
}

.card:nth-child(6) {
    animation-delay: 0.3s;
}

.card:nth-child(7) {
    animation-delay: 0.35s;
}

.card:nth-child(8) {
    animation-delay: 0.4s;
}

.card:nth-child(9) {
    animation-delay: 0.45s;
}

.card:nth-child(10) {
    animation-delay: 0.5s;
}

.card:nth-child(11) {
    animation-delay: 0.55s;
}

.card:nth-child(12) {
    animation-delay: 0.6s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>