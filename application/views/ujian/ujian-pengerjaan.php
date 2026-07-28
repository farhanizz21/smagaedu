<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengerjaan Ujian</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    .timer-box {
        font-size: 20px;
        font-weight: bold;
        color: red;
    }
    </style>
</head>

<body>
    <div id="fullscreenOverlay"
        style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; background:rgba(0,0,0,0.85); display:flex; align-items:center; justify-content:center;">
        <div
            style="background:#fff; border-radius:12px; padding:40px; max-width:480px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div style="font-size:48px; margin-bottom:16px;">&#x1F50D;</div>
            <h3 style="margin-bottom:12px; color:#333;">Masukkan Fullscreen</h3>
            <p style="color:#666; margin-bottom:20px; line-height:1.6;">Untuk memulai ujian, Anda wajib masuk ke mode
                fullscreen terlebih dahulu. Klik tombol di bawah untuk melanjutkan.</p>
            <button type="button" id="btnFullscreen" class="btn btn-warning btn-lg"
                style="width:100%; font-size:16px; padding:12px;">
                Masukkan Fullscreen
            </button>
            <p style="margin-top:16px; font-size:12px; color:#999;">Anda tidak dapat melihat soal sebelum masuk
                fullscreen.</p>
        </div>
    </div>
    <form id="ujianForm" method="post" action="<?= base_url('ujian/pengerjaan/'.$ujian->uuid); ?>">
        <input type="hidden" name="ujian_uuid" value="<?= $ujian->uuid; ?>">
        <div id="ujianContent" style="display:none;">
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="text-primary">Pengerjaan Ujian</h4>
                    <div class="timer-box">Sisa Waktu: <span id="timer">--:--</span></div>
                </div>
                <hr>
                <?php $no = 1; foreach ($soal as $s) : ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><strong><?= $no++; ?>. <?= $s->soal; ?></strong></p>
                        <?php if ($s->jenis_soal == 'pilihan_ganda'): ?>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="A" class="form-check-input">
                                A. <?= $s->jawaban_a ?>
                            </label>
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="B" class="form-check-input">
                                B. <?= $s->jawaban_b ?>
                            </label>
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="C" class="form-check-input">
                                C. <?= $s->jawaban_c ?>
                            </label>
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="D" class="form-check-input">
                                D. <?= $s->jawaban_d ?>
                            </label>
                        </div>
                        <?php elseif ($s->jenis_soal == 'pilihan_ganda_kompleks'): ?>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check-label">
                                <input type="checkbox" name="jawaban[<?= $s->uuid; ?>][]" value="A"
                                    class="form-check-input">
                                A. <?= $s->jawaban_a ?>
                            </label>
                            <label class="form-check-label">
                                <input type="checkbox" name="jawaban[<?= $s->uuid; ?>][]" value="B"
                                    class="form-check-input">
                                B. <?= $s->jawaban_b ?>
                            </label>
                            <label class="form-check-label">
                                <input type="checkbox" name="jawaban[<?= $s->uuid; ?>][]" value="C"
                                    class="form-check-input">
                                C. <?= $s->jawaban_c ?>
                            </label>
                            <label class="form-check-label">
                                <input type="checkbox" name="jawaban[<?= $s->uuid; ?>][]" value="D"
                                    class="form-check-input">
                                D. <?= $s->jawaban_d ?>
                            </label>
                        </div>
                        <?php elseif ($s->jenis_soal == 'menjodohkan'): ?>
                        <?php 
                        $jodohkan_pairs = isset($s->jodohkan_pairs) ? $s->jodohkan_pairs : [];
                        $letters = range('A', max(count($jodohkan_pairs) - 1, 1));
                        if (empty($jodohkan_pairs) && ($s->jawaban_a || $s->jawaban_b)) {
                            $jodohkan_pairs = [(object)['kunci' => $s->jawaban_a, 'jawaban' => $s->jawaban_b]];
                            $letters = ['A', 'B'];
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <h6 class="fw-bold text-gray-700 mb-2">Soal</h6>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($jodohkan_pairs as $index => $pair): ?>
                                    <div class="p-2 bg-gray-50 rounded border">
                                        <strong><?= $index + 1 ?>.</strong> <?= htmlspecialchars($pair->kunci) ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center text-gray-400 fw-bold">→</div>
                            </div>
                            <div class="col-md-5">
                                <h6 class="fw-bold text-gray-700 mb-2">Jawaban</h6>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($jodohkan_pairs as $index => $pair): 
                                        $letter = $letters[$index] ?? chr(65 + $index);
                                    ?>
                                    <div class="p-2 bg-blue-50 rounded border border-blue-200">
                                        <strong><?= $letter ?>.</strong> <?= htmlspecialchars($pair->jawaban) ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                            <h6 class="fw-bold text-gray-700 mb-3">Pasangkan:</h6>
                            <?php foreach ($jodohkan_pairs as $index => $pair): 
                                $letter = $letters[$index] ?? chr(65 + $index);
                            ?>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="fw-semibold text-gray-700"
                                    style="min-width: 24px;"><?= $index + 1 ?>.</span>
                                <span
                                    class="text-sm text-gray-600 flex-fill"><?= htmlspecialchars($pair->kunci) ?></span>
                                <select name="jawaban[<?= $s->uuid; ?>][<?= $index + 1; ?>]"
                                    class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Pilih</option>
                                    <?php foreach ($jodohkan_pairs as $i => $p): 
                                        $opt_letter = $letters[$i] ?? chr(65 + $i);
                                    ?>
                                    <option value="<?= $opt_letter ?>"><?= $opt_letter ?> -
                                        <?= htmlspecialchars($p->jawaban) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php elseif ($s->jenis_soal == 'benar_salah'): ?>
                        <div class="d-flex gap-3">
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="benar"
                                    class="form-check-input">
                                Benar
                            </label>
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="salah"
                                    class="form-check-input">
                                Salah
                            </label>
                        </div>
                        <?php elseif ($s->jenis_soal == 'essay'): ?>
                        <textarea name="jawaban[<?= $s->uuid; ?>]" rows="4" class="form-control"
                            placeholder="Masukkan jawaban Anda..."></textarea>
                        <?php else: ?>
                        <input type="text" name="jawaban[<?= $s->uuid; ?>]" class="form-control"
                            placeholder="Masukkan jawaban Anda...">
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-success">Kirim Jawaban</button>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>

<script>
var waktuUjian = 30 * 60;
var warningCount = 0;
var timerInterval;

function startTimer() {
    timerInterval = setInterval(function() {
        var menit = Math.floor(waktuUjian / 60);
        var detik = waktuUjian % 60;
        document.getElementById('timer').textContent = menit + ":" + (detik < 10 ? '0' : '') + detik;

        if (waktuUjian <= 0) {
            clearInterval(timerInterval);
            Swal.fire({
                title: "Waktu Habis!",
                text: "Jawaban akan dikirim otomatis.",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                document.getElementById('ujianForm').submit();
            });
        }

        waktuUjian--;
    }, 1000);
}

function enterFullscreen() {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen().catch(function(err) {
            console.log('Fullscreen not available:', err.message);
        });
    } else if (document.documentElement.webkitRequestFullscreen) {
        document.documentElement.webkitRequestFullscreen().catch(function(err) {
            console.log('Fullscreen not available:', err.message);
        });
    } else if (document.documentElement.msRequestFullscreen) {
        document.documentElement.msRequestFullscreen().catch(function(err) {
            console.log('Fullscreen not available:', err.message);
        });
    }
}

