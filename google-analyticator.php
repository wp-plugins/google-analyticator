<?php
/*
 * Plugin Name: Google Analyticator
 * Version: 1.4
 * Plugin URI: http://cavemonkey50.com/code/google-analyticator/
 * Description: Adds the necessary JavaScript code to enable <a href="http://www.google.com/analytics/">Google's Analytics</a>. After enabling this plugin visit <a href="options-general.php?page=google-analyticator.php">the options page</a> and enter your Google Analytics' UID and enable logging.
 * Author: Ronald Heft, Jr.
 * Author URI: http://cavemonkey50.com/
 */

// Constants for enabled/disabled state
define("ga_enabled", "enabled", true);
define("ga_disabled", "disabled", true);

// Defaults, etc.
define("key_ga_uid", "ga_uid", true);
define("key_ga_status", "ga_status", true);
define("key_ga_admin", "ga_admin_status", true);
define("key_ga_extra", "ga_extra", true);
define("key_ga_outbound", "ga_outbound", true);
define("key_ga_downloads", "ga_downloads", true);

define("ga_uid_default", "XX-XXXXX-X", true);
define("ga_status_default", ga_disabled, true);
define("ga_admin_default", ga_enabled, true);
define("ga_extra_default", "", true);
define("ga_outbound_default", ga_enabled, true);
define("ga_downloads_default", "", true);

// Create the default key and status
add_option(key_ga_status, ga_status_default, 'If Google Analytics logging in turned on or off.');
add_option(key_ga_uid, ga_uid_default, 'Your Google Analytics UID.');
add_option(key_ga_admin, ga_admin_default, 'If WordPress admins are counted in Google Analytics.');
add_option(key_ga_extra, ga_extra_default, 'Addition Google Analytics tracking options');
add_option(key_ga_outbound, ga_outbound_default, 'Add tracking of outbound links');
add_option(key_ga_downloads, ga_downloads_default, 'Download extensions to track with Google Analyticator');

// Create a option page for settings
add_action('admin_menu', 'add_ga_option_page');

// Hook in the options page function
function add_ga_option_page() {
	global $wpdb;
	add_options_page('Google Analyticator Options', 'Google Analytics', 8, basename(__FILE__), 'ga_options_page');
}

