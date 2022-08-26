var PostComment = (function () {
    var manage = function () {
        var postId = $("#postId").val();

        var table = $("#tblPostComment").DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: business_url + "/posts/comments/" + postId,
            order: [[0, "desc"]],
            columns: [
                { data: "id", name: "id" },
                { data: "comment", name: "comment", width: "550px" },
                { data: "comment_like_count", name: "comment_like_count" },
                { data: "comment_down_vote_count", name: "comment_down_vote_count" },
                { data: "flagged_comment_count", name: "flagged_comment_count" },
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
                    width: "20%",
                },
                { targets: 2, searchable: false, className: "text-center" },
                { targets: 3, searchable: false, className: "text-center" },
                { targets: 4, searchable: true, className: "text-center" },
                { targets: 5, searchable: true, className: "text-left" },
                { targets: 6, searchable: true, className: "text-left" },
                { targets: 7, searchable: true, className: "text-center" },
                { targets: 8, searchable: true, className: "text-center" },
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-center",
                },
            ],
        });

        $("body").on("click", "#btnAddComment", function (e) {
            e.preventDefault();

            $(".addComment").html("");
            $("#addCommentModal").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );

            var post_id = $(this).attr("data-post_id");
            var url = $(this).attr("data-url");

            submitcall(url, { post_id: post_id }, function (output) {
                $(".addComment").html(output);
            });
        });

        /** Add new comment code start here **/
        $("body").on("click", "#submitCommentBtn", function (e) {
            e.preventDefault();
            var addCommentForm = $("form[name='addCommentForm']");
            e.preventDefault();
            addCommentForm.validate({
                ignore: [],
                rules: {
                    comment: {
                        required: true,
                    },
                },
                messages: {
                    comment: {
                        required: "Please enter your comment.",
                    },
                },
            });
            if (addCommentForm.valid()) {
                var url = addCommentForm.attr("action");
                var postId = $("#post_id").val();
                var comment = $("#comment").val();
                submitcall(
                    url,
                    { post_id: postId, comment: comment },
                    function (output) {
                        var response = JSON.parse(output).response;
                        //alert(JSON.parse(output).response.status);
                        var responseType = response.status;
                        var responseTitle = response.title;
                        var message = response.message;
                        $("#addCommentForm")[0].reset();
                        $("#addCommentModal").modal("hide");

                        Swal.fire({
                            title: responseTitle,
                            text: message,
                            icon: responseType,
                            type: responseType,
                            timer: 5000,
                        }).then((okay) => {
                            if (okay) {
                                window.location.href =
                                    business_url + "/posts/comments/" + postId;
                            }
                        });
                        //alert(output);
                    }
                );
            }
        });
        /** Add new comment code end here **/

        /** Reply on user's post comment code start here **/
        $("body").on("click", "#submitReplyBtn", function (e) {
            e.preventDefault();
            var replyCommentForm = $("form[name='replyForm']");
            e.preventDefault();
            replyCommentForm.validate({
                ignore: [],
                rules: {
                    reply_comment: {
                        required: true,
                    },
                },
                messages: {
                    reply_comment: {
                        required: "Please enter your comment.",
                    },
                },
            });
            if (replyCommentForm.valid()) {
                var url = replyCommentForm.attr("action");
                var postId = $("#post_id").val();
                var commentBy = $("#comment_by").val();
                var commentId = $("#comment_id").val();
                var replyComment = $("#reply_comment").val();
                submitcall(
                    url,
                    {
                        postId: postId,
                        commentBy: commentBy,
                        commentId: commentId,
                        replyComment: replyComment,
                    },
                    function (output) {
                        console.log(output);
                        //alert(output);
                    }
                );
            }
        });
        /** Reply on user's post comment code endt here **/
    };

    var add = function () {};

    /** Edit record code start here **/
    var edit = function () {};
    /** Edit record code end here **/

    /** Reply on user's comment modal form open code start here **/
    var commentReply = function () {
        $("body").on("click", ".replyComment", function () {
            $(".commentInfo").html("");
            $("#commentDetails").modal(
                { backdrop: "static", keyboard: false },
                "show"
            );
            var commentId = $(this).attr("data-comment_id");
            var createdBy = $(this).attr("data-created_by");
            var post_id = $(this).attr("data-post_id");
            var url = $(this).attr("data-url");
            submitcall(
                url,
                {
                    post_id: post_id,
                    commentId: commentId,
                    createdBy: createdBy,
                },
                function (output) {
                    $(".commentInfo").html(output);
                }
            );
        });
    };
    /** Reply on user's comment modal form open code end here **/

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

    /***** AUTOTAG CODE START HERE ******/
    // $("#comment").on("keypress", function () {
    $("body").on("keypress", "#comment", function () {
        $.fn.atwho.debug = true;
        var jeremy = decodeURI("J%C3%A9r%C3%A9my");

        var at_config = {
            at: "@",
            //data: names,
            data: business_url + "/get-users",
            headerTpl: "<span>Select User</span>",
            insertTpl: "@${name}",
            displayTpl: "<li class='username'>@${name}</li>",
            limit: 400000,
            minLen: 2,
        };

        var hashTag_config = {
            at: "#",
            data: business_url + "/get-hash-tags",
            displayTpl: "<li>${name}</li>",
            insertTpl: "#${name}",
            delay: 400,
            limit: 400000,
            minLen: 2,
        };
        $inputor = $("#comment").atwho(at_config).atwho(hashTag_config);
        // $inputor.caret("pos", 500);
        $inputor.focus().atwho("run");

        hashTag_config.insertTpl = "";
        $("#editable").atwho(at_config).atwho(hashTag_config);
    });
    /***** AUTOTAG CODE END HERE ******/

    return {
        init: function () {
            manage();
            commentReply();
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
