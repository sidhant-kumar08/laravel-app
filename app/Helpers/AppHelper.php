<?php


namespace App\Helpers;

class AppHelper
{

    public static function checkUserIdInRequest($request)
    {
        if ($request->has("user_id")) {
            return $request->query("user_id");
        } else {
            abort(400, "Please provide a userId");
        }
    }
}
