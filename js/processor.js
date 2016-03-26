$(document).ready(function () {
    "use strict";

    $(".next_step").click(function () {
        var spinner = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
        
        $("#folder_results").html(spinner);
        $.post(
            'Processor.php',
            {
                folderPath : $(this).parent().find("input").val()
            },
            function (data) {
                if (data) {
                   $("#folder_results").html(data); 
                } else {
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
                }
            },
            'text'
        );
    });

    $("#folder_results").on("click", ".pagination a.prev_page", function () {

        var pageNumber = parseInt($(this).parent().attr("data-page-number")) - 1;

        if (pageNumber >= 1) {
            $.post(
                'FolderResults.php',
                {
                    last_id_prev : $(this).parent().attr("data-last-id-prev"),
                    page_number : pageNumber,
                    page_size : filePageSize
                },
                function (html) {
                    $("#folder_results").html(html);
                    $("table.file table.figures").find("td").each(function () {
                        $(this).css("width", $(this).find("a").html());
                    });
                },
                'text'
            );
        }
    });

    $("#folder_results").on("click", ".pagination a.next_page", function () {

        var pageNumber  = parseInt($(this).parent().attr("data-page-number")) + 1,
            resultCount = $("#folder_results .result-count").text();

        if ((pageNumber - 1) * filePageSize < resultCount) {
            $.post(
                'FolderResults.php',
                {
                    last_id_next : $(this).parent().attr("data-last-id-next"),
                    page_number : pageNumber,
                    page_size : filePageSize
                },
                function (html) {
                    $("#folder_results").html(html);
                    $("table.file table.figures").find("td").each(function () {
                        $(this).css("width", $(this).find("a").html());
                    });
                },
                'text'
            );
        }
    });
});