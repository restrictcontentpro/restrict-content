<?php
/**
 * User Checks
 *
 * @package     Restrict Content
 * @subpackage  User Checks
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Add a different filter to the post content based on
 * the current user's capabilities.
 *
 * @deprecated 2.1.4 Handled by rc_user_can_access() and rc_filter_restricted_content() instead.
 *
 * @return void
 */
function rcCheckUser() {
	if ( current_user_can( 'read' ) ) {
		if ( current_user_can( 'edit_posts' ) ) {
			if ( current_user_can( 'upload_files' ) ) {
				if ( current_user_can( 'moderate_comments' ) ) {
					if ( current_user_can( 'switch_themes' ) ) {
						//do nothing here for admin
					} else {
						add_filter( 'the_content', 'rcMetaDisplayEditor' );
					}
				} else {
					add_filter( 'the_content', 'rcMetaDisplayAuthor' );
				}
			} else {
				add_filter( 'the_content', 'rcMetaDisplayContributor' );
			}
		} else {
			add_filter( 'the_content', 'rcMetaDisplaySubscriber' );
		}
	} else {
		add_filter( 'the_content', 'rcMetaDisplayNone' );
	}
}

//add_action( 'loop_start', 'rcCheckUser' );

/**
 * Checks whether a user can access a post
 *
 * @param int $user_id ID of the user to check, or 0 for the current user.
 * @param int $post_id ID of the post to check, or 0 for the current post.
 *
 * @since 2.1.4
 * @return bool Whether or not the user has access to view the post.
 */
function rc_user_can_access( $user_id = 0, $post_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$required_level = get_post_meta( $post_id, 'rcUserLevel', true );

	if ( empty( $required_level ) || 'None' == $required_level || current_user_can( 'manage_options' ) ) {
		return true;
	}

	return user_can( $user_id, strtolower( $required_level ) );

}