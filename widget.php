<?php

defined( 'ABSPATH' ) or die();

/**
 * Create a widget for displaying a random movie from your list
 *
 * @since 1.4
 */
class listo_widget extends WP_Widget {
 
	function __construct() {

		add_action( 'widgets_init', 'register_listo_widget' );

		parent::__construct(
			'listo_widget', 
			__('Listolicious'), 
			array( 'description' => __( 'Add a widget for displaying a random movie from your Listolicious list', 'listolicious' ), ) 
		);
	}
	 
	/**
	 * Register widget for displaying a random movie from your list
	 *
	 * @since 1.4
	 */
	function register_listo_widget() {
	    register_widget( 'listo_widget' );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		 
		echo do_shortcode('[listolicious-widget]');
		echo $args['after_widget'];
	}
	         
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'listolicious' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	     
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
	}
}