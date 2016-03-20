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
                alert(html);
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
});