const sendNotification = async () => {
    const registration = await navigator.serviceWorker.getRegistration();

    if (Notification.permission === "granted") {
        showNotification(registration, "This is a test", "All good here");
    } else {
        if (Notification.permission !== "denied") {
            const permission = await Notification.requestPermission();

            if (permission === "granted") {
                showNotification(registration, "This is a test", "All good here");
            }
        }
    }
};

const showNotification = (registration, title, message) => {
    const payload = {
        body: message,
        icon: "/images/icons/icon-512x512.png",
    };

    if (registration && "showNotification" in registration) {
        registration.showNotification(title, payload);
    } else {
        new Notification(title, payload);
    }
};

const showExpenseNotification = async (expense) => {
    const registration = await navigator.serviceWorker.getRegistration();
    const title = expense.amount + " expense - " + expense.category.name;
    const message = "Created by " + expense.user.name;

    if (Notification.permission === "granted") {
        showNotification(registration, title, message);
    } else {
        if (Notification.permission !== "denied") {
            const permission = await Notification.requestPermission();

            if (permission === "granted") {
                showNotification(registration, title, message);
            }
        }
    }
};

export { showExpenseNotification, sendNotification };
