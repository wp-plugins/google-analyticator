<?php

# Include SimplePie if it doesn't exist
if ( !class_exists('SimplePie') ) {
	if ( function_exists('fetch_feed') ) {
		require_once (ABSPATH . WPINC . '/class-feed.php');
	} else {
		require_once('simplepie.inc');
	}
}

/**
 * Handles interactions with Google Analytics' Stat API
 *
 * @author Spiral Web Consulting
 **/
class GoogleAnalyticsStats
{
	
	# Class variables
	var $baseFeed = 'https://www.google.com/analytics/feeds';
	var $accountId;
	var $token = false;
	
	/**
	 * Constructor
	 *
	 * @param user - the google account's username
	 * @param pass - the google account's password
	 **/
	function GoogleAnalyticsStats($user, $pass)
	{	
		# Encode the login details for sending over HTTP
		$user = urlencode($user);
		$pass = urlencode($pass);
		
		# Request authentication with Google
		$response = $this->http('https://www.google.com/accounts/ClientLogin', 'accountType=GOOGLE&Email=' . $user . '&Passwd=' . $pass);
		
		# Get the authentication token
		$this->token = substr(strstr($response, "Auth="), 5);
	}
	
	/**
	 * Connects using the WordPress HTTP API to get data
	 *
	 * @param url - url to request
	 * @param post - post data to pass through WordPress
	 * @return the raw http response
	 **/
	function http($url, $post = false)
	{
		# Set the arguments to pass to WordPress
		$args = array(
			'sslverify' => false
		);
		
		# Add the optional post values
		if ( $post ) {
			$post .= '&service=analytics&source=wp-google-stats';
			$args['body'] = $post;
		}
		
		# Add the token information
		if ( $this->token ) {
			$args['headers'] = array('Authorization' => 'GoogleLogin auth=' . $this->token);
		}
		
		# Make the connection
		if ( $post )
			$response = wp_remote_post($url, $args);
		else
			$response = wp_remote_get($url, $args);
		
		# Return the body of the response
		return $response['body'];
	}
	
	/**
	 * Checks if the username and password worked by looking at the token
	 *
	 * @return Boolean if the login details worked
	 **/
	function checkLogin()
	{
		if ( $this->token != false )
			return true;
		else
			return false;
	}
	
	/**
	 * Sets the account id to use for queries
	 *
	 * @param id - the account id
	 **/
	function setAccount($id)
	{
		$this->accountId = $id;
	}
	
	/**
	 * Get a list of Analytics accounts
	 *
	 * @return a list of analytics accounts
	 **/
	function getAnalyticsAccounts()
	{		
		# Request the list of accounts
		$response = $this->http($this->baseFeed . '/accounts/default');
		
		# Check if the response received exists, else stop processing now
		if ( $response == '' )
			return array();
		
		# Parse the XML using SimplePie
		$simplePie = new SimplePie();
		$simplePie->set_raw_data($response);
		$simplePie->init();
		$simplePie->handle_content_type();
		$accounts = $simplePie->get_items();
		
		# Make an array of the accounts
		$ids = array();
		foreach ( $accounts AS $account ) {
			$id = array();
			
			# Get the list of properties
			$properties = $account->get_item_tags('http://schemas.google.com/analytics/2009', 'property');
			
			# Loop through the properties
			foreach ( $properties AS $property ) {
				
				# Get the property information
				$name = $property['attribs']['']['name'];
				$value = $property['attribs']['']['value'];
				
				# Add the propery data to the id array
				$id[$name] = $value;
				
			}
			
			# Add the backward compatibility array items
			$id['title'] = $account->get_title();
			$id['id'] = 'ga:' . $id['ga:profileId'];
			
			$ids[] = $id;
		}
		
		return $ids;
	}
	
	/**
	 * Get a specific data metric
	 *
	 * @param metric - the metric to get
	 * @param startDate - the start date to get
	 * @param endDate - the end date to get
	 * @return the specific metric
	 **/
	function getMetric($metric, $startDate, $endDate)
	{
		# Ensure the start date is after Jan 1 2005
		$startDate = $this->verifyStartDate($startDate);
		
		# Request the metric data
		$response = $this->http($this->baseFeed . "/data?ids=$this->accountId&start-date=$startDate&end-date=$endDate&metrics=$metric");
		
		# Parse the XML using SimplePie
		$simplePie = new SimplePie();
		$simplePie->set_raw_data($response);
		$simplePie->init();
		$simplePie->handle_content_type();
		$datas = $simplePie->get_items();
	
		# Read out the data until the metric is found
		foreach ( $datas AS $data ) {
			$data_tag = $data->get_item_tags('http://schemas.google.com/analytics/2009', 'metric');
		 	return $data_tag[0]['attribs']['']['value'];
		}
	}
	
	/**
	 * Checks the date against Jan. 1 2005 because GA API only works until that date
	 *
	 * @param date - the date to compare
	 * @return the correct date
	 **/
	function verifyStartDate($date)
	{
		if ( strtotime($date) > strtotime('2005-01-01') )
			return $date;
		else
			return '2005-01-01';
	}
	
} // END class

?>