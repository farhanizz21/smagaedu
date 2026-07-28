<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Peserta Ujian']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'ujian']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('ujian')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Peserta Ujian</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Ujian: <span
                    class="text-blue-600 font-semibold"><?= $ujian->nama; ?></span></p>
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

    <!-- Form Tambah Peserta -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Kelas</h3>
        <form method="post" action="<?= base_url('ujian/tambah_kelas/'.$ujian->uuid)?>">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <select name="kelas"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white"
                        required>
                        <option disabled selected>Pilih Kelas</option>
                        <?php foreach($kelas as $k): ?>
                        <option value="<?= $k->uuid ?>" <?= set_select('kelas', $k->uuid) ?>><?= $k->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Kelas
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <!-- Filter Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="filter-kelas" class="text-sm font-medium text-gray-600">Filter Kelas:</label>
                    <select id="filter-kelas" onchange="filterTable()"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelas_filter as $kf): ?>
                        <option value="<?= htmlspecialchars($kf) ?>"><?= htmlspecialchars($kf) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="filter-status" class="text-sm font-medium text-gray-600">Status:</label>
                    <select id="filter-status" onchange="filterTable()"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option value="">Semua Status</option>
                        <option value="belum_mengerjakan">Belum Mengerjakan</option>
                        <option value="sudah_mengerjakan">Sudah Mengerjakan</option>
                        <option value="sudah_dinilai">Sudah Dinilai</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="peserta-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama Siswa</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider
                            kelas-col">
                            Kelas</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-44">
                            Waktu Pengumpulan</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-20">
                            Nilai</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-40">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
                        foreach($peserta as $val) {
                            $waktu_pengumpulan = !empty($val->pengumpulan) ? date('d M Y H:i', strtotime($val->pengumpulan)) : '-';
                            $nilai = !empty($val->nilai_ujian) ? $val->nilai_ujian : '-';
                            $kelas_nama = !empty($val->kelas_nama) ? htmlspecialchars($val->kelas_nama) : '-';
                    ?>
                    <tr class="table-row-hover transition-colors" data-kelas="<?= $kelas_nama ?>"
                        data-pengumpulan="<?= !empty($val->pengumpulan) ? 'sudah_mengerjakan' : 'belum_mengerjakan' ?>"
                        data-nilai="<?= !empty($val->nilai_ujian) ? 'sudah_dinilai' : 'belum_dinilai' ?>">
                        <td class="px-6 py-4 text-gray-500 text-center"><?= $no ; ?></td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($val->nama); ?></td>
                        <td class="px-6 py-4 text-gray-600 kelas-col"><?= $kelas_nama; ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php if(!empty($val->pengumpulan)): ?>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                <?= $waktu_pengumpulan ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold">
                            <?php if(!empty($val->nilai_ujian)): ?>
                            <span
                                class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold 
                                <?= ($val->nilai_ujian >= 70) ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' ?>">
                                <?= $val->nilai_ujian ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('ujian/tambah_nilai/' . $ujian->uuid . '/' . $val->siswa_uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Nilai
                                </a>
                                <a href="<?= base_url('ujian/hapus_siswa/' . $val->uuid . '?ujian_uuid=' . $ujian->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Hapus siswa <?= htmlspecialchars($val->nama) ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </a>
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
        <?php if(empty($peserta)): ?>
        <div class="text-center py-12 text-gray-400">
            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada peserta ujian</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterTable() {
    const filterKelas = document.getElementById('filter-kelas').value;
    const filterStatus = document.getElementById('filter-status').value;
    const rows = document.querySelectorAll('#peserta-table tbody tr');

    rows.forEach(row => {
        const kelas = row.getAttribute('data-kelas');
        const statusPengumpulan = row.getAttribute('data-pengumpulan');
        const statusNilai = row.getAttribute('data-nilai');

        let showKelas = true;
        let showStatus = true;

        // Filter Kelas
        if (filterKelas && kelas !== filterKelas) {
            showKelas = false;
        }

        // Filter Status
        if (filterStatus) {
            if (filterStatus === 'belum_mengerjakan' && statusPengumpulan !== 'belum_mengerjakan') {
                showStatus = false;
            } else if (filterStatus === 'sudah_mengerjakan' && statusPengumpulan !== 'sudah_mengerjakan') {
                showStatus = false;
            } else if (filterStatus === 'sudah_dinilai' && statusNilai !== 'sudah_dinilai') {
                showStatus = false;
            }
        }

        row.style.display = (showKelas && showStatus) ? '' : 'none';
    });
}

lucide.createIcons();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>