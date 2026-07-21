document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map', {
        zoomControl: true,
        attributionControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    const startBtn = document.getElementById('trackStart');
    const pauseBtn = document.getElementById('trackPause');
    const resumeBtn = document.getElementById('trackResume');
    const stopBtn = document.getElementById('trackStop');
    const distEl = document.getElementById('trackDistance');
    const timeEl = document.getElementById('trackTime');
    const paceEl = document.getElementById('trackPace');
    const gpsStatus = document.getElementById('gpsStatus');
    const gpsStatusText = document.getElementById('gpsStatusText');

    let tracking = false;
    let paused = false;
    let watchId = null;
    let pathCoords = [];
    let polyline = null;
    let userMarker = null;
    let startTime = null;
    let elapsedSeconds = 0;
    let timerInterval = null;
    let lastPos = null;
    let totalDistance = 0;
    let initialLocationSet = false;
    let pausedTime = 0;
    let pauseStartedAt = null;
    let kmMarkers = [];
    let pacePerKm = [];
    let distanceMarkers = [];
    let lastKmThreshold = 0;
    let kmStartTime = null;
    let kmStartPos = null;

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

    function updateStats() {
        distEl.textContent = totalDistance.toFixed(2);

        const mins = Math.floor(elapsedSeconds / 60);
        const secs = Math.floor(elapsedSeconds % 60);
        timeEl.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

        if (totalDistance > 0) {
            const paceMin = elapsedSeconds / 60 / totalDistance;
            const pMin = Math.floor(paceMin);
            const pSec = Math.round((paceMin - pMin) * 60);
            paceEl.textContent = String(pMin).padStart(2, '0') + ':' + String(pSec).padStart(2, '0');
        } else {
            paceEl.textContent = '--:--';
        }
    }

    function haversineKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    let gpsNoiseCount = 0;

    function onPositionSuccess(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

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
            const d = haversineKm(lastPos[0], lastPos[1], lat, lng);

            if (d < 0.001) {
                gpsNoiseCount++;
                if (gpsNoiseCount < 3) return;
            }
            gpsNoiseCount = 0;

            const prevKm = Math.floor(totalDistance);
            totalDistance += d;
            updateStats();

            const currentKm = Math.floor(totalDistance);
            if (currentKm > lastKmThreshold) {
                const currentTime = Date.now() - pausedTime;
                const segTime = kmStartTime ? (currentTime - kmStartTime) / 1000 : (currentTime - startTime) / 1000;
                const segDist = currentKm - (kmStartPos ? lastKmThreshold : 0);
                const segPaceMin = segDist > 0 ? segTime / 60 / segDist : 0;
                const cumTime = Math.round((currentTime - startTime) / 1000);
                const cumMins = Math.floor(cumTime / 60);
                const cumSecs = cumTime % 60;

                const paceMins = Math.floor(segPaceMin);
                const paceSecs = Math.round((segPaceMin - paceMins) * 60);
                const paceStr = paceMins + ':' + String(paceSecs).padStart(2, '0');

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

                const markerIcon = L.divIcon({
                    className: 'km-marker',
                    html: '<div style="background:#fc5200;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + currentKm + '</div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);
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

        kmMarkers.forEach(function (m) { map.removeLayer(m); });
        kmMarkers = [];

        if (polyline) { map.removeLayer(polyline); polyline = null; }

        updateStats();
        startBtn.classList.add('hidden');
        pauseBtn.classList.remove('hidden');
        stopBtn.classList.remove('hidden');
        resumeBtn.classList.add('hidden');

        timerInterval = setInterval(() => {
            if (!paused) {
                elapsedSeconds = (Date.now() - startTime - pausedTime) / 1000;
                updateStats();
            }
        }, 200);
    }

    function pauseTracking() {
        paused = true;
        pauseStartedAt = Date.now();
        pauseBtn.classList.add('hidden');
        resumeBtn.classList.remove('hidden');
        stopBtn.classList.remove('hidden');
    }

    function resumeTracking() {
        paused = false;
        pausedTime += Date.now() - pauseStartedAt;
        pauseStartedAt = null;
        resumeBtn.classList.add('hidden');
        pauseBtn.classList.remove('hidden');
        stopBtn.classList.remove('hidden');
    }

    function stopTracking() {
        tracking = false;
        paused = false;
        if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }

        const distance = totalDistance;
        const duration = Math.round(elapsedSeconds);
        const pace = distance > 0 ? duration / 60 / distance : 0;
        const routeJson = pathCoords.length > 0 ? JSON.stringify(pathCoords) : null;

        const paceKm = distance > 1 && pacePerKm.length > 0 ? pacePerKm : [];
        const distMarkers = distanceMarkers.length > 0 ? distanceMarkers : [];

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
                type: 'gps'
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'detail.php?id=' + data.id;
            } else {
                alert('Gagal menyimpan: ' + (data.error || 'Unknown error'));
                resetUI();
            }
        })
        .catch(err => {
            alert('Network error');
            resetUI();
        });
    }

    function resetUI() {
        startBtn.classList.remove('hidden');
        pauseBtn.classList.add('hidden');
        resumeBtn.classList.add('hidden');
        stopBtn.classList.add('hidden');
        distEl.textContent = '0.00';
        timeEl.textContent = '00:00';
        paceEl.textContent = '--:--';
    }

    startBtn.addEventListener('click', startTracking);
    pauseBtn.addEventListener('click', pauseTracking);
    resumeBtn.addEventListener('click', resumeTracking);
    stopBtn.addEventListener('click', stopTracking);

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

    setTimeout(() => map.invalidateSize(), 300);
});
