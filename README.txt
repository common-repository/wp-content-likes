=== WordPress Content Likes WordPress Plugin ===
Contributors: radboris, zwilson, fsimmons
Donate link: www.gulosolutions.com
Tags: likes, kpi, analytics, user activity
Requires at least: 3.0.1
Tested up to: 5.3
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Track likes for WP published content in posts, pages, custom posts.

== Installation ==

1. How to install:

  * Upload the zipped to the `/wp-content/plugins/` directory
  * Install using composer

2. Activate the plugin in the 'Plugins' menu in WordPress
3. Choose what to track under Settings
4. Add the shortcode (`[wordpress-content-likes_like_button]`) to the block editor for a the post, plage or plugin.
5. Alternatively, display the like button with `<?php echo do_shortcode('[wordpress-content-likes_like_button]'); ?>`
6. Or, echo the like button using a custom hook -- <?php echo wp_content_likes_button(); ?> in a partial or page
7. Check plugin widget for highest ranked blogs, pages or custom post types

== Frequently Asked Questions ===

* How do I add the plugin shortcode?

`[wordpress-content-likes_like_button]`

== Changelog ==

1.1.2

* Fixed table name issue

1.1.1

* Fixed pageid check

* Restored jquery dependency

1.1.0

* Fixed likes count and JS loading

* Data and user interaction saved in custom table

1.0.16

* Update table, tracking data

* Refactor for metaboxes and widget

1.0.15

* Use file name for admin link

* Add utm to author

1.0.14

* Add table in admin section

* Fixed settings link issue

1.0.13

* Add JS class vars

* Add settings link

1.0.12

* restore shortcode

* add option to delete data

1.0.11

* add custom hook, `wp_content_likes_button`, to display button

* refactor admin for clarity and speed

1.0.10

* Fix JS error related to exporting like count and vote cookie

1.0.9

* Remove admin UI if no tracking is selected
* Selectively  display likes info based on type

1.0.8

* Fixed JS error for count not defined on certain pages

1.0.7

* Fixed bug where likes query was returning most recent post
* Changed amdin options page name, options, sanitized and validated input

1.0.6

* Change plugin slug
* Remove plugin jquery

1.0.5

* Fixed minor config issues

1.0.4

* Remove `LIKE` for icon

1.0.3

* Prevent multiple clicks on like button. Better export of JS vars

1.0.2

* Concatenate post id with IP

1.0.0

* Record likes for post, pages, custom posts and display highest count for each category in widget and in a custom column in dashboard for each content type
