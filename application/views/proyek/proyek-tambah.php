<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Tambah Proyek']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'proyek']); ?>
<?php endif; ?>

<!-- Quill Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Colorful Header -->
    <div
        class="relative bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 md:p-8 mb-8 text-white overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('proyek')?>"
                    class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center hover:bg-white/30 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-white"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Tambah Proyek</h1>
                    <p class="text-violet-100 text-sm mt-1">Buat proyek pembelajaran baru</p>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 right-20 w-32 h-32 bg-white/5 rounded-full -mb-10"></div>
    </div>

    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <form class="user" method="post" enctype="multipart/form-data" action="<?= base_url('proyek/tambah');?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="Masukkan Judul proyek" value="<?= set_value('judul'); ?>">
                    <div class="text-red-500 text-xs mt-1"><?= form_error('judul') ?></div>
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran <span
                            class="text-red-500">*</span></label>
                    <select name="namaMapel"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm bg-white">
                        <option disabled selected>Pilih Mata Pelajaran</option>
                        <?php 
                        foreach($mapel as $val){
                        ?>
                        <option value="<?= $val->uuid; ?>" <?= set_select('namaMapel', $val->uuid) ;?>>
                            <?= $val->nama; ?>
                        </option>
                        <?php 
                        }
                        ?>
                    </select>
                    <div class="text-red-500 text-xs mt-1"><?= form_error('namaMapel') ?></div>
                </div>
            </div>

            <!-- File Upload -->
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File Proyek <span
                        class="text-red-500">*</span></label>
                <input type="file" name="berkas" id="berkas"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                    placeholder="Masukkan materi Materi" value="<?= set_value('berkas'); ?>">
                <p class="text-xs text-gray-500 mt-1">File dapat berupa dokumen, foto. Maksimal 50 Mb</p>
                <div class="text-red-500 text-xs mt-1"><?= form_error('berkas') ?></div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <div id="editor" style="height: 200px;"><?= set_value('deskripsi', '', FALSE); ?></div>
                <input type="hidden" name="deskripsi" id="deskripsi"
                    value="<?= form_prep(set_value('deskripsi', '', FALSE)); ?>">
                <small class="text-gray-400 text-xs mt-1 block">Gunakan toolbar di atas untuk formatting teks</small>
                <div class="text-red-500 text-xs mt-1"><?= form_error('deskripsi') ?></div>
            </div>

            <!-- Tanggal -->
            <div class="grid md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_mulai" id="tgl_mulai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        value="<?= set_value('tgl_mulai'); ?>">
                    <div class="text-red-500 text-xs mt-1"><?= form_error('tgl_mulai') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tgl_selesai" id="tgl_selesai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm"
                        value="<?= set_value('tgl_selesai'); ?>">
                    <div class="text-red-500 text-xs mt-1"><?= form_error('tgl_selesai') ?></div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit" name="action" value="simpan"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
                <button type="submit" name="action" value="simpan_detail"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-green-600 shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl transition-all text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan dan Detail
                </button>
                <a href="<?= base_url('proyek')?>"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
// Endpoint upload gambar deskripsi
var quillUploadUrl = '<?= base_url('proyek/upload_gambar') ?>';

// Kirim gambar ke server, lalu URL-nya yang disisipkan ke editor.
// Default Quill menyimpan gambar sebagai base64 (data:image/...) sehingga
// gambar cepat memenuhi kapasitas kolom `deskripsi` (TEXT) dan gagal tersimpan.
function uploadGambarDeskripsi(file) {
    return new Promise(function(resolve, reject) {
        var formData = new FormData();
        formData.append('upload', file);

        fetch(quillUploadUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data && data.success && data.url) {
                    resolve(data.url);
                } else {
                    reject(new Error((data && data.message) ? data.message : 'Terjadi kesalahan pada server'));
                }
            }, function() {
                reject(new Error('Gagal terhubung ke server'));
            });
    });
}

function sisipkanGambarDeskripsi(file, range) {
    if (!file) {
        return;
    }
    if (file.type.indexOf('image/') !== 0) {
        alert('File yang dipilih bukan gambar.');
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran gambar maksimal 10MB.');
        return;
    }

    var index = (range && typeof range.index === 'number') ? range.index : quill.getLength();

    uploadGambarDeskripsi(file)
        .then(function(url) {
            quill.insertEmbed(index, 'image', url);
            quill.setSelection(index + 1, 0);
        })
        .catch(function(error) {
            alert('Gagal mengunggah gambar: ' + error.message);
        });
}

