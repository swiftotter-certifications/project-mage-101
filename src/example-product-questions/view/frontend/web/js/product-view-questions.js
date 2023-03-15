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
        $(element).find('.product-question')
            .addClass('collapsible')
            .collapsible({animate: animationSpeed, openedState: 'active'})
            .find('header').append(
                $('<span></span>').addClass('indicator')
            );
    }

    function initForms(element) {
        // For each question/answer form container
        $(element).find('.product-question-form').each((index, form) => {
            // Determine button label based on whether this is for a question or answer
            const buttonLabel = $(form).hasClass('question') ? 'Ask a Question' : 'Post an Answer';

            // Create a new button
            const button = $('<button></button>')
                .addClass('action primary form-show')
                .append($('<span></span>').text(buttonLabel));

            // Add the button before the form and hide the form
            $(form).before(button)
                .hide();

            // Set up handler to show the form
            button.on('click', () => {
                button.remove();
                $(form).show(animationSpeed);
            });
        });
    }

    return function(config, element) {
        animationSpeed = config["animationSpeed"] || 500;
        initCollapsibles(element);
        initForms(element);
    }
});
