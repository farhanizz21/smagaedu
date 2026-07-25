<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <!-- Page Header with Gradient -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-700 p-8 md:p-10 mb-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute bottom-1/4 right-1/3 w-32 h-32 bg-pink-400/10 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="<?= base_url('materi') ?>"
                            class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                        </a>
                        <div
                            class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Daftar Bab</h1>
                    </div>
                    <p class="text-purple-100 text-sm md:text-base ml-[104px]">Mata Pelajaran: <span
                            class="text-white font-semibold"><?= $mapel->nama ?? $materi->judul ?></span></p>
                </div>
                <?php if($can_manage): ?>
                <a href="<?= base_url('bab/tambah/' . $materi->uuid) ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-purple-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Bab
                </a>
                <?php endif; ?>
            </div>
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

    <!-- Bab List -->
    <?php if(!empty($bab)): ?>
    <div class="space-y-4">
        <?php 
            $color_palettes = [
                ['from' => 'from-violet-500', 'to' => 'to-purple-500', 'bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'border' => 'border-violet-200', 'hover' => 'hover:border-violet-300', 'badge' => 'bg-violet-100 text-violet-700', 'shadow' => 'shadow-violet-200/30'],
                ['from' => 'from-blue-500', 'to' => 'to-cyan-500', 'bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'border' => 'border-blue-200', 'hover' => 'hover:border-blue-300', 'badge' => 'bg-blue-100 text-blue-700', 'shadow' => 'shadow-blue-200/30'],
                ['from' => 'from-amber-500', 'to' => 'to-orange-500', 'bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'border' => 'border-amber-200', 'hover' => 'hover:border-amber-300', 'badge' => 'bg-amber-100 text-amber-700', 'shadow' => 'shadow-amber-200/30'],
                ['from' => 'from-rose-500', 'to' => 'to-pink-500', 'bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'border' => 'border-rose-200', 'hover' => 'hover:border-rose-300', 'badge' => 'bg-rose-100 text-rose-700', 'shadow' => 'shadow-rose-200/30'],
                ['from' => 'from-emerald-500', 'to' => 'to-green-500', 'bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'border' => 'border-emerald-200', 'hover' => 'hover:border-emerald-300', 'badge' => 'bg-emerald-100 text-emerald-700', 'shadow' => 'shadow-emerald-200/30'],
                ['from' => 'from-sky-500', 'to' => 'to-indigo-500', 'bg' => 'bg-sky-50', 'icon' => 'text-sky-600', 'border' => 'border-sky-200', 'hover' => 'hover:border-sky-300', 'badge' => 'bg-sky-100 text-sky-700', 'shadow' => 'shadow-sky-200/30'],
            ];
            $no = 0;
        ?>
        <?php foreach($bab as $val): ?>
        <?php 
            $palette = $color_palettes[$no % count($color_palettes)];
            $no++;
            $icons = ['file-text', 'file-spreadsheet', 'notebook', 'notebook-text', 'scroll', 'book-audio'];
            $icon = $icons[$no % count($icons)];
        ?>
        <div
            class="bg-white rounded-2xl border-2 <?= $palette['border'] ?> <?= $palette['hover'] ?> hover:shadow-lg <?= $palette['shadow'] ?> transition-all duration-200 p-5">
            <div class="flex items-center gap-4">
                <!-- Number badge -->
                <div class="w-12 h-12 rounded-xl <?= $palette['bg'] ?> flex items-center justify-center flex-shrink-0">
                    <span
                        class="font-bold text-lg <?= $palette['icon'] ?>"><?= str_pad($no, 2, '0', STR_PAD_LEFT) ?></span>
                </div>

                <!-- Icon -->
                <div class="w-12 h-12 rounded-xl <?= $palette['bg'] ?> flex items-center justify-center flex-shrink-0">
                    <i data-lucide="<?= $icon ?>" class="w-6 h-6 <?= $palette['icon'] ?>"></i>
                </div>

                <!-- Title & Description -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-lg mb-1"><?= $val->judul ?></h3>
                    <?php if (!empty($val->deskripsi)): ?>
                    <p class="text-sm text-gray-600 line-clamp-2"><?= $val->deskripsi ?></p>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <?php if($can_manage): ?>
                    <a href="<?= base_url('bab/edit/' . $val->uuid) ?>"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        Edit
                    </a>
                    <a href="<?= base_url('bab/hapus/' . $val->uuid) ?>"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-all"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus bab ini?')">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Expandable Details -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex flex-wrap gap-2">
                    <?php if (!empty($val->dokumentasi)): ?>
                    <button onclick="openPdfModal('<?= base_url('uploads/dokumentasi/' . $val->dokumentasi) ?>')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 hover:shadow-md transition-all">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat File
                    </button>
                    <?php endif; ?>
                    <?php if (!empty($val->dokumentasi_link)): ?>
                    <a href="<?= $val->dokumentasi_link ?>" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:shadow-md transition-all">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Buka Link
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Sub Bab & Ujian Section -->
                <?php if(isset($sub_materi_per_bab[$val->uuid]) && !empty($sub_materi_per_bab[$val->uuid])): ?>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Sub Bab</h4>
                    <div class="space-y-3">
                        <?php foreach($sub_materi_per_bab[$val->uuid] as $sm): ?>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-semibold text-gray-900 mb-1"><?= $sm->judul ?></h5>
                                    <?php if(!empty($sm->berkas)): ?>
                                    <a href="<?= base_url('uploads/sub_materi/' . $sm->berkas) ?>" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat File
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <?php if($can_manage): ?>
                                    <a href="<?= base_url('ujian/tambah_sub/' . $sm->uuid) ?>"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-purple-600 text-white hover:bg-purple-700 transition-colors shadow-sm">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        Tambah Ujian
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Ujian List for this sub bab -->
                            <?php if(isset($ujian_per_sub[$sm->uuid]) && !empty($ujian_per_sub[$sm->uuid])): ?>
                            <div class="mt-3 pl-4 border-l-2 border-purple-200">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Daftar Ujian:</p>
                                <div class="space-y-2">
                                    <?php foreach($ujian_per_sub[$sm->uuid] as $u): ?>
                                    <div
                                        class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?= $u->nama ?></p>
                                            <p class="text-xs text-gray-500"><?= $u->mapel_nama ?> •
                                                <?= $u->tgl_mulai_formatted ?></p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="<?= base_url('ujian/tambah_soal/' . $u->uuid) ?>"
                                                class="text-xs text-blue-600 hover:text-blue-800">Soal</a>
                                            <a href="<?= base_url('ujian/tambah_siswa/' . $u->uuid) ?>"
                                                class="text-xs text-emerald-600 hover:text-emerald-800">Peserta</a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Comment Section -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 mb-3">
                        <i data-lucide="message-square" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Komentar</span>
                        <span
                            class="text-xs text-gray-400">(<?= isset($komentar_data[$val->uuid]) ? count($komentar_data[$val->uuid]) : 0 ?>)</span>
                    </div>

                    <?php if(isset($komentar_data[$val->uuid]) && !empty($komentar_data[$val->uuid])): ?>
                    <div class="space-y-3 mb-4 max-h-48 overflow-y-auto pr-1">
                        <?php foreach($komentar_data[$val->uuid] as $kom): ?>
                        <div
                            class="flex gap-3 <?= $kom->created_by == $this->session->userdata('uuid') ? 'flex-row-reverse' : '' ?>">
                            <div
                                class="w-8 h-8 rounded-full <?= $kom->role == 'Guru' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?> flex items-center justify-center flex-shrink-0">
                                <i data-lucide="<?= $kom->role == 'Guru' ? 'graduation-cap' : 'user' ?>"
                                    class="w-4 h-4"></i>
                            </div>
                            <div
                                class="flex-1 min-w-0 <?= $kom->created_by == $this->session->userdata('uuid') ? 'text-right' : '' ?>">
                                <div class="inline-block max-w-[85%]">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-semibold text-gray-700"><?= $kom->pengomen ?></span>
                                        <span
                                            class="text-[10px] px-1.5 py-0.5 rounded-full <?= $kom->role == 'Guru' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' ?> font-medium"><?= $kom->role ?></span>
                                        <?php if($kom->created_by == $this->session->userdata('uuid')): ?>
                                        <a href="<?= base_url('bab/komentar_hapus/' . $kom->uuid) ?>"
                                            class="text-gray-300 hover:text-red-500 transition-colors"
                                            onclick="return confirm('Hapus komentar ini?')">
                                            <i data-lucide="x" class="w-3 h-3"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-600 bg-gray-50 rounded-xl px-3 py-2 leading-relaxed">
                                        <?= $kom->komentar ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        <?= date('d M Y H:i', strtotime($kom->modified_at)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-xs text-gray-400 mb-3 italic">Belum ada komentar</p>
                    <?php endif; ?>

                    <form action="<?= base_url('bab/komentar_tambah/' . $val->uuid) ?>" method="POST"
                        class="flex gap-2">
                        <input type="text" name="komentar" required
                            class="flex-1 px-3 py-2 rounded-xl text-xs border border-gray-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 outline-none transition-all placeholder:text-gray-300"
                            placeholder="Tulis komentar..." />
                        <button type="submit"
                            class="px-3 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r <?= $palette['from'] ?> <?= $palette['to'] ?> hover:shadow-md transition-all flex items-center gap-1">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border-2 border-gray-100 text-center py-16">
        <div
            class="w-20 h-20 rounded-3xl bg-gradient-to-br from-violet-100 to-purple-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="book-open" class="w-10 h-10 text-violet-400"></i>
        </div>
        <p class="text-gray-400 font-medium">Belum ada bab</p>
        <p class="text-gray-300 text-sm mt-1">Silakan tambah bab baru untuk mata pelajaran ini</p>
    </div>
    <?php endif; ?>
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

document.getElementById('pdfModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePdfModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePdfModal();
    }
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>