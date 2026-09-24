<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengerjaan Ujian</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f3f4f6; }

        /* ===== Topbar: nama ujian + waktu pengerjaan ===== */
        .exam-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }
        .exam-topbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .exam-title { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
        .exam-subtitle { font-size: 12px; color: #6b7280; margin: 2px 0 0; }
        .timer-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            font-weight: 800;
            font-size: 18px;
            padding: 8px 16px;
            border-radius: 12px;
            font-variant-numeric: tabular-nums;
        }
        .timer-box.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #d97706;
            animation: timer-pulse 1s infinite;
        }
        @keyframes timer-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* ===== Layout dua kolom ===== */
        .exam-layout {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }
        .exam-main { flex: 1 1 auto; min-width: 0; }
        .exam-side {
            width: 300px;
            flex-shrink: 0;
            position: sticky;
            top: 90px;
        }
        @media (max-width: 992px) {
            .exam-layout { flex-direction: column-reverse; }
            .exam-side { width: 100%; position: static; }
        }
        /* ===== Soal (kiri) ===== */
        .question-progress {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 12px;
        }
        .question-item { display: none; }
        .question-item.current { display: block; }
        .question-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .question-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            margin-bottom: 12px;
        }
        .question-text {
            font-size: 15px;
            line-height: 1.7;
            color: #111827;
            white-space: pre-wrap;
        }
        .question-item .form-check-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            background: #fff;
            transition: all 0.15s;
            font-weight: 500;
            color: #374151;
        }
        .question-item .form-check-label:hover {
            border-color: #93c5fd;
            background: #f8faff;
        }
        .question-item .form-check-label:has(input:checked) {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 0 0 3px rgba(37, 99, 246, 0.1);
            color: #1e40af;
        }
        .question-nav-buttons {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
        }

        /* ===== Panel navigasi (kanan) ===== */
        .side-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .side-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 14px;
        }
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        .nav-btn {
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            border: 1.5px solid #d1d5db;
            background: #f9fafb;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
            padding: 0;
        }
        .nav-btn:hover { border-color: #60a5fa; color: #2563eb; }
        .nav-btn.answered {
            background: #dcfce7;
            border-color: #22c55e;
            color: #15803d;
        }
        .nav-btn.current {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            box-shadow: 0 4px 10px rgba(37, 99, 246, 0.3);
        }
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
            font-size: 12px;
            color: #6b7280;
        }
        .legend-item { display: flex; align-items: center; gap: 6px; }
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            border: 1.5px solid #d1d5db;
            background: #f9fafb;
        }
        .legend-dot.answered { background: #dcfce7; border-color: #22c55e; }
        .legend-dot.current { background: #2563eb; border-color: #2563eb; }
        .answered-info {
            margin-top: 14px;
            font-size: 13px;
            color: #374151;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
        }

        /* ===== Penyesuaian HP portrait (<=575px) — desktop di atas 575px tidak berubah ===== */
        @media (max-width: 575px) {
            .exam-topbar-inner { padding: 10px 14px; gap: 10px; }
            .exam-topbar-inner > div:first-child { min-width: 0; flex: 1 1 auto; }
            .exam-title {
                font-size: 14px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .exam-subtitle { font-size: 11px; }
            .timer-box { flex-shrink: 0; font-size: 15px; padding: 6px 10px; gap: 6px; }
            .timer-box svg { width: 15px; height: 15px; }
            .exam-layout { padding: 14px; gap: 14px; }
            .question-card { padding: 16px; }
            .side-card { padding: 16px; }
            .question-nav-buttons .btn { min-height: 44px; }
            .nav-btn { min-width: 44px; min-height: 44px; }
            #btnSubmit { min-height: 44px; }
            #fullscreenOverlay > div { padding: 24px; }
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
    <form id="ujianForm" method="post" enctype="multipart/form-data" action="<?= base_url('ujian/pengerjaan/'.$ujian->uuid); ?>">
        <input type="hidden" name="ujian_uuid" value="<?= $ujian->uuid; ?>">
        <div id="ujianContent" style="display:none;">

            <!-- Topbar: nama ujian + waktu pengerjaan -->
            <div class="exam-topbar">
                <div class="exam-topbar-inner">
                    <div>
                        <p class="exam-title"><?= htmlspecialchars($ujian->nama); ?></p>
                        <p class="exam-subtitle">Pengerjaan Ujian<?= !empty($ujian->mapel_nama) ? ' &middot; ' . htmlspecialchars($ujian->mapel_nama) : ''; ?></p>
                    </div>
                    <div class="timer-box" id="timerBox" title="Sisa waktu pengerjaan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span id="timer">--:--</span>
                    </div>
                </div>
            </div>

            <!-- Layout: kiri soal & jawaban, kanan navigasi soal -->
            <div class="exam-layout">
                <div class="exam-main">
                    <div class="question-progress" id="questionProgress"></div>
                <?php $no = 1; foreach ($soal as $s) : ?>
                <div class="question-item card mb-3" id="question-<?= $s->uuid; ?>" data-uuid="<?= $s->uuid; ?>">
                    <div class="question-card">
                        <span class="question-badge">Soal <?= $no; ?> &middot; <?= ucwords(str_replace('_', ' ', $s->jenis_soal)); ?></span>
                        <p class="question-text"><strong><?= $no++; ?>. <?= $s->soal; ?></strong></p>
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
                            <label class="form-check-label">
                                <input type="radio" name="jawaban[<?= $s->uuid; ?>]" value="E" class="form-check-input">
                                E. <?= $s->jawaban_e ?>
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
                            <label class="form-check-label">
                                <input type="checkbox" name="jawaban[<?= $s->uuid; ?>][]" value="E"
                                    class="form-check-input">
                                E. <?= $s->jawaban_e ?>
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
                        <?php elseif ($s->jenis_soal == 'essay' && ($s->jenis_jawaban_essay ?? 'teks') === 'file'): ?>
                        <input type="file" name="jawaban_file[<?= $s->uuid; ?>]" class="form-control"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
                        <div class="form-text">Upload file jawaban Anda.</div>
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

                <?php if (empty($soal)): ?>
                <div class="question-card text-center text-muted">Belum ada soal untuk ujian ini.</div>
                <?php endif; ?>

                <!-- Navigasi antar soal -->
                <div class="question-nav-buttons">
                    <button type="button" class="btn btn-outline-secondary px-4" id="btnPrev" disabled>&larr;
                        Sebelumnya</button>
                    <button type="button" class="btn btn-primary px-4" id="btnNext">Berikutnya &rarr;</button>
                </div>
                </div><!-- /.exam-main -->

                <!-- Panel kanan: navigasi soal -->
                <aside class="exam-side">
                    <div class="side-card">
                        <div class="side-card-title">Navigasi Soal</div>
                        <div class="nav-grid" id="navGrid">
                            <?php $idx = 0; foreach ($soal as $s): ?>
                            <button type="button" class="nav-btn" data-index="<?= $idx; ?>"
                                data-uuid="<?= $s->uuid; ?>"><?= ++$idx; ?></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="legend">
                            <span class="legend-item"><span class="legend-dot current"></span> Dibuka</span>
                            <span class="legend-item"><span class="legend-dot answered"></span> Terjawab</span>
                            <span class="legend-item"><span class="legend-dot"></span> Kosong</span>
                        </div>
                        <div class="answered-info">Terjawab: <strong id="answeredCount">0</strong> dari
                            <strong><?= count($soal); ?></strong> soal</div>
                        <button type="button" class="btn btn-success w-100 mt-3" id="btnSubmit">Kirim Jawaban</button>
                    </div>
                </aside>
            </div><!-- /.exam-layout -->
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>

<script>
var waktuUjian = <?= max(1, (int)($durasi ?? 60)) * 60 ?>;
var warningCount = 0;
var timerInterval;
var currentIndex = 0;
var questionUuids = <?= json_encode(array_map(function ($s) { return $s->uuid; }, $soal)); ?>;
var totalSoal = questionUuids.length;

function renderTimer() {
    var menit = Math.floor(waktuUjian / 60);
    var detik = waktuUjian % 60;
    document.getElementById('timer').textContent =
        (menit < 10 ? '0' : '') + menit + ':' + (detik < 10 ? '0' : '') + detik;
    var box = document.getElementById('timerBox');
    if (box) {
        box.classList.toggle('warning', waktuUjian <= 60);
    }
}

function startTimer() {
    renderTimer();
    timerInterval = setInterval(function() {
        waktuUjian--;
        renderTimer();

        if (waktuUjian <= 0) {
            clearInterval(timerInterval);
            Swal.fire({
                title: "Waktu Habis!",
                text: "Jawaban akan dikirim otomatis.",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 3000
            }).then(function() {
                document.getElementById('ujianForm').submit();
            });
        }
    }, 1000);
}

function isAnswered(uuid) {
    var el = document.getElementById('question-' + uuid);
    if (!el) return false;

    var radios = el.querySelectorAll('input[type="radio"]');
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].checked) return true;
    }

    var checks = el.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checks.length; i++) {
        if (checks[i].checked) return true;
    }

    var files = el.querySelectorAll('input[type="file"]');
    for (var i = 0; i < files.length; i++) {
        if (files[i].files && files[i].files.length > 0) return true;
    }

    var selects = el.querySelectorAll('select');
    if (selects.length > 0) {
        for (var i = 0; i < selects.length; i++) {
            if (selects[i].value !== '') return true;
        }
        return false;
    }

    var texts = el.querySelectorAll('textarea, input[type="text"]');
    for (var i = 0; i < texts.length; i++) {
        if (texts[i].value.trim() !== '') return true;
    }
    return false;
}

