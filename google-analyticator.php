<?php
/*
 * Plugin Name: Google Analyticator
 * Version: 1.5
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
define("key_ga_footer", "ga_footer", true);

define("ga_uid_default", "XX-XXXXX-X", true);
define("ga_status_default", ga_disabled, true);
define("ga_admin_default", ga_enabled, true);
define("ga_extra_default", "", true);
define("ga_outbound_default", ga_enabled, true);
define("ga_downloads_default", "", true);
define("ga_footer_default", ga_disabled, true);

// Create the default key and status
add_option(key_ga_status, ga_status_default, 'If Google Analytics logging in turned on or off.');
add_option(key_ga_uid, ga_uid_default, 'Your Google Analytics UID.');
add_option(key_ga_admin, ga_admin_default, 'If WordPress admins are counted in Google Analytics.');
add_option(key_ga_extra, ga_extra_default, 'Addition Google Analytics tracking options');
add_option(key_ga_outbound, ga_outbound_default, 'Add tracking of outbound links');
add_option(key_ga_downloads, ga_downloads_default, 'Download extensions to track with Google Analyticator');
add_option(key_ga_footer, ga_footer_default, 'If Google Analyticator is outputting in the footer');

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
		
		// Update the footer
		$ga_footer = $_POST[key_ga_footer];
		if (($ga_footer != ga_enabled) && ($ga_footer != ga_disabled))
			$ga_footer = ga_footer_default;
		update_option(key_ga_footer, $ga_footer);

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
						<th width="30%" valign="top" style="padding-top: 10px;">
							<label for="<?php echo key_ga_footer ?>">Footer tracking code:</label>
						</th>
						<td>
							<?php
							echo "<select name='".key_ga_footer."' id='".key_ga_footer."'>\n";
							
							echo "<option value='".ga_enabled."'";
							if(get_option(key_ga_footer) == ga_enabled)
								echo " selected='selected'";
							echo ">Enabled</option>\n";
							
							echo "<option value='".ga_disabled."'";
							if(get_option(key_ga_footer) == ga_disabled)
								echo" selected='selected'";
							echo ">Disabled</option>\n";
							
							echo "</select>\n";
							?>
							<p style="margin: 5px 10px;">Enabling this option will insert the Google Analytics tracking code in your site's footer instead of your header. This will speed up your page loading if turned on. Not all themes support code in the footer, so if you turn this option on, be sure to check the Analytics code is still displayed on your site.</p>
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
if (get_option(key_ga_footer) == ga_enabled) {
	add_action('wp_footer', 'add_google_analytics');
} else {
	add_action('wp_head', 'add_google_analytics');
}

// The guts of the Google Analytics script
function add_google_analytics() {
	global $user_level;
	$uid = get_option(key_ga_uid);
	$extra = stripslashes(get_option(key_ga_extra));
	$extensions = str_replace (",", "|", get_option(key_ga_downloads));
	
	// If GA is enabled and has a valid key
	if ((get_option(key_ga_status) != ga_disabled) && ($uid != "XX-XXXXX-X")) {
		
		// Track if admin tracking is enabled or disabled and less than user level 8
		if ((get_option(key_ga_admin) == ga_enabled) || ((get_option(key_ga_admin) == ga_disabled) && ($user_level < 8))) {
			
			echo "<!-- Google Analytics Tracking by Google Analyticator: http://cavemonkey50.com/code/google-analyticator/ -->\n";
			echo "	<script src=\"http://www.google-analytics.com/urchin.js\" type=\"text/javascript\"></script>\n";
			// If outbound tracking is enabled
			if ( get_option (key_ga_outbound) == ga_enabled )
				echo "	<script src=\"" . get_option('siteurl') . "/wp-content/plugins/google-analyticator/ga_external-links.js\" type=\"text/javascript\"></script>\n";
			echo "	<script type=\"text/javascript\">\n";
			// If outbound tracking is enabled
			if ( get_option (key_ga_outbound) == ga_enabled ) {
				// If in the header
				if ( get_option (key_ga_footer) != ga_enabled )
					echo "		onContent(function() {\n";
				echo "		urchin = new urchin();\n";
				echo "		urchin.trackDownload = \"$extensions\";\n";
				echo "		urchin.trackLinks();\n";
				// If in the header
				if ( get_option (key_ga_footer) != ga_enabled )
					echo "		} );\n";
			}
			echo "		_uacct=\"$uid\"; $extra urchinTracker();\n";
			echo "	</script>\n";
					
		}
	}
}

?>