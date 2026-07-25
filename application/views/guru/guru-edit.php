<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Edit Guru']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'guru']); ?>
<?php endif; ?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('guru')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Edit Data Guru</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Perbarui informasi guru yang sudah ada</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form method="post" action="<?= base_url('guru/edit/'.$guru->uuid);?>">
            <input type="hidden" name="uuid" value="<?= $guru->uuid ?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="namaLengkap" id="namaLengkap"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Nama Lengkap" value="<?= $guru->nama; ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('namaLengkap')) ? '' : 'hidden' ?>">
                        <?= form_error('namaLengkap') ?>
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="username" id="username"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Username" value="<?= $guru->username; ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('username')) ? '' : 'hidden' ?>">
                        <?= form_error('username') ?>
                    </div>
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span
                            class="text-red-500">*</span></label>
                    <select name="namaMapel[]" class="multiple-table w-full" multiple="multiple" style="width:100%">
                        <?php foreach ($mapel as $val): ?>
                        <option value="<?= $val->uuid; ?>"
                            <?= in_array($val->uuid, $mapel_list ?? []) ? 'selected' : ''; ?>>
                            <?= $val->nama; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('namaMapel')) ? '' : 'hidden' ?>">
                        <?= form_error('namaMapel') ?>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin <span
                            class="text-red-500">*</span></label>
                    <select name="jenisKelamin"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option disabled selected>Pilih Jenis Kelamin</option>
                        <option value="1" <?= ($guru->jenis_kelamin=='L'||$guru->jenis_kelamin==1)?'selected':'';?>>
                            Laki-Laki</option>
                        <option value="2" <?= ($guru->jenis_kelamin=='P'||$guru->jenis_kelamin==2)?'selected':'';?>>
                            Perempuan</option>
                    </select>
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('jenisKelamin')) ? '' : 'hidden' ?>">
                        <?= form_error('jenisKelamin') ?>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
                <a href="<?= base_url('guru')?>"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.multiple-table').select2({
        placeholder: "Pilih Mata Pelajaran",
        allowClear: true,
        width: '100%'
    });

    $("#namaLengkap").change(function() {
        var namaLengkap = $(this).val().toLowerCase();
        var username = namaLengkap.replace(/\s+/g, '.');
        $('#username').val(username);
    });
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>
</write_to_file>