function pilihGambarDeskripsi() {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.addEventListener('change', function() {
        var file = input.files && input.files[0];
        if (file) {
            sisipkanGambarDeskripsi(file, quill.getSelection(true));
        }
    });
}

var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{
                'header': [1, 2, 3, false]
            }],
            ['bold', 'italic', 'underline', 'strike'],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            ['link', 'image'],
            ['clean']
        ],
        uploader: {
            mimetypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'],
            handler: function(range, files) {
                sisipkanGambarDeskripsi(files && files[0], range);
            }
        }
    },
    placeholder: 'Masukkan deskripsi proyek...'
});

// Timpa handler bawaan tombol gambar supaya gambar diunggah ke server
quill.getModule('toolbar').addHandler('image', pilihGambarDeskripsi);

// Ubah gambar base64 (mis. hasil paste HTML / gambar lama) menjadi file yang diunggah ke server
function dataUriKeFile(src, nama) {
    var bagian = /^data:([^;]+);base64,(.*)$/.exec(src);
    if (!bagian) {
        return null;
    }

    try {
        var biner = window.atob(bagian[2]);
    } catch (e) {
        return null;
    }

    var bytes = new Uint8Array(biner.length);
    for (var i = 0; i < biner.length; i++) {
        bytes[i] = biner.charCodeAt(i);
    }
    var ekstensi = (bagian[1].split('/')[1] || 'png').replace('jpeg', 'jpg');

    return new File([bytes], nama + '.' + ekstensi, {
        type: bagian[1]
    });
}

function unggahGambarBase64() {
    var gambar = Array.prototype.slice.call(quill.root.querySelectorAll('img')).filter(function(img) {
        return (img.getAttribute('src') || '').indexOf('data:image') === 0;
    });

    if (gambar.length === 0) {
        return Promise.resolve({
            dibuang: 0
        });
    }

    var dibuang = 0;

    return gambar.reduce(function(rantai, img, urutan) {
        return rantai.then(function() {
            var file = dataUriKeFile(img.getAttribute('src'), 'gambar-' + (urutan + 1));

            if (!file) {
                // Base64 tidak valid / gambar lama yang sudah terpotong
                img.parentNode.removeChild(img);
                dibuang++;
                return;
            }

            return uploadGambarDeskripsi(file).then(function(url) {
                img.setAttribute('src', url);
            });
        });
    }, Promise.resolve()).then(function() {
        return {
            dibuang: dibuang
        };
    });
}

var formDeskripsi = document.querySelector('form');
var deskripsiInput = document.getElementById('deskripsi');
var sedangKirim = false;

formDeskripsi.addEventListener('submit', function(e) {
    // Quill 2.x: pakai getSemanticHTML() agar heading (<h1>-<h3>) dan format
    // lain diekspor sebagai HTML semantik yang valid, bukan markup DOM internal Quill.
    deskripsiInput.value = quill.getSemanticHTML();

    if (sedangKirim || deskripsiInput.value.indexOf('data:image') === -1) {
        return;
    }

    // Masih ada gambar base64: unggah dulu agar tidak terpotong saat disimpan
    var pengirim = e.submitter;
    e.preventDefault();

    unggahGambarBase64()
        .then(function(hasil) {
            if (hasil.dibuang > 0) {
                alert(hasil.dibuang + ' gambar lama yang rusak telah dihapus dari deskripsi karena tidak dapat disimpan. Silakan masukkan kembali gambar tersebut.');
            }
            // Ekspor HTML semantik setelah gambar base64 selesai di-upload
            deskripsiInput.value = quill.getSemanticHTML();
            sedangKirim = true;
            if (pengirim && typeof pengirim.click === 'function') {
                pengirim.click();
            } else {
                formDeskripsi.submit();
            }
        })
        .catch(function(error) {
            alert('Gambar gagal disimpan: ' + error.message + '. Silakan coba simpan lagi.');
        });
});

</script>

<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/footer_tailwind'); ?>
<?php endif; ?>