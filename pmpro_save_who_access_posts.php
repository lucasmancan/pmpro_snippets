// Add this code below to your active theme's functions.php or custom plugin.
<?php
/**
 * Capture time when a user viewed a post only.
 * Do not capture admin views, only users.
 * Stores an array as $array['username'] => timestamp.
 */
 function wll_save_when_viewed() {
	global $current_user;

	$post_type = get_post_type();

	// only capture this if post type is of post. Ignore for any other post type or if user is not logged in, adjust accordingly 
	if( is_user_logged_in() && 'post' == $post_type && ! current_user_can( 'manage_options' ) ) {

		// get the post data first.
		$post_data = get_post();
		
		if(!pmpro_has_membership_access($post_data->ID) ) {
			return;
		} 

		// Get user data
		$username = $current_user->display_name;

		$time_array = get_post_meta( $post_data->ID, 'wll_viewed_post', true );

		// if no data available yet, let's create the array.
		if( empty( $time_array ) ) {
			$time_array = array();
		}

		// update the array with the user and timestamp.
		$time_array[$username] = time();

		update_post_meta( $post_data->ID, 'wll_viewed_post', $time_array );
	} 
}
add_action( 'wp', 'wll_save_when_viewed' );

 /**
 * Create a custom column header called 'Who Last Viewed'
 */
function wll_who_viewed_column_header( $defaults ) {
    $defaults[ 'wll_who_viewed' ] = 'Who Last Viewed';
    return $defaults;
}
add_filter('manage_posts_columns', 'wll_who_viewed_column_header');

/**
 * Add the content to the column, and show a maximum of three.
 */
function wll_who_viewed_column( $column_name, $post_ID ) {
	if( 'wll_who_viewed' == $column_name ) {
		$viewed = get_post_meta( $post_ID, 'wll_viewed_post', true );			
		
		if($viewed == null){
			return;
		}
		
		$viewed = array_slice( $viewed, 0, 3 );

		// Sort according to value descending.
		arsort( $viewed );

		foreach ($viewed as $username => $timetamp) {
			echo $username . ' | ' . date( 'd-m-y (H:i:s)', $timetamp ) . '<br>';
		}
	}
}
add_action( 'manage_posts_custom_column', 'wll_who_viewed_column', 10, 2 );
