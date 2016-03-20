function getSelectionText() {
    "use strict";
    var $text = "";
    if (window.getSelection) {
        $text = window.getSelection().toString();
    } else if (document.selection && document.selection.type !== "Control") {
        $text = document.selection.createRange().text;
    }
    return $text;
}

function compareText(rule, str, isKey) {
    "use strict";
    var index = rule.indexOf(str);
    while (index !== -1) {
        //rule = rule.substr(0, index) + "<span class='log_selection'>" + str + "</span>" + rule.substr(index + str.length);
        if (isKey) {
            rule = rule.substr(0, index) + "<span class='ellips'>"  + Array(str.length + 1).join("•") + "</span>" + rule.substr(index + str.length);
        } else {
            rule = rule.substr(0, index) + Array(str.length + 1).join("•") + rule.substr(index + str.length);
        }
        index = rule.indexOf(str);
    }
    return rule;
}

$(document).ready(function () {
    "use strict";

    $(".logContent .input").droppable({
        activeClass : "input-active",
        hoverClass : "input-hover",
        drop : function (event, ui) {
            $(ui.draggable).appendTo($(this));
            $(ui.draggable).css("margin-left", "0")
                .css("left", "0")
                .css("bottom", "0")
                .css("top", "0")
                .css("right", "0");
        }
    });

    $("#log_content").on("mouseup", "pre.logContent", function (evt) {
        var $selection = getSelectionText();
        if ($selection !== "") {
            var $newRule = compareText($(".rule").html(), $selection, evt.altKey);
            if ($newRule !== $(".rule").html()) {
                $(".rule").html($newRule);
                if (evt.altKey) {
                    $(".keys").append("<span class='log_selection'>" + $selection + "</span>");
                    $(".log_selection").draggable({
                        containment : ".log_information .logContent",
                        revert : 'invalid'
                    });
                }
            }
        }
    });

    $("#log_content").on("click", "button.add", function () {
        if ($(".key_value tr").length !== 0) {
            $(".key_value tr:last").after('<tr><td><button class="remove">-</button></td><td class="key"><input/></td><td class="input"></td></tr>');
        } else {
            $(".key_value").html('<tr><td><button class="remove">-</button></td><td class="key"><input/></td><td class="input"></td></tr>');
        }

        $(".logContent .input").droppable({
            activeClass : "input-active",
            hoverClass : "input-hover",
            drop : function (event, ui) {
                $(ui.draggable).appendTo($(this))
                    .css("margin-left", "0")
                    .css("left", "0")
                    .css("bottom", "0")
                    .css("top", "0")
                    .css("right", "0");
            }
        });

    });

    $("#log_content").on("click", "button.remove", function () {
        $(this).closest("tr").remove();
    });
});