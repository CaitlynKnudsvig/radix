import Ember from 'ember';

export default Ember.Route.extend({

    confirm: Ember.inject.service(),

    templateName: 'demographic/questions/edit',

    model: function() {
        return this.store.createRecord('demographic');
    },

    actions: {
        willTransition: function(transition) {
            let model = this.controller.get('model');
            this.get('confirm').unsaved(model, model.get('name'), transition, false, function() {
                model.rollbackAttributes();
            });

            if (!model.get('hasDirtyAttributes')) {
                this.send('recordAdded');
            }
        }
    }
});
