<?php
/*
Plugin Name: Listolicious
Description: The shortcode displays a movie list in the style of Mubi
Version:     1.2.1
Author:      Daniel Hånberg Alonso
Author URI:  http://webbilicious.se
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die();

/**
 * Main class for Listolicious
 */
class Listolicious {

	function __construct() {
		
		$this->init();

	}

	/**
	 * Initiates all hooks, actions and filters. 
	 *	 	
	 * @since 1.0
	 */
	public function init() {

		load_plugin_textdomain( 'listolicious', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );		
		add_action( 'wp_print_styles', array( $this, 'add_style' ) );
      	add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_style' ) );
		
		add_shortcode('listolicious', array( $this, 'shortcode' ) );

		add_action( 'init', array( $this, 'custom_post_type' ), 0 );
		add_action( 'init', array( $this, 'custom_taxonomy' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		/* Only load the admin actions if you are in the admin  */
		if ( is_admin() ) {

			add_action( 'save_post', array( $this, 'save_details')  );
			add_filter( 'manage_edit-movies_columns', array( $this, 'edit_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'add_columns' ) );
			add_action( 'quick_edit_custom_box', array( $this, 'quickedit' ), 10, 2 );
			add_action(	'admin_enqueue_scripts', array( $this, 'quickedit_script' ), 10,  1 );

		}
	}

	/**
	 * Adds stylesheet
	 *	 
	 * @since 1.0
	 */
	function add_style() {
		wp_register_style( 'listo_stylesheet', plugins_url( '/css/styles.css', __FILE__ ) );
		wp_enqueue_style( 'listo_stylesheet' );
	}

	/**
	 * Adds admin stylesheet
	 *	 
	 * @since 1.2
	 */
	function add_admin_style() {
		wp_register_style( 'listo_admin_stylesheet', plugins_url( '/css/admin.css', __FILE__ ) );
		wp_enqueue_style( 'listo_admin_stylesheet' );
	}	

	/**
	 * Creates the custom post type "movies"
	 *
	 * Because the plugin is made specifically for displaying a movie list with custom fields, we 
	 * need to create a custom post type.
	 *
	 * @since 1.0
	 */
	function custom_post_type() {

		$labels = array(
			'name'                  => __( 'Movies', 'listolicious' ),
			'singular_name'         => __( 'Movie', 'listolicious' ),
			'menu_name'             => __( 'Movies', 'listolicious' ),
			'name_admin_bar'        => __( 'Movies', 'listolicious' ),
			'archives'              => __( 'Movie Archives', 'listolicious' ),
			'all_items'             => __( 'All Movies', 'listolicious' ),
			'add_new_item'          => __( 'Add New Movie', 'listolicious' ),
			'new_item'              => __( 'New Movie', 'listolicious' ),
			'edit_item'             => __( 'Edit Movie', 'listolicious' ),
			'update_item'           => __( 'Update Movie', 'listolicious' ),
			'view_item'             => __( 'View Movie', 'listolicious' ),
			'search_items'          => __( 'Search Movie', 'listolicious' ),
		);

		$args = array(
			'label'                 => __( 'Movie', 'listolicious' ),
			'description'           => __( 'Movies for your list', 'listolicious' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-tickets-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'taxonomies'			=> array( 'lists' ), 
			'has_archive'           => true,		
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		);

		register_post_type( 'movies', $args );

	}

	/**
	 * Creates the taxonomy "lists"
	 *
	 * As we want to be able to create multiple lists, we need a custom taxonomy for this. 
	 *
	 * @since 1.1
	 */
	function custom_taxonomy() {

		$labels = array(
			'name'              => __( 'Lists', 'listolicious' ),
			'singular_name'     => __( 'List', 'listolicious' ),
			'menu_name'         => __( 'Lists', 'listolicious' ),
			'name_admin_bar'	=> __( 'Lists', 'listolicious' ),			
			'search_items'      => __( 'Search Lists', 'listolicious' ),
			'all_items'         => __( 'All Lists', 'listolicious' ),
			'edit_item'         => __( 'Edit List', 'listolicious' ),
			'update_item'       => __( 'Update List', 'listolicious' ),
			'add_new_item'      => __( 'Add New List', 'listolicious' ),
			'new_item_name'     => __( 'New List Name', 'listolicious' ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'public'                => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'publicly_queryable'    => true,
			'rewrite'               => array( 'slug' => 'list' ),
		);

		register_taxonomy( 'lists', 'movies', $args );

	}	

	/**
	 * Adds a metabox with custom fields
	 *
	 * @since 1.0
	 */
	function add_meta_boxes(){
		add_meta_box('details-meta', __('Details', 'listolicious'), array ($this, 'details'), 'movies', 'normal', 'high');
	}

	/**
	 * Adds custom fields
	 *
	 * Because we want to display the director and release year for the movies in our list,
	 * we need to create custom fields. 
	 *
	 * @since 1.0
	 */
	function details(){
		global $post;

		$custom = get_post_custom($post->ID);
		$listo_director = isset( $custom["listo_director"][0] ) ? $custom["listo_director"][0] : '';
		$listo_year = isset( $custom["listo_year"][0] ) ? $custom["listo_year"][0] : '';
		$listo_url = isset( $custom["listo_url"][0] ) ? $custom["listo_url"][0] : '';

		wp_nonce_field( 'save_listolicious', 'movie_edit_nonce' );
		?>
		<p><label><?php _e('Director', 'listolicious'); ?>:</label><br />
		<input type="text" class="listo-input" name="listo_director" value="<?php echo esc_attr( $listo_director ); ?>" /></p>
		<p><label><?php _e('Year', 'listolicious'); ?>:</label><br />
		<input type="text" class="listo-input_small" name="listo_year" value="<?php echo esc_attr( $listo_year ); ?>" /></p>
		<p><label><?php _e('Redirect URL', 'listolicious'); ?>:</label><br />
		<input type="text" class="listo-input" name="listo_url" value="<?php echo esc_attr( $listo_url ); ?>" /></p>
		<?php
	} 

	/**
	 * Saves/updates the new custom fields
	 *
	 * @since 1.0
	 */
	function save_details(){
		global $post;

		if ( ! empty( $_POST ) && check_admin_referer( 'save_listolicious', 'movie_edit_nonce' ) ) {

			$id = ( isset( $post->ID ) ? get_the_ID() : NULL );
		 	$listo_director = isset( $_POST['listo_director'] ) ? sanitize_text_field( $_POST['listo_director'] ) : '';
		 	$listo_year = isset( $_POST['listo_year'] ) ? sanitize_text_field( $_POST['listo_year'] ) : '';
		 	$listo_url = isset( $_POST['listo_url'] ) ? sanitize_text_field( $_POST['listo_url'] ) : '';

			update_post_meta( $id, "listo_director", $listo_director );
			update_post_meta( $id, "listo_year", $listo_year );
			update_post_meta( $id, "listo_url", $listo_url );
		}
	}

	/**
	 * Creates the shortcode which the plugin uses to display the list
	 *
	 * The plugin creates the shortcode [listolicious] for displaying the movie list.
	 * The shortcode comes with two attributes, "list" and "orderby".
	 * As a default the lists is ordered by the custom field "year" and shows all movies. 
	 * Example: [listolicious list="favourites" orderby="title"].
	 *
	 * @since 1.0
	 */
	function shortcode( $atts ) {

		$args = array();
		$output = '';
		$count = 0;		 
		
		$atts = shortcode_atts( 
			array( 
				'orderby' => '',
				'list' => '', 
			), $atts );		
		
		$args['order'] = 'ASC';
		$args['post_type'] = 'movies';
		$args['posts_per_page'] = -1;
			
		$list = sanitize_text_field( $atts[ 'list' ] );
		if( $list ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'lists',
					'field'    => 'slug',
					'terms'    => $list,
				),
			);
		}

		$orderby = sanitize_text_field( $atts[ 'orderby' ] );
		switch ($orderby) {
			case 'title':
				$args['orderby'] = 'title';
				break;
			
			case 'year':
			default:
				$args['meta_key'] = 'listo_year';
				$args['orderby'] = 'meta_value_num title';
				break;				
		}

		query_posts( $args );
     		
		if (have_posts()) :
			ob_start();?>
			<div class="listo-grid">
				<ul>
				<?php while (have_posts()) : the_post();
					$count++;
					$director = get_post_meta( get_the_ID(), 'listo_director' );
					$director = $director[0];
					$year = get_post_meta( get_the_ID(), 'listo_year' );
					$year = $year[0];
					$url = get_post_meta( get_the_ID(), 'listo_url' );
					$comma = (!empty($director) && !empty($year)) ? ', ' : ''; ?>
						<li class="listo-film">
							<div class="listo-film-inner">
								<div class="listo-film-position"><?php echo $count; ?></div>
								<div class="listo-film-meta-wrapper">
									<div class="listo-film-meta-inner">
										<h2 class="listo-film-heading"><?php if(empty($url)): ?>
											<a href="<?php echo get_permalink(); ?>" class="listo-film-title" lang="en"><?php echo get_the_title(); ?></a>
										<?php else: ?>
											<a href="<?php echo $url[0]; ?>" class="listo-film-title" target="_blank" lang="en"><?php echo get_the_title(); ?></a>
										<?php endif; ?>
										</h2>
										<div class="listo-film-meta"><?php echo esc_html( $director . $comma . $year ); ?></div>
									</div>
								</div>								
							</div>	
							<?php if(empty($url)): ?>
								<a href="<?php echo get_permalink(); ?>" class="listo-film-link"></a>
							<?php else: ?>
								<a href="<?php echo $url[0]; ?>" class="listo-film-link" target="_blank"></a>
							<?php endif; ?>
							<?php echo $this->get_thumbnail( get_the_ID() ); ?>
						</li>
	  			<?php endwhile; ?>
	  			</ul>
	  		</div>
		<?php $output = ob_get_clean();
		endif;
		wp_reset_query();
		return $output;
	}

	/**
	 * Edits columns in list view to accommodate new custom fields
	 *
	 * We only want to display information in the list view which is relevant to the custom post type.
	 *
	 * @since 1.0
	 */
	function edit_columns($columns){
		$columns = array(
			"cb" => '<input type="checkbox" />',
			"title" => __( 'Title', 'listolicious' ),
			"director" => __( 'Director', 'listolicious' ),
			"year" => __( 'Year', 'listolicious' ),
			"lists" => __( 'Lists', 'listolicious' ),
		);
	  	return $columns;
	}

	/**
	 * Outputs the data from our custom fields in the new list view columns
	 *
	 * @since 1.0
	 */
	function add_columns($column){
		global $post;
	
		switch ($column) {
		case "director":
			$custom = get_post_custom();
			echo esc_html( $custom['listo_director'][0] );
			break;
		case "year":
			$custom = get_post_custom();
			echo esc_html( $custom['listo_year'][0] );
			break;			
		case "lists":
	  		echo get_the_term_list($post->ID,'lists','',', ','');
      		break;
      	}
	}

	/**
	 * Adds quickedit button for editing in list view
	 *
	 * @since 1.0
	 */
	function quickedit($column_name, $post_type) {	
	    static $printNonce = TRUE;
	    if ( $printNonce ) {
	        $printNonce = FALSE;
	        wp_nonce_field( plugin_basename( __FILE__ ), 'movie_edit_nonce' );
	    }

	    ?>
	    <fieldset class="inline-edit-col-right">
	      <div class="inline-edit-col column-<?php echo $column_name; ?>">
	        <label class="inline-edit-group">
	        <?php 
	         switch ( $column_name ) {
	         case 'director':
	             ?><span class="title"><?php _e('Director', 'listolicious'); ?></span><input type="text" name="listo_director" /><?php
	             break;
	         case 'year':
	             ?><span class="title"><?php _e('Year', 'listolicious'); ?></span><input type="text" name="listo_year" /><?php
	             break;
	         }
	        ?>
	        </label>
	      </div>
	    </fieldset>
	    <?php
	}

	/**
	 * Adds quickedit script for getting values into quickedit fields
	 *
	 * @since 1.0
	 */
	public function quickedit_script( $hook = '' ) {

		if ( 'edit.php' === $hook &&
			isset( $_GET['post_type'] ) &&
			'movies' === $_GET['post_type'] ) {

			wp_enqueue_script( 'listo_quickedit', plugins_url('js/quickedit.js', __FILE__), false, null, true );

		}
	}

	/**
	 * Displays a placeholder image if a thumbnail does not exist
	 *
	 * @since 1.2.1
	 */
	function get_thumbnail( $post_id = '' ) {
		if ( has_post_thumbnail($post_id)): 
			$image = get_the_post_thumbnail( $post_id, '', array( 'class' => 'list-film-image' ) ); 
		else: 
			$image = '<img src="'. plugin_dir_url( __FILE__ ) . 'placeholder.jpg" class="list-film-image" />';
		endif; 

		return $image;
	}
}
$Listolicious = new Listolicious();