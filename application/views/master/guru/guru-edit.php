<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Edit Guru']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'guru']); ?>
<?php endif; ?>

<div class="max-w-5xl mx-auto px-6 py-8">
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
        <form method="post" action="<?= base_url('guru/edit/'.$guru->uuid);?>" id="guruForm">
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

            <!-- Mata Pelajaran & Kelas -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-semibold text-gray-700">Mata Pelajaran & Kelas <span
                            class="text-red-500">*</span></label>
                    <button type="button" id="tambahMapelBtn"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah Pelajaran
                    </button>
                </div>

                <!-- Container for selected mapel + kelas -->
                <div id="mapelKelasContainer" class="space-y-3">
                    <!-- Dynamically filled by JavaScript -->
                </div>

                <div id="mapelError" class="text-red-500 text-xs mt-1 hidden">
                    Pilih minimal satu mata pelajaran
                </div>
                <p class="text-xs text-gray-400 mt-2">Tambahkan mata pelajaran yang diampu, lalu klik "Tambah Kelas"
                    untuk memilih kelas yang diajar</p>
            </div>

            <!-- Hidden inputs container for form submission -->
            <div id="hiddenInputsContainer"></div>

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

<!-- Modal Tambah Mata Pelajaran -->
<div id="mapelModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div
            class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Pilih Mata Pelajaran</h3>
                    <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4 max-h-80 overflow-y-auto">
                <div class="space-y-2">
                    <?php foreach($mapel as $m): ?>
                    <label
                        class="mapel-option flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-blue-50 transition-colors <?= in_array($m->uuid, $mapel_list ?? []) ? 'bg-blue-50 border-blue-300' : '' ?>"
                        data-uuid="<?= $m->uuid; ?>" data-nama="<?= $m->nama; ?>">
                        <input type="checkbox"
                            class="mapel-option-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                            value="<?= $m->uuid; ?>" <?= in_array($m->uuid, $mapel_list ?? []) ? 'checked' : ''; ?>>
                        <span class="text-sm font-medium text-gray-900"><?= $m->nama; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button"
                    class="close-modal px-4 py-2 rounded-xl font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                    Batal
                </button>
                <button type="button" id="confirmMapelBtn"
                    class="px-4 py-2 rounded-xl font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 transition-all">
                    Tambahkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div id="kelasModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div
            class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900" id="kelasModalTitle">Pilih Kelas</h3>
                    <button type="button" class="close-kelas-modal text-gray-400 hover:text-gray-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4 max-h-80 overflow-y-auto">
                <div class="space-y-2" id="kelasModalList">
                    <?php foreach($kelas as $k): ?>
                    <label
                        class="kelas-option flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-green-50 transition-colors"
                        data-uuid="<?= $k->uuid; ?>" data-nama="<?= $k->nama; ?>">
                        <input type="checkbox"
                            class="kelas-option-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer"
                            value="<?= $k->uuid; ?>">
                        <span class="text-sm font-medium text-gray-900"><?= $k->nama; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button"
                    class="close-kelas-modal px-4 py-2 rounded-xl font-semibold text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                    Batal
                </button>
                <button type="button" id="confirmKelasBtn"
                    class="px-4 py-2 rounded-xl font-semibold text-sm text-white bg-green-600 hover:bg-green-700 transition-all">
                    Tambahkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var mapelData = <?= json_encode($mapel); ?>;
var kelasData = <?= json_encode($kelas); ?>;
var selectedMapels = []; // Array of {uuid, nama, kelas: [{uuid, nama}]}
var activeKelasMapelUuid = null; // Track which mapel we're adding classes to

// Pre-populate from existing data
var existingMapelList = <?= json_encode($mapel_list ?? []); ?>;
var existingKelasMap = <?= json_encode($kelas_map ?? []); ?>;

