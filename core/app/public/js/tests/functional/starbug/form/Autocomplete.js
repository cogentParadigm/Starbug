define([
    'intern!object',
    'intern/chai!assert',
    'require'
], function (registerSuite, assert, require, keys) {
    registerSuite({
        name: 'autocomplete',

        'find': function () {
          return this.remote.get(require.toUrl("core/app/public/js/tests/starbug/form/Autocomplete.html"))
                .setFindTimeout(5000)
                .findById('test1')
                .click()
                .pressKeys('First')
                .end()
                .findByCssSelector('.autocomplete-item')
                .click()
                .end()
                .findById('test1')
                .getProperty('value')
                .then(function(text) {
                  assert.strictEqual(text, 'First Item');
                })
                .end()
                .findByCssSelector('#test1 + input')
                .getProperty('value')
                .then(function(text) {
                  assert.strictEqual(text, '1');
                })
                .end()
                .findById('test1')
                .click()
                .pressKeys("\uE010\uE003\uE003\uE003\uE003\uE003\uE003\uE003\uE003\uE003\uE003")
                .end()
                .findByCssSelector('#test1 + input')
                .getProperty('value')
                .then(function(text) {
                  assert.strictEqual(text, '');
                })
                .end()
                .findById('test2')
                .getProperty('value')
                .then(function(text) {
                  assert.strictEqual(text, 'Second Item');
                });
        }
    });
});
