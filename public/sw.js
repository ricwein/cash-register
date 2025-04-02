const DB_NAME = "offline-requests";
const STORE_NAME = "requests";

// Open IndexedDB
function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);
        request.onerror = () => reject("Failed to open IndexedDB");
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, {autoIncrement: true});
            }
        };
    });
}

function sendSomething(event) {
    clients.get(event.clientId).then(client => {
        client.postMessage({type: "request-failed", url: event.request.url});
    });
}

async function saveRequest(request) {
    const db = await openDatabase();
    const tx = db.transaction(STORE_NAME, "readwrite");
    const store = tx.objectStore(STORE_NAME);

    const clonedRequest = {
        url: request.url,
        method: request.method,
        headers: Object.fromEntries(request.headers.entries()),
        body: await request.clone().text(),
    };

    store.add(clonedRequest);
}

self.addEventListener('fetch', event => {
    if (event.request.method === 'GET') {
        return
    }

    const url = new URL(event.request.url);
    if (!/^\/app\/\d+\/send/.test(url.pathname)) {
        return
    }

    const requestClone = event.request.clone(); // Clone the request for saving

    event.respondWith(
        fetch(event.request)
            .catch(async () => {
                const clonedRequest = {
                    url: requestClone.url,
                    method: requestClone.method,
                    headers: Object.fromEntries(requestClone.headers.entries()),
                    body: requestClone.method !== "GET" && requestClone.method !== "HEAD"
                        ? await requestClone.text()
                        : null, // Read body only if it's not GET/HEAD
                };

                await saveRequest(clonedRequest);
                return new Response(JSON.stringify({message: "Server nicht erreichbar, Zahlung wird später gesendet.", state: 'offline'}), {
                    headers: {"Content-Type": "application/json"},
                });
            })
    )
})

// Notify when the service worker is activated
self.addEventListener("activate", (event) => {
    event.waitUntil(clients.claim());
});
