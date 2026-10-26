<?php
$menu_key = $active_nav ?? '';
$route = trim($this->uri->uri_string(), '/');

if ($route === 'admin/manage_roles') {
    $menu_key = 'roles';
} elseif ($route === 'admin/manage_permissions') {
    $menu_key = 'permissions';
} elseif ($route === 'ganti_password') {
    $menu_key = 'password';
} elseif ($route === 'periode_akademik') {
    $menu_key = 'periode_akademik';
}

$role = is_superadmin() ? 'admin' : user_role();
$role_labels = [
    'siswa' => 'Siswa',
    'guru' => 'Guru',
    'kepala_sekolah' => 'Kepala Sekolah',
    'admin' => is_superadmin() ? 'Admin (Superadmin)' : 'Admin',
];
$role_label = $role_labels[$role] ?? 'Pengguna';

$guides = [
    'home' => [
        'title' => 'Beranda',
        'description' => 'Ringkasan dan pintasan kegiatan belajar di SMAGAEDU.',
        'notes' => [
            'siswa' => 'Gunakan pintasan untuk membuka materi, tugas, dan ujian yang perlu Anda ikuti.',
            'guru' => 'Gunakan pintasan untuk menuju kelas, materi, tugas, dan aktivitas pembelajaran.',
            'kepala_sekolah' => 'Gunakan ringkasan untuk memantau aktivitas pembelajaran dan membuka data guru.',
            'admin' => 'Gunakan menu utama untuk membuka fitur dan data yang perlu dikelola.',
        ],
        'steps' => ['Periksa pengingat dan informasi terbaru.', 'Pilih menu sesuai kegiatan yang ingin dilakukan.', 'Gunakan navigasi utama untuk kembali ke fitur lain.'],
    ],
    'panduan' => [
        'title' => 'Panduan',
        'description' => 'Dokumen bantuan yang tersedia untuk role Anda.',
        'notes' => [
            'siswa' => 'Buka dokumen yang ditujukan untuk siswa sebelum menggunakan fitur pembelajaran.',
            'guru' => 'Gunakan dokumen untuk melihat petunjuk pengelolaan kegiatan belajar.',
            'kepala_sekolah' => 'Gunakan dokumen sebagai referensi saat meninjau fitur sekolah.',
            'admin' => 'Kelola dokumen panduan dan periksa kesesuaian tujuan aksesnya.',
        ],
        'steps' => ['Cari judul panduan yang dibutuhkan.', 'Pilih tombol untuk membuka atau mengunduh berkas.', 'Jika Anda berwenang mengelola panduan, gunakan tambah, edit, atau hapus dengan teliti.'],
    ],
    'materi' => [
        'title' => 'Mata Pelajaran',
        'description' => 'Materi pembelajaran, bab, dan subbab mata pelajaran.',
        'notes' => [
            'siswa' => 'Pilih mata pelajaran dan buka bab atau subbab untuk mempelajari materi.',
            'guru' => 'Pilih mata pelajaran yang diajar untuk mengelola materi dan pertemuannya.',
            'kepala_sekolah' => 'Tinjau struktur materi yang tersedia sebagai referensi pembelajaran.',
            'admin' => 'Pastikan data mata pelajaran dan susunan materinya tertata dengan benar.',
        ],
        'steps' => ['Pilih mata pelajaran yang ingin dibuka.', 'Telusuri semester, bab, lalu subbab yang tersedia.', 'Gunakan aksi pada halaman untuk membaca atau mengelola materi sesuai hak akses.'],
        'role_steps' => [
            'guru' => [
                'Pilih mata pelajaran, semester, dan pekan yang ingin disiapkan.',
                'Gunakan Isi Pekan untuk menambahkan materi pekan, lalu Tambah Pertemuan untuk mengisi materi pertemuan.',
                'Saat menambah pertemuan, centang Buat Ujian Sekaligus untuk membuat ujian yang otomatis terhubung. Untuk pertemuan yang sudah ada, pilih Tambah Ujian; tugas juga dapat ditambahkan dari detail pertemuan.',
            ],
            'admin' => [
                'Pilih mata pelajaran, semester, dan pekan yang ingin ditinjau.',
                'Jika memiliki izin, gunakan Isi Pekan dan Tambah Pertemuan untuk mengelola materi.',
                'Ujian dapat dibuat sekaligus saat menambah pertemuan atau melalui Tambah Ujian pada pertemuan yang sudah ada. Tugas juga dapat dibuat dari detail pertemuan.',
            ],
        ],
        'related' => [
            'siswa' => 'Materi disusun per semester dan pekan. Buka pertemuan untuk mempelajari materi serta melihat tugas atau ujian yang terkait; pembuatan aktivitas tersebut dilakukan oleh guru.',
            'guru' => 'Ujian dan tugas tidak hanya dibuat dari menu masing-masing. Dari Mata Pelajaran, buka semester → pekan → pertemuan. Saat menambah pertemuan, centang Buat Ujian Sekaligus; dari daftar pertemuan yang sudah ada, gunakan Tambah Ujian atau Tambah Tugas.',
            'kepala_sekolah' => 'Guru dapat mengaitkan ujian dan tugas dengan materi pekanan melalui pertemuan. Ujian bisa dibuat bersamaan saat menambahkan pertemuan, sedangkan tugas dan ujian juga dapat ditambahkan dari detail pertemuan.',
            'admin' => 'Jika memiliki izin, alur dari Mata Pelajaran juga tersedia: buka semester → pekan → pertemuan. Centang Buat Ujian Sekaligus saat membuat pertemuan, atau gunakan Tambah Ujian / Tambah Tugas pada pertemuan.',
        ],
    ],
    'perangkat' => [
        'title' => 'Perangkat',
        'description' => 'Dokumen perangkat pembelajaran per mata pelajaran.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Pilih mata pelajaran untuk mengunggah atau memperbarui perangkat pembelajaran.',
            'kepala_sekolah' => 'Buka perangkat mata pelajaran untuk meninjau kelengkapan dokumen.',
            'admin' => 'Gunakan menu ini untuk melihat dan membantu penataan dokumen perangkat.',
        ],
        'steps' => ['Pilih mata pelajaran yang sesuai.', 'Buka detail perangkat yang ingin diperiksa.', 'Unggah atau perbarui dokumen hanya jika tombol pengelolaan tersedia.'],
    ],
    'proyek' => [
        'title' => 'Proyek',
        'description' => 'Daftar proyek, kelompok, dan aktivitas proyek.',
        'notes' => [
            'siswa' => 'Buka proyek yang ditugaskan, periksa instruksi, dan ikuti pembagian kelompok.',
            'guru' => 'Buat proyek, atur peserta atau kelompok, lalu pantau progres dan hasilnya.',
            'kepala_sekolah' => 'Tinjau pelaksanaan proyek dan hasil pembelajaran yang tersedia.',
            'admin' => 'Bantu memantau penggunaan menu proyek sesuai hak akses yang diberikan.',
        ],
        'steps' => ['Pilih proyek untuk melihat informasi dan tenggatnya.', 'Ikuti aksi yang tersedia pada detail proyek.', 'Periksa kembali kelompok dan berkas sebelum menyimpan perubahan.'],
        'related' => [
            'guru' => 'Proyek dibuat dari menu Proyek melalui Tambah Data. Pada formulir, pilih mata pelajaran, lengkapi berkas, deskripsi, dan tanggal mulai serta selesai.',
            'admin' => 'Proyek dibuat dari menu Proyek melalui Tambah Data jika akun Anda memiliki izin. Pilih mata pelajaran dan lengkapi informasi serta jadwal proyek.',
        ],
    ],
    'ujian' => [
        'title' => 'Ujian',
        'description' => 'Pengelolaan ujian, pengerjaan, peserta, dan nilai.',
        'notes' => [
            'siswa' => 'Periksa jadwal dan petunjuk ujian, lalu mulai ujian yang sudah dibuka.',
            'guru' => 'Siapkan ujian dan soal, atur peserta, kemudian tinjau hasil pengerjaan.',
            'kepala_sekolah' => 'Pantau jadwal serta ringkasan hasil ujian yang dapat diakses.',
            'admin' => 'Gunakan fitur ujian sesuai izin; verifikasi pengaturan sebelum ujian berlangsung.',
        ],
        'steps' => ['Pilih ujian yang ingin dibuka.', 'Baca keterangan, jadwal, dan status ujian.', 'Gunakan menu soal, peserta, atau nilai sesuai kewenangan Anda.'],
        'related' => [
            'guru' => 'Ujian juga dapat dibuat dari menu Mata Pelajaran: buka pekan dan pertemuan, lalu pilih Tambah Ujian. Saat membuat pertemuan baru, centang Buat Ujian Sekaligus; setelah tersimpan, Anda dapat melanjutkan menambahkan soal.',
            'admin' => 'Jika memiliki izin, ujian dapat dibuat dari menu Mata Pelajaran melalui pertemuan dengan memilih Tambah Ujian, atau saat membuat pertemuan dengan mencentang Buat Ujian Sekaligus.',
            'siswa' => 'Guru dapat membuat ujian dari menu Ujian maupun dari menu Mata Pelajaran pada pekan dan pertemuan. Periksa jadwal ujian yang sudah dibuka sebelum mulai mengerjakan.',
            'kepala_sekolah' => 'Ujian dapat dibuat guru dari menu Ujian maupun dari menu Mata Pelajaran pada pekan dan pertemuan. Gunakan daftar ini untuk meninjau ujian yang tersedia.',
        ],
    ],
    'tugas' => [
        'title' => 'Tugas',
        'description' => 'Panduan menggunakan daftar tugas sesuai role Anda.',
        'notes' => [
            'siswa' => 'Periksa judul, mata pelajaran, serta batas waktu; kerjakan tugas dan simpan jawaban.',
            'guru' => 'Buat tugas, tambahkan soal, lalu pantau jawaban siswa melalui pengumpulan.',
            'kepala_sekolah' => 'Gunakan daftar tugas untuk meninjau aktivitas pembelajaran yang tersedia.',
            'admin' => 'Bantu pengelolaan tugas sesuai hak akses; perhatikan bahwa tugas dengan jawaban terkumpul memiliki batas perubahan.',
        ],
        'steps' => ['Pilih tugas dan periksa informasi serta tenggatnya.', 'Lakukan aksi yang tersedia: mengerjakan, membuat soal, atau membuka pengumpulan.', 'Pastikan jawaban atau perubahan berhasil tersimpan sebelum meninggalkan halaman.'],
        'related' => [
            'guru' => 'Selain dari menu Tugas, tugas dapat dibuat langsung dari detail pertemuan di menu Mata Pelajaran melalui Tambah Tugas.',
            'admin' => 'Jika memiliki izin, tugas juga dapat dibuat dari detail pertemuan di menu Mata Pelajaran melalui Tambah Tugas.',
            'siswa' => 'Tugas dapat ditautkan ke pertemuan pada menu Mata Pelajaran. Buka pertemuan untuk membaca materi dan menemukan tugas terkait.',
        ],
        'role_steps' => [
            'siswa' => [
                'Periksa judul, mata pelajaran, dan batas waktu. Tugas baru atau yang belum dikerjakan ditandai pada Informasi Tugas.',
                'Pilih Kerjakan Tugas, isi jawaban, lalu simpan.',
                'Buka Lihat Jawaban untuk memeriksa tugas yang sudah dikumpulkan; jawaban yang terkumpul tidak dapat diubah.',
            ],
            'guru' => [
                'Pilih Tambah Tugas, tentukan mata pelajaran, judul, dan periode. Tugas juga dapat dibuat dari detail pertemuan.',
                'Buka Soal untuk menambahkan soal satu per satu atau mengimpor berkas Excel sebelum siswa mulai mengerjakan.',
                'Buka Pengumpulan untuk meninjau jawaban. Tugas yang sudah memiliki jawaban tidak dapat diedit, dihapus, atau ditambah soal.',
            ],
            'kepala_sekolah' => [
                'Pilih tugas yang ingin ditinjau dan periksa mata pelajaran serta tenggatnya.',
                'Buka informasi atau pengumpulan yang tersedia untuk melihat aktivitas siswa.',
                'Gunakan daftar tugas untuk kembali dan meninjau tugas lainnya.',
            ],
            'admin' => [
                'Pilih tugas dan periksa mata pelajaran serta periode yang digunakan.',
                'Jika memiliki izin, gunakan Tambah Tugas atau buka Soal dan Pengumpulan sesuai kebutuhan.',
                'Tugas yang sudah memiliki jawaban terkumpul tidak dapat diedit, dihapus, atau ditambah soal.',
            ],
        ],
    ],
    'kalender' => [
        'title' => 'Kalender',
        'description' => 'Jadwal dan agenda kegiatan akademik.',
        'notes' => [
            'siswa' => 'Periksa agenda untuk mengetahui jadwal kegiatan belajar.',
            'guru' => 'Gunakan kalender untuk melihat agenda dan menyiapkan kegiatan mengajar.',
            'kepala_sekolah' => 'Pantau agenda akademik untuk membantu koordinasi kegiatan sekolah.',
            'admin' => 'Periksa agenda yang tercatat dan lakukan perubahan hanya jika tersedia.',
        ],
        'steps' => ['Pilih tampilan atau periode kalender.', 'Buka agenda untuk membaca rincian kegiatan.', 'Perhatikan tanggal dan waktu agar tidak melewatkan kegiatan.'],
    ],
    'jadwal' => [
        'title' => 'Jadwal',
        'description' => 'Jadwal mengajar dan berkas jadwal guru.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Periksa jadwal mengajar dan unggah berkas jadwal jika diminta.',
            'kepala_sekolah' => 'Menu ini tidak tersedia untuk role kepala sekolah.',
            'admin' => 'Buka jadwal guru yang perlu diperiksa dan gunakan unggah sesuai kebutuhan.',
        ],
        'steps' => ['Periksa jadwal yang ditampilkan.', 'Gunakan tombol unggah untuk memilih berkas jadwal yang benar.', 'Pastikan berkas dan informasi jadwal sesuai sebelum mengirim.'],
    ],
    'kepala_sekolah' => [
        'title' => 'Data Guru',
        'description' => 'Informasi guru dan ringkasan aktivitas pembelajaran.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Pilih guru untuk melihat ringkasan aktivitas, materi, atau hasil ujian yang tersedia.',
            'admin' => 'Gunakan daftar ini untuk membantu pemantauan data dan aktivitas guru.',
        ],
        'steps' => ['Cari atau pilih guru pada daftar.', 'Buka detail untuk melihat informasi yang tersedia.', 'Gunakan kembali daftar untuk berpindah ke guru lain.'],
    ],
    'guru' => [
        'title' => 'Data Guru',
        'description' => 'Data akun guru dan pengaturan penugasannya.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Kelola data guru hanya jika tombol pengelolaan tersedia untuk akun Anda.',
            'admin' => 'Tambah, ubah, impor, atau atur penugasan guru; periksa data sebelum menyimpan.',
        ],
        'steps' => ['Gunakan pencarian atau filter untuk menemukan data.', 'Pilih tambah, edit, atau impor sesuai kebutuhan.', 'Periksa kembali data guru dan penugasannya sebelum menyimpan.'],
    ],
    'siswa' => [
        'title' => 'Data Siswa',
        'description' => 'Data akun siswa dan penempatan kelas.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Tinjau data siswa jika fitur ini tersedia untuk akun Anda.',
            'admin' => 'Kelola data siswa, kelas, atau impor data; periksa format berkas sebelum memproses.',
        ],
        'steps' => ['Cari siswa berdasarkan informasi yang tersedia.', 'Pilih tambah, edit, atau impor untuk mengelola data.', 'Pastikan kelas dan identitas siswa benar sebelum menyimpan.'],
    ],
    'kelas' => [
        'title' => 'Data Kelas',
        'description' => 'Daftar dan pengaturan kelas.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Tinjau daftar kelas jika fitur ini tersedia untuk akun Anda.',
            'admin' => 'Kelola kelas dan periksa kembali nama serta penempatan siswa sebelum menyimpan.',
        ],
        'steps' => ['Cari kelas yang ingin dikelola.', 'Gunakan tambah atau edit untuk memperbarui data.', 'Pastikan perubahan kelas sesuai dengan data akademik.'],
    ],
    'mapel' => [
        'title' => 'Data Mata Pelajaran',
        'description' => 'Data referensi mata pelajaran.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Tinjau data mata pelajaran jika fitur ini tersedia untuk akun Anda.',
            'admin' => 'Kelola daftar mata pelajaran dan impor data dengan memeriksa format serta isinya.',
        ],
        'steps' => ['Cari mata pelajaran pada daftar.', 'Pilih tambah, edit, atau impor sesuai kebutuhan.', 'Pastikan nama dan data mata pelajaran benar sebelum menyimpan.'],
    ],
    'kepala_sekolah_admin' => [
        'title' => 'Data Kepala Sekolah',
        'description' => 'Pengelolaan akun kepala sekolah.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Menu ini tidak tersedia untuk role kepala sekolah.',
            'admin' => 'Kelola akun kepala sekolah dan pastikan informasi akun sudah benar.',
        ],
        'steps' => ['Cari akun yang ingin dikelola.', 'Gunakan tambah atau edit untuk memperbarui data.', 'Periksa identitas dan status akun sebelum menyimpan.'],
    ],
    'admins' => [
        'title' => 'Data Admin',
        'description' => 'Pengelolaan akun admin.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Menu ini tidak tersedia untuk role kepala sekolah.',
            'admin' => 'Kelola akun admin yang berada dalam cakupan akses akun Anda.',
        ],
        'steps' => ['Cari akun admin pada daftar.', 'Pilih tambah atau edit sesuai kebutuhan.', 'Pastikan informasi akun dan hak aksesnya tepat sebelum menyimpan.'],
    ],
    'roles' => [
        'title' => 'Kelola Role',
        'description' => 'Pengaturan role dan pemetaan akses pengguna.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Menu ini tidak tersedia untuk role kepala sekolah.',
            'admin' => 'Perubahan role berdampak pada akses pengguna; lakukan hanya jika memahami konsekuensinya.',
        ],
        'steps' => ['Tinjau daftar role yang tersedia.', 'Periksa perubahan yang akan dilakukan.', 'Simpan perubahan setelah memastikan akses yang diberikan sesuai.'],
    ],
    'permissions' => [
        'title' => 'Kelola Permission',
        'description' => 'Pengaturan izin fitur untuk setiap role.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Menu ini tidak tersedia untuk role kepala sekolah.',
            'admin' => 'Perubahan permission dapat membuka atau membatasi fitur; tinjau dampaknya sebelum menyimpan.',
        ],
        'steps' => ['Pilih role atau permission yang ingin ditinjau.', 'Sesuaikan izin seperlunya berdasarkan kebutuhan akses.', 'Simpan dan pastikan fitur yang diizinkan sesuai.'],
    ],
    'settings' => [
        'title' => 'Pengaturan',
        'description' => 'Pengaturan dan konfigurasi aplikasi.',
        'notes' => [
            'siswa' => 'Menu ini tidak tersedia untuk role siswa.',
            'guru' => 'Menu ini tidak tersedia untuk role guru.',
            'kepala_sekolah' => 'Menu ini tidak tersedia untuk role kepala sekolah.',
            'admin' => 'Periksa nilai konfigurasi sebelum mengubahnya karena dapat memengaruhi penggunaan aplikasi.',
        ],
        'steps' => ['Tinjau bagian pengaturan yang tersedia.', 'Ubah nilai yang memang perlu disesuaikan.', 'Simpan perubahan dan pastikan konfigurasi tetap benar.'],
    ],
    'password' => [
        'title' => 'Ganti Password',
        'description' => 'Perbarui kata sandi akun Anda.',
        'notes' => [
            'siswa' => 'Gunakan kata sandi baru yang kuat dan jangan membagikannya kepada orang lain.',
            'guru' => 'Gunakan kata sandi baru yang kuat dan jangan membagikannya kepada orang lain.',
            'kepala_sekolah' => 'Gunakan kata sandi baru yang kuat dan jangan membagikannya kepada orang lain.',
            'admin' => 'Gunakan kata sandi baru yang kuat dan jangan membagikannya kepada orang lain.',
        ],
        'steps' => ['Masukkan kata sandi saat ini dan kata sandi baru.', 'Pastikan konfirmasi kata sandi sama.', 'Simpan perubahan lalu gunakan kata sandi baru saat masuk berikutnya.'],
    ],
    'periode_akademik' => [
        'title' => 'Periode Akademik',
        'description' => 'Informasi periode dan pekan pembelajaran aktif.',
        'notes' => [
            'siswa' => 'Periksa pekan dan semester aktif sebagai acuan kegiatan belajar.',
            'guru' => 'Gunakan informasi periode aktif untuk menyelaraskan jadwal dan kegiatan belajar.',
            'kepala_sekolah' => 'Gunakan informasi periode untuk memantau pelaksanaan kegiatan akademik.',
            'admin' => 'Periksa periode aktif dan ubah konfigurasi hanya bila diperlukan.',
        ],
        'steps' => ['Periksa semester dan pekan yang sedang berjalan.', 'Baca informasi periode sebelum menyesuaikan kegiatan.', 'Jika pengaturan tersedia, pastikan perubahan sesuai kalender akademik.'],
    ],
];

