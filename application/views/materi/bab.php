<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('materi/detail/' . $materi->mapel_uuid) ?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Bab</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Materi: <span
                    class="text-blue-600 font-semibold"><?= $materi->judul ?></span></p>
        </div>
        <?php if($can_manage): ?>
        <a href="<?= base_url('bab/tambah/' . $materi->uuid) ?>"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Bab
        </a>
        <?php endif; ?>
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

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden table-shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Judul Bab</th>
                        <?php if($can_manage): ?>
                        <th
                            class="text-center px-6 py-4 font-semibold text-gray-600 text-xs uppercase tracking-wider w-32">
                            Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $no = 1; foreach($bab as $val): ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4 text-gray-500 text-center"><?= $no++; ?></td>
                        <td class="px-6 py-4">
                            <a href="<?= base_url('sub_materi/index/' . $val->uuid) ?>"
                                class="font-medium text-gray-900 hover:text-blue-600 transition-colors block mb-1">
                                <?= $val->judul ?>
                            </a>
                            <?php if (!empty($val->deskripsi)): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                <?= $val->deskripsi ?>
                            </div>
                            <?php endif; ?>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <?php if (!empty($val->dokumentasi)): ?>
                                <button
                                    onclick="openPdfModal('<?= base_url('uploads/dokumentasi/' . $val->dokumentasi) ?>')"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">
                                    <i data-lucide="file-text" class="w-3 h-3"></i> Lihat File
                                </button>
                                <?php endif; ?>
                                <?php if (!empty($val->dokumentasi_link)): ?>
                                <a href="<?= $val->dokumentasi_link ?>" target="_blank"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-50 text-green-700 border border-green-200 hover:bg-green-100">
                                    <i data-lucide="link" class="w-3 h-3"></i> Link
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php if($can_manage): ?>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('bab/edit/' . $val->uuid) ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </a>
                                <a href="<?= base_url('bab/hapus/' . $val->uuid) ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus bab <?= $val->judul; ?>?')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($bab)): ?>
                    <tr>
                        <td colspan="<?= $can_manage ? 3 : 2 ?>" class="text-center py-12 text-gray-400">
                            <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                            <p class="text-sm">Belum ada bab</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PDF Modal -->
<div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-screen overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dokumen PDF</h3>
            <button onclick="closePdfModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto p-4 bg-gray-100">
            <iframe id="pdfFrame" src="" class="w-full h-[600px] border-0 rounded-lg"></iframe>
        </div>
    </div>
</div>

<script>
function openPdfModal(url) {
    document.getElementById('pdfModal').classList.remove('hidden');
    document.getElementById('pdfFrame').src = url;
    document.body.style.overflow = 'hidden';
}

function closePdfModal() {
    document.getElementById('pdfModal').classList.add('hidden');
    document.getElementById('pdfFrame').src = '';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('pdfModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePdfModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePdfModal();
    }
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>