var Notifications = (function () {
    var manage = function () {
        var table = $("#tblNotifications").DataTable({
            /* processing: true,
            serverSide: true,
            ajax: superadmin_url + "/users",
            columns: [
                { data: "name", name: "name" },
                { data: "email", name: "email" },
                { data: "phone", name: "phone" },
                { data: "role", name: "role" },
                { data: "created_by", name: "created_by" },
                { data: "status", name: "status" },
                { data: "action", name: "action" },
            ], */
            columnDefs: [
                { targets: 0, searchable: true },
                { targets: 1, searchable: true, className: "text-left" },
                { targets: 2, searchable: true, className: "text-center"},
                {
                    targets: "no-sort",
                    orderable: false,
                    order: [],
                    className: "text-left",
                },
            ],
        });
    };

    /* Add user data form vlidation code start here */
    var add = function () {};
    /** Delete record code start here **/
    var delete_record = function () {};
    /** Delete record code end here **/

    return {
        init: function () {
            manage();
            add();
            //edit();
            delete_record();
        },
    };
})();
