<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Pilih Siswa']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'proyek']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('proyek/detail/'.$proyek->uuid)?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Pilih Siswa</h1>
                    <p class="text-violet-100 text-sm mt-1">Proyek:
                        <span class="font-semibold"><?= $proyek->judul ?></span>
                    </p>
                </div>
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

    <!-- Daftar Kelompok -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Daftar Kelompok</h3>
            <button
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all text-sm"
                data-bs-toggle="modal" data-bs-target="#modalTambahKelompok">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kelompok
            </button>
        </div>
        <div class="space-y-3">
            <?php if(!empty($kelompok)): ?>
            <?php foreach($kelompok as $kel): ?>
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900"><?= $kel['kelompok'] ?></h4>
                        <p class="text-xs text-gray-500 mt-1"><?= count($kel['anggota'] ?? []) ?> anggota</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors btn-tambah-siswa"
                            data-kelompok="<?= $kel['kelompok_uuid'] ?>">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Tambah Siswa
                        </button>
                        <a href="<?= base_url('kelompok/hapus/'.$kel['kelompok_uuid'].'?proyek_uuid='.$proyek->uuid) ?>"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                            onclick="return confirm('Hapus kelompok <?= $kel['kelompok']; ?>?')">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </a>
                    </div>
                </div>
                <?php if(!empty($kel['anggota'])): ?>
                <div class="mt-3 flex flex-wrap gap-2">
                    <?php foreach($kel['anggota'] ?? [] as $a): ?>
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <?= $a['nama'] ?>
                        <a href="<?= base_url('kelompok/hapus_siswa_kelompok_by_relasi/'.$a['relasi'].'?proyek_uuid='.$proyek->uuid) ?>"
                            class="ml-1 hover:text-red-600" onclick="return confirm('Hapus siswa <?= $a['nama']; ?>?')">
                            <i data-lucide="x" class="w-3 h-3"></i>
                        </a>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p class="text-sm">Belum ada kelompok</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Daftar Siswa -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Siswa</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th
                            class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-12">
                            No.</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">
                            Nama</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">NIS
                        </th>
                        <th
                            class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-40">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                        $no = 1;
                        foreach($siswa as $s) {
                    ?>
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-center"><?= $no ; ?></td>
                        <td class="px-4 py-3 font-medium text-gray-900"><?= $s->nama; ?></td>
                        <td class="px-4 py-3 text-gray-600"><?= $s->nis; ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <?php
                                $in_kelompok = false;
                                foreach($kelompok as $kel) {
                                    foreach($kel['anggota'] ?? [] as $a) {
                                        if($a['siswa_uuid'] == $s->uuid) {
                                            $in_kelompok = true;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <?php if(!$in_kelompok): ?>
                                <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors btn-add-kelompok"
                                    data-siswa="<?= $s->uuid ?>" data-nama="<?= $s->nama ?>">
                                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Tambah ke Kelompok
                                </button>
                                <?php else: ?>
                                <span class="text-xs text-gray-500">Sudah ada di kelompok</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelompok -->
<div class="modal fade" id="modalTambahKelompok" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelompok Baru</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="post" id="formTambahKelompok" action="<?= base_url('kelompok/tambah') ?>">
                <div class="modal-body">
                    <input type="hidden" name="proyek_uuid" value="<?= $proyek->uuid ?>">
                    <div class="form-group">
                        <label>Nama Kelompok</label>
                        <input type="text" name="kelompok" class="form-control" required
                            placeholder="Contoh: Kelompok 1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa ke Kelompok -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa ke Kelompok</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="post" id="formTambahSiswa" action="<?= base_url('kelompok/tambah_siswa_by_kelompok') ?>">
                <div class="modal-body">
                    <input type="hidden" name="proyek_uuid" value="<?= $proyek->uuid ?>">
                    <div class="form-group">
                        <label>Pilih Kelompok</label>
                        <select name="kelompok_uuid" class="form-control" required>
                            <option disabled selected>Pilih Kelompok</option>
                            <?php foreach($kelompok as $kel): ?>
                            <option value="<?= $kel['kelompok_uuid'] ?>"><?= $kel['kelompok'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih Siswa</label>
                        <select name="siswa_uuid" class="form-control" required>
                            <option disabled selected>Pilih Siswa</option>
                            <?php foreach($siswa as $s): 
                                $in_kel = false;
                                foreach($kelompok as $kel) {
                                    foreach($kel['anggota'] ?? [] as $a) {
                                        if($a['siswa_uuid'] == $s->uuid) {
                                            $in_kel = true;
                                            break;
                                        }
                                    }
                                }
                                if(!$in_kel):
                            ?>
                            <option value="<?= $s->uuid ?>"><?= $s->nama ?> (<?= $s->nis ?>)</option>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Tambah Siswa</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-tambah-siswa').click(function() {
        var kelompok_uuid = $(this).data('kelompok');
        var modal = new bootstrap.Modal(document.getElementById('modalTambahSiswa'));
        modal.show();
        $('#modalTambahSiswa select[name=kelompok_uuid]').val(kelompok_uuid);
    });

    $('.btn-add-kelompok').click(function() {
        var siswa_uuid = $(this).data('siswa');
        var modal = new bootstrap.Modal(document.getElementById('modalTambahSiswa'));
        modal.show();
        $('#modalTambahSiswa select[name=siswa_uuid]').val(siswa_uuid);
    });

    $('#formTambahKelompok').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert('Gagal menambah kelompok');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });

    $('#formTambahSiswa').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(form.attr('action'), form.serialize(), function() {
            location.reload();
        });
    });
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>