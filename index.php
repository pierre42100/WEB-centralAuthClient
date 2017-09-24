<?php
/**
 * Test client for the centralAuth project
 *
 * @author Pierre HUBERT
 */

//Start session
session_start();

//Check if user is signed in or not
if(!isset($_SESSION['user'])){

	require_once("CentralAuth_library.php");

	//Create Central Auth client object
	$caclient = new CentralAuthClient(
		"http://devweb.local/centralAuth/project/",
		"CLIENT_ID", 
		"CLIENT_SECRET"
	);

	//Check if an old login ticket has to be deleted
	if(isset($_SESSION['CA_login_ticket']) AND !isset($_GET['auth_key']))
		//Delete old login ticket
		unset($_SESSION['CA_login_ticket']);

	//Check for login ticket
	if(!isset($_SESSION['CA_login_ticket'])){

		//Create login ticket
		$redirect_url = $_SERVER['PHP_SELF']."?auth_key=%AUTHORIZATION%"; //Return URL
		$login_ticket = $caclient->create_login_ticket($redirect_url);

		//Check for errors
		if(isset($login_ticket['error']))
			exit("Couldn't generate a login ticket !");

		//Store informations about login ticket in the session
		$_SESSION['CA_login_ticket'] = $login_ticket;
	}

	//Check for authorization token
	if(isset($_GET['auth_key'])){

		//Check if request was cancelled
		if($_GET['auth_key'] === "CANCEL")
			exit("Login operation cancelled !");

		//Else retrieve informations about the user on the server
		$user_infos = $caclient->get_user_infos(
			$_SESSION['CA_login_ticket']['login_ticket'], 
			$_GET['auth_key']
		);

		//Check for errors
		if(isset($user_infos['error']))
			exit("Couldn't retrieve user informations with the given token ! <a href='./'>Retry</a>");

		//Save user informations
		$_SESSION['user'] = $user_infos;

		//Delete login ticket informations (security)
		unset($_SESSION['login_ticket']);

	}
	else {
		//Redirect user to centralAuth to get him signed in
		header('Location: '.$_SESSION['CA_login_ticket']['login_url']);
		exit();
	}

}

//Check if user requested to get signed out
if(isset($_GET['signout'])){
	unset($_SESSION['user']);

	//Redirect user
	header('Location: ./');
}

//User signed in
echo "<p>User signed in !</p>";

//Display user informations
echo "<p>ID: ".$_SESSION['user']['id']."</p>";
echo "<p>Mail: ".$_SESSION['user']['mail']."</p>";
echo "<p>Name: ".$_SESSION['user']['name']."</p>";

//Signout user
echo "<a href='".$_SERVER['PHP_SELF']."?signout'>Sign out</a>";