var selected_menu_item = null;
var selected_content_step = null;
var analyse_steps = [];
var elevator_position = "0";
var menu_item_position = "0";
var filePageSize = 5;
var logPageSize  = 40;

$(document).ready(function () {
    "use strict";

    selected_content_step = $("#analyse_content");
    selected_menu_item = $("#menu").find("a[href='#analyse_content']").parent();
    selected_menu_item.css("opacity", "1");
    elevator_position = selected_menu_item.position().top + 5 + "px";
    $("#elevator").css('top', elevator_position);

    $('.icon').bind('mouseenter', function () {
        var elevator   = $('#elevator'),
            self       = $(this),
            initHeight = self.height();
        menu_item_position = self.position().top + 5;
        this.iid = setInterval(function () {
            elevator.css('top', menu_item_position + self.height() - initHeight + "px");
        }, 100);
    }).bind('mouseleave', function () {
        $("#elevator").css('top', elevator_position);
        this.iid && clearInterval(this.iid);
    });

    $("#content").on("click", "a.logFileLink, a.logContentLink, button.next_step", function () {
        var $href = $.attr(this, "href"),
            $cntr = $.attr(this, "data-container");
        $($href).animate({left: 0}, 800);
        $($cntr).animate({left: "-100%"}, 800);
        analyse_steps.push([$cntr, $href]);
        return false;//Avoid going to the link indicated by href attribute
    });

    $(".prev").click(function () {
        if (analyse_steps.length > 0) {
            $(analyse_steps[analyse_steps.length - 1][0]).animate({left: 0}, 800);
            $(analyse_steps[analyse_steps.length - 1][1]).animate({left: "100%"}, 800);
            analyse_steps.pop();
        }
    });

    $(".prev").hover(function () {
        if (analyse_steps.length > 0) {
            $(this).css("cursor", "pointer")
                   .css("opacity", "1");
        }
        return false;
    }, function () {
        $(this).css("cursor", "")
               .css("opacity", "");
    });

    $("#menu .item").click(function () {
        var target   = $($.attr(this, "href")),
            elevator = $("#elevator");

        if (selected_content_step[0] !== target[0]) {
            target.animate({top: 0}, 800);
            selected_content_step.animate({top: "-100%"}, 800, function () {
                $(this).css("top", "100%");
            });

            selected_menu_item.css("height", "").css("opacity", "");

            selected_content_step = target;
            selected_menu_item = $(this).parent();

            selected_menu_item.css("opacity", "1");
            elevator_position = menu_item_position + "px";
        }
        return false;
    });
});