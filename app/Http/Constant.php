<?php
    /**
     * It is constant file which use to define all the reusable variables which will never change.
     * @Created On: 08-02-2021;
     * @Update On : 22-02-2021;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
     */

  // ERROR STATUS CODES.
    define ("APP_NAME", "SixPac");
    define ("FRONT_URL", "http://127.0.0.1:8000/");
    define("SUPER_ADMIN_NAME", "System Admin");
    define ( 'ERROR_EMAILS_SEND_TO', serialize (array (
      "ritesh.rana@spec-india.com"
    )));
    define ( 'STATUS_CODE_INSUFFICIENT_DATA', 400);
    define ( 'STATUS_CODE_INVALID_MEDIA_TYPE_DATA', 415);
    define ( 'STATUS_CODE_SUCCESS', 200);
    define ( 'STATUS_CODE_UNAUTHORIZED_ACCESS', 401);
    define ( 'UNAUTHORIZED_TOKEN', 402);
    define ( 'STATUS_CODE_NOT_FOUND', 404);
    define ( 'STATUS_CODE_NOT_ALLOWED', 405);
    define ( 'STATUS_CODE_LOGOUT', 215);
    define ( 'STATUS_CODE_DUPLICATE', 409);
    define ( 'STATUS_CODE_ERROR', 500);
    define ( 'VALIDATOR_CODE_ERROR', 422);

    // Access tokens
    define ( "ACCESS_TOKEN", "SixPac");
    define ( "PERSONAL_ACCESS_TOKEN_EXPIRY_TIME", 20*60000);
    define ( "PERSONAL_ACCESS_TOKEN_REFRESH_TIME", 15*60000);
    ?>
