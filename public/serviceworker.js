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
    const data = e.data.json();
    const { title, message, interaction } = data;

    const options = {
        body: message,
        icon: "/images/icons/icon-512x512.png",
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
        },
        actions: [
            {
                action: "confirm",
                title: "OK",
            },
            {
                action: "close",
                title: "Close notification",
            },
        ],
        requireInteraction: interaction,
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

const notificationClickHandler = async (e) => {
    console.log("notification click", e.notification.tag);
    e.notification.close();
};

self.addEventListener("notificationclick", notificationClickHandler);
self.addEventListener("install", installHandler);
self.addEventListener("activate", activateHandler);
self.addEventListener("push", pushHandler);
