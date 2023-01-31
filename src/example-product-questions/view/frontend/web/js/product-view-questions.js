/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

define([
    'jquery',
    'mage/collapsible'
], function ($) {
    'use strict';

    let animationSpeed;

    function initCollapsibles(element) {
        $(element).find('.product-question').addClass('collapsible')
            .collapsible({animate: animationSpeed, openedState: 'active'})
            .find('header').append($('<span></span>').addClass('indicator'));
    }

    function initForms(element) {
        $(element).find('.product-question-form').each((index, form) => {
            const buttonLabel = $(form).hasClass('question') ? 'Ask a Question' : 'Post an Answer';
            const button = $('<button></button>')
                .addClass('action primary form-show')
                .append($('<span></span>').text(buttonLabel));

            $(form).before(button)
                .hide();

            button.on('click', () => {
                button.hide(animationSpeed);
                $(form).show(animationSpeed);
            });
        });
    }

    return function(config, element) {
        animationSpeed = config['animationSpeed'] ?? 500;
        initCollapsibles(element);
        initForms(element);
    }
});
