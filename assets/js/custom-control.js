(function ($) {

    $(window).on('load', function () {

        var e = elementor,
            ep = elementorPro,
            MailoptinIntegration = {
                fields: moElementor.fields,
                cache: {},

                getName: function getName() {
                    return 'Sendmsg';
                },

                onElementChange: function onElementChange(setting) {
                    var self = this;
                    self.updateFieldsMap();
                },

                onSectionActive: function onSectionActive() {
                    this.updateFieldsMap();
                },

                updateTags: function updateTags() {

                },

                removeControlSpinner: function removeControlSpinner(name) {
                    var $controlEl = this.getEditorControlView(name).$el;

                    $controlEl.find(':input').attr('disabled', false);
                    $controlEl.find('.elementor-control-spinner').remove();
                },

                updateFieldsMap: function updateFieldsMap() {
                    var self = this, data, key, controlView = self.getEditorControlView('sendmsg_url');


                    data = {
                        'action': 'mo_elementor_fetch_custom_fields'
                    };

                    key = 'temp' + '_' + 'users';

                    if (typeof self.cache[key] != 'undefined' && !_.isEmpty(self.cache[key])) {
                        return self.getEditorControlView('mailoptin_fields_map').updateMap(self.cache[key]);
                    }

                    self.addControlSpinner('sendmsg_url');

                    // hide the mapping view
                    self.getEditorControlView('mailoptin_fields_map').$el.hide();

                    $.post(moElementor.ajax_url, data, function (response) {
                        if ('success' in response && response.success === true) {
                            result = self.cache[key] = response.data.fields;
                            self.getEditorControlView('mailoptin_fields_map').updateMap(result);
                            self.getEditorControlView('mailoptin_fields_map').$el.show();
                        }
                        self.removeControlSpinner('sendmsg_url');
                    });
                },
            };

        ep.modules.forms.mailoptin = Object.assign(ep.modules.forms.mailchimp, MailoptinIntegration);

        ep.modules.forms.mailoptin.addSectionListener('section_mailoptin', MailoptinIntegration.onSectionActive);

    });

})(jQuery);