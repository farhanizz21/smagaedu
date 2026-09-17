<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Perangkat Mata Pelajaran']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'perangkat']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <i data-lucide="monitor-smartphone" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Perangkat Mata Pelajaran
                    </h1>
                </div>
            </div>
            <p class="text-blue-100 mt-1 text-sm ml-15">Kelola Modul dan ATP (Alur Tujuan Pembelajaran)</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
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
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
            $colors = [
                'from-blue-400 to-blue-600',
                'from-emerald-400 to-teal-600',
                'from-amber-400 to-orange-500',
                'from-violet-400 to-purple-600',
                'from-pink-400 to-rose-600',
                'from-cyan-400 to-blue-600'
            ];
            $ctr = 0;
        ?>
        <?php foreach($mapel as $val): ?>
        <?php $color = $colors[$ctr % count($colors)]; $ctr++; ?>
        <a href="<?= base_url('perangkat/detail/' . $val->uuid) ?>"
            class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col">
            <div class="h-2 bg-gradient-to-r <?= $color ?>"></div>
            <div class="p-6 flex flex-col items-center text-center gap-4 flex-1">
                <div class="relative">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br <?= $color ?> flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="folder-open" class="w-8 h-8 text-white"></i>
                    </div>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 text-lg group-hover:bg-clip-text group-hover:text-transparent transition-all duration-300"
                        style="-webkit-text-fill-color: transparent; background: linear-gradient(135deg, #2563eb, #7c3aed); -webkit-background-clip: text;">
                        <?= $val->nama ?>
                    </h5>
                    <p class="text-sm text-gray-500 mt-1">Kelola Modul & ATP</p>
                </div>
                <span
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold bg-gray-50 text-gray-700 border border-gray-200 group-hover:bg-gradient-to-r group-hover:from-blue-50 group-hover:to-indigo-50 group-hover:text-indigo-700 group-hover:border-indigo-200 transition-all duration-300">
                    <i data-lucide="eye" class="w-4 h-4"></i> Lihat Perangkat
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-gray-200 text-center py-16 text-gray-400">
        <div
            class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="folder-open" class="w-10 h-10 text-amber-500"></i>
        </div>
        <p class="text-sm font-medium text-gray-600">Tidak ada mata pelajaran yang tersedia</p>
    </div>
    <?php endif; ?>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>