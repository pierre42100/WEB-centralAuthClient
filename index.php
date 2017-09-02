<?php
/**
 * Test client for the centralAuth project
 *
 * @author Pierre HUBERT
 */

require_once("CentralAuth_library.php");

//Create Central Auth client object
$caclient = new CentralAuthClient("CLIENT_ID", "CLIENT_SECRET");