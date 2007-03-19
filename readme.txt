=== Plugin Name ===
Contributors: cavemonkey50
Donate link: http://cavemonkey50.com/code/
Tags: stats, google, analytics, tracking
Requires at least: 2.0
Tested up to: 2.1
Stable tag: 1.5

Adds the necessary JavaScript code to enable Google Analytics.

== Description ==

Google Analyticator adds the necessary JavaScript code to enable Google Analytics logging on any WordPress blog. This eliminates the need to edit your template code to begin logging.

**Features**

Google Analyticator Has the Following Features:

- Inserts tracking code on all pages WordPress manages.
- Automatically tracks outbound links.
- Provides support for download link tracking.
- Easy install: only need to know your tracking UID.
- Expandable: can insert additional tracking code if needed, while maintaining ease of use.
- Option to disable tracking of WordPress administrators.
- Can include tracking code in the footer, speeding up load times.
- Complete control over options; disable any feature if needed.

**Usage**

In your WordPress administration page go to Options > Google Analytics. From there enter your UID and enable logging. Information on how to obtain your UID can be found on the options page.

Once you save your settings the JavaScript code should now be appearing on all of your WordPress pages.

== Installation ==

Drop the google_analyticator folder into /wp-content/plugins/, and activate the plugin.

== Frequently Asked Questions ==

=Where is the Google Analytics code displayed?=

The Google Analytics code is added to the <head> section of your theme by default. It should be somewhere near the bottom of that section.

=Why don’t I see the Google Analytics code on my website?=

If you have switched off admin logging, you will not see the code. You can try enabling it temporarily or log out of your WordPress account to see if the code is displaying.

=Why is Google saying my tracking code isn’t installed?=

Google’s servers are slow at crawling for the tracking code. While the code may be visible on your site, it takes Google a number of days to realize it. The good news is hits are being recorded during this time; they just won’t be visible until Google acknowledges your tracking code.