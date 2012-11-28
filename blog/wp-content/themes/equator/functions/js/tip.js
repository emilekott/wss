/* 
* CenterIt (v.1.1.2)
* by James Studdart (www.jamesstuddart.co.uk)
* james@studdart.co.uk
*
* Copyright (c) 2009 James Studdart
* Licensed under the GPL license. 
*
*
* NOTE: Requires jQuery framework (www.jquery.com)
*	Developed for: jQuery 1.3.2
*
* Special Thanks to James Parker
*
*/
/*
    UPDATE: CenterIt (v.1.1.2) 
    by James Studdart (25/02/2010)
    - fix for IE6 and below centering, as IE6 and below do not support fixed positioning
*/
/*
    UPDATE: CenterIt (v.1.1.1) 
    by James Studdart (21/02/2010)
    - Added control.show() to ensure chosen element that will be centred is shown, otherwise centreIt does nothing 
*/
/*
    UPDATE:  CenterIt (v.1.1.0)
    by James Studdart
    - Added code to check for NaN on padding and margins
*/


(function($) {
    $.fn.CenterIt = function(options) {

        var defaults = {
            ignorechildren: true
        };
        var settings = $.extend({}, defaults, options);

        var control = $(this);

        control.show();

        $(document).ready(function() { CenterItem(); });
        $(window).resize(function() { CenterItem(); });

        function CenterItem() {


            var controlHeight = 0;
            var controlWidth = 0;

            if (settings.ignorechildren) {
                controlHeight = control.height();
                controlWidth = control.width();
            } else {

                var children = control.children();

                for (var i = 0; i < children.length; i++) {
                    if (children[i].style.display != 'none') {
                        controlHeight = children[i].clientHeight;
                        controlWidth = children[i].clientWidth;
                    }
                }
            }

            var controlMarginCSS = control.css("margin");
            var controlPaddingCSS = control.css("padding");


            if (controlMarginCSS != null) {
                //Work out Margins
                controlMarginCSS = controlMarginCSS.replace(/auto/gi, '0');
                controlMarginCSS = controlMarginCSS.replace(/px/gi, '');
                controlMarginCSS = controlMarginCSS.replace(/pt/gi, '');
            }

            var totalMargin = "";

            if (controlMarginCSS != "" && controlMarginCSS != null) {
                totalMargin = controlMarginCSS.split(' ');
            }

            var horizontalMargin = 0;
            var verticalMargin = 0;

            if (totalMargin != "NaN") {
                if (totalMargin.length > 0) {
                    horizontalMargin = parseInt(totalMargin[1]) + parseInt(totalMargin[3]);
                    verticalMargin = parseInt(totalMargin[2]) + parseInt(totalMargin[2]);
                }
            }


            if (controlPaddingCSS != null) {
                //Work out Padding
                controlPaddingCSS = controlPaddingCSS.replace(/auto/gi, '0');
                controlPaddingCSS = controlPaddingCSS.replace(/px/gi, '');
                controlPaddingCSS = controlPaddingCSS.replace(/pt/gi, '');
            }
            var totalPadding = "";

            if (controlPaddingCSS != "" && controlPaddingCSS != null) {
                totalPadding = controlPaddingCSS.split(' ');
            }

            var horizontalPadding = 0;
            var verticalPadding = 0;

            if (totalPadding != "NaN") {
                if (totalPadding.length > 0) {
                    horizontalPadding = parseInt(totalPadding[1]) + parseInt(totalPadding[3]);
                    verticalPadding = parseInt(totalPadding[2]) + parseInt(totalPadding[2]);
                }
            }

            if (verticalMargin == "NaN" || isNaN(verticalMargin))
            { verticalMargin = 0; }
            if (verticalPadding == "NaN" || isNaN(verticalPadding))
            { verticalPadding = 0; }

            //Apply  CSS
            var windowHeight = $(window).height();
            var windowWidth = $(window).width();

            if ($.browser.msie && $.browser.version.substr(0, 1) < 7) {
                //IE6 HACK as IE6 does not support fixed positioning
                control.css("position", "absolute");
            }
            else {
                control.css("position", "fixed");

            }
            
            control.css("height", controlHeight + "px");
            control.css("width", controlWidth + "px");



            control.css("top", ((windowHeight - (controlHeight + verticalMargin + verticalPadding)) / 2) + "px");

            control.css("left", ((windowWidth - (controlWidth + horizontalMargin + horizontalPadding)) / 2) + "px");
        }
    }
})(jQuery);