function ga_options_page() {
	// If we are a postback, store the options
 	if (isset($_POST['info_update'])) {
		check_admin_referer();
		
		// Update the status
		$ga_status = $_POST[key_ga_status];
		if (($ga_status != ga_enabled) && ($ga_status != ga_disabled))
			$ga_status = ga_status_default;
		update_option(key_ga_status, $ga_status);

		// Update the UID
		$ga_uid = $_POST[key_ga_uid];
		if ($ga_uid == '')
			$ga_uid = ga_uid_default;
		update_option(key_ga_uid, $ga_uid);

		// Update the admin logging
		$ga_admin = $_POST[key_ga_admin];
		if (($ga_admin != ga_enabled) && ($ga_admin != ga_disabled))
			$ga_admin = ga_admin_default;
		update_option(key_ga_admin, $ga_admin);

		// Update the extra tracking code
		$ga_extra = $_POST[key_ga_extra];
		update_option(key_ga_extra, $ga_extra);

		// Update the outbound tracking
		$ga_outbound = $_POST[key_ga_outbound];
		if (($ga_outbound != ga_enabled) && ($ga_outbound != ga_disabled))
			$ga_outbound = ga_outbound_default;
		update_option(key_ga_outbound, $ga_outbound);
		
		// Update the download tracking code
		$ga_downloads = $_POST[key_ga_downloads];
		update_option(key_ga_downloads, $ga_downloads);

		// Give an updated message
		echo "<div class='updated'><p><strong>Google Analyticator options updated</strong></p></div>";
	}

	// Output the options page
	?>

		<form method="post" action="options-general.php?page=google-analyticator.php">
		<div class="wrap">
			<h2>Google Analyticator Options</h2>
			<fieldset class='options'>
				<legend>Basic Options</legend>
				<?php if (get_option(key_ga_status) == ga_disabled) { ?>
					<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
					Google Analytics integration is currently <strong>DISABLED</strong>.
					</div>
				<?php } ?>
				<?php if ((get_option(key_ga_uid) == "XX-XXXXX-X") && (get_option(key_ga_status) != ga_disabled)) { ?>
					<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
					Google Analytics integration is currently enabled, but you did not enter a UID. Tracking will not occur.
					</div>
				<?php } ?>
				<table class="editform" cellspacing="2" cellpadding="5" width="100%">
					<tr>
						<th width="30%" valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_status ?>">Google Analytics logging is:</label>
						</th>
						<td>
							<?php
							echo "<select name='".key_ga_status."' id='".key_ga_status."'>\n";
							
							echo "<option value='".ga_enabled."'";
							if(get_option(key_ga_status) == ga_enabled)
								echo " selected='selected'";
							echo ">Enabled</option>\n";
							
							echo "<option value='".ga_disabled."'";
							if(get_option(key_ga_status) == ga_disabled)
								echo" selected='selected'";
							echo ">Disabled</option>\n";
							
							echo "</select>\n";
							?>
						</td>
					</tr>
					<tr>
						<th valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_uid; ?>">Your Google Analytics' UID:</label>
						</th>
						<td>
							<?php
							echo "<input type='text' size='50' ";
							echo "name='".key_ga_uid."' ";
							echo "id='".key_ga_uid."' ";
							echo "value='".get_option(key_ga_uid)."' />\n";
							?>
							<p style="margin: 5px 10px;">Enter your Google Analytics' UID in this box. The UID is needed for Google Analytics to log your website stats. Your UID can be found by looking in the JavaScript Google Analytics gives you to put on your page. Look for your UID in between <strong>_uacct = "UA-11111-1";</strong> in the JavaScript. In this example you would put <strong>UA-11111-1</strong> in the UID box.</p>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class='options'>
				<legend>Advanced Options</legend>
					<table class="editform" cellspacing="2" cellpadding="5" width="100%">
					<tr>
						<th width="30%" valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_admin ?>">WordPress admin logging:</label>
						</th>
						<td>
							<?php
							echo "<select name='".key_ga_admin."' id='".key_ga_admin."'>\n";
							
							echo "<option value='".ga_enabled."'";
							if(get_option(key_ga_admin) == ga_enabled)
								echo " selected='selected'";
							echo ">Enabled</option>\n";
							
							echo "<option value='".ga_disabled."'";
							if(get_option(key_ga_admin) == ga_disabled)
								echo" selected='selected'";
							echo ">Disabled</option>\n";
							
							echo "</select>\n";
							?>
							<p style="margin: 5px 10px;">Disabling this option will prevent all logged in WordPress admins from showing up on your Google Analytics reports. A WordPress admin is defined as a user with a level 8 or higher. Your user level is <?php global $user_level; echo $user_level; ?>.</p>
						</td>
					</tr>
					<tr>
						<th width="30%" valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_outbound ?>">Outbound link tracking:</label>
						</th>
						<td>
							<?php
							echo "<select name='".key_ga_outbound."' id='".key_ga_outbound."'>\n";
							
							echo "<option value='".ga_enabled."'";
							if(get_option(key_ga_outbound) == ga_enabled)
								echo " selected='selected'";
							echo ">Enabled</option>\n";
							
							echo "<option value='".ga_disabled."'";
							if(get_option(key_ga_outbound) == ga_disabled)
								echo" selected='selected'";
							echo ">Disabled</option>\n";
							
							echo "</select>\n";
							?>
							<p style="margin: 5px 10px;">Disabling this option will turn off the tracking of outbound links. It's recommended not to disable this option unless you're a privacy advocate (now why would you be using Google Analytics in the first place?) or it's causing some kind of weird issue.</p>
						</td>
					</tr>
					<tr>
						<th valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_extra; ?>">Additional tracking code:</label>
						</th>
						<td>
							<?php
							echo "<input type='text' size='50' ";
							echo "name='".key_ga_extra."' ";
							echo "id='".key_ga_extra."' ";
							echo "value='".stripslashes(get_option(key_ga_extra))."' />\n";
							?>
							<p style="margin: 5px 10px;">Enter any additional bits of tracking code that you would like to include in the script. For example to track a subdomain in your main domain's profile you would add <strong>_udn="example.com";</strong>. Addition tracking code information can be found in <a href="http://www.google.com/support/analytics/">Google Analytic's FAQ</a>.</p>
						</td>
					</tr>
					<tr>
						<th valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_downloads; ?>">Download extensions to track:</label>
						</th>
						<td>
							<?php
							echo "<input type='text' size='50' ";
							echo "name='".key_ga_downloads."' ";
							echo "id='".key_ga_downloads."' ";
							echo "value='".stripslashes(get_option(key_ga_downloads))."' />\n";
							?>
							<p style="margin: 5px 10px;">Enter any extensions of files you would like to be tracked as a download. For example to track all MP3s and PDFs enter <strong>mp3,pdf</strong>. <em>Outbound link tracking must be enabled for downloads to be tracked.</em></p>
						</td>
					</tr>
					</table>
			</fieldset>
			<p class="submit">
				<input type='submit' name='info_update' value='Update Options' />
			</p>
		</div>
		</form>

<?php
}