$guide = $guides[$menu_key] ?? [
    'title' => 'Menu',
    'description' => 'Petunjuk singkat menggunakan halaman ini.',
    'notes' => [],
    'steps' => ['Periksa informasi yang ditampilkan.', 'Gunakan tombol aksi sesuai kebutuhan dan hak akses.', 'Pastikan perubahan berhasil tersimpan.'],
];
$role_note = $guide['notes'][$role] ?? 'Gunakan fitur yang tersedia sesuai hak akses role Anda.';
$guide_steps = $guide['role_steps'][$role] ?? $guide['steps'];
$related_note = $guide['related'][$role] ?? null;
$button_style = ($button_style ?? '') === 'hero'
    ? 'bg-white/15 border border-white/30 text-white hover:bg-white/25'
    : 'bg-violet-600 text-white shadow-sm hover:bg-violet-700';
?>

<button type="button" id="openMenuGuide" aria-haspopup="dialog" aria-controls="menuGuideModal"
    aria-label="Buka panduan menu ini"
    class="inline-flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 <?= $button_style ?>">
    <i data-lucide="circle-help" class="h-4 w-4"></i><span class="hidden sm:inline">Panduan</span>
</button>

<dialog id="menuGuideModal" aria-labelledby="menuGuideTitle"
    class="w-[min(92vw,40rem)] max-w-none rounded-2xl border-0 p-0 shadow-2xl backdrop:bg-gray-950/50">
    <div class="overflow-hidden rounded-2xl bg-white">
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-5">
            <div>
                <h2 id="menuGuideTitle" class="text-lg font-bold text-gray-900">
                    Panduan <?= html_escape($role_label) ?> · <?= html_escape($guide['title']) ?>
                </h2>
                <p class="mt-1 text-sm text-gray-500"><?= html_escape($guide['description']) ?></p>
            </div>
            <button type="button" id="closeMenuGuide"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                aria-label="Tutup panduan">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="px-6 pt-5">
            <p class="rounded-xl border border-violet-100 bg-violet-50/60 p-4 text-sm leading-relaxed text-violet-900">
                <?= html_escape($role_note) ?>
            </p>
        </div>
        <ol class="grid gap-3 p-6 text-sm text-gray-700">
            <?php foreach ($guide_steps as $index => $step): ?>
            <li class="rounded-xl border border-violet-100 bg-violet-50/50 p-4">
                <span class="font-semibold text-violet-800"><?= $index + 1 ?>. Langkah <?= $index + 1 ?></span>
                <p class="mt-1 text-xs leading-relaxed text-gray-600"><?= html_escape($step) ?></p>
            </li>
            <?php endforeach; ?>
        </ol>
        <?php if ($related_note): ?>
        <div class="mx-6 mb-6 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-blue-800">Keterkaitan menu</p>
            <p class="mt-1 text-sm leading-relaxed text-blue-900"><?= html_escape($related_note) ?></p>
        </div>
        <?php endif; ?>
        <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-6 py-4">
            <button type="button" id="closeMenuGuideFooter"
                class="rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                Mengerti
            </button>
        </div>
    </div>
</dialog>

<script>
(function () {
    var dialog = document.getElementById('menuGuideModal');
    var openButton = document.getElementById('openMenuGuide');
    var closeButtons = [
        document.getElementById('closeMenuGuide'),
        document.getElementById('closeMenuGuideFooter')
    ];

    if (!dialog || !openButton) {
        return;
    }

    openButton.addEventListener('click', function () {
        dialog.showModal();
    });

    closeButtons.forEach(function (button) {
        if (button) {
            button.addEventListener('click', function () {
                dialog.close();
            });
        }
    });

    dialog.addEventListener('click', function (event) {
        if (event.target === dialog) {
            dialog.close();
        }
    });
})();
</script>
