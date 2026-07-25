<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Perangkat - ' . $mapel->nama]); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'perangkat']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <a href="<?= base_url('perangkat') ?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Perangkat Mata Pelajaran
                    </h1>
                    <p class="text-emerald-100 text-sm mt-1">Mata Pelajaran:
                        <span class="font-semibold"><?= $mapel->nama ?></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
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
        <div
            class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md">
                        <i data-lucide="book" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Upload Modul</h3>
                        <p class="text-xs text-gray-500">PDF, DOC, DOCX, ZIP (max 10MB)</p>
                    </div>
                </div>
                <form action="<?= base_url('perangkat/upload/' . $mapel->uuid . '/modul') ?>" method="POST"
                    enctype="multipart/form-data" class="space-y-3">
                    <div>
                        <input type="text" name="nama_file" placeholder="Nama Modul" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all">
                    </div>
                    <div>
                        <input type="file" name="file_perangkat" required
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-lg shadow-blue-200 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="upload" class="w-4 h-4"></i> Upload Modul
                    </button>
                </form>
            </div>
        </div>

        <!-- Upload ATP -->
        <div
            class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="h-1 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-md">
                        <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Upload ATP</h3>
                        <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS (max 10MB)</p>
                    </div>
                </div>
                <form action="<?= base_url('perangkat/upload/' . $mapel->uuid . '/atp') ?>" method="POST"
                    enctype="multipart/form-data" class="space-y-3">
                    <div>
                        <input type="text" name="nama_file" placeholder="Nama ATP" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all">
                    </div>
                    <div>
                        <input type="file" name="file_perangkat" required
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 shadow-lg shadow-emerald-200 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="upload" class="w-4 h-4"></i> Upload ATP
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Daftar File -->
    <div class="space-y-6">
        <!-- Modul Section -->
        <div
            class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b border-blue-200">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <i data-lucide="book" class="w-4 h-4 text-white"></i>
                    </div>
                    <h3 class="font-bold text-white">Modul</h3>
                </div>
            </div>
            <?php 
                $modul_files = array_filter($perangkat, function($p) { return $p->jenis_file == 'modul'; });
            ?>
            <?php if(!empty($modul_files)): ?>
            <div class="divide-y divide-gray-100">
                <?php foreach($modul_files as $file): ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-blue-50/50 transition-colors group">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <p
                                class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-700 transition-colors">
                                <?= $file->nama_file ?></p>
                            <p class="text-xs text-gray-500"><?= $file->file ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                        <a href="<?= base_url('perangkat/download/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md hover:shadow-lg transition-all">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
                        </a>
                        <?php if($is_admin || $file->guru_uuid == $this->session->userdata('uuid')): ?>
                        <a href="<?= base_url('perangkat/hapus/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-red-600 border-2 border-red-600 hover:bg-red-600 hover:text-white transition-all"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus file <?= $file->nama_file; ?>?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12 text-gray-400">
                <div
                    class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="book" class="w-8 h-8 text-blue-600"></i>
                </div>
                <p class="text-sm font-semibold text-gray-600">Belum ada modul</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- ATP Section -->
        <div
            class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 border-b border-emerald-200">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <i data-lucide="file-text" class="w-4 h-4 text-white"></i>
                    </div>
                    <h3 class="font-bold text-white">ATP (Alur Tujuan Pembelajaran)</h3>
                </div>
            </div>
            <?php 
                $atp_files = array_filter($perangkat, function($p) { return $p->jenis_file == 'atp'; });
            ?>
            <?php if(!empty($atp_files)): ?>
            <div class="divide-y divide-gray-100">
                <?php foreach($atp_files as $file): ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-emerald-50/50 transition-colors group">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <p
                                class="text-sm font-bold text-gray-900 truncate group-hover:text-emerald-700 transition-colors">
                                <?= $file->nama_file ?></p>
                            <p class="text-xs text-gray-500"><?= $file->file ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                        <a href="<?= base_url('perangkat/download/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md hover:shadow-lg transition-all">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
                        </a>
                        <?php if($is_admin || $file->guru_uuid == $this->session->userdata('uuid')): ?>
                        <a href="<?= base_url('perangkat/hapus/' . $file->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-red-600 border-2 border-red-600 hover:bg-red-600 hover:text-white transition-all"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus file <?= $file->nama_file; ?>?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12 text-gray-400">
                <div
                    class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-100 to-teal-200 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="file-text" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <p class="text-sm font-semibold text-gray-600">Belum ada ATP</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>