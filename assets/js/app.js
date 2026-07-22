document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('div.border').forEach(function (el) {
        if (el.classList.contains('bg-[#EF4444]/10') || el.classList.contains('bg-[#fc5200]/10')) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            }, 5000);
        }
    });
});