$(document).ready(function() {
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function(event) {
        history.go(1);
    });

    $('#btnFullscreen').on('click', function() {
        enterFullscreen();
        $('#fullscreenOverlay').fadeOut(300, function() {
            $('#ujianContent').fadeIn(300);
            startTimer();
        });
    });

    console.log('aa');
});

$(window).on('blur', function() {
    console.log("Tab berpindah! Warning ke-" + warningCount);
    warningCount++;
    if (warningCount > 1) {
        Swal.fire({
            icon: "error",
            title: "Pelanggaran",
            allowOutsideClick: false,
            text: "Anda telah meninggalkan halaman lebih dari 2 kali! Ujian akan dikirim otomatis.",
        }).then(() => {
            document.getElementById('ujianForm').submit();
        });
    } else {
        Swal.fire({
            title: "Perhatian",
            text: "Jangan beralih tab! Jika dilakukan 2 kali, ujian akan dikirim otomatis.",
            icon: "warning"
        });
    }
});
// var warningCount = 0;
// $(window).on('blur', function() {
//     warningCount++;
//     if (warningCount > 2) {
//         alert("Anda telah meninggalkan halaman lebih dari 2 kali! Ujian akan dikirim otomatis.");
//         document.getElementById('ujianForm').submit();
//     } else {
//         alert("Jangan beralih tab! Jika dilakukan 2 kali, ujian akan dikirim otomatis.");
//     }
// });
</script>