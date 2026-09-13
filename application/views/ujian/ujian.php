<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-rose-500 to-red-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Daftar Ujian</h1>
                </div>
            </div>
            <p class="text-rose-100 mt-1 text-sm ml-15">Kelola dan ikuti ujian di SMARTEDU</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
        <?php if(has_role(['superadmin', 'admin', 'guru'])){?>
        <div class="relative z-10 mt-4">
            <a href="<?= base_url('ujian/tambah')?>"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-rose-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all text-sm">
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

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama Ujian</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Mata Pelajaran</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Jadwal</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Guru</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-40">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
                        foreach($ujian as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-center"><?= $no ; ?></td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?= $val->nama; ?></div>
                            <?php if(!empty($val->bab_uuid)): ?>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 border border-violet-200">
                                    <i data-lucide="layers" class="w-2.5 h-2.5"></i>
                                    Dari Sub Bab
                                </span>
                                <span class="text-[11px] text-gray-500">
                                    <?= !empty($val->bab_judul) ? htmlspecialchars($val->bab_judul) : '' ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->mapel_nama; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->tgl_mulai_formatted; ?> -
                            <?= $val->tgl_selesai_formatted; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->guru_nama; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <?php if(!has_role(['guru'])){?>
                                <?php if($val->pengerjaan == 1 && $val->pengumpulan == 1) { ?>
                                <span class="text-xs text-gray-500 text-center">Sudah dikerjakan</span>
                                <?php } else if($val->pengerjaan == 1 && $val->pengumpulan == NULL) { ?>
                                <a class="btn-pengerjaan inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors"
                                    data-uuid="<?= $val->uuid ?>">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i> Mulai
                                </a>
                                <?php } else if($val->pengerjaan == NULL && $val->pengumpulan == NULL ){ ?>
                                <span class="text-xs text-gray-400 text-center">Bukan peserta</span>
                                <?php } ?>
                                <?php } ?>

                                <?php if(has_role(['superadmin', 'admin']) || $this->session->userdata('uuid') == $val->created_by ){?>

                                <a href="<?=base_url('ujian/tambah_kelas/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition-colors"
                                    data-toggle="tooltip" data-placement="top" title="Peserta Ujian">
                                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="<?=base_url('ujian/tambah_soal/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors"
                                    data-toggle="tooltip" data-placement="top" title="Tambah Soal">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                </a>
                                <?php if (!$val->attempted): ?>
                                <a href="<?= base_url('ujian/edit/'.$val->uuid) ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors"
                                    data-toggle="tooltip" data-placement="top" title="Edit Ujian">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <?php else: ?>
                                <a href="#"
                                    class="btn-edit-blocked inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 cursor-not-allowed"
                                    data-nama="<?= htmlspecialchars($val->nama) ?>"
                                    data-toggle="tooltip" data-placement="top" title="Edit Ujian (sudah dikerjakan)">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <?php endif; ?>
                                <a href="<?=base_url('ujian/hapus/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    data-toggle="tooltip" data-placement="top" title="Hapus Ujian"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus ujian <?= $val->nama; ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </a>

                                <?php } else if (!has_role(['siswa'])) { ?>
                                <span class="text-xs text-gray-400 text-center">Tidak ada akses</span>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($ujian)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada data ujian</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    const base_url = "<?= base_url(); ?>";
    $('.btn-pengerjaan').on("click", function() {
        const uuid = $(this).data('uuid');
        Swal.fire({
            title: "Konfirmasi Memulai Ujian",
            text: "Anda akan memasuki sesi ujian. Harap diperhatikan bahwa berpindah tab selama ujian berlangsung tidak diperkenankan. Jika dilakukan, ujian akan otomatis berakhir.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Mulai Ujian",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = base_url + "ujian/pengerjaan/" + uuid;
            }
        });
    });
    $('.btn-edit-blocked').on("click", function(e) {
        e.preventDefault();
        const nama = $(this).data('nama');
        Swal.fire({
            title: "Tidak dapat Edit",
            text: "Ujian \"" + nama + "\" sudah dikerjakan oleh siswa, sehingga tidak dapat di edit.",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK"
        });
    });
    lucide.createIcons();
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>