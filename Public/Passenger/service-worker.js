const CACHE_NAME = "tshwanebusmate-cache-v1";
const urlsToCache = [
  "/",
  "/home.html",
  "/styles.css",
  "/icons/android-chrome-icon-192x192.png",
  "/icons/android-chrome-icon-512x512.png"
];

//install event – cache files
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(urlsToCache);
    })
  );
});

//fetch event – serve cached files if available
self.addEventListener("fetch", event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});
