<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();

$templates = [];
$stmt = $pdo->prepare("SELECT * FROM interval_templates WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$templates = $stmt->fetchAll();

$title = 'Interval Builder - Run Tracker';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-6 pb-28 md:pb-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="track.php" class="text-[#9CA3AF] hover:text-[#1F2937] transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold">Interval Builder</h1>
    </div>

    <form id="intervalForm" class="space-y-4">
        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Konfigurasi Workout</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Nama Workout</label>
                    <input type="text" id="workoutName" class="input-field" placeholder="Contoh: Interval 800m" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Warm-up</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="warmup-opt option-btn option-active" data-value="none">Tidak</button>
                        <button type="button" class="warmup-opt option-btn option-inactive" data-value="duration">Durasi</button>
                        <button type="button" class="warmup-opt option-btn option-inactive" data-value="distance">Jarak</button>
                    </div>
                    <div id="warmupValueContainer" class="hidden mt-2">
                        <div class="flex gap-2 items-center">
                            <input type="number" id="warmupValue" class="input-field flex-1" step="0.01" min="0" placeholder="Nilai">
                            <select id="warmupUnit" class="input-field w-28 hidden">
                                <option value="km">km</option>
                                <option value="m">meter</option>
                            </select>
                            <span id="warmupDurationWrap" class="flex gap-2 items-center">
                                <input type="number" id="warmupDurMin" class="input-field w-20 text-center" placeholder="0" min="0" value="">
                                <span class="text-sm text-[#9CA3AF]">menit</span>
                                <input type="number" id="warmupDurSec" class="input-field w-20 text-center" placeholder="0" min="0" max="59" value="">
                                <span class="text-sm text-[#9CA3AF]">detik</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Jumlah Interval</label>
                    <div class="flex items-center gap-4">
                        <button type="button" id="intervalDec" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-lg font-bold text-[#1F2937] hover:bg-gray-50 transition-colors">-</button>
                        <span id="intervalCount" class="text-2xl font-bold text-[#fc5200] w-8 text-center">8</span>
                        <button type="button" id="intervalInc" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-lg font-bold text-[#1F2937] hover:bg-gray-50 transition-colors">+</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-lg font-semibold mb-4">High Intensity</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Tipe</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="high-type-opt option-btn option-active-orange" data-value="distance">Jarak</button>
                        <button type="button" class="high-type-opt option-btn option-inactive" data-value="duration">Durasi</button>
                    </div>
                </div>
                <div id="highDistanceWrap" class="flex gap-2">
                    <input type="number" id="highValue" class="input-field flex-1" step="0.01" min="0.01" placeholder="Contoh: 0.8" required>
                    <select id="highUnit" class="input-field w-28">
                        <option value="km">km</option>
                        <option value="m">meter</option>
                    </select>
                </div>
                <div id="highDurationWrap" class="hidden flex gap-2 items-center">
                    <input type="number" id="highDurMin" class="input-field w-24 text-center" placeholder="1" min="0" value="">
                    <span class="text-sm text-[#9CA3AF]">menit</span>
                    <input type="number" id="highDurSec" class="input-field w-24 text-center" placeholder="30" min="0" max="59" value="">
                    <span class="text-sm text-[#9CA3AF]">detik</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Target Pace (wajib)</label>
                    <div class="flex gap-2 items-center">
                        <input type="number" id="targetPaceMin" class="input-field w-24 text-center" placeholder="4" min="1" max="60" required>
                        <span class="text-lg font-bold text-[#1F2937]">:</span>
                        <input type="number" id="targetPaceSec" class="input-field w-24 text-center" placeholder="30" min="0" max="59" required>
                        <span class="text-sm text-[#9CA3AF]">/km</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Recovery</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Tipe</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="rec-type-opt option-btn option-active-green" data-value="distance">Jarak</button>
                        <button type="button" class="rec-type-opt option-btn option-inactive" data-value="duration">Durasi</button>
                    </div>
                </div>
                <div id="recDistanceWrap" class="flex gap-2">
                    <input type="number" id="recValue" class="input-field flex-1" step="0.01" min="0.01" placeholder="Contoh: 0.4" required>
                    <select id="recUnit" class="input-field w-28">
                        <option value="km">km</option>
                        <option value="m">meter</option>
                    </select>
                </div>
                <div id="recDurationWrap" class="hidden flex gap-2 items-center">
                    <input type="number" id="recDurMin" class="input-field w-24 text-center" placeholder="1" min="0" value="">
                    <span class="text-sm text-[#9CA3AF]">menit</span>
                    <input type="number" id="recDurSec" class="input-field w-24 text-center" placeholder="0" min="0" max="59" value="">
                    <span class="text-sm text-[#9CA3AF]">detik</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Mode Recovery</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="rec-mode-opt option-btn option-active-green" data-value="jog">Jog</button>
                        <button type="button" class="rec-mode-opt option-btn option-inactive" data-value="walk">Walk</button>
                        <button type="button" class="rec-mode-opt option-btn option-inactive" data-value="stand">Stand</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Cool-down</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#9CA3AF] mb-1">Cool-down</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="cool-opt option-btn option-active" data-value="none">Tidak</button>
                        <button type="button" class="cool-opt option-btn option-inactive" data-value="duration">Durasi</button>
                        <button type="button" class="cool-opt option-btn option-inactive" data-value="distance">Jarak</button>
                    </div>
                    <div id="coolValueContainer" class="hidden mt-2">
                        <div class="flex gap-2 items-center">
                            <input type="number" id="coolValue" class="input-field flex-1" step="0.01" min="0" placeholder="Nilai">
                            <select id="coolUnit" class="input-field w-28 hidden">
                                <option value="km">km</option>
                                <option value="m">meter</option>
                            </select>
                            <span id="coolDurationWrap" class="flex gap-2 items-center">
                                <input type="number" id="coolDurMin" class="input-field w-20 text-center" placeholder="0" min="0" value="">
                                <span class="text-sm text-[#9CA3AF]">menit</span>
                                <input type="number" id="coolDurSec" class="input-field w-20 text-center" placeholder="0" min="0" max="59" value="">
                                <span class="text-sm text-[#9CA3AF]">detik</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
            <div id="summaryContent" class="text-sm text-[#4B5563] space-y-1">
                <p id="summaryName" class="font-semibold text-[#1F2937]">Interval 8x</p>
                <p id="summaryHigh">High: 0 km @ --:-- /km</p>
                <p id="summaryRec">Recovery: 0 km (Jog)</p>
                <p id="summaryWarmup">Warm-up: -</p>
                <p id="summaryCool">Cool-down: -</p>
            </div>
        </div>
    </form>

    <div class="flex flex-col gap-3 mt-6">
        <button id="saveTemplateBtn" class="btn-secondary w-full flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
            Simpan sebagai Template
        </button>
        <button id="startIntervalBtn" class="btn-primary w-full flex items-center justify-center gap-2 disabled:opacity-50" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            MULAI INTERVAL
        </button>
    </div>

    <?php if (count($templates) > 0): ?>
    <div class="card mt-6">
        <h2 class="text-lg font-semibold mb-4">Template Tersimpan</h2>
        <div class="space-y-3">
            <?php foreach ($templates as $t): ?>
            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer template-item"
                 data-name="<?= htmlspecialchars($t['name']) ?>"
                 data-warmup-type="<?= $t['warmup_type'] ?>"
                 data-warmup-value="<?= $t['warmup_value'] ?>"
                 data-warmup-unit="<?= $t['warmup_unit'] ?>"
                 data-interval-count="<?= $t['interval_count'] ?>"
                 data-high-type="<?= $t['high_type'] ?>"
                 data-high-value="<?= $t['high_value'] ?>"
                 data-high-unit="<?= $t['high_unit'] ?>"
                 data-target-pace="<?= $t['target_pace'] ?>"
                 data-rec-type="<?= $t['recovery_type'] ?>"
                 data-rec-value="<?= $t['recovery_value'] ?>"
                 data-rec-unit="<?= $t['recovery_unit'] ?>"
                 data-rec-mode="<?= $t['recovery_mode'] ?>"
                 data-cool-type="<?= $t['cooldown_type'] ?>"
                 data-cool-value="<?= $t['cooldown_value'] ?>"
                 data-cool-unit="<?= $t['cooldown_unit'] ?>">
                <div>
                    <p class="font-semibold"><?= htmlspecialchars($t['name']) ?></p>
                    <p class="text-sm text-[#9CA3AF]"><?= $t['interval_count'] ?>x <?= $t['high_value'] ?> <?= $t['high_unit'] ?> @ <?= formatPace($t['target_pace']) ?></p>
                </div>
                <svg class="w-5 h-5 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/bottom-nav.php'; ?>
<?php include '../includes/footer.php'; ?>
<style>
.option-btn { padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #D1D5DB; font-size: 0.875rem; font-weight: 500; transition: all 0.15s; cursor: pointer; }
.option-btn:active { transform: scale(0.97); }
.option-active { background: #fc5200; color: white; border-color: #fc5200; }
.option-active-orange { background: #fc5200; color: white; border-color: #fc5200; }
.option-active-green { background: #10B981; color: white; border-color: #10B981; }
.option-inactive { background: white; color: #9CA3AF; border-color: #D1D5DB; }
.option-inactive:hover { border-color: #9CA3AF; background: #F9FAFB; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var state = {
        warmupType: 'none',
        warmupValue: '',
        warmupUnit: 'km',
        warmupDurMin: '',
        warmupDurSec: '',
        intervalCount: 8,
        highType: 'distance',
        highValue: '',
        highUnit: 'km',
        highDurMin: '',
        highDurSec: '',
        targetPaceMin: '',
        targetPaceSec: '',
        recType: 'distance',
        recValue: '',
        recUnit: 'km',
        recDurMin: '',
        recDurSec: '',
        recMode: 'jog',
        coolType: 'none',
        coolValue: '',
        coolUnit: 'km',
        coolDurMin: '',
        coolDurSec: ''
    };

    function toggleOptions(btns, value, activeClass) {
        btns.forEach(function (btn) {
            if (btn.dataset.value === value) {
                btn.classList.remove('option-active', 'option-active-orange', 'option-active-green', 'option-inactive');
                btn.classList.add(activeClass);
            } else {
                btn.classList.remove('option-active', 'option-active-orange', 'option-active-green');
                btn.classList.add('option-inactive');
            }
        });
    }

    function updateWarmupFields() {
        var wrap = document.getElementById('warmupValueContainer');
        wrap.classList.toggle('hidden', state.warmupType === 'none');
        var isDur = state.warmupType === 'duration';
        var isDist = state.warmupType === 'distance';
        document.getElementById('warmupValue').classList.toggle('hidden', isDur);
        document.getElementById('warmupUnit').classList.toggle('hidden', !isDist);
        document.getElementById('warmupDurationWrap').classList.toggle('hidden', !isDur);
    }

    function updateCoolFields() {
        var wrap = document.getElementById('coolValueContainer');
        wrap.classList.toggle('hidden', state.coolType === 'none');
        var isDur = state.coolType === 'duration';
        var isDist = state.coolType === 'distance';
        document.getElementById('coolValue').classList.toggle('hidden', isDur);
        document.getElementById('coolUnit').classList.toggle('hidden', !isDist);
        document.getElementById('coolDurationWrap').classList.toggle('hidden', !isDur);
    }

    function updateHighFields() {
        var isDur = state.highType === 'duration';
        document.getElementById('highDistanceWrap').classList.toggle('hidden', isDur);
        document.getElementById('highDurationWrap').classList.toggle('hidden', !isDur);
    }

    function updateRecFields() {
        var isDur = state.recType === 'duration';
        document.getElementById('recDistanceWrap').classList.toggle('hidden', isDur);
        document.getElementById('recDurationWrap').classList.toggle('hidden', !isDur);
    }

    document.querySelectorAll('.warmup-opt').forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.warmupType = this.dataset.value;
            toggleOptions(document.querySelectorAll('.warmup-opt'), state.warmupType, 'option-active');
            updateWarmupFields();
            updateSummary();
        });
    });

    document.getElementById('intervalDec').addEventListener('click', function () {
        if (state.intervalCount > 1) { state.intervalCount--; document.getElementById('intervalCount').textContent = state.intervalCount; updateSummary(); }
    });

    document.getElementById('intervalInc').addEventListener('click', function () {
        if (state.intervalCount < 50) { state.intervalCount++; document.getElementById('intervalCount').textContent = state.intervalCount; updateSummary(); }
    });

    document.querySelectorAll('.high-type-opt').forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.highType = this.dataset.value;
            toggleOptions(document.querySelectorAll('.high-type-opt'), state.highType, 'option-active-orange');
            updateHighFields();
            updateSummary();
        });
    });

    document.querySelectorAll('.rec-type-opt').forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.recType = this.dataset.value;
            toggleOptions(document.querySelectorAll('.rec-type-opt'), state.recType, 'option-active-green');
            updateRecFields();
            updateSummary();
        });
    });

    document.querySelectorAll('.rec-mode-opt').forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.recMode = this.dataset.value;
            toggleOptions(document.querySelectorAll('.rec-mode-opt'), state.recMode, 'option-active-green');
            updateSummary();
        });
    });

    document.querySelectorAll('.cool-opt').forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.coolType = this.dataset.value;
            toggleOptions(document.querySelectorAll('.cool-opt'), state.coolType, 'option-active');
            updateCoolFields();
            updateSummary();
        });
    });

    function collectValues() {
        state.highValue = document.getElementById('highValue').value;
        state.highUnit = document.getElementById('highUnit').value;
        state.highDurMin = document.getElementById('highDurMin').value;
        state.highDurSec = document.getElementById('highDurSec').value;
        state.recValue = document.getElementById('recValue').value;
        state.recUnit = document.getElementById('recUnit').value;
        state.recDurMin = document.getElementById('recDurMin').value;
        state.recDurSec = document.getElementById('recDurSec').value;
        state.targetPaceMin = document.getElementById('targetPaceMin').value;
        state.targetPaceSec = document.getElementById('targetPaceSec').value;
        state.warmupValue = document.getElementById('warmupValue').value;
        state.warmupUnit = document.getElementById('warmupUnit').value;
        state.warmupDurMin = document.getElementById('warmupDurMin').value;
        state.warmupDurSec = document.getElementById('warmupDurSec').value;
        state.coolValue = document.getElementById('coolValue').value;
        state.coolUnit = document.getElementById('coolUnit').value;
        state.coolDurMin = document.getElementById('coolDurMin').value;
        state.coolDurSec = document.getElementById('coolDurSec').value;
    }

    function getHighDisplay() {
        collectValues();
        if (state.highType === 'distance') {
            var v = state.highValue || '0';
            var u = state.highUnit === 'm' ? 'm' : 'km';
            return v + ' ' + u;
        }
        var m = state.highDurMin || '0';
        var s = state.highDurSec || '00';
        return m + 'm ' + s.padStart(2, '0') + 's';
    }

    function getRecDisplay() {
        collectValues();
        if (state.recType === 'distance') {
            var v = state.recValue || '0';
            var u = state.recUnit === 'm' ? 'm' : 'km';
            return v + ' ' + u;
        }
        var m = state.recDurMin || '0';
        var s = state.recDurSec || '00';
        return m + 'm ' + s.padStart(2, '0') + 's';
    }

    function getWarmupDisplay() {
        collectValues();
        if (state.warmupType === 'none') return '-';
        if (state.warmupType === 'distance') return (state.warmupValue || '0') + ' ' + (state.warmupUnit === 'm' ? 'm' : 'km');
        var m = state.warmupDurMin || '0';
        var s = state.warmupDurSec || '00';
        return m + 'm ' + s.padStart(2, '0') + 's';
    }

    function getCoolDisplay() {
        collectValues();
        if (state.coolType === 'none') return '-';
        if (state.coolType === 'distance') return (state.coolValue || '0') + ' ' + (state.coolUnit === 'm' ? 'm' : 'km');
        var m = state.coolDurMin || '0';
        var s = state.coolDurSec || '00';
        return m + 'm ' + s.padStart(2, '0') + 's';
    }

    function getPaceDisplay() {
        collectValues();
        if (!state.targetPaceMin) return '--:--';
        var s = state.targetPaceSec || '00';
        return state.targetPaceMin + ':' + s.padStart(2, '0');
    }

    function updateSummary() {
        collectValues();
        var name = document.getElementById('workoutName').value || ('Interval ' + state.intervalCount + 'x');
        document.getElementById('summaryName').textContent = name;
        document.getElementById('summaryHigh').textContent = 'High: ' + getHighDisplay() + ' @ ' + getPaceDisplay() + ' /km';
        document.getElementById('summaryRec').textContent = 'Recovery: ' + getRecDisplay() + ' (' + state.recMode.charAt(0).toUpperCase() + state.recMode.slice(1) + ')';
        document.getElementById('summaryWarmup').textContent = 'Warm-up: ' + getWarmupDisplay();
        document.getElementById('summaryCool').textContent = 'Cool-down: ' + getCoolDisplay();

        var startBtn = document.getElementById('startIntervalBtn');
        if (state.targetPaceMin) {
            startBtn.disabled = false;
            startBtn.classList.remove('disabled:opacity-50');
        } else {
            startBtn.disabled = true;
            startBtn.classList.add('disabled:opacity-50');
        }
    }

    var inputIds = ['highValue', 'highUnit', 'highDurMin', 'highDurSec', 'recValue', 'recUnit', 'recDurMin', 'recDurSec', 'targetPaceMin', 'targetPaceSec', 'warmupValue', 'warmupUnit', 'warmupDurMin', 'warmupDurSec', 'coolValue', 'coolUnit', 'coolDurMin', 'coolDurSec', 'workoutName'];
    inputIds.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateSummary);
            el.addEventListener('change', updateSummary);
        }
    });
    updateSummary();

    document.getElementById('startIntervalBtn').addEventListener('click', function () {
        collectValues();
        var targetPace = parseFloat(state.targetPaceMin) + parseFloat(state.targetPaceSec || 0) / 60;
        var params = new URLSearchParams();
        params.set('name', document.getElementById('workoutName').value || ('Interval ' + state.intervalCount + 'x'));
        params.set('warmup_type', state.warmupType);
        if (state.warmupType === 'duration') {
            var warmSec = parseInt(state.warmupDurMin || 0) * 60 + parseInt(state.warmupDurSec || 0);
            params.set('warmup_value', (warmSec / 60).toFixed(2));
            params.set('warmup_unit', 'minutes');
        } else if (state.warmupType === 'distance') {
            params.set('warmup_value', state.warmupValue || '0');
            params.set('warmup_unit', state.warmupUnit);
        } else {
            params.set('warmup_value', '0');
            params.set('warmup_unit', 'minutes');
        }
        params.set('interval_count', state.intervalCount);
        params.set('high_type', state.highType);
        if (state.highType === 'duration') {
            var secs = parseInt(state.highDurMin || 0) * 60 + parseInt(state.highDurSec || 0);
            params.set('high_value', (secs / 60).toFixed(2));
            params.set('high_unit', 'minutes');
        } else {
            params.set('high_value', state.highValue);
            params.set('high_unit', state.highUnit);
        }
        params.set('target_pace', targetPace.toFixed(2));
        params.set('rec_type', state.recType);
        if (state.recType === 'duration') {
            var secs2 = parseInt(state.recDurMin || 0) * 60 + parseInt(state.recDurSec || 0);
            params.set('rec_value', (secs2 / 60).toFixed(2));
            params.set('rec_unit', 'minutes');
        } else {
            params.set('rec_value', state.recValue);
            params.set('rec_unit', state.recUnit);
        }
        params.set('rec_mode', state.recMode);
        params.set('cool_type', state.coolType);
        if (state.coolType === 'duration') {
            var coolSec = parseInt(state.coolDurMin || 0) * 60 + parseInt(state.coolDurSec || 0);
            params.set('cool_value', (coolSec / 60).toFixed(2));
            params.set('cool_unit', 'minutes');
        } else if (state.coolType === 'distance') {
            params.set('cool_value', state.coolValue || '0');
            params.set('cool_unit', state.coolUnit);
        } else {
            params.set('cool_value', '0');
            params.set('cool_unit', 'minutes');
        }
        window.location.href = 'interval_track.php?' + params.toString();
    });

    document.getElementById('saveTemplateBtn').addEventListener('click', function () {
        collectValues();
        var name = document.getElementById('workoutName').value.trim();
        if (!name) { alert('Masukkan nama workout terlebih dahulu'); return; }
        var targetPace = parseFloat(state.targetPaceMin) + parseFloat(state.targetPaceSec || 0) / 60;
        if (!state.targetPaceMin) { alert('Lengkapi Target Pace terlebih dahulu'); return; }

        var data = {
            name: name,
            warmup_type: state.warmupType,
            warmup_value: state.warmupType === 'duration' ? ((parseInt(state.warmupDurMin || 0) * 60 + parseInt(state.warmupDurSec || 0)) / 60).toFixed(2) : (state.warmupValue || '0'),
            warmup_unit: state.warmupType === 'duration' ? 'minutes' : state.warmupUnit,
            interval_count: state.intervalCount,
            high_type: state.highType,
            high_value: state.highType === 'duration' ? ((parseInt(state.highDurMin || 0) * 60 + parseInt(state.highDurSec || 0)) / 60).toFixed(2) : state.highValue,
            high_unit: state.highType === 'duration' ? 'minutes' : state.highUnit,
            target_pace: targetPace.toFixed(2),
            rec_type: state.recType,
            rec_value: state.recType === 'duration' ? ((parseInt(state.recDurMin || 0) * 60 + parseInt(state.recDurSec || 0)) / 60).toFixed(2) : state.recValue,
            rec_unit: state.recType === 'duration' ? 'minutes' : state.recUnit,
            rec_mode: state.recMode,
            cool_type: state.coolType,
            cool_value: state.coolType === 'duration' ? ((parseInt(state.coolDurMin || 0) * 60 + parseInt(state.coolDurSec || 0)) / 60).toFixed(2) : (state.coolValue || '0'),
            cool_unit: state.coolType === 'duration' ? 'minutes' : state.coolUnit
        };

        fetch('../api/save_template.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) { alert('Template berhasil disimpan!'); location.reload(); }
            else { alert('Gagal: ' + (data.error || 'Unknown error')); }
        })
        .catch(function () { alert('Network error'); });
    });

    document.querySelectorAll('.template-item').forEach(function (item) {
        item.addEventListener('click', function () {
            var d = this.dataset;
            document.getElementById('workoutName').value = d.name;

            state.warmupType = d.warmupType || 'none';
            toggleOptions(document.querySelectorAll('.warmup-opt'), state.warmupType, 'option-active');
            updateWarmupFields();
            if (d.warmupType === 'duration' && d.warmupValue) {
                var totalWarmSec = Math.round(parseFloat(d.warmupValue) * 60);
                document.getElementById('warmupDurMin').value = Math.floor(totalWarmSec / 60);
                document.getElementById('warmupDurSec').value = totalWarmSec % 60;
            } else if (d.warmupType === 'distance' && d.warmupValue) {
                document.getElementById('warmupValue').value = d.warmupValue;
                document.getElementById('warmupUnit').value = d.warmupUnit || 'km';
            }

            state.intervalCount = parseInt(d.intervalCount) || 8;
            document.getElementById('intervalCount').textContent = state.intervalCount;

            state.highType = d.highType || 'distance';
            toggleOptions(document.querySelectorAll('.high-type-opt'), state.highType, 'option-active-orange');
            updateHighFields();
            if (d.highType === 'duration' && d.highValue) {
                var totalSec = Math.round(parseFloat(d.highValue) * 60);
                document.getElementById('highDurMin').value = Math.floor(totalSec / 60);
                document.getElementById('highDurSec').value = totalSec % 60;
            } else if (d.highValue) {
                document.getElementById('highValue').value = d.highValue;
                if (d.highUnit) document.getElementById('highUnit').value = d.highUnit;
            }

            var pace = parseFloat(d.targetPace) || 0;
            document.getElementById('targetPaceMin').value = Math.floor(pace) || '';
            document.getElementById('targetPaceSec').value = Math.round((pace - Math.floor(pace)) * 60) || '';

            state.recType = d.recType || 'distance';
            toggleOptions(document.querySelectorAll('.rec-type-opt'), state.recType, 'option-active-green');
            updateRecFields();
            if (d.recType === 'duration' && d.recValue) {
                var totalSec2 = Math.round(parseFloat(d.recValue) * 60);
                document.getElementById('recDurMin').value = Math.floor(totalSec2 / 60);
                document.getElementById('recDurSec').value = totalSec2 % 60;
            } else if (d.recValue) {
                document.getElementById('recValue').value = d.recValue;
                if (d.recUnit) document.getElementById('recUnit').value = d.recUnit;
            }

            state.recMode = d.recMode || 'jog';
            toggleOptions(document.querySelectorAll('.rec-mode-opt'), state.recMode, 'option-active-green');

            state.coolType = d.coolType || 'none';
            toggleOptions(document.querySelectorAll('.cool-opt'), state.coolType, 'option-active');
            updateCoolFields();
            if (d.coolType === 'duration' && d.coolValue) {
                var totalCoolSec = Math.round(parseFloat(d.coolValue) * 60);
                document.getElementById('coolDurMin').value = Math.floor(totalCoolSec / 60);
                document.getElementById('coolDurSec').value = totalCoolSec % 60;
            } else if (d.coolType === 'distance' && d.coolValue) {
                document.getElementById('coolValue').value = d.coolValue;
                document.getElementById('coolUnit').value = d.coolUnit || 'km';
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
            updateSummary();
        });
    });
});
</script>