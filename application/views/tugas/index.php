<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Tugas']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'tugas']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <div
        class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
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
                <a href="<?= base_url('tugas/tambah') ?>"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-violet-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Tugas
                </a>
                <?php endif; ?>
                <?php $this->load->view('partials/menu_guide', ['active_nav' => 'tugas', 'button_style' => 'hero']); ?>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
    </div>

    <?php foreach (array('success_msg' => 'green', 'error_msg' => 'red') as $message_key => $color): ?>
    <?php if (!has_role('siswa')): ?>
    <?php if ($this->session->userdata($message_key)): ?>
    <div class="mb-5 p-4 rounded-xl bg-<?= $color ?>-50 border border-<?= $color ?>-200 text-<?= $color ?>-700 text-sm">
        <?= html_escape($this->session->userdata($message_key)); ?>
        <?php $this->session->unset_userdata($message_key); ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <?php endforeach; ?>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <form method="get" action="<?= base_url('tugas') ?>"
            class="flex flex-col lg:flex-row lg:items-end gap-3 p-4 border-b border-gray-200 bg-gray-50/50">
            <div class="flex-1">
                <label for="tugasSearch" class="block text-xs font-semibold text-gray-600 mb-1.5">Cari tugas</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="tugasSearch" type="search" name="q" value="<?= html_escape($filters['q']) ?>"
                        placeholder="Cari judul tugas atau mata pelajaran..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 text-sm bg-white">
                </div>
            </div>
            <div class="w-full lg:w-64">
                <label for="tugasMapel" class="block text-xs font-semibold text-gray-600 mb-1.5">Mata Pelajaran</label>
                <select id="tugasMapel" name="mapel_uuid"
                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white">
                    <option value="">Semua mata pelajaran</option>
                    <?php foreach ($mapel as $subject): ?>
                    <option value="<?= html_escape($subject->uuid) ?>"
                        <?= $filters['mapel_uuid'] === $subject->uuid ? 'selected' : '' ?>>
                        <?= html_escape($subject->nama) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full lg:w-auto">
                <label for="tugasPerPage" class="block text-xs font-semibold text-gray-600 mb-1.5">Tampilkan</label>
                <select id="tugasPerPage" name="per_page"
                    class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm bg-white">
                    <?php foreach ($per_page_options as $option): ?>
                    <option value="<?= $option ?>" <?= $per_page === $option ? 'selected' : '' ?>><?= $option ?> /
                        halaman</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700">
                    <i data-lucide="search" class="w-4 h-4"></i> Terapkan
                </button>
                <?php if ($filters['q'] !== '' || $filters['mapel_uuid'] !== ''): ?>
                <a href="<?= base_url('tugas') ?>"
                    class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50">
                    Reset
                </a>
                <?php endif; ?>
            </div>
        </form>
        <?php if ($total_rows > 0): ?>
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-gray-200 bg-gray-50/50">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700"><?= $start_no ?></span>–<span
                    class="font-semibold text-gray-700"><?= $end_no ?></span>
                dari <span class="font-semibold text-gray-700"><?= $total_rows ?></span> tugas
            </p>
        </div>
        <?php endif; ?>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200">
                        <th
                            class="text-center px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider w-14">
                            No.</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">
                            Judul Tugas</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">
                            Mata Pelajaran</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">
                            Periode</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">
                            Guru</th>
                        <th
                            class="text-center px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider w-[360px]">
                            Aksi</th>
                    </tr>
                </thead>
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
                                <span
                                    class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i
                                        data-lucide="book-marked" class="w-3.5 h-3.5"></i></span>
                                <span class="font-medium"><?= html_escape($item->mapel_nama ?? '-') ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-1.5 text-gray-600"><i data-lucide="calendar-days"
                                        class="w-3 h-3 text-green-600"></i><span><span
                                            class="text-gray-400">Mulai</span>
                                        <?= html_escape($item->tgl_mulai_formatted ?? date('d M Y H:i', strtotime($item->tgl_mulai))) ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600"><i data-lucide="calendar-clock"
                                        class="w-3 h-3 text-rose-500"></i><span><span
                                            class="text-gray-400">Selesai</span>
                                        <?= html_escape($item->tgl_selesai_formatted ?? date('d M Y H:i', strtotime($item->tgl_selesai))) ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2 text-gray-600">
                                <span
                                    class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0"><i
                                        data-lucide="user-round" class="w-3.5 h-3.5"></i></span>
                                <span><?= html_escape($item->guru_nama ?? '-') ?></span>
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <?php if (has_role('siswa')): ?>
                            <?php
                            $sudah_dikerjakan = !empty($item->sudah_dikerjakan);
                            $jadwal_dimulai = strtotime($item->tgl_mulai) <= time();
                            $terlambat = strtotime($item->tgl_selesai) < time();
                            $status_terlambat = $sudah_dikerjakan ? !empty($item->terlambat) : $terlambat;
                            ?>
                            <div class="flex flex-wrap justify-center gap-1.5">
                                <?php if ($status_terlambat): ?>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <i data-lucide="triangle-alert" class="w-3 h-3"></i> Terlambat
                                </span>
                                <?php endif; ?>
                                <?php if (!$sudah_dikerjakan && !$jadwal_dimulai): ?>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-lucide="clock-3" class="w-3.5 h-3.5"></i> Belum bisa mengerjakan
                                </span>
                                <?php else: ?>
                                <a href="<?= base_url('tugas/kerjakan/' . $item->uuid) ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold <?= $sudah_dikerjakan ? 'bg-gray-100 text-gray-600' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' ?>">
                                    <i data-lucide="<?= $sudah_dikerjakan ? 'file-check-2' : ($terlambat ? 'triangle-alert' : 'square-pen') ?>"
                                        class="w-3.5 h-3.5"></i>
                                    <?= $sudah_dikerjakan ? 'Lihat Jawaban' : ($terlambat ? 'Kerjakan Tugas' : 'Kerjakan Tugas') ?>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php elseif ($item->can_manage): ?>
                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                <a href="<?= base_url('tugas/tambah_soal/' . $item->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 whitespace-nowrap"><i
                                        data-lucide="list-plus" class="w-3.5 h-3.5"></i>Soal</a>
                                <a href="<?= base_url('tugas/pengumpulan/' . $item->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100 hover:bg-violet-100 whitespace-nowrap"><i
                                        data-lucide="users" class="w-3.5 h-3.5"></i>Pengumpulan</a>
                                <?php if (!$item->has_student_submissions): ?>
                                <a href="<?= base_url('tugas/edit/' . $item->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 hover:bg-amber-100"><i
                                        data-lucide="pencil" class="w-3.5 h-3.5"></i>Edit</a>
                                <a href="<?= base_url('tugas/hapus/' . $item->uuid) ?>"
                                    onclick="return confirm('Hapus tugas ini?')"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 border border-red-100 hover:bg-red-100"
                                    title="Hapus tugas"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                                <?php else: ?>
                                <button type="button" disabled title="Tugas sudah dikerjakan siswa"
                                    class="inline-flex items-center gap-1 px-2.5 py-2 rounded-lg text-xs font-semibold bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed"><i
                                        data-lucide="pencil" class="w-3.5 h-3.5"></i>Edit</button>
                                <button type="button" disabled title="Tugas sudah dikerjakan siswa"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed"
                                    aria-label="Hapus tugas tidak tersedia"><i data-lucide="trash-2"
                                        class="w-4 h-4"></i></button>
                                <span class="basis-full text-center text-xs text-amber-700">Tidak dapat diedit atau
                                    dihapus karena sudah dikerjakan siswa.</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($tugas)): ?>
        <div class="text-center py-12 text-gray-400">
            <?= $filters['q'] !== '' || $filters['mapel_uuid'] !== '' ? 'Tidak ada tugas yang sesuai dengan filter.' : 'Belum ada tugas.' ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($pagination_links)): ?>
        <div class="p-4 border-t border-gray-200"><?= $pagination_links ?></div>
        <?php endif; ?>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>