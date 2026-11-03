<?php
$jadwal_akses = isset($jadwal_akses) && is_array($jadwal_akses) ? $jadwal_akses : array();
$tanggal_bersama = isset($jadwal_akses['tanggal_akses']) ? $jadwal_akses['tanggal_akses'] : '';
$tanggal_per_kelas = isset($jadwal_akses['kelas']) && is_array($jadwal_akses['kelas'])
    ? $jadwal_akses['kelas']
    : array();
$samakan_jadwal = isset($jadwal_akses['sama_untuk_semua'])
    ? $jadwal_akses['sama_untuk_semua']
    : TRUE;
$is_post = $this->input->method(TRUE) === 'POST';
$posted_same = $this->input->post('samakan_jadwal');
$samakan_jadwal = $is_post ? $posted_same === '1' : $samakan_jadwal;
$posted_tanggal_bersama = $this->input->post('tanggal_akses');
$tanggal_bersama = $is_post
    ? ($posted_tanggal_bersama === NULL ? '' : $posted_tanggal_bersama)
    : $tanggal_bersama;
$hari = array(1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu');
?>
<section class="border-t border-gray-100 pt-6">
    <div class="mb-4">
        <h2 class="text-sm font-bold text-gray-800">Jadwal Akses Materi</h2>
        <p class="mt-1 text-xs leading-relaxed text-gray-500">
            Akses juga tetap menunggu admin membuka pekan materi ini. Jika tanggal dikosongkan, jadwal mengikuti pekan aktif dari admin.
        </p>
    </div>

    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
        <input type="checkbox" name="samakan_jadwal" id="samakan-jadwal" value="1"
            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-200"
            <?= $samakan_jadwal ? 'checked' : '' ?>>
        <span>
            <span class="block text-sm font-semibold text-gray-800">Samakan jadwal akses seluruh kelas</span>
            <span class="mt-1 block text-xs text-gray-500">Tanggal bersama berlaku untuk semua kelas yang mengikuti mata pelajaran ini.</span>
        </span>
    </label>

    <div class="mt-4">
        <label for="tanggal-akses" class="mb-1.5 block text-sm font-semibold text-gray-700">
            Tanggal akses bersama
        </label>
        <input type="date" name="tanggal_akses" id="tanggal-akses"
            value="<?= html_escape($tanggal_bersama); ?>"
            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
        <p id="hari-akses-bersama" class="mt-1 text-xs text-gray-500">
            <?php if ($tanggal_bersama && strtotime($tanggal_bersama)): ?>
            Hari: <?= $hari[(int) date('N', strtotime($tanggal_bersama))] ?>
            <?php else: ?>
            Pilih tanggal untuk melihat harinya.
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($kelas_mapel)): ?>
    <div id="jadwal-per-kelas" class="mt-5 space-y-3 <?= $samakan_jadwal ? 'hidden' : '' ?>">
        <p class="text-xs font-medium text-gray-600">
            Kelas yang tidak diatur khusus akan menggunakan tanggal bersama di atas.
        </p>
        <?php $posted_kelas = $this->input->post('jadwal_kelas'); ?>
        <?php $posted_tanggal_kelas = $this->input->post('tanggal_akses_kelas'); ?>
        <?php foreach ($kelas_mapel as $kelas): ?>
        <?php
            $kelas_uuid = $kelas->uuid;
            $punya_override = $is_post
                ? (is_array($posted_kelas) && isset($posted_kelas[$kelas_uuid]) && $posted_kelas[$kelas_uuid] === '1')
                : array_key_exists($kelas_uuid, $tanggal_per_kelas);
            $tanggal_kelas = $is_post
                ? (is_array($posted_tanggal_kelas) && isset($posted_tanggal_kelas[$kelas_uuid]) ? $posted_tanggal_kelas[$kelas_uuid] : '')
                : (isset($tanggal_per_kelas[$kelas_uuid]) ? $tanggal_per_kelas[$kelas_uuid] : '');
        ?>
        <div class="rounded-xl border border-gray-200 p-4">
            <label class="flex cursor-pointer items-center gap-2">
                <input type="checkbox" name="jadwal_kelas[<?= html_escape($kelas_uuid) ?>]" value="1"
                    class="jadwal-kelas-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-200"
                    <?= $punya_override ? 'checked' : '' ?>>
                <span class="text-sm font-semibold text-gray-800">Atur jadwal khusus <?= html_escape($kelas->nama) ?></span>
            </label>
            <div class="jadwal-kelas-fields mt-3 <?= $punya_override ? '' : 'hidden' ?>">
                <label class="mb-1.5 block text-xs font-medium text-gray-600">
                    Tanggal akses <?= html_escape($kelas->nama) ?>
                </label>
                <input type="date" name="tanggal_akses_kelas[<?= html_escape($kelas_uuid) ?>]"
                    value="<?= html_escape($tanggal_kelas); ?>"
                    class="jadwal-kelas-date w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                <p class="jadwal-kelas-hari mt-1 text-xs text-gray-500">
                    <?php if ($tanggal_kelas && strtotime($tanggal_kelas)): ?>
                    Hari: <?= $hari[(int) date('N', strtotime($tanggal_kelas))] ?>
                    <?php else: ?>
                    Pilih tanggal untuk melihat harinya.
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<script>
(function() {
    var sameSchedule = document.getElementById('samakan-jadwal');
    var classSchedules = document.getElementById('jadwal-per-kelas');
    var dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    function updateDay(input, output) {
        if (!input || !output) return;
        if (!input.value) {
            output.textContent = 'Pilih tanggal untuk melihat harinya.';
            return;
        }
        var date = new Date(input.value + 'T00:00:00');
        output.textContent = isNaN(date.getTime()) ? '' : 'Hari: ' + dayNames[date.getDay()];
    }

    function updateScheduleMode() {
        if (classSchedules) {
            classSchedules.classList.toggle('hidden', sameSchedule.checked);
        }
    }

    if (sameSchedule) {
        sameSchedule.addEventListener('change', updateScheduleMode);
        updateScheduleMode();
    }

    var commonDate = document.getElementById('tanggal-akses');
    var commonDay = document.getElementById('hari-akses-bersama');
    if (commonDate && commonDay) {
        commonDate.addEventListener('change', function() {
            updateDay(commonDate, commonDay);
        });
    }

    document.querySelectorAll('.jadwal-kelas-checkbox').forEach(function(checkbox) {
        var fields = checkbox.closest('.rounded-xl').querySelector('.jadwal-kelas-fields');
        checkbox.addEventListener('change', function() {
            fields.classList.toggle('hidden', !checkbox.checked);
        });
    });

    document.querySelectorAll('.jadwal-kelas-date').forEach(function(input) {
        var output = input.parentNode.querySelector('.jadwal-kelas-hari');
        input.addEventListener('change', function() {
            updateDay(input, output);
        });
    });
})();
</script>
