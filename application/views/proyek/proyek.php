<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Proyek']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'proyek']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <i data-lucide="folder-kanban" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Daftar Proyek</h1>
                </div>
            </div>
            <p class="text-violet-100 mt-1 text-sm ml-15">Kelola proyek pembelajaran di SMAGAEDU</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
        <?php if(has_role(['superadmin', 'admin', 'guru'])){?>
        <div class="relative z-10 mt-4">
            <a href="<?=base_url('proyek/tambah')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-violet-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Data
            </a>
        </div>
        <?php } ?>
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
    <?php if(!empty($proyek)): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
            $color_palettes = [
                ['from' => 'from-violet-500', 'to' => 'to-purple-500', 'bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'hover' => 'hover:border-violet-300 hover:shadow-violet-200/50', 'btn' => 'from-violet-500 to-purple-500'],
                ['from' => 'from-blue-500', 'to' => 'to-cyan-500', 'bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'hover' => 'hover:border-blue-300 hover:shadow-blue-200/50', 'btn' => 'from-blue-500 to-cyan-500'],
                ['from' => 'from-emerald-500', 'to' => 'to-green-500', 'bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'hover' => 'hover:border-emerald-300 hover:shadow-emerald-200/50', 'btn' => 'from-emerald-500 to-green-500'],
                ['from' => 'from-amber-500', 'to' => 'to-orange-500', 'bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'hover' => 'hover:border-amber-300 hover:shadow-amber-200/50', 'btn' => 'from-amber-500 to-orange-500'],
                ['from' => 'from-rose-500', 'to' => 'to-pink-500', 'bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'hover' => 'hover:border-rose-300 hover:shadow-rose-200/50', 'btn' => 'from-rose-500 to-pink-500'],
                ['from' => 'from-cyan-500', 'to' => 'to-teal-500', 'bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'hover' => 'hover:border-cyan-300 hover:shadow-cyan-200/50', 'btn' => 'from-cyan-500 to-teal-500'],
            ];
            $i = 0;
        ?>
        <?php foreach($proyek as $val): ?>
        <?php if(has_role(['superadmin', 'admin', 'guru']) || $val->pengerjaan == 1 ): ?>
        <?php $palette = $color_palettes[$i % count($color_palettes)]; $i++; ?>
        <div
            class="group relative bg-white rounded-2xl border-2 border-gray-100 <?= $palette['hover'] ?> hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
            <!-- Colorful top accent bar -->
            <div class="h-2 w-full bg-gradient-to-r <?= $palette['from'] ?> <?= $palette['to'] ?>"></div>

            <div class="p-6 flex flex-col flex-1">
                <!-- Icon & Mapel -->
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-14 h-14 rounded-2xl <?= $palette['bg'] ?> flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="folder-kanban" class="w-7 h-7 <?= $palette['icon'] ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h6 class="font-bold text-gray-900 text-lg leading-snug truncate"><?= $val->judul ?></h6>
                        <span
                            class="inline-block mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider <?= $palette['bg'] ?> <?= $palette['icon'] ?>">
                            <?= $val->mapel ?>
                        </span>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                    <?= substr(strip_tags($val->deskripsi), 0, 100) ?>...</p>

                <!-- Date Info -->
                <div class="space-y-1.5 mb-4">
                    <p class="text-xs text-gray-600 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Mulai :
                        <?= date('d M Y, H.m', strtotime($val->tgl_mulai)) ?> WIB
                    </p>
                    <?php 
                        $waktu_sekarang = date('Y-m-d H:i');
                        $warna = (strtotime($val->tgl_selesai) < strtotime($waktu_sekarang)) ? 'text-red-600' : 'text-gray-600';
                        ?>
                    <p class="text-xs <?= $warna ?> flex items-center gap-1.5">
                        <i data-lucide="calendar-check" class="w-3.5 h-3.5"></i> Selesai :
                        <?= date('d M Y, H.m', strtotime($val->tgl_selesai)) ?> WIB
                    </p>
                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i> Dibuat oleh : <?= $val->guru ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto flex items-center gap-2">
                    <a href="<?= base_url('proyek/detail/'. $val->uuid) ?>"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-white bg-gradient-to-r <?= $palette['btn'] ?> shadow-md hover:shadow-lg hover:scale-[1.02] transition-all text-sm">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Detail
                    </a>
                    <?php if(has_role(['superadmin', 'admin']) || $this->session->userdata('uuid') == $val->created_by ){?>
                    <a href="<?= base_url('proyek/hapus/'. $val->uuid) ?>"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                        title="Hapus"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus proyek <?= $val->judul; ?>?')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </a>
                    <?php } ?>
                </div>
            </div>

            <!-- Decorative dots pattern -->
            <div class="absolute -bottom-6 -right-6 w-24 h-24 opacity-5 pointer-events-none">
                <div class="grid grid-cols-4 gap-2">
                    <?php for($d=0; $d<16; $d++): ?>
                    <div class="w-2 h-2 rounded-full bg-gray-800"></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-gray-200 text-center py-16">
        <div
            class="w-20 h-20 rounded-full bg-gradient-to-br from-violet-100 to-purple-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="folder-kanban" class="w-10 h-10 text-violet-500"></i>
        </div>
        <p class="text-sm font-medium text-gray-600">Belum ada data proyek</p>
    </div>
    <?php endif; ?>
</div>

<style>
/* Smooth entrance animation for cards */
.card {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
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