<!-- Web Application Manifest -->
<link rel="manifest" href="{{ route('laravelpwa.manifest') }}">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="{{ $config['theme_color'] }}">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="application-name" content="{{ $config['short_name'] }}">
<link rel="icon" sizes="{{ data_get(end($config['icons']), 'sizes') }}"
      href="{{ data_get(end($config['icons']), 'src') }}">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="apple-mobile-web-app-status-bar-style" content="{{  $config['status_bar'] }}">
<meta name="apple-mobile-web-app-title" content="{{ $config['short_name'] }}">
<link rel="apple-touch-icon" href="{{ data_get(end($config['icons']), 'src') }}">


<link href="{{ $config['splash']['640x1136'] }}"
      media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['750x1334'] }}"
      media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1242x2208'] }}"
      media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1125x2436'] }}"
      media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['828x1792'] }}"
      media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1242x2688'] }}"
      media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1536x2048'] }}"
      media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1668x2224'] }}"
      media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1668x2388'] }}"
      media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['2048x2732'] }}"
      media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="{{ $config['background_color'] }}">
<meta name="msapplication-TileImage" content="{{ data_get(end($config['icons']), 'src') }}">

<!--<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            window.registration = registration;
            console.log('registration1', registration);
            registration.showNotification("this is a notification");
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
</script>-->
<script>
    if ('serviceWorker' in navigator) {
        const registerServiceWorker = async () => {
            await navigator.serviceWorker.register('/serviceworker.js');
            const registration = await navigator.serviceWorker.ready;

            if (registration.waiting && registration.active) {
                console.log('new sw waiting');
                window.swNeedUpdate = true;
            }

            registration.onupdatefound = () => {
                const installingWorker = registration.installing;

                if (installingWorker) {
                    console.log('installing sw found');
                    installingWorker.onstatechange = async () => {
                        if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('new sw installed');
                            window.swNeedUpdate = true;

                            await SWHelper.prepareCachesForUpdate();
                            await SWHelper.skipWaiting();
                        }
                    };
                }

            };
        };

        registerServiceWorker();

        const SWHelper = {
            async getWaitingWorker() {
                const registrations = await navigator?.serviceWorker?.getRegistrations() || [];
                const registrationWithWaiting = registrations.find(reg => reg.waiting);
                return registrationWithWaiting?.waiting;
            },

            async skipWaiting() {
                return (await SWHelper.getWaitingWorker())?.postMessage({type: 'SKIP_WAITING'});
            },

            async prepareCachesForUpdate() {
                return (await SWHelper.getWaitingWorker())?.postMessage({type: 'PREPARE_CACHES_FOR_UPDATE'});
            }
        };

        window.addEventListener('beforeunload', async () => {
            if (window.swNeedUpdate) {
                console.log('send skipWaiting');
                await SWHelper.skipWaiting();
            }
        });
    }
</script>
