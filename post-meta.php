<?php
/**
 * Add post meta box for featured audio.
 */

// Add the Piece Files Meta box, for the sheet music post type.
function featured_audio_add_meta_box() {
	$post_types = apply_filters( 'featured_audio_post_types', array( 'post', 'page' ) );
	add_meta_box( 'featured_audio', __( 'Featured Audio', 'featured-audio' ), 'featured_audio_meta_box', $post_types, 'side', 'low' );
}
add_action( 'add_meta_boxes', 'featured_audio_add_meta_box' );

// Enqueue scripts & styles.
function featured_audio_admin_scripts() {
    global $post_type, $post;
	$post_types = apply_filters( 'featured_audio_post_types', array( 'post', 'page' ) );
	if ( in_array( $post_type, $post_types ) ) {
		// Enqueue admin JS.
		wp_enqueue_script( 'featured-audio-admin', plugins_url( '/featured-audio-admin.js', __FILE__), '', '', true );

		// Load data into JS, including translated strings.
		$stored_meta = get_post_meta( $post->ID );
		if ( isset ( $stored_meta['featured-audio'] ) && 0 != absint( $stored_meta['featured-audio'] ) ) {
			$audio_attachment = wp_prepare_attachment_for_js( absint( $stored_meta['featured-audio'] ) );
		} else {
			$audio_attachment = false;
		}
		wp_localize_script( 'featured-audio-admin', 'featuredAudioOptions', array(
			'audioAttachment' => $audio_attachment,
			'l10n' => array(
				'featuredAudio' => __( 'Featured Audio', 'featured-audio' ),
				'select' => __( 'Select', 'featured-audio' ),
				'change' => __( 'Change', 'featured-audio' ),
			),
		) );
	}
}
add_action( 'admin_print_scripts-post-new.php', 'featured_audio_admin_scripts' );
add_action( 'admin_print_scripts-post.php', 'featured_audio_admin_scripts' );

// Callback that renders the contents of the Featured Audio meta box.
function featured_audio_meta_box( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'featured_audio_nonce' );
	$stored_meta = get_post_meta( $post->ID );
	if ( isset ( $stored_meta['featured-audio'] ) && 0 !== absint( $stored_meta['featured-audio'][0] ) ) {
		$audio_attachment_id = absint( $stored_meta['featured-audio'][0] );
		$audio = get_post( $audio_attachment_id );
		$audio_attachment_title = $audio->post_title;
		$audio_attachment = wp_prepare_attachment_for_js( $audio_attachment_id );
	} else {
		$audio_attachment_id = '';
		$audio_attachment_title = '';
		$audio_attachment = false;
	}
	?>
	<div id="featured-audio" class="piece-attachment">
		<script type="text/javascript">var initialAudioAttachment = <?php echo wp_json_encode( $audio_attachment ); ?></script>
		<p><strong id="audio-attachment-title"><?php echo $audio_attachment_title; ?></strong></p>
		<div id="audio-preview-container" style="margin-top: -.5em; margin-bottom: 1em;"></div>
		<button type="button" class="button button-secondary" id="featured-audio-upload"><?php _e( 'Select', 'featured-audio' ); ?></button>
		<button type="button" class="button-link" style="margin: .4em 0 0 .5em; display: none;" id="featured-audio-remove"><?php _e( 'Remove', 'featured-audio' ); ?></button>
		<input type="hidden" name="featured-audio" id="audio-attachment-id" value="<?php echo $audio_attachment_id; ?>" />
	</div>
	<?php
}

/**
 * Save the custom fields on post save.
 */
function featured_audio_post_meta_save( $post_id ) {
	// Bail if this isn't a valid time to save post meta.
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'featured_audio_nonce' ] ) && wp_verify_nonce( $_POST[ 'featured_audio_nonce' ], basename( __FILE__ ) ) ) ? true : false;
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	// Sanitize and save post meta.
	if ( isset( $_POST[ 'featured-audio' ] ) ) {
		update_post_meta( $post_id, 'featured-audio', absint( $_POST[ 'featured-audio' ] ) );
	}
}
add_action( 'save_post', 'featured_audio_post_meta_save' );
