<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<?php
/**
 * Helper tampilan card sub bab.
 * Kolom `deskripsi` disimpan sebagai HTML (hasil editor Quill, bisa berisi gambar),
 * sehingga perlu diubah menjadi teks biasa agar preview di card tetap ringkas.
 */
if (!function_exists('sub_bab_plain_text')) {
    function sub_bab_plain_text($html)
    {
        if (empty($html)) {
            return '';
        }

        // Buang tag yang terpotong di akhir teks (mis. gambar base64 yang tidak
        // tersimpan penuh karena melebihi kapasitas kolom `deskripsi`).
        $text = preg_replace('/<[a-zA-Z\/!][^>]*$/s', ' ', $html);
        $text = preg_replace('/<br\s*\/?>/i', ' ', $text);
        $text = preg_replace('/<\/(p|div|li|h[1-6]|blockquote)>/i', ' ', $text);
        $text = preg_replace('/<[^>]+>/', ' ', $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}

if (!function_exists('sub_bab_trim_text')) {
    function sub_bab_trim_text($text, $limit = 220)
    {
        if ($text === null || $text === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($text, 'UTF-8') <= $limit) {
                return $text;
            }

            return rtrim(mb_substr($text, 0, $limit, 'UTF-8')) . '…';
        }

        if (strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(substr($text, 0, $limit)) . '...';
    }
}

if (!function_exists('sub_bab_images')) {
    /**
     * Ambil daftar src gambar yang benar-benar bisa ditampilkan (bukan data URI/base64).
     * Gambar base64 biasanya sudah rusak karena terpotong batas kolom `deskripsi`.
     */
    function sub_bab_images($html)
    {
        $gambar = array();

        if (empty($html) || !preg_match_all('/<img[^>]+src\s*=\s*["\']([^"\']+)["\']/i', $html, $match)) {
            return $gambar;
        }

        foreach ($match[1] as $src) {
            if (stripos($src, 'data:') === 0) {
                continue;
            }

            $gambar[] = $src;
        }

        return $gambar;
    }
}

if (!function_exists('sub_bab_prepare_html')) {
    /**
     * Siapkan HTML deskripsi untuk ditampilkan di card:
     * gambar base64 (data URI) yang rusak diganti dengan penanda agar
     * halaman tidak berat dan tampilan tetap rapi.
     */
    function sub_bab_prepare_html($html)
    {
        if (empty($html)) {
            return '';
        }

        $penanda = '<span class="deskripsi-gambar-rusak">Gambar tidak dapat ditampilkan karena datanya tidak tersimpan penuh. Silakan unggah ulang gambar melalui menu Edit.</span>';

        // Gambar base64 (data URI) yang strukturnya masih utuh
        $html = preg_replace('/<img[^>]+src\s*=\s*["\']\s*data:[^"\']*["\'][^>]*>/i', $penanda, $html);

        // Gambar base64 yang terpotong kapasitas kolom (tag-nya tidak tertutup)
        $html = preg_replace('/<img[^>]*src\s*=\s*["\']?\s*data:[^>]*$/is', $penanda, $html);

        // Sisa tag yang terpotong (mis. heading/paragraf yang tidak selesai)
        $html = preg_replace('/<[a-zA-Z\/!][^>]*$/s', '', $html);

        return trim($html);
    }
}
?>

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
                        <a href="<?= base_url('mata_pelajaran') ?>"
                            class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition-colors">
                            <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                        </a>
                        <div
                            class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Daftar Sub Bab</h1>
                    </div>
                    <div class="ml-[104px] mt-2 space-y-1">
                        <p class="text-purple-100 text-sm md:text-base">
                            <span class="font-medium">Mata Pelajaran:</span>
                            <span class="text-white font-semibold"><?= $mapel->nama ?? $materi->judul ?></span>
                        </p>
                        <p class="text-purple-100 text-sm md:text-base">
                            <span class="font-medium">Bab:</span>
                            <span class="text-white font-semibold"><?= $materi->judul ?></span>
                        </p>
                    </div>
                </div>
                <?php if($can_manage): ?>
                <a href="<?= base_url('sub_bab/tambah/' . $materi->uuid) ?>"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-purple-900 bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all text-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Sub Bab
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
            // Catatan: hanya gunakan nama ikon yang tersedia di lucide 0.263 (dipakai di header_tailwind.php),
            // agar badge ikon tidak kosong karena ikon gagal dirender.
            $icons = ['file-text', 'file-spreadsheet', 'book-open', 'library', 'scroll', 'clipboard-list'];
            $icon = $icons[$no % count($icons)];
            $is_locked = isset($bab_unlocked[$val->uuid]) && !$bab_unlocked[$val->uuid];
            $has_ujian = isset($bab_has_ujian[$val->uuid]) && $bab_has_ujian[$val->uuid];
        ?>
        <div
            class="bg-white rounded-2xl border-2 <?= $palette['border'] ?> <?= $palette['hover'] ?> hover:shadow-lg <?= $palette['shadow'] ?> transition-all duration-200 p-5 relative overflow-hidden <?= $is_locked ? 'opacity-60' : '' ?>">
            <?php if($is_locked): ?>
            <div
                class="absolute inset-0 bg-gray-900/30 backdrop-blur-[1px] rounded-2xl flex items-center justify-center z-10">
                <div class="text-center text-white">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="lock" class="w-7 h-7 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-semibold mb-1">Sub Bab Terkunci</p>
                    <p class="text-xs opacity-80">
                        <?php if($has_ujian): ?>
                        Selesaikan ujian sub bab sebelumnya untuk membuka
                        <?php else: ?>
                        Selesaikan sub bab sebelumnya untuk membuka
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:gap-4">
                <!-- Number & Icon badge -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-11 h-11 rounded-xl <?= $palette['bg'] ?> flex items-center justify-center">
                        <span
                            class="font-bold text-base <?= $palette['icon'] ?>"><?= str_pad($no, 2, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="w-11 h-11 rounded-xl <?= $palette['bg'] ?> flex items-center justify-center">
                        <i data-lucide="<?= $icon ?>" class="w-5 h-5 <?= $palette['icon'] ?>"></i>
                    </div>
                </div>

                <!-- Title & Description -->
                <div class="flex-1 min-w-0">
                    <?php
                        // Deskripsi berupa HTML (editor Quill) + gambar, jadi ditampilkan
                        // sebagai ringkasan teks 2 baris + thumbnail agar card tetap rapi.
                        $deskripsi_html = isset($val->deskripsi) ? (string) $val->deskripsi : '';
                        $deskripsi_plain = sub_bab_plain_text($deskripsi_html);
                        $deskripsi_preview = sub_bab_trim_text($deskripsi_plain, 220);
                        $deskripsi_gambar_list = sub_bab_images($deskripsi_html);
                        $deskripsi_img = !empty($deskripsi_gambar_list) ? $deskripsi_gambar_list[0] : '';
                        $deskripsi_img_total = (int) preg_match_all('/<img/i', $deskripsi_html);
                        $deskripsi_isi = sub_bab_prepare_html($deskripsi_html);
                        $deskripsi_is_long = ($deskripsi_preview !== $deskripsi_plain) || ($deskripsi_img_total > 0);
                        $deskripsi_ada_isi = ($deskripsi_preview !== '') || ($deskripsi_img_total > 0);
                        $deskripsi_id = 'deskripsi-bab-' . $no;
                        $deskripsi_label = $deskripsi_preview !== '' ? 'Selengkapnya' : 'Lihat gambar';
                    ?>
                    <h3 class="font-semibold text-gray-900 text-base sm:text-lg leading-snug line-clamp-2 break-words">
                        <?= $val->judul ?></h3>

                    <?php if ($deskripsi_ada_isi): ?>
                    <div class="mt-2 flex items-start gap-3">
                        <?php if ($deskripsi_img !== ''): ?>
                        <button type="button" data-src="<?= htmlspecialchars($deskripsi_img, ENT_QUOTES, 'UTF-8') ?>"
                            class="js-preview-image relative w-20 h-14 sm:w-24 sm:h-16 rounded-xl overflow-hidden border <?= $palette['border'] ?> bg-gray-50 flex-shrink-0 group"
                            title="Lihat gambar">
                            <img src="<?= htmlspecialchars($deskripsi_img, ENT_QUOTES, 'UTF-8') ?>"
                                alt="Gambar sub bab"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy" decoding="async">
                            <?php if ($deskripsi_img_total > 1): ?>
                            <span
                                class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded-md bg-black/60 text-white text-[10px] font-medium">
                                +<?= $deskripsi_img_total - 1 ?>
                            </span>
                            <?php endif; ?>
                        </button>
                        <?php endif; ?>

                        <div class="flex-1 min-w-0">
                            <?php if ($deskripsi_preview !== ''): ?>
                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-2 break-words"><?= htmlspecialchars($deskripsi_preview, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>

                            <?php if ($deskripsi_is_long): ?>
                            <button type="button" onclick="toggleDeskripsi('<?= $deskripsi_id ?>', this)"
                                data-label-collapsed="<?= $deskripsi_label ?>"
                                class="mt-1 inline-flex items-center gap-1 text-xs font-semibold <?= $palette['icon'] ?> hover:underline">
                                <span><?= $deskripsi_label ?></span>
                                <i data-lucide="chevron-down" class="js-chevron w-3.5 h-3.5"></i>
                            </button>

                            <div id="<?= $deskripsi_id ?>"
                                class="deskripsi-html hidden mt-3 max-h-60 overflow-y-auto pr-1 text-sm text-gray-600 leading-relaxed">
                                <?= $deskripsi_isi ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-start">
                    <?php if($can_manage): ?>
                    <a href="<?= base_url('sub_bab/edit/' . $val->uuid) ?>"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        Edit
                    </a>
                    <a href="<?= base_url('sub_bab/hapus/' . $val->uuid) ?>"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-all"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus sub bab ini?')">
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

                <!-- Ujian Section -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-gray-400"></i>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ujian</span>
                            <span
                                class="text-xs text-gray-400">(<?= isset($ujian_per_bab[$val->uuid]) ? count($ujian_per_bab[$val->uuid]) : 0 ?>)</span>
                        </div>
                        <?php if($can_manage): ?>
                        <a href="<?= base_url('sub_bab/tambah_ujian/' . $val->uuid) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium <?= $palette['badge'] ?> border <?= $palette['border'] ?> hover:shadow-md transition-all">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            Buat Ujian
                        </a>
                        <?php endif; ?>
                    </div>

                    <?php if(isset($ujian_per_bab[$val->uuid]) && !empty($ujian_per_bab[$val->uuid])): ?>
                    <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                        <?php foreach($ujian_per_bab[$val->uuid] as $u): ?>
                        <div
                            class="flex items-center justify-between gap-2 bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                            <div class="flex items-center gap-2 min-w-0">
                                <i data-lucide="clipboard-list"
                                    class="w-3.5 h-3.5 <?= $palette['icon'] ?> flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-700 truncate"><?= $u->nama ?></p>
                                    <p class="text-[10px] text-gray-400"><?= $u->tgl_mulai_formatted ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <?php if(!has_role(['siswa'])): ?>
                                <a href="<?= base_url('ujian/tambah_soal/' . $u->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors"
                                    data-toggle="tooltip" title="Tambah Soal">
                                    <i data-lucide="plus" class="w-3 h-3"></i>
                                </a>
                                <a href="<?= base_url('ujian/tambah_kelas/' . $u->uuid) ?>"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition-colors"
                                    data-toggle="tooltip" title="Peserta">
                                    <i data-lucide="users" class="w-3 h-3"></i>
                                </a>
                                <?php else: ?>
                                <a href="<?= base_url('ujian/pengerjaan/' . $u->uuid) ?>"
                                    class="btn-mulai inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors"
                                    data-toggle="tooltip" title="Mulai Ujian" data-uuid="<?= $u->uuid ?>">
                                    <i data-lucide="play" class="w-3 h-3"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-xs text-gray-400 italic">Belum ada ujian</p>
                    <?php endif; ?>
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

<!-- Styling konten deskripsi (HTML dari editor) agar tetap rapi di dalam card -->
<style>
    .deskripsi-html>*:first-child {
        margin-top: 0;
    }

    .deskripsi-html>*:last-child {
        margin-bottom: 0;
    }

    .deskripsi-html p {
        margin: 0 0 0.5rem;
    }

    .deskripsi-html ul,
    .deskripsi-html ol {
        margin: 0 0 0.5rem;
        padding-left: 1.25rem;
    }

    .deskripsi-html ul {
        list-style: disc;
    }

    .deskripsi-html ol {
        list-style: decimal;
    }

    .deskripsi-html h1,
    .deskripsi-html h2,
    .deskripsi-html h3 {
        font-weight: 600;
        color: #111827;
        margin: 0.5rem 0;
    }

    .deskripsi-html blockquote {
        border-left: 3px solid #e5e7eb;
        padding-left: 0.75rem;
        margin: 0.5rem 0;
        color: #6b7280;
        font-style: italic;
    }

    .deskripsi-html a {
        color: #7c3aed;
        text-decoration: underline;
    }

    /* Format bawaan Quill (indent & perataan) agar tampilan tetap sama seperti di editor */
    .deskripsi-html .ql-align-center {
        text-align: center;
    }

    .deskripsi-html .ql-align-right {
        text-align: right;
    }

    .deskripsi-html .ql-align-justify {
        text-align: justify;
    }

    .deskripsi-html .ql-indent-1 {
        padding-left: 1.5rem;
    }

    .deskripsi-html .ql-indent-2 {
        padding-left: 3rem;
    }

    .deskripsi-html .ql-indent-3 {
        padding-left: 4.5rem;
    }

    .deskripsi-html pre {
        background: #f9fafb;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin: 0.5rem 0;
        white-space: pre-wrap;
        overflow-x: auto;
    }

    .deskripsi-html table {
        width: 100%;
        border-collapse: collapse;
        margin: 0.5rem 0;
        font-size: 0.8125rem;
    }

    .deskripsi-html th,
    .deskripsi-html td {
        border: 1px solid #e5e7eb;
        padding: 0.375rem 0.5rem;
    }

    /* Gambar/video dari editor tidak boleh melebihi lebar card */
    .deskripsi-html img,
    .deskripsi-html video,
    .deskripsi-html iframe {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 0.75rem;
        margin: 0.5rem 0;
    }

    .deskripsi-html img {
        cursor: zoom-in;
    }

    /* Penanda untuk gambar lama yang datanya (base64) tidak tersimpan penuh */
    .deskripsi-html .deskripsi-gambar-rusak {
        display: block;
        padding: 0.5rem 0.75rem;
        margin: 0.5rem 0;
        border: 1px dashed #fca5a5;
        border-radius: 0.75rem;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 0.75rem;
        line-height: 1.4;
    }

    .js-chevron {
        transform-box: fill-box;
        transform-origin: center;
    }
</style>

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

<!-- Image Modal (pratinjau gambar deskripsi) -->
<div id="imageModal" class="fixed inset-0 bg-black/70 hidden z-[60] flex items-center justify-center p-4">
    <button type="button" onclick="closeImageModal()"
        class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <img id="imageModalImg" src="" alt="Pratinjau gambar"
        class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
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

/* Toggle deskripsi lengkap pada card sub bab */
function toggleDeskripsi(id, btn) {
    var box = document.getElementById(id);
    if (!box) {
        return;
    }

    var label = btn.querySelector('span');
    var chevron = btn.querySelector('.js-chevron');
    var tersembunyi = box.classList.contains('hidden');

    if (tersembunyi) {
        box.classList.remove('hidden');
        if (label) {
            label.textContent = 'Sembunyikan';
        }
        if (chevron) {
            chevron.classList.add('rotate-180');
        }
    } else {
        box.classList.add('hidden');
        if (label) {
            label.textContent = btn.getAttribute('data-label-collapsed') || 'Selengkapnya';
        }
        if (chevron) {
            chevron.classList.remove('rotate-180');
        }
    }
}

/* Lightbox gambar deskripsi */
function openImageModal(url) {
    if (!url) {
        return;
    }
    document.getElementById('imageModalImg').src = url;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModalImg').src = '';
    document.body.style.overflow = 'auto';
}

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

/* Thumbnail gambar pada card */
var thumbnailGambar = document.querySelectorAll('.js-preview-image');
for (var i = 0; i < thumbnailGambar.length; i++) {
    thumbnailGambar[i].addEventListener('click', function() {
        openImageModal(this.getAttribute('data-src'));
    });
}

/* Gambar di dalam deskripsi lengkap bisa diklik untuk diperbesar */
var kotakDeskripsi = document.querySelectorAll('.deskripsi-html');
for (var j = 0; j < kotakDeskripsi.length; j++) {
    kotakDeskripsi[j].addEventListener('click', function(e) {
        if (e.target && e.target.tagName === 'IMG') {
            openImageModal(e.target.getAttribute('src'));
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePdfModal();
        closeImageModal();
    }
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>