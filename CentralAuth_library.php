<?php
/**
 * Central auth client library
 *
 * @author Pierre HUBERT
 */

class CentralAuthClient {

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
	 * @param string $client_id The ID of the client application
	 * @param string $client_secret The password of the client application
	 */
	public function __construct(string $client_id, string $client_secret){

		//Store client ID and client Secret informations
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;

	}

}