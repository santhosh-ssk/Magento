define([
    'uiComponent',
    'Zilker_MailerCode/js/model/mailercode'
], function (
    Component,
    mailercode,
) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            mailercode.loadData();
        },
        mailCodeApplied: function () {
            return mailercode.getIsApplied();
        },
    });


});
