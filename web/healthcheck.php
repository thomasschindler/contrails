<?php
/**
*       call a method in the configuration which is fully responsible for the response
*       it defaults to sending status 200
*/

include_once("../inc/etc/config/default.cnf");
include_once("../inc/etc/system/oos.cnf");

if(method_exists("conf","healthcheck"))
{
        CONF::healthcheck();
}

header("HTTP/1.0 200 OK");

?>