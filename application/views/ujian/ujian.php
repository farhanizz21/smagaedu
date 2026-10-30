<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="relative bg-gradient-to-r from-rose-500 to-red-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <i data-lucide="clipboard-list" class="w-7 h-7 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Daftar Ujian</h1>
                    </div>
                </div>
                <p class="text-rose-100 mt-1 text-sm ml-15">Kelola dan ikuti ujian di SMAGAEDU</p>
            </div>
            <div class="flex items-center gap-2">
                <?php if(has_role(['superadmin', 'admin', 'guru']) && (is_admin_or_superadmin() || has_permission('manage_ujian'))): ?>
                <a href="<?= base_url('ujian/tambah') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-rose-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Ujian
                </a>
                <?php endif; ?>
                <?php $this->load->view('partials/menu_guide', ['active_nav' => 'ujian', 'button_style' => 'hero']); ?>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
    </div>

    <?php foreach (array('success_msg' => 'green', 'error_msg' => 'red') as $message_key => $color): ?>
    <?php if ($this->session->userdata($message_key)): ?>
    <div class="mb-5 p-4 rounded-xl bg-<?= $color ?>-50 border border-<?= $color ?>-200 text-<?= $color ?>-700 text-sm">
        <?= html_escape($this->session->userdata($message_key)); ?>
        <?php $this->session->unset_userdata($message_key); ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <?php if ($total_rows > 0): ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-200 bg-gray-50/50">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700"><?= $start_no ?></span>&ndash;<span
                    class="font-semibold text-gray-700"><?= $end_no ?></span>
                dari <span class="font-semibold text-gray-700"><?= $total_rows ?></span> ujian
            </p>
            <form method="get" action="<?= base_url('ujian') ?>" class="flex items-center gap-2">
                <label for="ujianPerPage" class="text-sm text-gray-600">Tampilkan</label>
                <select id="ujianPerPage" name="per_page" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
                    <?php foreach ($per_page_options as $option): ?>
                    <option value="<?= $option ?>" <?= $per_page === $option ? 'selected' : '' ?>><?= $option ?> / halaman</option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th class="text-center px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider w-14">No.</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Nama Ujian</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Periode</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Guru</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider w-[360px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($ujian as $index => $val): ?>
                    <tr class="group transition-colors hover:bg-violet-50/40">
                        <td class="px-4 py-3 text-center text-gray-500"><?= $start_no + $index ?></td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-gray-900"><?= html_escape($val->nama) ?></div>
                            <?php if (!empty($val->bab_uuid)): ?>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-violet-100 text-violet-700 border border-violet-200">
                                    <i data-lucide="layers" class="w-2.5 h-2.5"></i>
                                    Dari Sub Bab
                                </span>
                                <?php if (!empty($val->bab_judul)): ?>
                                <span class="text-[11px] text-gray-500"><?= html_escape($val->bab_judul) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2 text-gray-600">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i data-lucide="book-marked" class="w-3.5 h-3.5"></i></span>
                                <span class="font-medium"><?= html_escape($val->mapel_nama ?? '-') ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-1.5 text-gray-600"><i data-lucide="calendar-days" class="w-3 h-3 text-green-600"></i><span><span class="text-gray-400">Mulai</span> <?= html_escape($val->tgl_mulai_formatted ?? date('d M Y H:i', strtotime($val->tgl_mulai))) ?></span></div>
                                <div class="flex items-center gap-1.5 text-gray-600"><i data-lucide="calendar-clock" class="w-3 h-3 text-rose-500"></i><span><span class="text-gray-400">Selesai</span> <?= html_escape($val->tgl_selesai_formatted ?? date('d M Y H:i', strtotime($val->tgl_selesai))) ?></span></div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2 text-gray-600">
                                <span class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0"><i data-lucide="user-round" class="w-3.5 h-3.5"></i></span>
                                <span><?= html_escape($val->guru_nama ?? '-') ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                <?php if(!has_role(['guru'])){?>
                                <?php if($val->pengerjaan == 1 && $val->pengumpulan == 1) { ?>
                                <?php if (!empty($val->terlambat)): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <i data-lucide="triangle-alert" class="w-3 h-3"></i> Terlambat
                                </span>
                                <?php else: ?>
                                <span class="text-xs text-gray-500 text-center">Sudah dikerjakan</span>
                                <?php endif; ?>
                                <?php } else if($val->pengerjaan == 1 && $val->pengumpulan == NULL) { ?>
                                <?php if (empty($val->jadwal_dimulai)): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-lucide="clock-3" class="w-3 h-3"></i> Belum dimulai
                                </span>
                                <?php else: ?>
                                <?php if (!empty($val->jadwal_terlewat)): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <i data-lucide="triangle-alert" class="w-3 h-3"></i> Terlambat
                                </span>
                                <?php endif; ?>
                                <a class="btn-pengerjaan inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors"
                                    data-uuid="<?= html_escape($val->uuid) ?>">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i> Mulai Ujian
                                </a>
                                <?php endif; ?>
                                <?php } else if($val->pengerjaan == NULL && $val->pengumpulan == NULL ){ ?>
                                <span class="text-xs text-gray-400 text-center">Bukan peserta</span>
                                <?php } ?>
                                <?php } ?>

                                <?php if(has_role(['superadmin', 'admin']) || $this->session->userdata('uuid') == $val->created_by ){?>

                                <a href="<?=base_url('ujian/tambah_kelas/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100 hover:bg-violet-100 whitespace-nowrap"
                                    data-toggle="tooltip" data-placement="top" title="Peserta Ujian">
                                    <i data-lucide="users" class="w-3.5 h-3.5"></i>Peserta
                                </a>
                                <a href="<?=base_url('ujian/tambah_soal/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 whitespace-nowrap"
                                    data-toggle="tooltip" data-placement="top" title="Tambah Soal">
                                    <i data-lucide="list-plus" class="w-3.5 h-3.5"></i>Soal
                                </a>
                                <?php if (!$val->attempted): ?>
                                <a href="<?= base_url('ujian/edit/'.$val->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100 whitespace-nowrap"
                                    data-toggle="tooltip" data-placement="top" title="Edit Ujian">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    Edit
                                </a>
                                <?php else: ?>
                                <a href="#"
                                    class="btn-edit-blocked inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed whitespace-nowrap"
                                    data-nama="<?= html_escape($val->nama) ?>"
                                    data-toggle="tooltip" data-placement="top" title="Edit Ujian (sudah dikerjakan)">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    Edit
                                </a>
                                <?php endif; ?>
                                <a href="<?=base_url('ujian/hapus/'.$val->uuid)?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 whitespace-nowrap"
                                    data-toggle="tooltip" data-placement="top" title="Hapus Ujian"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus ujian <?= html_escape($val->nama); ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Hapus
                                </a>

                                <?php } else if (!has_role(['siswa'])) { ?>
                                <span class="text-xs text-gray-400 text-center">Tidak ada akses</span>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($ujian)): ?>
        <div class="text-center py-12 text-gray-400">Belum ada ujian.</div>
        <?php endif; ?>
        <?php if (!empty($pagination_links)): ?>
        <div class="p-4 border-t border-gray-200"><?= $pagination_links ?></div>
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