$(document).ready(function() {
    // Auto-generate username from nama lengkap
    $("#namaLengkap").change(function() {
        var namaLengkap = $(this).val().toLowerCase();
        var username = namaLengkap.replace(/\s+/g, '.');
        $('#username').val(username);
    });

    // Load existing data
    loadExistingData();

    // Open Tambah Mapel Modal
    $('#tambahMapelBtn').on('click', function() {
        // Update modal checkboxes to reflect current selections
        $('.mapel-option').each(function() {
            var uuid = $(this).data('uuid');
            var isSelected = selectedMapels.some(function(m) {
                return m.uuid === uuid;
            });
            $(this).find('.mapel-option-checkbox').prop('checked', isSelected);
            if (isSelected) {
                $(this).addClass('bg-blue-50 border-blue-300');
            } else {
                $(this).removeClass('bg-blue-50 border-blue-300');
            }
        });
        $('#mapelModal').removeClass('hidden');
    });

    // Close Modal
    $('.close-modal').on('click', function() {
        $('#mapelModal').addClass('hidden');
    });

    $('#mapelModal').on('click', function(e) {
        if (e.target === this) $('#mapelModal').addClass('hidden');
    });

    // Mapel option click - toggle checkbox
    $('.mapel-option').on('click', function(e) {
        if (e.target.type !== 'checkbox') {
            var checkbox = $(this).find('.mapel-option-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
        }
        var checkbox = $(this).find('.mapel-option-checkbox');
        if (checkbox.prop('checked')) {
            $(this).addClass('bg-blue-50 border-blue-300');
        } else {
            $(this).removeClass('bg-blue-50 border-blue-300');
        }
    });

    // Confirm Mapel Selection
    $('#confirmMapelBtn').on('click', function() {
        var newSelection = [];
        $('.mapel-option-checkbox:checked').each(function() {
            var uuid = $(this).val();
            newSelection.push(uuid);
        });

        // Remove mapels that are no longer selected
        selectedMapels = selectedMapels.filter(function(m) {
            return newSelection.indexOf(m.uuid) !== -1;
        });

        // Add new mapels
        newSelection.forEach(function(uuid) {
            if (!selectedMapels.some(function(m) {
                    return m.uuid === uuid;
                })) {
                var nama = '';
                mapelData.forEach(function(m) {
                    if (m.uuid === uuid) nama = m.nama;
                });
                selectedMapels.push({
                    uuid: uuid,
                    nama: nama,
                    kelas: []
                });
            }
        });

        renderMapelKelas();
        $('#mapelModal').addClass('hidden');
    });

    // Open Kelas Modal
    $(document).on('click', '.tambah-kelas-btn', function() {
        activeKelasMapelUuid = $(this).data('mapel-uuid');
        var mapelName = $(this).data('mapel-nama');
        $('#kelasModalTitle').text('Pilih Kelas untuk ' + mapelName);

        var currentMapel = selectedMapels.find(function(m) {
            return m.uuid === activeKelasMapelUuid;
        });
        var selectedKelasUuids = currentMapel ? currentMapel.kelas.map(function(k) {
            return k.uuid;
        }) : [];

        $('.kelas-option').each(function() {
            var uuid = $(this).data('uuid');
            var isChecked = selectedKelasUuids.indexOf(uuid) !== -1;
            $(this).find('.kelas-option-checkbox').prop('checked', isChecked);
            if (isChecked) {
                $(this).addClass('bg-green-50 border-green-300');
            } else {
                $(this).removeClass('bg-green-50 border-green-300');
            }
        });

        $('#kelasModal').removeClass('hidden');
    });

    // Close Kelas Modal
    $('.close-kelas-modal').on('click', function() {
        $('#kelasModal').addClass('hidden');
    });

    $('#kelasModal').on('click', function(e) {
        if (e.target === this) $('#kelasModal').addClass('hidden');
    });

    // Kelas option click
    $(document).on('click', '.kelas-option', function(e) {
        if (e.target.type !== 'checkbox') {
            var checkbox = $(this).find('.kelas-option-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked'));
        }
        var checkbox = $(this).find('.kelas-option-checkbox');
        if (checkbox.prop('checked')) {
            $(this).addClass('bg-green-50 border-green-300');
        } else {
            $(this).removeClass('bg-green-50 border-green-300');
        }
    });

    // Confirm Kelas Selection
    $('#confirmKelasBtn').on('click', function() {
        var selectedKelas = [];
        $('.kelas-option-checkbox:checked').each(function() {
            var uuid = $(this).val();
            var nama = $(this).closest('.kelas-option').data('nama');
            selectedKelas.push({
                uuid: uuid,
                nama: nama
            });
        });

        var mapelIndex = selectedMapels.findIndex(function(m) {
            return m.uuid === activeKelasMapelUuid;
        });
        if (mapelIndex !== -1) {
            selectedMapels[mapelIndex].kelas = selectedKelas;
        }

        renderMapelKelas();
        $('#kelasModal').addClass('hidden');
    });

    // Remove mapel
    $(document).on('click', '.remove-mapel-btn', function() {
        var uuid = $(this).data('mapel-uuid');
        selectedMapels = selectedMapels.filter(function(m) {
            return m.uuid !== uuid;
        });
        renderMapelKelas();
    });

    // Remove kelas from mapel
    $(document).on('click', '.remove-kelas-btn', function() {
        var mapelUuid = $(this).data('mapel-uuid');
        var kelasUuid = $(this).data('kelas-uuid');
        var mapelIndex = selectedMapels.findIndex(function(m) {
            return m.uuid === mapelUuid;
        });
        if (mapelIndex !== -1) {
            selectedMapels[mapelIndex].kelas = selectedMapels[mapelIndex].kelas.filter(function(k) {
                return k.uuid !== kelasUuid;
            });
        }
        renderMapelKelas();
    });

    // Form submit - build hidden inputs
    $('#guruForm').on('submit', function(e) {
        var hasMapel = selectedMapels.length > 0;
        if (!hasMapel) {
            e.preventDefault();
            $('#mapelError').removeClass('hidden');
            return;
        }
        $('#mapelError').addClass('hidden');

        $('#hiddenInputsContainer').empty();

        selectedMapels.forEach(function(m) {
            $('<input>').attr({
                type: 'hidden',
                name: 'namaMapel[]',
                value: m.uuid
            }).appendTo('#hiddenInputsContainer');

            m.kelas.forEach(function(k) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'kelasMapel[' + m.uuid + '][]',
                    value: k.uuid
                }).appendTo('#hiddenInputsContainer');
            });
        });
    });

    function loadExistingData() {
        existingMapelList.forEach(function(uuid) {
            var nama = '';
            mapelData.forEach(function(m) {
                if (m.uuid === uuid) nama = m.nama;
            });
            var kelasList = [];
            if (existingKelasMap[uuid]) {
                existingKelasMap[uuid].forEach(function(kelasUuid) {
                    var kelasNama = '';
                    kelasData.forEach(function(k) {
                        if (k.uuid === kelasUuid) kelasNama = k.nama;
                    });
                    kelasList.push({
                        uuid: kelasUuid,
                        nama: kelasNama
                    });
                });
            }
            selectedMapels.push({
                uuid: uuid,
                nama: nama,
                kelas: kelasList
            });
        });
        renderMapelKelas();
    }

    function renderMapelKelas() {
        var container = $('#mapelKelasContainer');
        container.empty();

        if (selectedMapels.length === 0) {
            container.html(
                '<p class="text-sm text-gray-400 italic py-4 text-center border-2 border-dashed border-gray-200 rounded-xl">Belum ada mata pelajaran dipilih. Klik "Tambah Pelajaran" untuk menambahkan.</p>'
            );
            return;
        }

        selectedMapels.forEach(function(m) {
            var card = $('<div>').addClass(
                'bg-white rounded-xl border border-gray-200 overflow-hidden');

            var header = $('<div>').addClass(
                'flex items-center justify-between px-5 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100'
            );
            header.append(
                '<div class="flex items-center gap-2"><div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center"><i data-lucide="book" class="w-4 h-4 text-blue-600"></i></div><span class="font-semibold text-gray-900">' +
                m.nama + '</span></div>');

            var headerActions = $('<div>').addClass('flex items-center gap-2');
            var tambahKelasBtn = $('<button>').addClass(
                    'tambah-kelas-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-green-700 bg-green-50 border border-green-200 hover:bg-green-100 transition-all'
                )
                .attr('type', 'button')
                .attr('data-mapel-uuid', m.uuid)
                .attr('data-mapel-nama', m.nama)
                .html('<i data-lucide="plus" class="w-3 h-3"></i> Tambah Kelas');

            var removeBtn = $('<button>').addClass(
                    'remove-mapel-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-all'
                )
                .attr('type', 'button')
                .attr('data-mapel-uuid', m.uuid)
                .html('<i data-lucide="trash-2" class="w-3 h-3"></i> Hapus');

            headerActions.append(tambahKelasBtn, removeBtn);
            header.append(headerActions);
            card.append(header);

            var body = $('<div>').addClass('px-5 py-3');
            if (m.kelas.length > 0) {
                var kelasWrap = $('<div>').addClass('flex flex-wrap gap-2');
                m.kelas.forEach(function(k) {
                    var kelasBadge = $('<span>').addClass(
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200'
                    );
                    kelasBadge.append('<i data-lucide="users" class="w-3 h-3"></i>');
                    kelasBadge.append('<span>' + k.nama + '</span>');

                    var removeKelasBtn = $('<button>').addClass(
                            'remove-kelas-btn text-green-400 hover:text-red-500 transition-colors'
                        )
                        .attr('type', 'button')
                        .attr('data-mapel-uuid', m.uuid)
                        .attr('data-kelas-uuid', k.uuid)
                        .html('<i data-lucide="x" class="w-3 h-3"></i>');
                    kelasBadge.append(removeKelasBtn);
                    kelasWrap.append(kelasBadge);
                });
                body.append(kelasWrap);
            } else {
                body.append(
                    '<p class="text-sm text-gray-400 italic">Belum ada kelas. Klik "Tambah Kelas" untuk menambahkan.</p>'
                );
            }
            card.append(body);
            container.append(card);
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
});
</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>