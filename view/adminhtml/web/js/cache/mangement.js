define([
        'uiComponent',
        'jquery',
        'mage/mage'
    ], function (Component, $) {
        'use strict';

        return Component.extend({
            initialize: function () {
                $('.fasterize_clean_form').mage('form').mage('validation');
            }
        });
    }
);
