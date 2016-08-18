/* global inlineEditPost */
function listo_quick_edit() {

	var $ = jQuery;
	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			var $post_row = $( '#post-' + $post_id );

			// get the data
			var $director = $( '.column-director', $post_row ).text();
			var $year = $( '.column-year', $post_row ).text();

			// populate the data
			$( ':input[name="listo_director"]', $edit_row ).val( $director );
			$( ':input[name="listo_year"]', $edit_row ).val( $year );
		}
	};
}

// Another way of ensuring inlineEditPost.edit isn't patched until it's defined
if ( inlineEditPost ) {
	listo_quick_edit();
} else {
	jQuery( listo_quick_edit );
}