/**
 * This will restrict all pages except Paid Memberships Pro pages or the home page of your website to non-members / non-approved members / logged-out users.
 * This won't affect administrators.
 * Add this code to your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_redirect_non_members() {

 <?php
 
 global $pmpro_pages;

	if( is_page( $pmpro_pages ) || is_home() || current_user_can( 'manage_options' )) {
		return;
	}

	$access = false;
		
	global $current_user, $pmpro_pages;
	
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;

	if( !empty( $user_id ) ) {
		// Get approval status
		if(  pmpro_has_membership_access( $post->ID ) ) {
					
			$access = true;
			} else {
				$access = false;
			}

		// Make sure logged-in non-members don't have access.
		if( ! pmpro_hasMembershipLevel() ) {
			$access = false;
		}
	}

	// if the user is not approved, redirect to BuddyPress restricted page or home page if add on not enabled.
	if ( ! $access ) {
		wp_redirect( home_url() );
		exit;
	}
}
add_action( 'template_redirect', 'my_pmpro_redirect_non_members', 45 );
