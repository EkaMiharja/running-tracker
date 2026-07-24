document.addEventListener('DOMContentLoaded', function () {
    var cfg = INTERVAL_CONFIG;

    var map = L.map('map', {
        zoomControl: true,
        attributionControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    var PageLockControl = L.Control.extend({
        options: { position: 'topright' },
        onAdd: function() {
            var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML =
                '<button id="pageLockBtn" title="Kunci halaman" style="background:white;width:36px;height:36px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' +
                '<svg id="pageLockIcon" width="18" height="18" viewBox="0 0 24 24" fill="#374151"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>' +
                '</button>';
            L.DomEvent.disableClickPropagation(div);
            return div;
        }
    });
    new PageLockControl().addTo(map);

    function syncPageLockState() {
        var icon = document.getElementById('pageLockIcon');
        var btn = document.getElementById('pageLockBtn');
        if (pageLocked) {
            document.body.classList.add('page-locked');
            if (icon) { icon.setAttribute('fill', '#EF4444'); icon.innerHTML = '<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/>'; }
            if (btn) btn.title = 'Kunci halaman aktif';
        } else {
            document.body.classList.remove('page-locked');
            if (icon) { icon.setAttribute('fill', '#374151'); icon.innerHTML = '<path d="M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6h1.9c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm0 12H6V10h12v10z"/>'; }
            if (btn) btn.title = 'Kunci halaman';
        }
    }

    document.getElementById('pageLockBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        pageLocked = !pageLocked;
        syncPageLockState();
    });

    var startBtn = document.getElementById('trackStart');
    var pauseBtn = document.getElementById('trackPause');
    var lockBtn = document.getElementById('trackLock');
    var lockIcon = document.getElementById('lockIcon');
    var finishBtn = document.getElementById('trackFinish');
    var startContainer = document.getElementById('startContainer');
    var controlBar = document.getElementById('controlBar');
    var distEl = document.getElementById('trackDistance');
    var timeEl = document.getElementById('trackTime');
    var paceEl = document.getElementById('trackPace');
    var gpsStatus = document.getElementById('gpsStatus');
    var gpsStatusText = document.getElementById('gpsStatusText');

    var intervalLabel = document.getElementById('intervalLabel');
    var intervalStatusText = document.getElementById('intervalStatusText');
    var intervalTimer = document.getElementById('intervalTimer');
    var circleTimerNum = document.getElementById('circleTimerNum');
    var circleTimerStatus = document.getElementById('circleTimerStatus');
    var progressRing = document.getElementById('progressRing');
    var headerCard = document.getElementById('headerCard');
    var viewMap = document.getElementById('viewMap');
    var viewTimer = document.getElementById('viewTimer');
    var tabMap = document.getElementById('tabMap');
    var tabTimer = document.getElementById('tabTimer');
    var viewContainer = document.getElementById('viewContainer');
    var currentView = 'map';
    var circumference = 2 * Math.PI * 108;
    var phaseTotalSeconds = 0;
    var tracking = false;
    var paused = false;
    var locked = true;
    var watchId = null;
    var pathCoords = [];
    var polyline = null;
    var userMarker = null;
    var startTime = null;
    var elapsedSeconds = 0;
    var timerInterval = null;
    var lastPos = null;
    var totalDistance = 0;
    var initialLocationSet = false;
    var pausedTime = 0;
    var pauseStartedAt = null;
    var kmMarkers = [];
    var pacePerKm = [];
    var distanceMarkers = [];
    var lastKmThreshold = 0;
    var kmStartTime = null;
    var kmStartPos = null;
    var pageLocked = false;

    var currentInterval = 0;
    var intervalPhase = 'warmup';
    var sessionTotals = [];
    var intervalStartTime = null;
    var intervalElapsed = 0;
    var intervalPausedTime = 0;
    var intervalPauseStartedAt = null;
    var warmupActive = cfg.warmup.type !== 'none' && cfg.warmup.value > 0;
    var cooldownPhase = false;

    function setGpsStatus(ok) {
        if (ok) {
            gpsStatus.className = 'w-3 h-3 rounded-full bg-[#fc5200] shadow-lg shadow-[#fc5200]/50';
            gpsStatusText.textContent = 'Lokasi aktif';
            gpsStatusText.className = 'text-xs text-[#fc5200]';
        } else {
            gpsStatus.className = 'w-3 h-3 rounded-full bg-[#EF4444] animate-pulse';
            gpsStatusText.textContent = 'Mencari lokasi...';
            gpsStatusText.className = 'text-xs text-[#EF4444]';
        }
    }

    setGpsStatus(false);

    function switchView(view) {
        currentView = view;
        if (view === 'map') {
            viewMap.classList.remove('hidden');
            viewTimer.classList.add('hidden');
            headerCard.classList.remove('hidden');
            tabMap.classList.add('bg-[#fc5200]', 'text-white');
            tabMap.classList.remove('text-[#9CA3AF]');
            tabTimer.classList.remove('bg-[#fc5200]', 'text-white');
            tabTimer.classList.add('text-[#9CA3AF]');
            setTimeout(function () { map.invalidateSize(); }, 100);
        } else {
            viewMap.classList.add('hidden');
            viewTimer.classList.remove('hidden');
            headerCard.classList.add('hidden');
            tabTimer.classList.add('bg-[#fc5200]', 'text-white');
            tabTimer.classList.remove('text-[#9CA3AF]');
            tabMap.classList.remove('bg-[#fc5200]', 'text-white');
            tabMap.classList.add('text-[#9CA3AF]');
        }
    }

    function updateCircularTimer() {
        var totalSec = getPhaseTotalSeconds();
        var remaining = totalSec - intervalElapsed;
        if (remaining < 0) remaining = 0;

        var displayTime;
        if (totalSec === Infinity) {
            displayTime = intervalElapsed;
        } else {
            displayTime = remaining;
        }

        circleTimerNum.textContent = formatTime(displayTime);
        circleTimerStatus.textContent = intervalStatusText.textContent;
        circleTimerStatus.style.color = intervalStatusText.style.color;

        if (totalSec !== Infinity && totalSec > 0) {
            var progress = remaining / totalSec;
            var offset = circumference * (1 - progress);
            progressRing.style.strokeDashoffset = offset;
        } else {
            progressRing.style.strokeDashoffset = 0;
        }
    }

    function resetCircularTimer() {
        progressRing.style.strokeDashoffset = 0;
        circleTimerNum.textContent = '00:00';
    }

    tabMap.addEventListener('click', function () { switchView('map'); });
    tabTimer.addEventListener('click', function () { switchView('timer'); });

    var touchStartX = 0;
    var touchStartY = 0;
    viewContainer.addEventListener('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    viewContainer.addEventListener('touchend', function (e) {
        var dx = e.changedTouches[0].clientX - touchStartX;
        var dy = e.changedTouches[0].clientY - touchStartY;
        if (Math.abs(dx) > 60 && Math.abs(dx) > Math.abs(dy) * 1.5) {
            if (dx < 0 && currentView === 'map') {
                switchView('timer');
            } else if (dx > 0 && currentView === 'timer') {
                switchView('map');
            }
        }
    }, { passive: true });

    function formatTime(secs) {
        var m = Math.floor(secs / 60);
        var s = Math.floor(secs % 60);
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function updateStats() {
        distEl.textContent = totalDistance.toFixed(2);

        var mins = Math.floor(elapsedSeconds / 60);
        var secs = Math.floor(elapsedSeconds % 60);
        timeEl.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

        if (totalDistance > 0) {
            var paceMin = elapsedSeconds / 60 / totalDistance;
            var pMin = Math.floor(paceMin);
            var pSec = Math.round((paceMin - pMin) * 60);
            paceEl.textContent = String(pMin).padStart(2, '0') + ':' + String(pSec).padStart(2, '0');
        } else {
            paceEl.textContent = '--:--';
        }
    }

    function haversineKm(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function getPhaseDuration(phaseType, phaseValue, phaseUnit) {
        if (phaseType === 'duration') {
            return phaseValue * 60;
        }
        return Infinity;
    }

    function getTargetValue(type, value, unit) {
        if (type === 'distance') {
            return unit === 'm' ? value / 1000 : value;
        }
        return Infinity;
    }

    var highTarget = getTargetValue(cfg.high.type, cfg.high.value, cfg.high.unit);
    var highDuration = getPhaseDuration(cfg.high.type, cfg.high.value, cfg.high.unit);
    var recTarget = getTargetValue(cfg.recovery.type, cfg.recovery.value, cfg.recovery.unit);
    var recDuration = getPhaseDuration(cfg.recovery.type, cfg.recovery.value, cfg.recovery.unit);
    var warmupTarget = warmupActive ? getTargetValue(cfg.warmup.type, cfg.warmup.value, cfg.warmup.unit) : 0;
    var warmupDuration = warmupActive ? getPhaseDuration(cfg.warmup.type, cfg.warmup.value, cfg.warmup.unit) : 0;
    var coolTarget = (cfg.cooldown.type !== 'none' && cfg.cooldown.value > 0) ? getTargetValue(cfg.cooldown.type, cfg.cooldown.value, cfg.cooldown.unit) : 0;
    var coolDuration = (cfg.cooldown.type !== 'none' && cfg.cooldown.value > 0) ? getPhaseDuration(cfg.cooldown.type, cfg.cooldown.value, cfg.cooldown.unit) : 0;

    var targetPaceSecPerKm = cfg.target_pace * 60;

    function getPhaseTotalSeconds() {
        if (intervalPhase === 'warmup') {
            if (cfg.warmup.type === 'duration') return warmupDuration;
            return Infinity;
        }
        if (intervalPhase === 'high') {
            if (cfg.high.type === 'duration') return highDuration;
            return highTarget * targetPaceSecPerKm;
        }
        if (intervalPhase === 'recovery') {
            if (cfg.recovery.type === 'duration') return recDuration;
            return Infinity;
        }
        if (intervalPhase === 'cooldown') {
            if (cfg.cooldown.type === 'duration') return coolDuration;
            return Infinity;
        }
        return Infinity;
    }

    var phaseDistanceAtStart = 0;

    function updateIntervalDisplay() {
        var totalIntervals = cfg.interval_count;
        var label = '';

        if (intervalPhase === 'warmup') {
            label = 'Warm-up';
            intervalStatusText.textContent = 'WARM-UP';
            intervalStatusText.style.color = '#3B82F6';
            progressRing.style.stroke = '#3B82F6';
        } else if (intervalPhase === 'high') {
            label = 'Interval ' + currentInterval + ' / ' + totalIntervals;
            intervalStatusText.textContent = 'HIGH INTENSITY';
            intervalStatusText.style.color = '#fc5200';
            progressRing.style.stroke = '#fc5200';
        } else if (intervalPhase === 'recovery') {
            label = 'Recovery ' + currentInterval + ' / ' + totalIntervals;
            intervalStatusText.textContent = 'RECOVERY';
            intervalStatusText.style.color = '#10B981';
            progressRing.style.stroke = '#10B981';
        } else if (intervalPhase === 'cooldown') {
            label = 'Cool-down';
            intervalStatusText.textContent = 'COOL-DOWN';
            intervalStatusText.style.color = '#3B82F6';
            progressRing.style.stroke = '#3B82F6';
        } else if (intervalPhase === 'done') {
            label = 'Selesai';
            intervalStatusText.textContent = 'FINISHED';
            intervalStatusText.style.color = '#6B7280';
            progressRing.style.stroke = '#6B7280';
        }

        intervalLabel.textContent = label;
    }

    function checkIntervalTransition() {
        var distSincePhaseStart = totalDistance - phaseDistanceAtStart;
        var phaseTime = intervalElapsed;

        if (intervalPhase === 'warmup') {
            var warmupMet = false;
            if (cfg.warmup.type === 'distance' && distSincePhaseStart >= warmupTarget) warmupMet = true;
            if (cfg.warmup.type === 'duration' && phaseTime >= warmupDuration) warmupMet = true;
            if (warmupMet) {
                phaseDistanceAtStart = totalDistance;
                intervalElapsed = 0;
                intervalStartTime = Date.now() - pausedTime;
                intervalPausedTime = 0;
                currentInterval = 1;
                intervalPhase = 'high';
                resetCircularTimer();
                updateIntervalDisplay();
            }
        } else if (intervalPhase === 'high') {
            var highMet = false;
            if (cfg.high.type === 'distance' && distSincePhaseStart >= highTarget) highMet = true;
            if (cfg.high.type === 'duration' && phaseTime >= highDuration) highMet = true;
            if (highMet) {
                sessionTotals.push({ type: 'high', interval: currentInterval, time: Math.round(phaseTime), distance: parseFloat(distSincePhaseStart.toFixed(4)) });
                phaseDistanceAtStart = totalDistance;
                intervalElapsed = 0;
                intervalStartTime = Date.now() - pausedTime;
                intervalPausedTime = 0;
                intervalPhase = 'recovery';
                resetCircularTimer();
                updateIntervalDisplay();
            }
        } else if (intervalPhase === 'recovery') {
            var recMet = false;
            if (cfg.recovery.type === 'distance' && distSincePhaseStart >= recTarget) recMet = true;
            if (cfg.recovery.type === 'duration' && phaseTime >= recDuration) recMet = true;
            if (recMet) {
                sessionTotals.push({ type: 'recovery', interval: currentInterval, time: Math.round(phaseTime), distance: parseFloat(distSincePhaseStart.toFixed(4)) });
                phaseDistanceAtStart = totalDistance;
                intervalElapsed = 0;
                intervalStartTime = Date.now() - pausedTime;
                intervalPausedTime = 0;
                currentInterval++;
                if (currentInterval > cfg.interval_count) {
                    if (cfg.cooldown.type !== 'none' && cfg.cooldown.value > 0) {
                        intervalPhase = 'cooldown';
                    } else {
                        intervalPhase = 'done';
                    }
                } else {
                    intervalPhase = 'high';
                }
                resetCircularTimer();
                updateIntervalDisplay();
            }
        } else if (intervalPhase === 'cooldown') {
            var coolMet = false;
            if (cfg.cooldown.type === 'distance' && distSincePhaseStart >= coolTarget) coolMet = true;
            if (cfg.cooldown.type === 'duration' && phaseTime >= coolDuration) coolMet = true;
            if (coolMet) {
                intervalPhase = 'done';
                resetCircularTimer();
                updateIntervalDisplay();
            }
        }
    }

    var gpsNoiseCount = 0;

    function onPositionSuccess(pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;

        setGpsStatus(true);

        if (!initialLocationSet) {
            initialLocationSet = true;
            map.setView([lat, lng], 17);

            userMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'user-location-marker',
                    html: '<div style="background:#fc5200;width:18px;height:18px;border-radius:50%;border:3px solid white;box-shadow:0 0 0 4px rgba(252,82,0,0.3),0 2px 6px rgba(0,0,0,0.4);"></div>',
                    iconSize: [18, 18],
                    iconAnchor: [9, 9]
                })
            }).addTo(map);
        }

        if (!tracking || paused) return;

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        }

        if (lastPos) {
            var d = haversineKm(lastPos[0], lastPos[1], lat, lng);

            if (d < 0.001) {
                gpsNoiseCount++;
                if (gpsNoiseCount < 3) return;
            }
            gpsNoiseCount = 0;

            var prevKm = Math.floor(totalDistance);
            totalDistance += d;
            updateStats();

            var currentKm = Math.floor(totalDistance);
            if (currentKm > lastKmThreshold) {
                var currentTime = Date.now() - pausedTime;
                var segTime = kmStartTime ? (currentTime - kmStartTime) / 1000 : (currentTime - startTime) / 1000;
                var segDist = currentKm - (kmStartPos ? lastKmThreshold : 0);
                var segPaceMin = segDist > 0 ? segTime / 60 / segDist : 0;
                var cumTime = Math.round((currentTime - startTime) / 1000);
                var cumMins = Math.floor(cumTime / 60);
                var cumSecs = cumTime % 60;

                var paceMins = Math.floor(segPaceMin);
                var paceSecs = Math.round((segPaceMin - paceMins) * 60);
                var paceStr = paceMins + ':' + String(paceSecs).padStart(2, '0');

                pacePerKm.push({
                    km: currentKm,
                    pace: paceStr,
                    time: cumMins + ':' + String(cumSecs).padStart(2, '0')
                });

                distanceMarkers.push({
                    km: currentKm,
                    lat: lat,
                    lng: lng
                });

                var markerIcon = L.divIcon({
                    className: 'km-marker',
                    html: '<div style="background:#fc5200;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + currentKm + '</div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                var marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);
                kmMarkers.push(marker);

                lastKmThreshold = currentKm;
                kmStartTime = Date.now() - pausedTime;
                kmStartPos = [lat, lng];
            }

            if (d > 0.002) {
                pathCoords.push([lat, lng]);

                if (polyline) {
                    polyline.setLatLngs(pathCoords);
                } else {
                    polyline = L.polyline(pathCoords, {
                        color: '#fc5200',
                        weight: 4,
                        opacity: 0.8
                    }).addTo(map);
                }

                map.setView([lat, lng], map.getZoom());
            }
        }

        lastPos = [lat, lng];
    }

    function onPositionError(err) {
        console.warn('GPS error:', err.message);
        setGpsStatus(false);

        if (!initialLocationSet) {
            map.setView([-6.2088, 106.8456], 15);
            initialLocationSet = true;
        }
    }

    function startTracking() {
        tracking = true;
        paused = false;
        locked = true;
        totalDistance = 0;
        elapsedSeconds = 0;
        lastPos = null;
        pathCoords = [];
        pausedTime = 0;
        pauseStartedAt = null;
        lastKmThreshold = 0;
        pacePerKm = [];
        distanceMarkers = [];
        kmStartTime = null;
        kmStartPos = null;
        startTime = Date.now();

        currentInterval = 0;
        intervalPhase = warmupActive ? 'warmup' : 'high';
        if (intervalPhase === 'high') currentInterval = 1;
        intervalElapsed = 0;
        intervalStartTime = Date.now();
        intervalPausedTime = 0;
        intervalPauseStartedAt = null;
        phaseDistanceAtStart = 0;
        sessionTotals = [];
        cooldownPhase = false;

        kmMarkers.forEach(function (m) { map.removeLayer(m); });
        kmMarkers = [];

        if (polyline) { map.removeLayer(polyline); polyline = null; }

        updateStats();
        updateIntervalDisplay();

        startContainer.classList.add('hidden');
        controlBar.classList.remove('hidden');
        finishBtn.disabled = true;
        finishBtn.classList.remove('unlocked');
        lockBtn.classList.remove('unlocked');
        lockIcon.innerHTML = '<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/>';

        pauseBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg> <span>PAUSE</span>';
        pauseBtn.classList.remove('resume-state');

        timerInterval = setInterval(function () {
            if (!paused) {
                elapsedSeconds = (Date.now() - startTime - pausedTime) / 1000;
                intervalElapsed = (Date.now() - intervalStartTime - intervalPausedTime) / 1000;
                updateStats();

                var totalSec = getPhaseTotalSeconds();
                var remainingSec = totalSec - intervalElapsed;
                if (remainingSec < 0) remainingSec = 0;
                if (totalSec === Infinity) {
                    intervalTimer.textContent = formatTime(intervalElapsed);
                } else {
                    intervalTimer.textContent = formatTime(remainingSec);
                }

                updateCircularTimer();
                checkIntervalTransition();
            }
        }, 200);
    }

    function togglePause() {
        if (!tracking || locked) return;

        if (paused) {
            paused = false;
            pausedTime += Date.now() - pauseStartedAt;
            intervalPausedTime += Date.now() - intervalPauseStartedAt;
            pauseStartedAt = null;
            intervalPauseStartedAt = null;
            pauseBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg> <span>PAUSE</span>';
            pauseBtn.classList.remove('resume-state');
        } else {
            paused = true;
            pauseStartedAt = Date.now();
            intervalPauseStartedAt = Date.now();
            pauseBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> <span>RESUME</span>';
            pauseBtn.classList.add('resume-state');
        }
    }

    function toggleLock() {
        if (!tracking) return;

        if (locked) {
            locked = false;
            finishBtn.disabled = false;
            pauseBtn.disabled = false;
            lockBtn.classList.add('unlocked');
            lockIcon.innerHTML = '<path d="M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6h1.9c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm0 12H6V10h12v10z"/>';
        } else {
            locked = true;
            finishBtn.disabled = true;
            pauseBtn.disabled = true;
            lockBtn.classList.remove('unlocked');
            lockIcon.innerHTML = '<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/>';
        }
    }

    function finishRun() {
        if (locked) return;

        tracking = false;
        paused = false;
        if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }

        var distance = totalDistance;
        var duration = Math.round(elapsedSeconds);
        var pace = distance > 0 ? duration / 60 / distance : 0;
        var routeJson = pathCoords.length > 0 ? JSON.stringify(pathCoords) : null;

        var paceKm = distance > 1 && pacePerKm.length > 0 ? pacePerKm : [];
        var distMarkers = distanceMarkers.length > 0 ? distanceMarkers : [];

        var intervalData = {
            config: cfg,
            phases: sessionTotals,
            current_phase: intervalPhase,
            current_interval: currentInterval
        };

        fetch('../api/save_activity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                distance: distance.toFixed(2),
                duration: duration,
                pace: pace.toFixed(2),
                route_path: routeJson,
                pace_per_km: paceKm,
                distance_markers: distMarkers,
                type: 'interval',
                workout_name: cfg.workout_name,
                interval_data: intervalData
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                window.location.href = 'detail.php?id=' + data.id;
            } else {
                alert('Gagal menyimpan: ' + (data.error || 'Unknown error'));
                resetUI();
            }
        })
        .catch(function () {
            alert('Network error');
            resetUI();
        });
    }

    function resetUI() {
        startContainer.classList.remove('hidden');
        controlBar.classList.add('hidden');
        distEl.textContent = '0.00';
        timeEl.textContent = '00:00';
        paceEl.textContent = '--:--';
        intervalTimer.textContent = '00:00';
        intervalStatusText.textContent = 'READY';
        intervalStatusText.style.color = '#6B7280';
        intervalLabel.textContent = 'Interval';
        resetCircularTimer();
        progressRing.style.stroke = '#fc5200';
        switchView('map');

        locked = true;
        finishBtn.disabled = true;
        lockBtn.classList.remove('unlocked');
        lockIcon.innerHTML = '<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/>';

        pauseBtn.disabled = true;
        pauseBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg> <span>PAUSE</span>';
        pauseBtn.classList.remove('resume-state');
    }

    startBtn.addEventListener('click', startTracking);
    pauseBtn.addEventListener('click', togglePause);
    lockBtn.addEventListener('click', toggleLock);
    finishBtn.addEventListener('click', finishRun);

    if ('geolocation' in navigator) {
        watchId = navigator.geolocation.watchPosition(onPositionSuccess, onPositionError, {
            enableHighAccuracy: true,
            maximumAge: 3000,
            timeout: 10000
        });
    } else {
        setGpsStatus(false);
        map.setView([-6.2088, 106.8456], 15);
        initialLocationSet = true;
    }

    setTimeout(function () { map.invalidateSize(); }, 300);
});