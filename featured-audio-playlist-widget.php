<?php
/**
 * Widget that displays a playlist of audio featured on the current page for non-singular pages.
*/

// Register 'Featured Audio Playlist' widget.
function featured_audio_widget_init() {
	return register_widget( 'Featured_Audio_Playlist_Widget' );
}
add_action( 'widgets_init', 'featured_audio_widget_init' );

class Featured_Audio_Playlist_Widget extends WP_Widget {
	/* Constructor */
	function __construct() {
		parent::__construct( 'Featured_Audio_Playlist_Widget', __( 'Featured Audio Playlist', 'featured-audio' ), array(
			'description' => __( 'A playlist of audio featured on the current page.', 'featured-audio' ),
			'customize_selective_refresh' => false,
		) );
	}

	/* This is the Widget */
	function widget( $args, $instance ) {
		global $post;
		extract( $args );

		$playlist = get_the_featured_audio_playlist();
		if ( '' === $playlist ) {
			return;
		}

		if ( ! array_key_exists( 'title', $instance ) ) {
			$instance['title'] = '';
		}

		// Widget options
		$title = apply_filters( 'widget_title', $instance['title'] ); // Title

		// Output
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo $playlist;

		echo $after_widget;
	}

	/* Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/* Widget settings */
	function form( $instance ) {
	    if ( $instance ) {
			$title = $instance['title'];
	    } else {
			$title = '';
	    }

		// The widget form. ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title:', 'featured-audio' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
		</p>
		<p><?php _e( 'Note: this widget will only be displayed on pages with multiple posts that have featured audio, such as the main blog page or a category view.', 'featured-audio' ); ?></p>
	<?php
	}
}
