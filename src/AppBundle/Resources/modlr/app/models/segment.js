import DS from 'ember-data';
import Keyable from 'modlr/models/mixins/keyable';

export default DS.Model.extend(Keyable, {
    workspace: DS.belongsTo('workspace')
});
