'use strict';

var data;

self.addEventListener('push', function(e) {
  data = {};
  if (e.data) {
    data = e.data.json();
  }
  data.url = data.url || false;
  e.waitUntil(
    self.registration.showNotification(data.subject, data)
  );
});
self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  if (data.url) {
    event.waitUntil(
      clients.openWindow(data.url)
    );
  }
});