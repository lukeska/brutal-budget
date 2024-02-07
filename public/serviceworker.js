const buildFiles = [
    "/offline",
    "/css/app.css",
    "/js/app.js",
    "/images/icons/icon-72x72.png",
    "/images/icons/icon-96x96.png",
    "/images/icons/icon-128x128.png",
    "/images/icons/icon-144x144.png",
    "/images/icons/icon-152x152.png",
    "/images/icons/icon-192x192.png",
    "/images/icons/icon-384x384.png",
    "/images/icons/icon-512x512.png",
];

const staticFiles = [];

const routes = [];

const filesToCache = [...buildFiles, ...staticFiles, ...routes];

self.numBadges = 0;
const version = Date.now();

const cacheName = `bb-cache-${version}`;

const debug = true;

const log = debug ? console.log.bind(console) : () => {};

const installHandler = (e) => {
    e.waitUntil(
        self.clients
            .matchAll({
                includeUncontrolled: true,
            })
            .then((clients) => {
                caches
                    .open(cacheName)
                    .then((cache) =>
                        cache.addAll(filesToCache.map((file) => new Request(file, { cache: "no-cache" }))),
                    );
            }),
    );
};

const activateHandler = (e) => {
    // log('[ServiceWorker] Activate');
    // sendMessage({msg: 'activate'});

    e.waitUntil(
        caches
            .keys()
            .then((names) =>
                Promise.all(names.filter((name) => name !== cacheName).map((name) => caches.delete(name))),
            ),
    );
};

const getClients = async () =>
    await self.clients.matchAll({
        includeUncontrolled: true,
    });

const hasActiveClients = async () => {
    const clients = await getClients();

    return clients.some(({ visibilityState }) => visibilityState === "visible");
};

const sendMessage = async (message) => {
    const clients = await getClients();

    clients.forEach((client) => client.postMessage({ type: "message", message }));
};

const pushHandler = async (e) => {
    console.log(e.data.json());
    const fullData = e.data.json();
    const { title, body, data, interaction, actions, tag } = fullData;

    const options = {
        body: body,
        icon: null,
        //icon: "/images/icons/icon-512x512.png",
        badge: "/images/icons/badge.png",
        vibrate: [100, 50, 100],
        data: data,
        actions: actions,
        requireInteraction: interaction,
        tag: tag,
    };

    e.waitUntil(
        self.registration
            .showNotification(title, options)
            .then(hasActiveClients)
            .then((activeClients) => {
                if (!activeClients) {
                    console.log("set badge");
                    self.numBadges += 1;
                    navigator.setAppBadge(self.numBadges);

                    sendMessage(`badges: ${self.numBadges}`);
                } else {
                    sendMessage("no badge");
                }
            })
            .catch((err) => sendMessage(err)),
    );
};

const messageHandler = async ({ data }) => {
    console.log("message", data);

    const { type } = data;

    switch (type) {
        case "clearBadges":
            self.numBadges = 0;
            if ("clearAppBadge" in navigator) {
                navigator.clearAppBadge();
            }
            break;

        case "SKIP_WAITING":
            const clients = await self.clients.matchAll({
                includeUncontrolled: true,
            });

            if (clients.length < 2) {
                self.skipWaiting();
            }

            break;
    }
};

const notificationClickHandler = async (e) => {
    console.log("notification event 2", e);
    console.log("notification click", e.notification);
    e.notification.close();

    if (e.action === "view_expense") {
        e.waitUntil(
            clients.matchAll().then(function (clientList) {
                if (clientList && clientList.length) {
                    clientList[0].navigate(e.notification.data.destination_url); // Navigate the first available client
                } else {
                    // If no client is available, open a new window with the specified URL
                    clients.openWindow(e.notification.data.destination_url);
                }
            }),
        );
    }
};

self.addEventListener("notificationclick", notificationClickHandler);
self.addEventListener("install", installHandler);
self.addEventListener("activate", activateHandler);
self.addEventListener("push", pushHandler);
self.addEventListener("message", messageHandler);
