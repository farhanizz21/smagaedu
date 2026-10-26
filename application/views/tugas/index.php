<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Tugas']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'tugas']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Daftar Tugas</h1>
                </div>
            </div>
            <p class="text-violet-100 mt-1 text-sm ml-15">Kelola dan kerjakan tugas di SMAGAEDU</p>
            </div>
            <div class="flex items-center gap-2">
                <?php if(has_role(['superadmin', 'admin', 'guru']) && (is_admin_or_superadmin() || has_permission('manage_ujian'))): ?>
                <a href="<?= base_url('tugas/tambah') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-violet-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Tugas
                </a>
                <?php endif; ?>
                <?php if (has_role(['siswa', 'guru', 'admin', 'superadmin'])): ?>
                <button type="button" id="openTaskGuide" aria-haspopup="dialog" aria-controls="taskGuideModal" class="inline-flex items-center gap-2 px-3 py-2.5 rounded-xl font-semibold text-white bg-white/15 border border-white/30 hover:bg-white/25 transition-all text-sm">
                    <i data-lucide="circle-help" class="w-4 h-4"></i><span class="hidden sm:inline">Panduan</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
    </div>

    <?php if (has_role(['siswa', 'guru', 'admin', 'superadmin'])): ?>
    <dialog id="taskGuideModal" aria-labelledby="taskGuideTitle" class="w-[min(92vw,40rem)] max-w-none rounded-2xl border-0 p-0 shadow-2xl backdrop:bg-gray-950/50">
        <div class="overflow-hidden rounded-2xl bg-white">
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-5">
                <div>
                    <h2 id="taskGuideTitle" class="text-lg font-bold text-gray-900"><?= has_role('siswa') ? 'Panduan Siswa' : 'Panduan Guru' ?></h2>
                    <p class="mt-1 text-sm text-gray-500"><?= has_role('siswa') ? 'Cara melihat, mengerjakan, dan mengumpulkan tugas.' : 'Langkah membuat tugas dan memantau pengumpulan siswa.' ?></p>
                </div>
                <button type="button" id="closeTaskGuide" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800" aria-label="Tutup panduan">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <ol class="grid gap-3 p-6 text-sm text-gray-700">
                <?php if (has_role('siswa')): ?>
                <li class="rounded-xl border border-green-100 bg-green-50/50 p-4"><span class="font-semibold text-green-800">1. Pilih tugas</span><p class="mt-1 text-xs leading-relaxed text-gray-600">Periksa judul, mata pelajaran, dan batas waktu. Panel Informasi Tugas menyorot tugas baru atau yang belum dikerjakan.</p></li>
                <li class="rounded-xl border border-green-100 bg-green-50/50 p-4"><span class="font-semibold text-green-800">2. Kerjakan dan simpan</span><p class="mt-1 text-xs leading-relaxed text-gray-600">Pilih Kerjakan Tugas, isi jawaban, lalu simpan. Setelah berhasil, Anda akan kembali ke daftar tugas.</p></li>
                <li class="rounded-xl border border-green-100 bg-green-50/50 p-4"><span class="font-semibold text-green-800">3. Lihat jawaban</span><p class="mt-1 text-xs leading-relaxed text-gray-600">Tugas yang sudah disimpan dapat dibuka melalui Lihat Jawaban. Jawaban yang sudah dikumpulkan tidak dapat diubah.</p></li>
                <?php else: ?>
                <li class="rounded-xl border border-violet-100 bg-violet-50/50 p-4"><span class="font-semibold text-violet-800">1. Buat tugas</span><p class="mt-1 text-xs leading-relaxed text-gray-600">Klik Tambah Tugas, pilih mata pelajaran, isi judul dan periode. Tugas untuk pertemuan juga dapat dibuat dari detail pertemuan.</p></li>
                <li class="rounded-xl border border-violet-100 bg-violet-50/50 p-4"><span class="font-semibold text-violet-800">2. Tambahkan soal</span><p class="mt-1 text-xs leading-relaxed text-gray-600">Buka Soal untuk menambah soal satu per satu atau mengimpor format Excel sebelum siswa mulai mengerjakan.</p></li>
                <li class="rounded-xl border border-violet-100 bg-violet-50/50 p-4"><span class="font-semibold text-violet-800">3. Pantau pengumpulan</span><p class="mt-1 text-xs leading-relaxed text-gray-600">Buka Pengumpulan untuk melihat jawaban siswa. Setelah ada jawaban, tugas tidak dapat diedit, dihapus, atau ditambah soal.</p></li>
                <?php endif; ?>
            </ol>
            <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-6 py-4">
                <button type="button" id="closeTaskGuideFooter" class="rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">Mengerti</button>
            </div>
        </div>
    </dialog>
    <?php endif; ?>

    <?php foreach (array('success_msg' => 'green', 'error_msg' => 'red') as $message_key => $color): ?>
    <?php if ($this->session->userdata($message_key)): ?>
    <div class="mb-5 p-4 rounded-xl bg-<?= $color ?>-50 border border-<?= $color ?>-200 text-<?= $color ?>-700 text-sm">
        <?= html_escape($this->session->userdata($message_key)); ?>
        <?php $this->session->unset_userdata($message_key); ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <?php if (has_role('siswa') && !empty($pending_tasks)): ?>
    <section class="mb-6 rounded-2xl border border-blue-200 bg-blue-50/70 p-5">
        <div class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell-ring" class="w-4.5 h-4.5"></i>
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-blue-900">Informasi Tugas</h2>
                <p class="text-sm text-blue-800 mt-1">Tugas baru, diperbarui, atau yang belum Anda kerjakan.</p>
                <div class="mt-3 space-y-2">
                    <?php foreach ($pending_tasks as $pending_task): ?>
                    <?php $recently_updated = strtotime($pending_task->modified_at) >= strtotime('-7 days'); ?>
                    <a href="<?= base_url('tugas/kerjakan/' . $pending_task->uuid) ?>" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 rounded-xl border border-blue-100 bg-white/80 px-3 py-2.5 hover:bg-white transition-colors">
                        <span class="min-w-0">
                            <span class="block font-semibold text-sm text-gray-800 truncate"><?= html_escape($pending_task->judul) ?></span>
                            <span class="block text-xs text-gray-500 mt-0.5"><?= html_escape($pending_task->mapel_nama ?? '-') ?> · Batas <?= html_escape(date('d M Y H:i', strtotime($pending_task->tgl_selesai))) ?></span>
                        </span>
                        <span class="inline-flex items-center gap-1 self-start sm:self-center whitespace-nowrap text-[11px] font-semibold rounded-full px-2.5 py-1 <?= $recently_updated ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' ?>">
                            <i data-lucide="<?= $recently_updated ? 'sparkles' : 'circle-alert' ?>" class="w-3 h-3"></i>
                            <?= $recently_updated ? 'Baru / diperbarui' : 'Belum dikerjakan' ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <?php if ($total_rows > 0): ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-200 bg-gray-50/50">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700"><?= $start_no ?></span>–<span class="font-semibold text-gray-700"><?= $end_no ?></span>
                dari <span class="font-semibold text-gray-700"><?= $total_rows ?></span> tugas
            </p>
            <form method="get" action="<?= base_url('tugas') ?>" class="flex items-center gap-2">
                <label for="tugasPerPage" class="text-sm text-gray-600">Tampilkan</label>
                <select id="tugasPerPage" name="per_page" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white">
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
                    <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Judul Tugas</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Mata Pelajaran</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Periode</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Guru</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider w-[360px]">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($tugas as $index => $item): ?>
                    <tr class="group transition-colors hover:bg-violet-50/40">
                        <td class="px-4 py-3 text-center text-gray-500">
                            <?= $start_no + $index ?>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-gray-900"><?= html_escape($item->judul) ?></div>
                            <?php if (!empty($item->pertemuan_judul)): ?>
                            <div class="inline-flex items-center gap-1.5 mt-1 text-xs text-violet-700">
                                <i data-lucide="book-open" class="w-3 h-3"></i>
                                <span><?= html_escape($item->pertemuan_judul) ?></span>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2 text-gray-600">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i data-lucide="book-marked" class="w-3.5 h-3.5"></i></span>
                                <span class="font-medium"><?= html_escape($item->mapel_nama ?? '-') ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-1.5 text-gray-600"><i data-lucide="calendar-days" class="w-3 h-3 text-green-600"></i><span><span class="text-gray-400">Mulai</span> <?= html_escape($item->tgl_mulai_formatted ?? date('d M Y H:i', strtotime($item->tgl_mulai))) ?></span></div>
                                <div class="flex items-center gap-1.5 text-gray-600"><i data-lucide="calendar-clock" class="w-3 h-3 text-rose-500"></i><span><span class="text-gray-400">Selesai</span> <?= html_escape($item->tgl_selesai_formatted ?? date('d M Y H:i', strtotime($item->tgl_selesai))) ?></span></div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2 text-gray-600">
                                <span class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0"><i data-lucide="user-round" class="w-3.5 h-3.5"></i></span>
                                <span><?= html_escape($item->guru_nama ?? '-') ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <?php if (has_role('siswa')): ?>
                            <div class="flex justify-center">
                            <a href="<?= base_url('tugas/kerjakan/' . $item->uuid) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold <?= !empty($item->sudah_dikerjakan) ? 'bg-gray-100 text-gray-600' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' ?>">
                                <i data-lucide="<?= !empty($item->sudah_dikerjakan) ? 'file-check-2' : 'square-pen' ?>" class="w-3.5 h-3.5"></i>
                                <?= !empty($item->sudah_dikerjakan) ? 'Lihat Jawaban' : 'Kerjakan Tugas' ?>
                            </a>
                            </div>
                            <?php elseif ($item->can_manage): ?>
                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                <a href="<?= base_url('tugas/tambah_soal/' . $item->uuid) ?>" class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 whitespace-nowrap"><i data-lucide="list-plus" class="w-3.5 h-3.5"></i>Soal</a>
                                <a href="<?= base_url('tugas/pengumpulan/' . $item->uuid) ?>" class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100 hover:bg-violet-100 whitespace-nowrap"><i data-lucide="users" class="w-3.5 h-3.5"></i>Pengumpulan</a>
                                <?php if (!$item->has_student_submissions): ?>
                                <a href="<?= base_url('tugas/edit/' . $item->uuid) ?>" class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100"><i data-lucide="pencil" class="w-3.5 h-3.5"></i>Edit</a>
                                <a href="<?= base_url('tugas/hapus/' . $item->uuid) ?>" onclick="return confirm('Hapus tugas ini?')" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 border border-red-100 hover:bg-red-100" title="Hapus tugas"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                                <?php else: ?>
                                <button type="button" disabled title="Tugas sudah dikerjakan siswa" class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed"><i data-lucide="pencil" class="w-3.5 h-3.5"></i>Edit</button>
                                <button type="button" disabled title="Tugas sudah dikerjakan siswa" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed" aria-label="Hapus tugas tidak tersedia"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                <span class="basis-full text-center text-xs text-amber-700">Tidak dapat diedit atau dihapus karena sudah dikerjakan siswa.</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($tugas)): ?><div class="text-center py-12 text-gray-400">Belum ada tugas.</div><?php endif; ?>
        <?php if (!empty($pagination_links)): ?>
        <div class="p-4 border-t border-gray-200"><?= $pagination_links ?></div>
        <?php endif; ?>
    </div>
</div>

<?php if (has_role(['siswa', 'guru', 'admin', 'superadmin'])): ?>
<script>
    (function () {
        var dialog = document.getElementById('taskGuideModal');
        var openButton = document.getElementById('openTaskGuide');
        var closeButtons = [
            document.getElementById('closeTaskGuide'),
            document.getElementById('closeTaskGuideFooter')
        ];

        if (!dialog || !openButton) {
            return;
        }

        openButton.addEventListener('click', function () {
            dialog.showModal();
        });

        closeButtons.forEach(function (button) {
            if (button) {
                button.addEventListener('click', function () {
                    dialog.close();
                });
            }
        });

        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    })();
</script>
<?php endif; ?>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
