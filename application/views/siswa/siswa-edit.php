<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('siswa')?>" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Edit Data Siswa</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Perbarui informasi siswa yang sudah ada</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form method="post" action="<?= base_url('siswa/edit/'.$siswa->uuid);?>">
            <input type="hidden" name="uuid" value="<?= $siswa->uuid ?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- NIS -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIS (Nomor Induk Siswa) <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nis" id="nis"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan NIS (10 digit angka)" value="<?= $siswa->nis; ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('nis')) ? '' : 'hidden' ?>">
                        <?= form_error('nis') ?>
                    </div>
                    <small class="text-gray-400 text-xs mt-1 block">Kolom NIS hanya menerima 10 digit angka</small>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="namaLengkap" id="namaLengkap"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Nama Lengkap" value="<?= $siswa->nama; ?>">
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
                        placeholder="Masukkan Username" value="<?= $siswa->username; ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('username')) ? '' : 'hidden' ?>">
                        <?= form_error('username') ?>
                    </div>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Lahir <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        value="<?= $siswa->tgl_lahir; ?>">
                    <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('tanggal_lahir')) ? '' : 'hidden' ?>">
                        <?= form_error('tanggal_lahir') ?>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin <span
                            class="text-red-500">*</span></label>
                    <select name="jenisKelamin"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option disabled selected>Pilih Jenis Kelamin</option>
                        <option value="1" <?= $siswa->jenis_kelamin==1?'selected':'';?>>Laki-Laki</option>
                        <option value="2" <?= $siswa->jenis_kelamin==2?'selected':'';?>>Perempuan</option>
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
                <a href="<?= base_url('siswa')?>"
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
    $("#namaLengkap").change(function() {
        var namaLengkap = $(this).val().toLowerCase();
        var username = namaLengkap.replace(/\s+/g, '.');
        $('#username').val(username);
    });
});
</script>