/*jslint browser: true*/
/*global $, jQuery, document, enp, store*/

jQuery(document).ready(function ($) {
    "use strict";
    function getNum(val) {
        if (isNaN(val)) {
            return 0;
        }
        return val;
    }
    function lightsOutAddImage(image) {
        var linkBefore = '',
            linkAfter = '',
            linkTarget = '';
        if (enp.urlToSendUser) {
            if (enp.openNewWindow === 'on') {
                linkTarget = ' target="blank" ';
            }
            linkBefore = '<a href="' + enp.urlToSendUser + '" ' + linkTarget + '>';
            linkAfter = '</a>';
        }
        $('body').append('<div class="enp-lightsout"></div><div class="enp-image"><p>' + linkBefore + image + linkAfter + '</p></div>');
    }
    function lightsOn() {
        $('.enp-image').fadeOut('fast');
        $('.enp-lightsout').fadeOut('fast');
    }
    var storeName = enp.postId,
        dataStore = store.get(storeName),
        image = enp.image,
        timesShown = 0,
        secondsUntilNextShow = 0,
        nextShow = 0,
        timesToShow = getNum(enp.numberTimesToShow),
        timeNow = Math.round((new Date()).getTime() / 1000),
        returnObject;
    if (dataStore !== undefined) {
        timesShown = getNum(dataStore.timesShown);
        nextShow = getNum(dataStore.nextShow);
    }
    secondsUntilNextShow = getNum(enp.hoursBetweenShow) * 3600;
    if (timesToShow > timesShown && timeNow > nextShow) {
        lightsOutAddImage(image);
        timesShown += 1;
        nextShow = parseInt(timeNow, 10) + parseInt(secondsUntilNextShow, 10);
        returnObject = { timesShown: timesShown, nextShow: nextShow };
        store.set(storeName, returnObject);
    }
    $('.enp-image').on('click', 'img', function (e) {
        e.stopPropagation();
    });
    $('body').on('click', '.enp-image', function () {
        lightsOn();
    });
    $(document).keyup(function (e) {
        if ($('.enp-image').length > 0 && e.keyCode === 27) {
            lightsOn();
        }
    });
});