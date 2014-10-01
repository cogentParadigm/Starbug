define(['intern!object', 'intern/chai!assert', 'dojo/_base/lang', 'starbug/store/Api', 'sb/data', '../../support/apiMocker'], function (registerSuite, assert, lang, Api, sb, mocker) {
        registerSuite({
                name:'sb/data',
                setup:function() {
                  window.WEBSITE_URL = '/';
                  mocker.start();
                },
                teardown:function() {
                  mocker.stop();
                },
                get:function() {
                  var users = sb.get('users');
                  assert.strictEqual(users instanceof Api, true, 'sb.get should return a store');
                  var uris = sb.get('uris', 'test');
                  assert.strictEqual(uris instanceof Api, true, 'sb.get should return a store');
                },
                query:function() {
                  var promise = sb.query('users');
                  assert.strictEqual(lang.isFunction(promise.then), true, 'sb.query should return a promise');
                },
                error: function() {
                  var promise = sb.store('users', 'errors');
                  assert.strictEqual(lang.isFunction(promise.then), true, 'sb.query should return a promise');
                },
                store:function() {
                  var promise = sb.store('users', {first_name:'Ali', last_name:'Gangji'});
                  assert.strictEqual(lang.isFunction(promise.then), true, 'sb.store should return a promise');
                }
        });
});
