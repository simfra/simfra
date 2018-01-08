<?php

namespace Core\Session;

class Session
{
    private static $sessionName = "SESSION_NAME";
    public static function start($name = "")
    {
        if (!@session_id()) {
            if ($name == "") {
                session_name(session::$sessionName);
            } else {
                session_name(session::$sessionName = $name);
            }
            session_start();
        }
    }
}