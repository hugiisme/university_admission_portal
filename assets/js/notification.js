function displayNotificationBox() {
    const notifications = document.querySelectorAll('.notification');

    notifications.forEach(notification => {
        // Get timeout from the data-timeout attribute
        const timeout = parseInt(notification.getAttribute('data-timeout'), 10);

        // Add the 'show' class to make it visible
        notification.classList.add('show');

        // After the timeout, hide the notification
        setTimeout(() => {
            notification.classList.remove('show');
            notification.classList.add('hide');  // This will hide the notification
        }, timeout);  // Timeout should match the value in 'data-timeout'
    });
}

window.onload = function() {
    displayNotificationBox();
};
