<?php
/**
 * Test client for the centralAuth project
 *
 * @author Pierre HUBERT
 */

//Start session
session_start();

require_once("CentralAuth_library.php");

//Create Central Auth client object
$caclient = new CentralAuthClient(
	"http://devweb.local/centralAuth/project/",
	"CLIENT_ID", 
	"CLIENT_SECRET"
);

//Check for login ticket
if(!isset($_SESSION['CA_login_ticket'])){

	//Create login ticket
	$redirect_url = $_SERVER['PHP_SELF']."?auth_key=%AUTHORIZATION%"; //Return URL
	$login_ticket = $caclient->create_login_ticket($redirect_url);

	//Check for errors
	if(isset($login_ticket['error']))
		exit("Couldn't generate a login ticket !");

	print_r($login_ticket);

}