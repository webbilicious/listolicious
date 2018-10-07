<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_post_meta_by_key( "listo_director" );
delete_post_meta_by_key( "listo_year" );
delete_post_meta_by_key( "listo_url" );