<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Tugas']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'tugas']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="relative bg-gradient-to-r from-violet-600 to-indigo-700 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('tugas') ?>" class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center"><i data-lucide="arrow-left" class="w-5 h-5"></i></a>
            <div><h1 class="text-2xl font-extrabold"><?= !empty($tugas) ? 'Edit Tugas' : 'Tambah Tugas' ?></h1><p class="text-violet-100 text-sm">Tentukan mata pelajaran dan periode tugas.</p></div>
        </div>
    </div>
    <?php if ($this->session->userdata('error_msg')): ?>
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm"><?= html_escape($this->session->userdata('error_msg')); $this->session->unset_userdata('error_msg'); ?></div>
    <?php endif; ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8">
        <?php
        $is_edit = !empty($tugas);
        $mapel_terkunci = !empty($bab) || ($is_edit && !empty($tugas->bab_uuid));
        $form_action = $is_edit
            ? 'tugas/edit/' . $tugas->uuid
            : ($bab ? 'tugas/tambah_pertemuan/' . $bab->uuid : 'tugas/tambah');
        $tgl_mulai_value = $is_edit ? date('Y-m-d\TH:i', strtotime($tugas->tgl_mulai)) : '';
        $tgl_selesai_value = $is_edit ? date('Y-m-d\TH:i', strtotime($tugas->tgl_selesai)) : '';
        ?>
        <form method="post" action="<?= base_url($form_action) ?>">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <?php if ($mapel_terkunci): ?>
                    <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-700"><?= html_escape($selected_mapel->nama ?? '') ?></div>
                    <input type="hidden" name="mapel_uuid" value="<?= html_escape($mapel_uuid) ?>">
                    <?php else: ?>
                    <select name="mapel_uuid" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm bg-white">
                        <option value="">Pilih Mata Pelajaran</option>
                        <?php foreach ($mapel as $subject): ?>
                        <option value="<?= html_escape($subject->uuid) ?>" <?= set_select('mapel_uuid', $subject->uuid, $is_edit && $subject->uuid === $mapel_uuid) ?>><?= html_escape($subject->nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('mapel_uuid', '<div class="text-red-500 text-xs mt-1">', '</div>') ?>
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Tugas <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" required maxlength="100" value="<?= set_value('judul', $is_edit ? $tugas->judul : '') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm" placeholder="Contoh: Latihan Bab 1">
                    <?= form_error('judul', '<div class="text-red-500 text-xs mt-1">', '</div>') ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_mulai" required value="<?= set_value('tgl_mulai', $tgl_mulai_value) ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm">
                    <?= form_error('tgl_mulai', '<div class="text-red-500 text-xs mt-1">', '</div>') ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_selesai" required value="<?= set_value('tgl_selesai', $tgl_selesai_value) ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm">
                    <?= form_error('tgl_selesai', '<div class="text-red-500 text-xs mt-1">', '</div>') ?>
                </div>
            </div>
            <?php if ($bab): ?><p class="text-xs text-violet-700 mt-4">Tugas ini ditautkan ke pertemuan: <?= html_escape($bab->judul) ?></p><?php endif; ?>
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-violet-600 hover:bg-violet-700 text-sm"><i data-lucide="save" class="w-4 h-4"></i><?= $is_edit ? 'Simpan Perubahan' : 'Simpan dan Tambah Soal' ?></button>
                <a href="<?= base_url('tugas') ?>" class="px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
