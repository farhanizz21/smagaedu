<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Perangkat - ' . $mapel->nama]); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'perangkat']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('perangkat') ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Perangkat</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Mata Pelajaran:
                <span class="text-blue-600 font-semibold"><?= $mapel->nama ?></span>
            </p>
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

    <!-- Upload Form (hanya untuk guru pengampu atau admin) -->
    <?php if($pengampu == true || $is_admin): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Upload Modul -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i data-lucide="book" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Upload Modul</h3>
                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, ZIP (max 10MB)</p>
                </div>
            </div>
            <form action="<?= base_url('perangkat/upload/' . $mapel->uuid . '/modul') ?>" method="POST"
                enctype="multipart/form-data" class="space-y-3">
                <div>
                    <input type="text" name="nama_file" placeholder="Nama Modul" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <input type="file" name="file_perangkat" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all text-sm">
                    <i data-lucide="upload" class="w-4 h-4"></i> Upload Modul
                </button>
            </form>
        </div>

        <!-- Upload ATP -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Upload ATP</h3>
                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS (max 10MB)</p>
                </div>
            </div>
            <form action="<?= base_url('perangkat/upload/' . $mapel->uuid . '/atp') ?>" method="POST"
                enctype="multipart/form-data" class="space-y-3">
                <div>
                    <input type="text" name="nama_file" placeholder="Nama ATP" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div>
                    <input type="file" name="file_perangkat" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-all text-sm">
                    <i data-lucide="upload" class="w-4 h-4"></i> Upload ATP
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Daftar File -->
    <div class="space-y-6">
        <!-- Modul Section -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="book" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="font-semibold text-blue-900">Modul</h3>
                </div>
            </div>
            <?php 
                $modul_files = array_filter($perangkat, function($p) { return $p->jenis_file == 'modul'; });
            ?>
            <?php if(!empty($modul_files)): ?>
            <div class="divide-y divide-gray-100">
                <?php foreach($modul_files as $file): ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= $file->nama_file ?></p>
                            <p class="text-xs text-gray-500"><?= $file->file ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                        <a href="<?= base_url('perangkat/download/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
                        </a>
                        <?php if($is_admin || $file->guru_uuid == $this->session->userdata('uuid')): ?>
                        <a href="<?= base_url('perangkat/hapus/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus file <?= $file->nama_file; ?>?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <i data-lucide="book" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                <p class="text-sm">Belum ada modul</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- ATP Section -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                    <h3 class="font-semibold text-emerald-900">ATP (Alur Tujuan Pembelajaran)</h3>
                </div>
            </div>
            <?php 
                $atp_files = array_filter($perangkat, function($p) { return $p->jenis_file == 'atp'; });
            ?>
            <?php if(!empty($atp_files)): ?>
            <div class="divide-y divide-gray-100">
                <?php foreach($atp_files as $file): ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= $file->nama_file ?></p>
                            <p class="text-xs text-gray-500"><?= $file->file ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                        <a href="<?= base_url('perangkat/download/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
                        </a>
                        <?php if($is_admin || $file->guru_uuid == $this->session->userdata('uuid')): ?>
                        <a href="<?= base_url('perangkat/hapus/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus file <?= $file->nama_file; ?>?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <i data-lucide="file-text" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                <p class="text-sm">Belum ada ATP</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>