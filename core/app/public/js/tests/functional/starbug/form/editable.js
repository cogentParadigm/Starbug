define(['intern!object','intern/chai!assert','require'], function (registerSuite, assert, require) {
    registerSuite({
        name: 'editable',

        'test': function () {
          return this.remote.get(require.toUrl("core/app/public/js/tests/starbug/form/editable.html"))
                .setFindTimeout(10000)
                .findById('test1')
                .getVisibleText().then(function(text) {
                  //verify initial state
                  assert.strictEqual(text, 'This is editable text');
                })
                .click()
                .end()
                .execute("return dijit.byId('dijit_form_Button_1')._onClick({});")
                .findById('test1')
                .getVisibleText().then(function(text) {
                  //verify state again after edit + cancel
                  assert.strictEqual(text, 'This is editable text');
                })
                .click()
                .end()
                .findByCssSelector('input')
                .type("This is new text")
                .end()
                .execute("return dijit.byId('dijit_form_Button_0')._onClick({});")
                .findById('test1')
                .getVisibleText().then(function(text) {
                  //verify state again after edit + save
                  assert.strictEqual(text, 'This is new text');
                })
                .end()
                .execute("return store.data[0].label;")
                .then(function(val) {
                  //verify propagation to store
                  assert.strictEqual(val, 'This is new text');
                })
                .findById('test2')
                .getVisibleText().then(function(text) {
                  //verify initial state
                  assert.strictEqual(text, 'Second Option');
                })
                .click()
                .end()
                .execute("return dijit.byId('dijit_form_Button_5')._onClick({});")
                .findById('test2')
                .getVisibleText().then(function(text) {
                  //verify state again after edit + cancel
                  assert.strictEqual(text, 'Second Option');
                })
                .click()
                .end()
                .execute("return dijit.byId('dijit_form_Select_1').setValue(3);")
                .execute("return dijit.byId('dijit_form_Button_4')._onClick({});")
                .findById('test2')
                .getVisibleText().then(function(text) {
                  //verify state again after edit + save
                  assert.strictEqual(text, 'Third Option');
                })
                .end()
                .execute("return store.data[1].x;")
                .then(function(val) {
                  //verify propagation to store
                  assert.strictEqual(val, 3);
                })
                .findById('test3')
                .getVisibleText().then(function(text) {
                  //verify initial state
                  assert.strictEqual(text, 'This is a textarea');
                })
                .click()
                .end()
                .execute("return dijit.byId('dijit_form_Button_9')._onClick({});")
                .findById('test3')
                .getVisibleText().then(function(text) {
                  //verify state again after edit + cancel
                  assert.strictEqual(text, 'This is a textarea');
                })
                .click()
                .end()
                .execute("dijit.byId('dijit_form_Textarea_1').set('value', 'This is new textarea content');")
                .execute("return dijit.byId('dijit_form_Button_8')._onClick({});")
                .findById('test3')
                .getVisibleText().then(function(text) {
                  //verify state again after edit + save
                  assert.strictEqual(text, 'This is new textarea content');
                })
                .end()
                .execute("return store.data[2].msg;")
                .then(function(val) {
                  //verify propagation to store
                  assert.strictEqual(val, 'This is new textarea content');
                });
        }
    });
});
