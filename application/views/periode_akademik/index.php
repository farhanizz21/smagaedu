<main class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Pekan &amp; Semester Saat Ini</h1>

    <?php if ($this->session->flashdata('success_msg')): ?>
    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        <?= html_escape($this->session->flashdata('success_msg')); ?>
    </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error_msg')): ?>
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <?= html_escape($this->session->flashdata('error_msg')); ?>
    </div>
    <?php endif; ?>

    <?php if (validation_errors()): ?>
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <?= validation_errors(); ?>
    </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white p-6 shadow-lg">
        <p class="text-gray-600">Periode pembelajaran yang berlaku untuk seluruh pengguna dan mata pelajaran:</p>
        <div class="mt-5 flex flex-wrap gap-3">
            <span class="rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700">
                Pekan <?= html_escape($periode->pekan); ?>
            </span>
            <span class="rounded-full bg-indigo-100 px-4 py-2 text-sm font-semibold text-indigo-700">
                Semester <?= html_escape($periode->semester); ?>
            </span>
        </div>

        <?php if (has_role(array('admin', 'superadmin'))): ?>
        <div class="mt-8 border-t border-gray-100 pt-6">
            <h2 class="text-lg font-semibold text-gray-800">Atur Periode Akademik</h2>
            <p class="mt-1 text-sm text-gray-500">Perubahan akan langsung berlaku dan terlihat oleh semua pengguna.</p>

            <?= form_open('periode_akademik/simpan', array('class' => 'mt-5 grid gap-5 sm:grid-cols-2')); ?>
                <div>
                    <label for="pekan" class="mb-2 block text-sm font-medium text-gray-700">Pekan</label>
                    <select id="pekan" name="pekan" required
                        class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-800 focus:border-blue-500 focus:outline-none">
                        <?php for ($week = 1; $week <= 20; $week++): ?>
                        <option value="<?= $week; ?>"
                            <?= set_select('pekan', (string) $week, (int) $periode->pekan === $week); ?>>
                            Pekan <?= $week; ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label for="semester" class="mb-2 block text-sm font-medium text-gray-700">Semester</label>
                    <select id="semester" name="semester" required
                        class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-800 focus:border-blue-500 focus:outline-none">
                        <option value="1" <?= set_select('semester', '1', (int) $periode->semester === 1); ?>>Semester 1</option>
                        <option value="2" <?= set_select('semester', '2', (int) $periode->semester === 2); ?>>Semester 2</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            <?= form_close(); ?>
        </div>
        <?php else: ?>
        <p class="mt-8 border-t border-gray-100 pt-5 text-sm text-gray-500">
            Hanya admin yang dapat mengubah pekan dan semester.
        </p>
        <?php endif; ?>
    </section>
</main>
