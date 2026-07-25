<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Proyek']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'proyek']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Daftar Proyek</h1>
            <p class="text-gray-500 mt-1 text-sm">Kelola proyek pembelajaran di SMARTEDU</p>
        </div>
        <?php if($this->session->userdata('role') == 2 ){?>
        <a href="<?=base_url('proyek/tambah')?>"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Data
        </a>
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

    <!-- Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php if(!empty($proyek)) : ?>
            <?php foreach($proyek as $val) : ?>
            <?php if($this->session->userdata('role') == 1 || $this->session->userdata('role') == 2 || $val->pengerjaan == 1 ){?>
            <div
                class="bg-white rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all flex flex-col">
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-semibold text-gray-500">Mata Pelajaran:</span>
                        <span class="text-sm font-bold text-blue-600"><?= $val->mapel ?></span>
                    </div>
                    <h5 class="font-semibold text-gray-900 text-base leading-snug mb-2"><?= $val->judul ?></h5>
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                        <?= substr(strip_tags($val->deskripsi), 0, 100) ?>...</p>

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

                    <div class="mt-auto flex items-center gap-2">
                        <a href="<?= base_url('proyek/detail/'. $val->uuid) ?>"
                            class="inline-flex items-center justify-center gap-2 flex-1 px-4 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Detail
                        </a>
                        <?php if($this->session->userdata('role') == 1 || $this->session->userdata('uuid') == $val->created_by ){?>
                        <a href="<?= base_url('proyek/hapus/'. $val->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus proyek <?= $val->judul; ?>?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-span-full text-center py-12 text-gray-400">
                <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p class="text-sm">Belum ada data proyek</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>