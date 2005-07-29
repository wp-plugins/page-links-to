<?php
/*
Plugin Name: Page Links To
Plugin URI: http://txfx.net/code/wordpress/page-links-to/
Description: Allows you to set a "links_to" meta key with a URI value that will be be used when listing WP pages.  Good for setting up navigational links to non-WP sections of your 
Version: 1.0
Author URI: http://txfx.net/
*/

/*  Copyright 2005  Mark Jaquith (email: mark.gpl@txfx.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
=== INSTRUCTIONS ===
1) upload this file to /wp-content/plugins/
2) activate this plugin in the WordPress interface
3) create a new page with a title of your choosing, and with the parent page of your choosing.  Leave the content of the page blank.
4) add a meta key "links_to" with a full URI value (like "http://google.com/") (obviously without the quotes)

That's it!  Now, when you use wp_list_page(), that page should link to the "links_to" value, instead of its page
*/ 

function txfx_get_page_links_to_meta () {
	global $wpdb, $page_links_to_cache;

	if (!isset($page_links_to_cache)) {

		$links_to = $wpdb->get_results(
		"SELECT	post_id, meta_value " .
		"FROM $wpdb->postmeta, $wpdb->posts " .
		"WHERE post_id = ID AND meta_key = 'links_to' AND post_status = 'static'");
		} else {
			return $page_links_to_cache;
		}

		if (!$links_to) {
			$page_links_to_cache = false;
			return false;
		}

		foreach ($links_to as $link) {
		$page_links_to_cache[$link->post_id] = $link->meta_value;
		}

		return $page_links_to_cache;
	}

function txfx_filter_links_to_pages ($link, $page_id) {
	$page_links_to_cache = txfx_get_page_links_to_meta();

	if ( $page_links_to_cache[$page_id] )
		$link = $page_links_to_cache[$page_id];

	return $link;
}

add_filter('page_link', 'txfx_filter_links_to_pages', 10, 2);
?>