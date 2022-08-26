var FlaggedPosts = (function () {
    var manage = function () {
        var table = $("#tblFlaggedPosts").DataTable({
            processing: true,
            serverSide: true,
            // language: {
            //     processing:
            //         "<i class='fa fa-refresh fa-spin'></i> Loading.... ",
            // },
            ajax: superadmin_url + "/posts/flagged-posts",
            order: [[0, "desc"]],
            columns: [
                { data: "id", name: "id" },
                { data: "post_content", name: "post_content" },
                { data: "comments_count", name: "comments_count" },
                { data: "likes_count", name: "likes_count" },
                { data: "flagged_post_count", name: "flagged_post_count" },
                { data: "post_type", name: "post_type" },
                { data: "user_id", name: "user_id" },
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
                    width: "25%",
                },
                { targets: 2, searchable: false, className: "text-center" },
                { targets: 3, searchable: true, className: "text-center" },
                { targets: 4, searchable: true, className: "text-center" },
                {
                    targets: 5,
                    searchable: true,
                    className: "text-center",
                    visible: false,
                },
                { targets: 6, searchable: true, className: "text-left" },
                {
                    targets: 7,
                    searchable: true,
                    className: "text-left",
                    width: "12%",
                },
                { targets: 8, searchable: false, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-center",
                },
            ],
        });
    };

    /** View details code start here **/
    var view = function () {
        $("body").on("click", ".viewRecord", function () {
            $(".full_details").html("");
            $("#viewDetails").modal(
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

    /** View post liked users details code start here **/
    var userLikesView = function () {
        $("body").on("click", ".viewLikesRecord", function () {
            $(".full_details").html("");
            $("#viewUserDetails").modal(
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
    /** View post liked users code end here **/

    /** View comment flagged users details code start here **/
    var userCommentFlaggedView = function () {
        $("body").on("click", ".viewFlaggedRecord", function () {
            $(".flag_full_details").html("");
            $("#viewFlaggedByUsers").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            submitcall(url, { object_id: id }, function (output) {
                $(".flag_full_details").html(output);
            });
        });
    };
    /** View comment flagged users code end here **/

    /** Soft delete record code start here **/
    var delete_record = function () {
        $("body").on("click", ".delete", function () {
            var id = $(this).attr("data-id");
            var url = superadmin_url + "/my-posts/destroy/" + id;
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
    /** Soft delete record code end here **/

    /** Permanently Delete record code start here **/
    var force_delete_record = function () {
        $("body").on("click", ".delete", function () {
            var id = $(this).attr("data-id");
            var url = superadmin_url + "/posts/force-delete/" + id;
            $("#deleteForm").attr("action", url);

            $("#DeleteModal").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
        });
        $("body").on("click", "#yesDeleteBtn", function () {
            $("#deleteForm").submit();
        });
    };
    /** Permanently record code end here **/

    /** Restore record code start here **/
    var restore_record = function () {
        $("body").on("click", ".restore", function () {
            var id = $(this).attr("data-id");
            var url = superadmin_url + "/posts/restore/" + id;
            $("#restoreForm").attr("action", url);
            $("#restoreModal").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
        });
        $("body").on("click", "#yesBtn", function () {
            $("#restoreForm").submit();
        });
    };
    /** Restore record code end here **/

    return {
        init: function () {
            manage();
            view();
            userLikesView();
            userCommentFlaggedView();
            delete_record();
        },
        add: function () {
            // add();
        },
        edit: function () {
            // edit();
        },
    };
})();
