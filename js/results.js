var $firstTime = true;

$(document).ready(function () {
    "use strict";

    $("#folder_results").on("click", ".colors a", function () {
        var $this = $(this),
            $td = $this.closest("td"),
            $toShow = false,
            $class = $this.closest("td").attr('class').split(' ')[0],
            $table = $("#folder_results table.file table.figures");

        //Emphasize the button
        if ($this.attr("data-isSelected") === "true") {
            $td.css("opacity", "");
            $this.attr("data-isSelected", "false");
        } else {
            $td.css("opacity", "1");
            $this.attr("data-isSelected", "true");
            $toShow = true;
        }

        //Display all cells belonging to selected class
        if ($toShow) {
            $table.find("." + $class).each(
                function (index, element) {
                    $(this).css("width", $(this).find("a").html());
                }
            );
        } else {
            $table.find("." + $class).each(
                function (index, element) {
                    $(this).css("width", "0px");
                }
            );
        }

        //Hide every cell of other class
        if ($firstTime) {
            $td.siblings().each(
                function (index, element) {
                    $(this).css("opacity", "");
                    var $td = $table.find("td").not("[class='" + $class + "']").css("width", "0px");
                }
            );
            $firstTime = false;
        }
    });

    $("#folder_results").on("click", ".logFileLink", function () {
        //alert("Sami");
        $.post(
            'FileContent.php',
            {
                log_file_id : $(this).attr("data-mongo-id"),
                page_size : logPageSize
            },
            function (html) {
                $("#file_content").html(html);
                /*$("table.file table.figures").find("td").each(function () {
                    $(this).css("width", "0");
                    $(this).animate({width :  $(this).find("a").html()}, 100);
                });*/
            },
            'text'
        );
    });
    
    $("#file_content").on("click", ".pagination a.prev_page", function () {

        var pageNumber = parseInt($(this).parent().attr("data-page-number")) - 1;

        if (pageNumber >= 1) {
            $.post(
                'FileContent.php',
                {
                    log_file_id : $(this).parent().attr("data-mongo-id"),
                    last_id_prev : $(this).parent().attr("data-last-id-prev"),
                    page_number : pageNumber,
                    page_size : logPageSize
                },
                function (html) {
                    $("#file_content").html(html);
                },
                'text'
            );
        }
    });

    $("#file_content").on("click", ".pagination a.next_page", function () {

        var pageNumber  = parseInt($(this).parent().attr("data-page-number")) + 1,
            resultCount = $("#file_content .result-count").text();
        
        if ((pageNumber - 1) * logPageSize < resultCount) {
            $.post(
                'FileContent.php',
                {
                    log_file_id : $(this).parent().attr("data-mongo-id"),
                    last_id_next : $(this).parent().attr("data-last-id-next"),
                    page_number : pageNumber,
                    page_size : logPageSize
                },
                function (html) {
                    $("#file_content").html(html);
                },
                'text'
            );
        }
    });
});