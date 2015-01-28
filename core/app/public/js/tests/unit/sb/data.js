define(['intern!object', 'intern/chai!assert', 'dojo/_base/lang', 'sb/store/Api', 'sb/data', '../../support/apiMocker'], function (registerSuite, assert, lang, Api, sb, mocker) {
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
                  var users = sb.query('users', 'admin', {value:'test'});
                  assert.strictEqual(users instanceof Api, true, 'sb.query should return a store');
                  assert.strictEqual(users.queryLog.length, 1, 'the store should have exactly one entry in the query log');
                  assert.strictEqual(users.queryLog[0].type, 'filter', 'the query log entry should be a filter');
                  assert.strictEqual(users.queryLog[0].arguments[0].value, 'test', 'the query log should have the argument we passed in');
                },
                error: function() {
                  //var promise = sb.store('users', 'errors');
                  //assert.strictEqual(lang.isFunction(promise.then), true, 'sb.store should return a promise');
                },
                store:function() {
                  //var promise = sb.store('users', {first_name:'Ali', last_name:'Gangji'});
                  //assert.strictEqual(lang.isFunction(promise.then), true, 'sb.store should return a promise');
                }
        });
});