// Add the script
add_action('wp_head', 'add_google_analytics');

// Add the ougoing links script
function outgoing_links() {
	if (get_option(key_ga_outbound) == ga_enabled) {
		add_filter('comment_text', 'ga_outgoing');
		add_filter('get_comment_author_link', 'ga_outgoing_comment_author');
		add_filter('the_content', 'ga_outgoing');
		add_filter('the_excerpt', 'ga_outgoing');
	}
}

// The guts of the Google Analytics script
function add_google_analytics() {
	global $user_level;
	$uid = get_option(key_ga_uid);
	$extra = stripslashes(get_option(key_ga_extra));
	if ((get_option(key_ga_status) != ga_disabled) && ($uid != "XX-XXXXX-X")) {
		if ((get_option(key_ga_admin) == ga_enabled) || ((get_option(key_ga_admin) == ga_disabled) && ($user_level < 8))) {
			echo "<!-- Google Analytics Tracking by Google Analyticator: http://cavemonkey50.com/code/google-analyticator/ -->\n";
			echo "	<script src=\"http://www.google-analytics.com/urchin.js\" type=\"text/javascript\"></script><script type=\"text/javascript\"> _uacct=\"$uid\"; $extra urchinTracker(); </script>\n";
			outgoing_links();
		}
	}
}

// Finds all the links contained in a post or comment
function ga_outgoing($input) {
	static $link_pattern = '/<a (.*?)href="(.*?)\/\/(.*?)"(.*?)>(.*?)<\/a>/i';
	static $link_pattern_2 = '/<a (.*?)href=\'(.*?)\/\/(.*?)\'(.*?)>(.*?)<\/a>/i';
	$input = preg_replace_callback($link_pattern, ga_parse_link, $input);
	$input = preg_replace_callback($link_pattern_2, ga_parse_link, $input);
	return $input;
}

// Takes the comment author link and adds the Google outgoing tracking code
function ga_outgoing_comment_author($input) {
	static $link_pattern = '(.*href\s*=\s*)[\"\']*(.*)[\"\'] (.*)';
	ereg($link_pattern, $input, $matches);
	if ($matches[2] == "") return $input;
	
	$target = ga_find_domain($matches[2]);
	$local_host = ga_find_domain($_SERVER["HTTP_HOST"]);
	if ( $target["domain"] != $local_host["domain"]  ){
		$tracker_code .= " onclick=\"javascript:urchinTracker ('/outbound/".$target["host"]."');\" ";
	} 
	return $matches[1] . "\"" . $matches[2] . "\"" . $tracker_code . $matches[3];
}

// Takes a link and adds the Google outgoing tracking code
function ga_parse_link($matches){
	$local_host = ga_find_domain($_SERVER["HTTP_HOST"]);
	$target = ga_find_domain($matches[3]);
	$url = $matches[3];
	$file_extension = strtolower(substr(strrchr($url,"."),1));
	if ( $target["domain"] != $local_host["domain"]  ){
		$tracker_code .= " onclick=\"javascript:urchinTracker ('/outbound/".$target["host"]."');\"";
	}
	if ( ($target["domain"] == $local_host["domain"])  && (ga_check_download($file_extension)) ){
		$url = strtolower(substr(strrchr($url,"/"),1));
		$tracker_code .= " onclick=\"javascript:urchinTracker ('/downloads/".$file_extension."/".$url."');\"";
	}
	return '<a href="' . $matches[2] . '//' . $matches[3] . '"' . $matches[1] . $matches[4].$tracker_code.'>' . $matches[5] . '</a>';    
}

// Checks to see if the link is on your site
function ga_find_domain($url){
	$host_pattern = "/^(http:\/\/)?([^\/]+)/i";
	$domain_pattern = "/[^\.\/]+\.[^\.\/]+$/";

	preg_match($host_pattern, $url, $matches);
	$host = $matches[2];
	preg_match($domain_pattern, $host, $matches);
	return array("domain"=>$matches[0],"host"=>$host);    
}

// Checks to see if the requested URL is a download
function ga_check_download($file_extension){
	if (get_option(key_ga_downloads)){
		$extensions = explode(',', get_option(key_ga_downloads));
	
		foreach ($extensions as $extension) {
			if ($extension == $file_extension)
				return true;
		}
	}
}

?>