$(document).ready(function () {
    "use strict";

    $("#log_content").on("click", "button.save", function () {

		var fileID = $(this).attr("data-mongo-id"),
            regex  = /(<([^>]+)>)/ig;
		
        $.post(
            'UpdatePattern.php',
            {
                pattern_id  : $(this).attr("data-pattern-id"),
                regex	    : $("#log_content .rule").html().replace(regex, "")
            },
            function (html) {
				$.post(
                    'FolderResults.php',
                    {
                        page_size : filePageSize
                    },
                    function (html) {
                        $("#folder_results").html(html);
                        $("table.file table.figures").find("td").each(function () {
                            $(this).css("width", "0");
                            $(this).animate({width :  $(this).find("a").html()}, 100);
                        });
                    },
                    'text'
                );
			
                $.post(
					'FileContent.php',
					{
						log_file_id : fileID,
						page_size : logPageSize
					},
					function (html) {
						$("#file_content").html(html);
						if (analyse_steps.length > 0) {
							$(analyse_steps[analyse_steps.length - 1][0]).animate({left: 0}, 800);
							$(analyse_steps[analyse_steps.length - 1][1]).animate({left: "100%"}, 800);
							analyse_steps.pop();
						}
						/*$("table.file table.figures").find("td").each(function () {
							$(this).css("width", "0");
							$(this).animate({width :  $(this).find("a").html()}, 100);
						});*/
					},
					'text'
				);
            },
            'text'
        );

    });
    
    $("#log_content").on("click", "button.cancel", function () {
        if (analyse_steps.length > 0) {
            $(analyse_steps[analyse_steps.length - 1][0]).animate({left: 0}, 800);
            $(analyse_steps[analyse_steps.length - 1][1]).animate({left: "100%"}, 800);
            analyse_steps.pop();
        }
    });
    
    $("#log_content").on("click", "a.logSelection", function () {
        $.post(
            'LogContent.php',
            {
                pattern_id  : $(this).attr("data-pattern-id"),
                log_file_id : $(this).attr("data-mongo-id"),
                log_index   : $(this).attr("data-log-index")
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