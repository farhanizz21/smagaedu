<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Kalender Akademik']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'kalender']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="relative bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-7 h-7 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Kalender Akademik</h1>
                    </div>
                </div>
                <p class="text-blue-100 mt-1 text-sm ml-15">Jadwal dan kegiatan akademik</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <?php $this->load->view('partials/menu_guide', ['active_nav' => 'kalender', 'button_style' => 'hero']); ?>
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

    <!-- Calendar Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <div id='calendar'></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>