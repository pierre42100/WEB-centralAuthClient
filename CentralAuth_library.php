<?php
/**
 * Central auth client library3
 *
 * This client is made to work with the first version
 * of the API (V1)
 *
 * @author Pierre HUBERT
 */

class CentralAuthClient {

	/**
	 * Server URL
	 *
	 * @var string
	 */
	private $server_url;

	/**
	 * Client ID
	 *
	 * @var string
	 */
	private $client_id;

	/**
	 * Client Secret
	 *
	 * @var string
	 */
	private $client_secret;

	/**
	 * Public constructor
	 *
	 * @param stirng $server_url The URL of the CentralAuth Server
	 * @param string $client_id The ID of the client application
	 * @param string $client_secret The password of the client application
	 */
	public function __construct(string $server_url, string $client_id, string $client_secret){

		//Save server URL
		$this->server_url = $server_url;

		//Store client ID and client Secret informations
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;

	}

	/**
	 * Create a new login ticket
	 *
	 * @param string $redirect_url The URL where CentralAuth should
	 * redirect user once login is completed
	 * @return array Informations about the newly created login ticket
	 * * string login_ticket : The newly created login ticket
	 * * string login_url : The URL where user should be redirected to perform login
	 * * string error (optionnal): This is returned only in case of error
	 */
	public function create_login_ticket(string $redirect_url) : array {

		//Perform a request on the server
		$params = array(
			//Redirect URL
			"redirect_url" => $redirect_url,
		);
		$result = $this->post_request("create_ticket", $params);

		//Check for errors
		if($result['response_code'] !== 200 || isset($result['error']))
			return array("error" => "cURL request: ".$result['response_code']);

		//Extract response
		$response = $result['response'];
		unset($result);

		//Check if response include the required informations
		if(!isset($response['login_ticket']) OR !isset($response['login_url']))
			return array("error" => "Invalid response !");

		//Return informations about the login ticket
		return array(
			"login_ticket" => $response['login_ticket'],
			"login_url" => $response['login_url']
		);

	}

	/**
	 * Retrieve informations about a user on the Central Auth Server
	 *
	 * @param string $login_ticket The login ticket
	 * @param string $authorization_token The authorization token
	 * @return array The result
	 * * error : This array entry is included when an error occured
	 */
	public function get_user_infos(string $login_ticket, string $authorization_token) : array {

		//Perform a request on the server
		$params = array(
			"login_ticket" => $login_ticket,
			"authorization_token" => $authorization_token,
		);
		$result = $this->post_request("get_user_infos", $params);
		
		//Check for errors
		if($result['response_code'] !== 200 || isset($result['error']))
			return array("error" => "cURL request: ".$result['response_code']);

		//Check result response
		if(!isset($result['response']['user_infos']))
			return array("error" => "Unexcepted response from server !");

		//Return parsed result
		return array(
			"id" => $result['response']['user_infos']['id'],
			"name" => $result['response']['user_infos']['name'],
			"mail" => $result['response']['user_infos']['mail'],
		);
	}

	/**
	 * Perform a POST request on the Central Auth Server
	 *
	 * @param string $uri The URI on the server of the request
	 * @param array $params Parametres to include in the request
	 * @return array The result
	 */
	private function post_request(string $uri, array $params = array()) : array {

		//Generate the complete request URL
		$request_url = $this->server_url."api/v1/".$uri;

		//Append application token to the request
		$params['client_id'] = $this->client_id;
		$params['client_secret'] = $this->client_secret;

		//Setup URL
		$ch_opt[CURLOPT_URL] = $request_url;
		$ch_opt[CURLOPT_HEADER] = FALSE;

		//Perform a post request
		$ch_opt[CURLOPT_POST] = TRUE;
		$ch_opt[CURLOPT_POSTFIELDS] = $params;

		//Generic config
		$ch_opt[CURLOPT_RETURNTRANSFER] = TRUE;

		//Setup cURL
		$ch = curl_init();
		curl_setopt_array($ch, $ch_opt);

		//Execute URL
		$response = curl_exec($ch);

		//Check for errors
		if(curl_errno($ch)){

			//Retrieve error informations
			$error = curl_error($ch);

		}

		//Check for server error
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		//Close cURL
		curl_close($ch);


		//Process response
		$result = array(
			"response" => $response,
			"response_code" => $response_code,
		);

		//Check for errors
		if(isset($error))
			$result['error'] = $error;

		//Try to decode response
		if(!isset($result['error'])){
			//Decode JSON response
			$response = json_decode($result['response'], true);

			//Check if any error occured
			if(count($response) > 0)
				$result['response'] = $response;
		}

		//Return result
		return $result;
	}

}