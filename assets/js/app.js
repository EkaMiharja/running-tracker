document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('[class*="bg-\\[\\#EF4444\\]\\/10"], [class*="bg-\\[\\#fc5200\\]\\/10"]');
    if (alerts.length > 0) {
        setTimeout(function () {
            alerts.forEach(function (el) {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            });
        }, 5000);
    }
});
