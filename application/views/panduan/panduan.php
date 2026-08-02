<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Panduan']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'panduan']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <i data-lucide="book-open" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Panduan</h1>
                </div>
            </div>
            <p class="text-amber-100 mt-1 text-sm ml-15">Kelola dokumen panduan di SMARTEDU</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
        <?php if(has_role(['superadmin', 'admin', 'guru'])){?>
        <div class="relative z-10 mt-4">
            <a href="<?= base_url('panduan/tambah')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-amber-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all text-sm">
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
    <?php if(!empty($panduan)): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
            $colors = [
                ['from' => 'from-amber-400', 'to' => 'to-orange-500', 'bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'badge_guru' => 'bg-purple-50 text-purple-700 border-purple-100', 'badge_siswa' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                ['from' => 'from-blue-400', 'to' => 'to-indigo-600', 'bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'badge_guru' => 'bg-purple-50 text-purple-700 border-purple-100', 'badge_siswa' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                ['from' => 'from-emerald-400', 'to' => 'to-teal-600', 'bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'badge_guru' => 'bg-purple-50 text-purple-700 border-purple-100', 'badge_siswa' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                ['from' => 'from-violet-400', 'to' => 'to-purple-600', 'bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'badge_guru' => 'bg-purple-50 text-purple-700 border-purple-100', 'badge_siswa' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                ['from' => 'from-rose-400', 'to' => 'to-pink-600', 'bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'badge_guru' => 'bg-purple-50 text-purple-700 border-purple-100', 'badge_siswa' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                ['from' => 'from-cyan-400', 'to' => 'to-blue-600', 'bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'badge_guru' => 'bg-purple-50 text-purple-700 border-purple-100', 'badge_siswa' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
            ];
            $ctr = 0;
        ?>
        <?php foreach($panduan as $val): ?>
        <?php $color = $colors[$ctr % count($colors)]; $ctr++; ?>
        <div
            class="group relative bg-white rounded-2xl border-2 border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
            <!-- Colorful top accent bar -->
            <div class="h-2 w-full bg-gradient-to-r <?= $color['from'] ?> <?= $color['to'] ?>"></div>

            <div class="p-6 flex flex-col flex-1">
                <!-- Icon with gradient background -->
                <div class="flex items-start gap-4 mb-4">
                    <div
                        class="w-14 h-14 rounded-2xl <?= $color['bg'] ?> flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="book-open" class="w-7 h-7 <?= $color['icon'] ?>"></i>
                    </div>
                </div>

                <h5 class="font-bold text-gray-900 text-lg leading-snug mb-3"><?= $val->judul; ?></h5>

                <!-- Tujuan badges -->
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <?php 
                    $tujuan_arr = json_decode($val->tujuan);
                    if (is_array($tujuan_arr) && !empty($tujuan_arr)):
                        foreach ($tujuan_arr as $t):
                            if ($t == 1):
                    ?>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color['badge_guru'] ?> border">
                        <i data-lucide="user" class="w-3 h-3 mr-1"></i> Guru
                    </span>
                    <?php elseif ($t == 2): ?>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color['badge_siswa'] ?> border">
                        <i data-lucide="graduation-cap" class="w-3 h-3 mr-1"></i> Siswa
                    </span>
                    <?php 
                            endif;
                        endforeach;
                    else:
                    ?>
                    <span class="text-gray-400 text-xs">-</span>
                    <?php endif; ?>
                </div>

                <div class="mt-auto flex items-center gap-2">
                    <a href="<?= base_url('uploads/panduan/' . $val->berkas) ?>" target="_blank"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-white bg-gradient-to-r <?= $color['from'] ?> <?= $color['to'] ?> shadow-md hover:shadow-lg hover:scale-[1.02] transition-all text-sm">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Berkas
                    </a>
                    <?php if(has_role(['superadmin', 'admin', 'guru'])){?>
                    <a href="<?=base_url('panduan/edit/'.$val->uuid)?>"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors"
                        title="Edit">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </a>
                    <a href="<?=base_url('panduan/hapus/'.$val->uuid)?>"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                        title="Hapus"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus panduan <?= $val->judul; ?>?')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </a>
                    <?php } ?>
                </div>
            </div>

            <!-- Decorative dots pattern -->
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
    <div class="bg-white rounded-2xl border border-gray-200 text-center py-16">
        <div
            class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="book-open" class="w-10 h-10 text-amber-500"></i>
        </div>
        <p class="text-sm font-medium text-gray-600">Belum ada panduan untuk role Anda</p>
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