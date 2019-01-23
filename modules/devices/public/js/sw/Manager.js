define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dojo/Evented",
  "sb/store/Api"
], function(declare, lang, Evented, Api) {
  return declare([Evented], {
    publicKey: null,
    serviceWorkerUrl: null,
    constructor: function() {
      this.isSubscribed = false;
      this.isSupported = 'serviceWorker' in navigator;
      this.isPushSupported = 'PushManager' in window;
      this.registration = null;
      this.subscription = null;
      this.collection = new Api({model: 'devices', action: 'select'});
    },
    register: function() {
      if(this.isSupported) {
        console.log("Service Worker API is supported. Attempting registration..");
        navigator.serviceWorker.register(this.serviceWorkerUrl).then(lang.hitch(this, function(registration) {
          // Registration was successful
          console.log('ServiceWorker registration successful: ', registration);
          this.onRegister(registration);
        }), function(err) {
          // registration failed :(
          console.log('ServiceWorker registration failed: ', err);
        });
      }
    },
    onRegister: function(registration) {
      this.registration = registration;
      this.emit("register");
      if (this.isPushSupported) {
        // Set the initial subscription value
        this.registration.pushManager.getSubscription().then(lang.hitch(this, function(subscription) {
          this.isSubscribed = !(subscription === null);
          this.updateSubscription(subscription);
          this.emit('registered');
        }));
      } else {
        this.emit('registered');
      }

    },
    subscribe: function() {
      const applicationServerKey = this.urlB64ToUint8Array(this.publicKey);
      this.registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey
      }).then(lang.hitch(this, function(subscription) {
        console.log('User is subscribed.');

        this.updateSubscription(subscription);

        this.isSubscribed = true;

        this.emit("subscribe");
      })).catch(lang.hitch(this, function(err) {
        console.log('Failed to subscribe the user: ', err);
        this.emit("error", {type: "subscribe", error: err});
        this.updateSubscription(null);
      }));
    },
    unsubscribe: function() {
      this.registration.pushManager.getSubscription().then(function(subscription) {
        if (subscription) {
          return subscription.unsubscribe();
        }
      }).catch(function(error) {
        this.emit("error", {type: "unsubscribe", error: error});
        console.log('Error unsubscribing', error);
      }).then(lang.hitch(this, function() {
        this.updateSubscription(null);

        console.log('User is unsubscribed.');
        this.isSubscribed = false;

        this.emit("unsubscribe");
      }));
    },
    updateSubscription: function(subscription) {
      this.subscription = subscription;
      if (this.subscription) {
        this.collection.put({token:JSON.stringify(this.subscription), platform: 'web', environment: 'default'}, {action: 'register'});
      }
    },
    urlB64ToUint8Array: function(base64String) {
      const padding = '='.repeat((4 - base64String.length % 4) % 4);
      const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

      const rawData = window.atob(base64);
      const outputArray = new Uint8Array(rawData.length);

      for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
      }
      return outputArray;
    }
  });
});