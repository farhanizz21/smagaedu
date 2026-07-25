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
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Peserta Ujian</h3>
        <form method="post" action="<?= base_url('ujian/tambah_siswa/'.$ujian->uuid)?>">
            <input type="hidden" name="ujian_uuid" value="<?= $ujian->uuid ?>">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <select name="siswa"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white"
                        required>
                        <option disabled selected>Pilih Siswa</option>
                        <?php foreach($siswa as $s): ?>
                        <?php 
                            // Check if student already in ujian
                            $already_in = false;
                            foreach($peserta as $p) {
                                if ($p->siswa_uuid == $s->uuid) {
                                    $already_in = true;
                                    break;
                                }
                            }
                            if (!$already_in):
                        ?>
                        <option value="<?= $s->uuid ?>"><?= $s->nama ?> (<?= $s->username ?>)</option>
                        <?php 
                            endif;
                            endforeach; 
                        ?>
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Peserta
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama Siswa</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Username</th>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-40">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
                        foreach($peserta as $val) {
                    ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-center"><?= $no ; ?></td>
                        <td class="px-6 py-4 font-medium text-gray-900"><?= $val->nama; ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= $val->username; ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('ujian/tambah_nilai/' . $ujian->uuid . '/' . $val->siswa_uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Nilai
                                </a>
                                <a href="<?= base_url('ujian/hapus_siswa/' . $val->uuid . '?ujian_uuid=' . $ujian->uuid)?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Hapus siswa <?= $val->nama; ?>?')">
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
lucide.createIcons();
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>