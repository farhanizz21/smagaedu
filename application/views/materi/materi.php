<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Mata Pelajaran']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Daftar Mata Pelajaran</h1>
            <p class="text-gray-500 mt-1 text-sm">Pilih mata pelajaran untuk melihat daftar materi</p>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <div id="mapelList">
            <input
                class="search w-full sm:w-80 px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400 mb-6"
                placeholder="Cari mata pelajaran..." />

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 list">
                <?php if (!empty($mapel)) : ?>
                <?php foreach ($mapel as $val) : ?>
                <div
                    class="card h-full bg-white rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all p-5 flex flex-col justify-between">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="book" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <h6 class="name font-semibold text-gray-900 text-base leading-snug"><?= $val->nama ?></h6>
                    </div>
                    <a href="<?= base_url('materi/detail/' . $val->uuid) ?>"
                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Lihat Materi
                    </a>
                </div>
                <?php endforeach; ?>
                <?php else : ?>
                <div class="col-span-full text-center py-12 text-gray-400">
                    <i data-lucide="book" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada data mata pelajaran</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <ul class="pagination flex flex-wrap justify-center items-center gap-1.5 mt-8"></ul>
        </div>
    </div>
</div>

<style>
.pagination li {
    display: inline-block;
}

.pagination li a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    color: #374151;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.15s ease;
}

.pagination li a:hover {
    border-color: #93c5fd;
    background-color: #eff6ff;
    color: #2563eb;
}

.pagination li.active a {
    background-color: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}

.pagination li.disabled a {
    color: #cbd5e1;
    pointer-events: none;
    opacity: 0.6;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script>
var options = {
    valueNames: ['name'],
    page: 6,
    pagination: true
};

var mapelList = new List('mapelList', options);
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>