function refreshNav() {
    var answered = 0;
    document.querySelectorAll('.nav-btn').forEach(function(btn) {
        var uuid = btn.getAttribute('data-uuid');
        var idx = parseInt(btn.getAttribute('data-index'), 10);
        var ok = isAnswered(uuid);
        btn.classList.toggle('answered', ok && idx !== currentIndex);
        btn.classList.toggle('current', idx === currentIndex);
        if (ok) answered++;
    });
    var cnt = document.getElementById('answeredCount');
    if (cnt) cnt.textContent = answered;
}

function showQuestion(index) {
    if (totalSoal === 0) return;
    if (index < 0) index = 0;
    if (index > totalSoal - 1) index = totalSoal - 1;
    currentIndex = index;

    document.querySelectorAll('.question-item').forEach(function(el, i) {
        el.classList.toggle('current', i === index);
    });

    var prog = document.getElementById('questionProgress');
    if (prog) prog.textContent = 'Soal ' + (index + 1) + ' dari ' + totalSoal;

    document.getElementById('btnPrev').disabled = (index === 0);

    var next = document.getElementById('btnNext');
    if (index === totalSoal - 1) {
        next.innerHTML = 'Soal Terakhir';
        next.disabled = true;
    } else {
        next.innerHTML = 'Berikutnya &rarr;';
        next.disabled = false;
    }

    refreshNav();
    window.scrollTo({ top: 0, behavior: 'smooth' });
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

    showQuestion(0);

    $('#btnFullscreen').on('click', function() {
        enterFullscreen();
        $('#fullscreenOverlay').fadeOut(300, function() {
            $('#ujianContent').fadeIn(300);
            startTimer();
        });
    });

    $('#btnPrev').on('click', function() {
        showQuestion(currentIndex - 1);
    });

    $('#btnNext').on('click', function() {
        showQuestion(currentIndex + 1);
    });

    $('#navGrid').on('click', '.nav-btn', function() {
        showQuestion(parseInt($(this).data('index'), 10));
    });

    $('#ujianForm').on('change input', 'input, textarea, select', function() {
        refreshNav();
    });

    $('#btnSubmit').on('click', function() {
        Swal.fire({
            title: 'Kirim Jawaban?',
            text: 'Pastikan semua soal sudah dijawab. Jawaban tidak dapat diubah setelah dikirim.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim Jawaban',
            cancelButtonText: 'Periksa Lagi',
            confirmButtonColor: '#16a34a'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('ujianForm').submit();
            }
        });
    });
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
</script>