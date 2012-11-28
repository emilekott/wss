<?php
/**
 * Manages WordPress comments
 *
 * @package WordPress
 * @subpackage Comment
 */

/**
 * Checks whether a comment passes internal checks to be allowed to add.
 *
 * If comment moderation is set in the administration, then all comments,
 * regardless of their type and whitelist will be set to false. If the number of
 * links exceeds the amount in the administration, then the check fails. If any
 * of the parameter contents match the blacklist of words, then the check fails.
 *
 * If the number of links exceeds the amount in the administration, then the
 * check fails. If any of the parameter contents match the blacklist of words,
 * then the check fails.
 *
 * If the comment is a trackback and part of the blogroll, then the trackback is
 * automatically whitelisted. If the comment author was approved before, then
 * the comment is automatically whitelisted.
 *
 * If none of the checks fail, then the failback is to set the check to pass
 * (return true).
 *
 * @since 1.2.0
 * @uses $wpdb
 *
 * @param string $author Comment Author's name
 * @param string $email Comment Author's email
 * @param string $url Comment Author's URL
 * @param string $comment Comment contents
 * @param string $user_ip Comment Author's IP address
 * @param string $user_agent Comment Author's User Agent
 * @param string $comment_type Comment type, either user submitted comment,
 *		trackback, or pingback
 * @return bool Whether the checks passed (true) and the comments should be
 *		displayed or set to moderated
 */
function check_comment($author, $email, $url, $comment, $user_ip, $user_agent, $comment_type) {
	global $wpdb;

	if ( 1 == get_option('comment_moderation') )
		return false; // If moderation is set to manual

	if ( get_option('comment_max_links') && preg_match_all("/<[Aa][^>]*[Hh][Rr][Ee][Ff]=['\"]([^\"'>]+)[^>]*>/", apply_filters('comment_text',$comment), $out) >= get_option('comment_max_links') )
		return false; // Check # of external links

	$mod_keys = trim(get_option('moderation_keys'));
	if ( !empty($mod_keys) ) {
		$words = explode("\n", $mod_keys );

		foreach ( (array) $words as $word) {
			$word = trim($word);

			// Skip empty lines
			if ( empty($word) )
				continue;

			// Do some escaping magic so that '#' chars in the
			// spam words don't break things:
			$word = preg_quote($word, '#');

			$pattern = "#$word#i";
			if ( preg_match($pattern, $author) ) return false;
			if ( preg_match($pattern, $email) ) return false;
			if ( preg_match($pattern, $url) ) return false;
			if ( preg_match($pattern, $comment) ) return false;
			if ( preg_match($pattern, $user_ip) ) return false;
			if ( preg_match($pattern, $user_agent) ) return false;
		}
	}

	// Comment whitelisting:
	if ( 1 == get_option('comment_whitelist')) {
		if ( 'trackback' == $comment_type || 'pingback' == $comment_type ) { // check if domain is in blogroll
			$uri = parse_url($url);
			$domain = $uri['host'];
			$uri = parse_url( get_option('home') );
			$home_domain = $uri['host'];
			if ( $wpdb->get_var($wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_url LIKE (%s) LIMIT 1", '%'.$domain.'%'