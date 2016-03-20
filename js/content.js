$(document).ready(function () {
    "use strict";

    $("#file_content").on("click", ".logContentLink", function () {
        //alert("Sami");
        $.post(
            'LogContent.php',
            {
                pattern_id  : $(this).attr("data-pattern-id"),
                log_file_id : $(this).attr("data-mongo-id")
            },
            function (html) {
                $("#log_content").html(html);
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
                /*$("table.file table.figures").find("td").each(function () {
                    $(this).css("width", "0");
                    $(this).animate({width :  $(this).find("a").html()}, 100);
                });*/
            },
            'text'
        );

    });
});