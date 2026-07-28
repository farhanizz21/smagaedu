<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Jadwal Mengajar']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'jadwal']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Jadwal Mengajar</h1>
            <p class="text-gray-500 mt-1 text-sm">Upload jadwal mengajar berupa gambar untuk dilihat Kepala Sekolah</p>
        </div>
        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
            <i data-lucide="upload" class="w-4 h-4"></i>
            Upload Jadwal
        </button>
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

    <!-- Upload Modal -->
    <div id="uploadModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Upload Jadwal Mengajar</h3>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="<?= base_url('guru/upload_jadwal') ?>" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">File Gambar Jadwal <span
                            class="text-red-500">*</span></label>
                    <input type="file" name="file_jadwal" accept="image/*" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, GIF, WebP. Maksimal 5MB</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi (opsional)</label>
                    <input type="text" name="deskripsi" placeholder="Contoh: Jadwal Semester Genap 2026"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400">
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all text-sm shadow-lg shadow-blue-200">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Upload
                    </button>
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Jadwal Grid -->
    <?php if (!empty($jadwal)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($jadwal as $j): ?>
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow group">
            <div class="relative">
                <a href="<?= base_url('uploads/jadwal/'.$j->file_gambar) ?>" target="_blank">
                    <img src="<?= base_url('uploads/jadwal/'.$j->file_gambar) ?>" alt="Jadwal"
                        class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </a>
            </div>
            <div class="p-4">
                <?php if ($j->deskripsi): ?>
                <p class="text-sm font-semibold text-gray-900 mb-1"><?= $j->deskripsi ?></p>
                <?php endif; ?>
                <p class="text-xs text-gray-500 flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    <?= date('d M Y H:i', strtotime($j->created_at)) ?>
                </p>
                <div class="mt-3 flex items-center gap-2">
                    <a href="<?= base_url('uploads/jadwal/'.$j->file_gambar) ?>" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        Lihat
                    </a>
                    <a href="<?= base_url('guru/hapus_jadwal/'.$j->uuid) ?>"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                        onclick="return confirm('Hapus jadwal ini?')">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        Hapus
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center table-shadow">
        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="calendar" class="w-10 h-10 text-blue-500"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Jadwal</h3>
        <p class="text-sm text-gray-500 mb-6">Upload jadwal mengajar Anda agar dapat dilihat oleh Kepala Sekolah</p>
        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all text-sm shadow-lg shadow-blue-200">
            <i data-lucide="upload" class="w-4 h-4"></i>
            Upload Jadwal Sekarang
        </button>
    </div>
    <?php endif; ?>
</div>

<script>
// Close modal on click outside
document.getElementById('uploadModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>