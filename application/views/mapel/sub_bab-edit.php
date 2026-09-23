<?php if(!isset($from_controller)): ?>
<?php $this->load->view('partials/header_tailwind', ['title' => 'Edit Bab']); ?>
<?php $this->load->view('partials/navbar', ['active_nav' => 'materi']); ?>
<?php endif; ?>

<!-- Quill Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

<div class="max-w-3xl mx-auto px-6 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= base_url('sub_bab/index/' . $materi->uuid) ?>"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Edit Bab</h1>
            </div>
            <p class="text-gray-500 text-sm ml-8">Materi: <span
                    class="text-blue-600 font-semibold"><?= $materi->judul ?></span></p>
        </div>
    </div>

    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <?= $this->session->flashdata('error_msg'); ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 table-shadow">
        <?= form_open_multipart('sub_bab/edit/' . $bab->uuid); ?>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Sub Bab <span
                        class="text-red-500">*</span></label>
                <input type="text" name="judul" id="judul"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                    placeholder="Masukkan Judul Sub Bab" value="<?= set_value('judul', $bab->judul); ?>">
                <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('judul')) ? '' : 'hidden' ?>">
                    <?= form_error('judul') ?>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <div id="editor" style="height: 200px;"><?= set_value('deskripsi', $bab->deskripsi, FALSE); ?></div>
                <input type="hidden" name="deskripsi" id="deskripsi"
                    value="<?= form_prep(set_value('deskripsi', $bab->deskripsi, FALSE)); ?>">
                <small class="text-gray-400 text-xs mt-1 block">Gunakan toolbar di atas untuk formatting teks</small>
            </div>

            <!-- Dokumentasi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dokumentasi</label>
                <?php if (!empty($bab->dokumentasi)): ?>
                <p class="text-xs text-gray-400 mb-2">File saat ini:
                    <a href="<?= base_url('uploads/dokumentasi/' . $bab->dokumentasi) ?>" target="_blank"
                        class="text-blue-600 hover:underline"><?= $bab->dokumentasi ?></a>.
                    Kosongkan jika tidak ingin mengubah file.
                </p>
                <?php endif; ?>
                <input type="file" name="dokumentasi" id="dokumentasi"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                <small class="text-gray-400 text-xs">format file : PDF, DOCX, PPTX. Maximal 50Mb</small>
                <div class="text-red-500 text-xs mt-1 <?= !empty(form_error('dokumentasi')) ? '' : 'hidden' ?>">
                    <?= form_error('dokumentasi') ?>
                </div>
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1.5">Atau masukkan link dokumentasi</label>
                    <input type="url" name="dokumentasi_link" id="dokumentasi_link"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm placeholder:text-gray-400"
                        placeholder="https://docs.google.com/..."
                        value="<?= set_value('dokumentasi_link', $bab->dokumentasi_link); ?>">
                    <small class="text-gray-400 text-xs">Contoh: Google Drive, Google Docs, atau link lain</small>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all text-sm">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan
            </button>
            <a href="<?= base_url('sub_bab/index/' . $materi->uuid) ?>"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all text-sm">
                <i data-lucide="x" class="w-4 h-4"></i> Batal
            </a>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
// Endpoint upload gambar deskripsi
var quillUploadUrl = '<?= base_url('sub_bab/upload_gambar') ?>';

// Kirim gambar ke server, lalu URL-nya yang disisipkan ke editor.
// Default Quill menyimpan gambar sebagai base64 (data:image/...) sehingga
// melebihi kapasitas kolom `deskripsi` (TEXT) dan gambar gagal tersimpan.
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
            })
            .catch(function() {
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
    placeholder: 'Masukkan deskripsi bab...'
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
    deskripsiInput.value = quill.root.innerHTML;

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
            deskripsiInput.value = quill.root.innerHTML;
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