<?php if (!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Materi Pekan']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<main class="max-w-5xl mx-auto px-6 py-8">
    <header class="mb-8 rounded-3xl bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-700 p-8 text-white shadow-lg">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="<?= base_url('mata_pelajaran/detail/' . $mapel->uuid . '?semester=' . ($materi->semester ?? 1)) ?>"
                    class="mb-5 inline-flex items-center gap-2 text-sm font-medium text-purple-100 transition hover:text-white">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Kembali ke daftar pekan
                </a>
                <p class="text-sm font-medium text-purple-100"><?= html_escape($mapel->nama ?? '') ?></p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight"><?= html_escape($materi->judul) ?></h1>
                <p class="mt-2 text-sm text-purple-100">Pilih pertemuan untuk membaca materi lengkap dan melihat aktivitasnya.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white">
                    <i data-lucide="calendar-check" class="h-4 w-4"></i>
                    <?= (int) $pertemuan_count ?>/3 Pertemuan
                </span>
                <?php if ($can_add_meeting && $pertemuan_count < 3): ?>
                <a href="<?= base_url('sub_bab/tambah/' . $materi->uuid) ?>"
                    class="inline-flex items-center gap-2 rounded-xl bg-white/90 px-5 py-2.5 text-sm font-semibold text-purple-900 shadow-lg transition hover:bg-white hover:shadow-xl">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tambah Pertemuan
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if ($this->session->userdata('success_msg')): ?>
    <div class="mb-6 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
        <i data-lucide="check-circle" class="h-5 w-5 shrink-0 text-green-500"></i>
        <?= html_escape($this->session->userdata('success_msg')); ?>
        <?php $this->session->unset_userdata('success_msg'); ?>
    </div>
    <?php endif; ?>

    <?php if ($this->session->userdata('error_msg')): ?>
    <div class="mb-6 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <i data-lucide="alert-circle" class="h-5 w-5 shrink-0 text-red-500"></i>
        <?= html_escape($this->session->userdata('error_msg')); ?>
        <?php $this->session->unset_userdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($bab)): ?>
    <div class="space-y-4">
        <?php foreach ($bab as $index => $pertemuan): ?>
        <?php
            $description_html = isset($pertemuan->deskripsi) ? (string) $pertemuan->deskripsi : '';
            $description_html = preg_replace('/<[a-zA-Z\/!][^>]*$/s', ' ', $description_html);
            $description = preg_replace('/<br\s*\/?>/i', ' ', $description_html);
            $description = preg_replace('~</(p|div|li|h[1-6]|blockquote)>~i', ' ', $description);
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $description = trim(preg_replace('/\s+/', ' ', $description));
            $description_limit = 220;
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                $description = mb_strlen($description, 'UTF-8') > $description_limit
                    ? rtrim(mb_substr($description, 0, $description_limit, 'UTF-8')) . '…'
                    : $description;
            } elseif (strlen($description) > $description_limit) {
                $description = rtrim(substr($description, 0, $description_limit)) . '...';
            }
            $is_unlocked = !empty($bab_unlocked[$pertemuan->uuid]);
            if ($description === '') {
                $description = 'Deskripsi belum tersedia.';
            }
        ?>
        <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-violet-200 hover:shadow-md sm:p-6">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-violet-600">
                        Pertemuan <?= (int) $index + 1 ?>
                    </p>
                    <h2 class="text-lg font-bold leading-snug text-gray-900">
                        <?= html_escape($pertemuan->judul) ?>
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">
                        <?= html_escape($description) ?>
                    </p>
                </div>
                <?php if ($is_unlocked): ?>
                <a href="<?= base_url('sub_bab/detail/' . rawurlencode($pertemuan->uuid)) ?>"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
                    Lihat detail
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
                <?php else: ?>
                <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-500"
                    aria-label="Pertemuan terkunci">
                    <i data-lucide="lock" class="h-4 w-4"></i>
                    Terkunci
                </span>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
        <i data-lucide="book-open" class="mx-auto h-10 w-10 text-violet-300"></i>
        <h2 class="mt-3 font-semibold text-gray-800">Belum ada pertemuan</h2>
        <p class="mt-1 text-sm text-gray-500">Materi pertemuan untuk pekan ini belum tersedia.</p>
    </div>
    <?php endif; ?>
</main>

<?php if (!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
