var FlaggedComment = (function () {
    var manage = function () {
        var table = $("#tblFlaggedComments").DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: business_url + "/posts/comment/flagged/",
            fnDrawCallback: function () {
                $(".status_switch").bootstrapSwitch();
            },
            order: [[0, "desc"]],
            columns: [
                { data: "id", name: "id" },
                { data: "comment", name: "comment", width: "550px" },
                { data: "up_vote", name: "up_vote" },
                { data: "down_vote", name: "down_vote" },
                { data: "flagged", name: "flagged" },
                { data: "created_by", name: "created_by" },
                { data: "created_at", name: "created_at" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    className: "text-left",
                    visible: false,
                },
                {
                    targets: 1,
                    searchable: true,
                    className: "tbl-overflow-txt text-left",
                    width: "20%",
                },
                { targets: 2, searchable: false, className: "text-center" },
                { targets: 3, searchable: false, className: "text-center" },
                { targets: 4, searchable: true, className: "text-center" },
                { targets: 5, searchable: true, className: "text-left" },
                { targets: 6, searchable: true, className: "text-left" },
                { targets: 5, searchable: true, className: "text-center" },
                { targets: 6, searchable: true, className: "text-center" },
                { targets: 7, searchable: true, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-center",
                },
            ],
        });
    };

    /*** Change status code start here ***/
    $("body").on(
        "switchChange.bootstrapSwitch",
        'input[class="status_switch"]',
        function (event, state) {
            var id = $(this).attr("data-id");
            var url = business_url + "/posts/comment/flagged/changeStatus/";
            if (state) {
                $("#status_" + id).val(1);
            } else {
                $("#status_" + id).val(0);
            }
            $("#statusForm").attr("action", url);
            $("#StatusModal").modal({
                backdrop: "static",
                keyboard: false,
            });
            $("#comment_id").val(id);
            if (state) {
                $("#new_status").val(1);
            } else {
                $("#new_status").val(0);
            }
        }
    );

    $("body").on("click", "#comment_yesBtn", function () {
        $("#statusForm").submit();
    });

    $("body").on("click", "#comment_closeBtn, #StausModalClose", function () {
        var id = $("#comment_id").val();
        if ($("#status_" + id).val() == 1) {
            $("#status_" + id).bootstrapSwitch("state", false);
        } else {
            $("#status_" + id).bootstrapSwitch("state", true);
        }
    });
    /*** Change status code end here ***/

    var add = function () {};

    /** Edit record code start here **/
    var edit = function () {};
    /** Edit record code end here **/

    /** View comment up vote users details code start here **/
    var userCommentUpVoteView = function () {
        $("body").on("click", ".viewUpVotesRecord", function () {
            $(".full_details").html("");
            $("#viewUpVoteUsers").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            submitcall(url, { object_id: id }, function (output) {
                $(".full_details").html(output);
            });
        });
    };
    /** View comment up vote users code end here **/

    /** View comment down vote users details code start here **/
    var userCommentDownVoteView = function () {
        $("body").on("click", ".viewDownVotesRecord", function () {
            $(".full_details").html("");
            $("#viewDownVoteUsers").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            submitcall(url, { object_id: id }, function (output) {
                $(".full_details").html(output);
            });
        });
    };
    /** View comment down vote users code end here **/

    /** View comment flagged users details code start here **/
    var userCommentFlaggedView = function () {
        $("body").on("click", ".viewFlaggedRecord", function () {
            $(".full_details").html("");
            $("#viewFlaggedByUsers").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            submitcall(url, { object_id: id }, function (output) {
                $(".full_details").html(output);
            });
        });
    };
    /** View comment flagged users code end here **/

    /** View details code start here **/
    var view = function () {
        $("body").on("click", ".viewCommentRecord", function () {
            $(".full_details").html("");
            $("#viewCommentDetails").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            submitcall(url, { object_id: id }, function (output) {
                $(".full_details").html(output);
            });
        });
    };
    /** View details code end here **/

    /** Delete record code start here **/
    var delete_record = function () {
        $("body").on("click", ".delete", function () {
            var id = $(this).attr("data-id");
            var url = business_url + "/posts/destroy/" + id;
            $("#deleteForm").attr("action", url);
            $("#DeleteModal").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
        });
        $("body").on("click", "#yesBtn", function () {
            $("#deleteForm").submit();
        });
    };
    /** Delete record code end here **/

    return {
        init: function () {
            manage();
            view();
            delete_record();
            userCommentUpVoteView();
            userCommentDownVoteView();
            userCommentFlaggedView();
        },
        add: function () {
            add();
        },
        edit: function () {
            edit();
        },
    };
})();
