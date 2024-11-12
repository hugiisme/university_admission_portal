document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
        item.classList.add('active');
    });
});
