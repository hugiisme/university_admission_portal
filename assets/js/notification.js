function displayNotificationBox() {
    const notifications = document.querySelectorAll('.notification');

    notifications.forEach(notification => {
        const timeout = parseInt(notification.getAttribute('data-timeout'), 10);

        notification.classList.add('show');

        setTimeout(() => {
            notification.classList.remove('show');
            notification.classList.add('hide');  
        }, timeout);  
    });
}

window.onload = function() {
    displayNotificationBox();
};

