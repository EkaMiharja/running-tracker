function formatLabel(d) {
    const parts = d.split('-');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1];
}

function formatPace(v) {
    const m = Math.floor(v);
    const s = Math.round((v - m) * 60);
    return m + ':' + String(s).padStart(2, '0');
}

document.addEventListener('DOMContentLoaded', function () {
    const distCanvas = document.getElementById('distChart');
    const paceCanvas = document.getElementById('paceChart');
    const tabs = document.querySelectorAll('.tab-btn');
    const dots = document.querySelectorAll('.dot');

    if (!distCanvas) return;

    let distChart = null;
    let paceChart = null;

    if (labels.length > 0) {
        distChart = new Chart(distCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels.map(formatLabel),
                datasets: [{
                    label: 'Jarak (km)',
                    data: values,
                    borderColor: '#fc5200',
                    backgroundColor: 'rgba(252, 82, 0, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#fc5200',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { color: 'rgba(0, 0, 0, 0.06)', display: true },
                        ticks: { color: '#9CA3AF', maxTicksLimit: 8, font: { size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.06)' },
                        ticks: { color: '#9CA3AF', font: { size: 11 }, callback: function (v) { return v + ' km'; } },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    if (paceLabels.length > 0) {
        paceChart = new Chart(paceCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: paceLabels.map(formatLabel),
                datasets: [{
                    label: 'Pace (min/km)',
                    data: paceValues,
                    borderColor: '#0EA5E9',
                    backgroundColor: 'rgba(14, 165, 233, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#0EA5E9',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { color: 'rgba(0, 0, 0, 0.06)', display: true },
                        ticks: { color: '#9CA3AF', maxTicksLimit: 8, font: { size: 11 } }
                    },
                    y: {
                        reverse: true,
                        grid: { color: 'rgba(0, 0, 0, 0.06)' },
                        ticks: {
                            color: '#9CA3AF', font: { size: 11 },
                            callback: function (v) { return formatPace(v); }
                        }
                    }
                }
            }
        });
    }

    function switchTab(index) {
        tabs.forEach(function (t, i) {
            if (i === index) {
                t.className = 'tab-btn flex-1 py-3 text-sm font-semibold text-center transition-colors bg-[#fc5200] text-white';
            } else {
                t.className = 'tab-btn flex-1 py-3 text-sm font-semibold text-center transition-colors text-[#9CA3AF] hover:text-[#1F2937]';
            }
        });

        dots.forEach(function (d, i) {
            d.className = 'dot w-2 h-2 rounded-full ' + (i === index ? 'bg-[#fc5200]' : 'bg-gray-300');
        });

        if (index === 0) {
            distCanvas.className = 'absolute inset-0 w-full h-full transition-opacity duration-300';
            paceCanvas.className = 'absolute inset-0 w-full h-full transition-opacity duration-300 opacity-0 pointer-events-none';
        } else {
            distCanvas.className = 'absolute inset-0 w-full h-full transition-opacity duration-300 opacity-0 pointer-events-none';
            paceCanvas.className = 'absolute inset-0 w-full h-full transition-opacity duration-300';
        }
    }

    tabs.forEach(function (btn, i) {
        btn.addEventListener('click', function () { switchTab(i); });
    });

    var touchStartX = 0;
    var touchEndX = 0;
    var container = document.getElementById('chartContainer');

    container.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    container.addEventListener('touchend', function (e) {
        touchEndX = e.changedTouches[0].screenX;
        var diff = touchStartX - touchEndX;
        var active = 0;
        tabs.forEach(function (t, i) {
            if (t.classList.contains('bg-[#fc5200]')) active = i;
        });
        if (Math.abs(diff) > 40) {
            if (diff > 0 && active < tabs.length - 1) switchTab(active + 1);
            else if (diff < 0 && active > 0) switchTab(active - 1);
        }
    }, { passive: true });
});
