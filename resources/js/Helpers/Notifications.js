const sendNotification = async () => {
    const registration = await navigator.serviceWorker.getRegistration();
    if (Notification.permission === "granted") {
        showNotification(registration, "All good here");
    } else {
        if (Notification.permission !== "denied") {
            const permission = await Notification.requestPermission();

            if (permission === "granted") {
                showNotification(registration, "All good here");
            }
        }
    }
};

const showNotification = (registration, message) => {
    const title = "This is a test";

    const payload = {
        body: message,
    };

    if (registration && "showNotification" in registration) {
        console.log("registration2", registration);
        registration.showNotification(title, payload);
    } else {
        new Notification(title, payload);
    }
};

const showExpenseNotification = (expense) => {
    if (Notification.permission === "granted") {
        // Create and display the notification
        const notification = new Notification(expense.amount + " expense - " + expense.category.name, {
            body: "Created by " + expense.user.name,
            icon: "/images/icons/icon-512x512.png",
        });

        // Handle click on the notification if necessary
        notification.onclick = () => {
            // Handle the click event, e.g., open a relevant page
            console.log("Notification clicked");
        };
    } else if (Notification.permission !== "denied") {
        // Ask for notification permission if not granted or denied
        Notification.requestPermission().then((permission) => {
            if (permission === "granted") {
                // Show the notification after permission is granted
                showNotification(notificationData);
            }
        });
    }
};

export { showExpenseNotification, sendNotification };
