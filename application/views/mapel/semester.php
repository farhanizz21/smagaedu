<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Pilih Semester']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<main class="max-w-7xl mx-auto px-6 py-8">
    <div class="mb-8">
        <a href="<?= base_url('mata_pelajaran') ?>"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Mata Pelajaran
        </a>
        <div class="mt-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-lucide="book-open" class="w-6 h-6 text-blue-700"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pilih Semester</h1>
                <p class="mt-1 text-sm text-gray-600"><?= $mapel->nama ?></p>
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <?php foreach ([1, 2] as $semester): ?>
        <a href="<?= base_url('mata_pelajaran/detail/' . $mapel->uuid . '?semester=' . $semester) ?>"
            class="group relative flex min-h-48 items-center justify-between overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <div>
                <p class="text-xs font-semibold uppercase text-blue-700">Semester</p>
                <h2 class="mt-2 text-2xl font-bold text-gray-900"><?= $semester ?></h2>
                <p class="mt-2 text-sm text-gray-600">Lihat daftar bab <?= $mapel->nama ?></p>
            </div>
            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 transition group-hover:bg-blue-700 group-hover:text-white">
                <i data-lucide="arrow-right" class="h-5 w-5"></i>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
</main>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>