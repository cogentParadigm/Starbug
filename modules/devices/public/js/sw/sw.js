'use strict';

var data;

self.addEventListener('push', function(e) {
  data = {};
  if (e.data) {
    data = e.data.json();
  }
  data.url = data.url || false;
  var options = {
    body: data.body,
  };
  e.waitUntil(
    self.registration.showNotification(data.subject, data)
  );
});
self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  if (URL) {
    event.waitUntil(
      clients.openWindow(URL)
    );
    URL = false;
  }
});