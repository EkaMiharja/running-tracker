document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('trendChart');
    if (!canvas || labels.length === 0) return;

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.map(d => {
                const parts = d.split('-');
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1];
            }),
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
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0, 0, 0, 0.06)', display: true },
                    ticks: {
                        color: '#9CA3AF',
                        maxTicksLimit: 8,
                        font: { size: 11 }
                    }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.06)' },
                    ticks: {
                        color: '#9CA3AF',
                        font: { size: 11 },
                        callback: function (v) { return v + ' km'; }
                    },
                    beginAtZero: true
                }
            }
        }
    });
});
