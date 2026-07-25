<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Daftar Mata Pelajaran']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Page Header with Gradient -->
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-8 md:p-10 mb-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-yellow-400/10 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Mata Pelajaran</h1>
                    </div>
                    <p class="text-blue-100 text-sm md:text-base ml-[60px]">Pilih mata pelajaran untuk mulai belajar dan
                        mengakses materi pembelajaran</p>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-white/80 text-sm">
                    <i data-lucide="sparkles" class="w-4 h-4 text-yellow-300"></i>
                    <span><?= count($mapel) ?> Mata Pelajaran Tersedia</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="mb-8">
        <div id="mapelList">
            <div class="relative max-w-md mb-6">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input
                    class="search w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-gray-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none transition-all text-sm placeholder:text-gray-400 bg-white shadow-sm"
                    placeholder="Cari mata pelajaran..." />
            </div>

            <!-- Colorful Card Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 list">
                <?php if (!empty($mapel)) : ?>
                <?php 
                    $color_palettes = [
                        ['from' => 'from-blue-500', 'to' => 'to-cyan-500', 'bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'hover' => 'hover:border-blue-300 hover:shadow-blue-200/50', 'badge' => 'bg-blue-100 text-blue-700'],
                        ['from' => 'from-emerald-500', 'to' => 'to-green-500', 'bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'hover' => 'hover:border-emerald-300 hover:shadow-emerald-200/50', 'badge' => 'bg-emerald-100 text-emerald-700'],
                        ['from' => 'from-purple-500', 'to' => 'to-pink-500', 'bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'hover' => 'hover:border-purple-300 hover:shadow-purple-200/50', 'badge' => 'bg-purple-100 text-purple-700'],
                        ['from' => 'from-orange-500', 'to' => 'to-amber-500', 'bg' => 'bg-orange-50', 'icon' => 'text-orange-600', 'hover' => 'hover:border-orange-300 hover:shadow-orange-200/50', 'badge' => 'bg-orange-100 text-orange-700'],
                        ['from' => 'from-rose-500', 'to' => 'to-red-500', 'bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'hover' => 'hover:border-rose-300 hover:shadow-rose-200/50', 'badge' => 'bg-rose-100 text-rose-700'],
                        ['from' => 'from-sky-500', 'to' => 'to-indigo-500', 'bg' => 'bg-sky-50', 'icon' => 'text-sky-600', 'hover' => 'hover:border-sky-300 hover:shadow-sky-200/50', 'badge' => 'bg-sky-100 text-sky-700'],
                        ['from' => 'from-teal-500', 'to' => 'to-cyan-500', 'bg' => 'bg-teal-50', 'icon' => 'text-teal-600', 'hover' => 'hover:border-teal-300 hover:shadow-teal-200/50', 'badge' => 'bg-teal-100 text-teal-700'],
                        ['from' => 'from-violet-500', 'to' => 'to-purple-500', 'bg' => 'bg-violet-50', 'icon' => 'text-violet-600', 'hover' => 'hover:border-violet-300 hover:shadow-violet-200/50', 'badge' => 'bg-violet-100 text-violet-700'],
                        ['from' => 'from-amber-500', 'to' => 'to-yellow-500', 'bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'hover' => 'hover:border-amber-300 hover:shadow-amber-200/50', 'badge' => 'bg-amber-100 text-amber-700'],
                        ['from' => 'from-cyan-500', 'to' => 'to-blue-500', 'bg' => 'bg-cyan-50', 'icon' => 'text-cyan-600', 'hover' => 'hover:border-cyan-300 hover:shadow-cyan-200/50', 'badge' => 'bg-cyan-100 text-cyan-700'],
                        ['from' => 'from-fuchsia-500', 'to' => 'to-pink-500', 'bg' => 'bg-fuchsia-50', 'icon' => 'text-fuchsia-600', 'hover' => 'hover:border-fuchsia-300 hover:shadow-fuchsia-200/50', 'badge' => 'bg-fuchsia-100 text-fuchsia-700'],
                        ['from' => 'from-lime-500', 'to' => 'to-green-500', 'bg' => 'bg-lime-50', 'icon' => 'text-lime-600', 'hover' => 'hover:border-lime-300 hover:shadow-lime-200/50', 'badge' => 'bg-lime-100 text-lime-700'],
                    ];
                    $i = 0;
                ?>
                <?php foreach ($mapel as $val) : ?>
                <?php 
                    $palette = $color_palettes[$i % count($color_palettes)];
                    $i++;
                    $icons = ['book', 'book-open', 'bookmark', 'library', 'graduation-cap', 'pen-tool', 'feather', 'globe', 'compass', 'star', 'heart', 'zap'];
                    $icon = $icons[$i % count($icons)];
                ?>
                <div
                    class="card group relative bg-white rounded-2xl border-2 border-gray-100 <?= $palette['hover'] ?> hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                    <!-- Colorful top accent bar -->
                    <div class="h-2 w-full bg-gradient-to-r <?= $palette['from'] ?> <?= $palette['to'] ?>"></div>

                    <div class="p-6">
                        <!-- Icon with gradient background -->
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-14 h-14 rounded-2xl <?= $palette['bg'] ?> flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="<?= $icon ?>" class="w-7 h-7 <?= $palette['icon'] ?>"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="name font-bold text-gray-900 text-lg leading-snug truncate"><?= $val->nama ?>
                                </h6>
                                <span
                                    class="inline-block mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider <?= $palette['badge'] ?>">
                                    Mata Pelajaran
                                </span>
                            </div>
                        </div>

                        <!-- Action Button with gradient -->
                        <a href="<?= base_url('materi/detail/' . $val->uuid) ?>"
                            class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl font-semibold text-white bg-gradient-to-r <?= $palette['from'] ?> <?= $palette['to'] ?> shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 text-sm group/btn">
                            <span>Lihat Materi</span>
                            <i data-lucide="arrow-right"
                                class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <!-- Decorative dots pattern -->
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 opacity-5">
                        <div class="grid grid-cols-4 gap-2">
                            <?php for($d=0; $d<16; $d++): ?>
                            <div class="w-2 h-2 rounded-full bg-gray-800"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else : ?>
                <div class="col-span-full text-center py-16">
                    <div
                        class="w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="book" class="w-10 h-10 text-blue-400"></i>
                    </div>
                    <p class="text-gray-400 font-medium">Belum ada data mata pelajaran</p>
                    <p class="text-gray-300 text-sm mt-1">Silakan hubungi admin untuk menambahkan mata pelajaran</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <ul class="pagination flex flex-wrap justify-center items-center gap-2 mt-10"></ul>
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
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination li a:hover {
    border-color: #818cf8;
    background-color: #eef2ff;
    color: #6366f1;
    transform: translateY(-1px);
}

.pagination li.active a {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-color: transparent;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.pagination li.disabled a {
    color: #cbd5e1;
    pointer-events: none;
    opacity: 0.6;
}

/* Smooth entrance animation for cards */
.card {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.card:nth-child(1) {
    animation-delay: 0.05s;
}

.card:nth-child(2) {
    animation-delay: 0.1s;
}

.card:nth-child(3) {
    animation-delay: 0.15s;
}

.card:nth-child(4) {
    animation-delay: 0.2s;
}

.card:nth-child(5) {
    animation-delay: 0.25s;
}

.card:nth-child(6) {
    animation-delay: 0.3s;
}

.card:nth-child(7) {
    animation-delay: 0.35s;
}

.card:nth-child(8) {
    animation-delay: 0.4s;
}

.card:nth-child(9) {
    animation-delay: 0.45s;
}

.card:nth-child(10) {
    animation-delay: 0.5s;
}

.card:nth-child(11) {
    animation-delay: 0.55s;
}

.card:nth-child(12) {
    animation-delay: 0.6s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
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