<?php
/**
 * Plugin Name: Featured Audio
 * Plugin URI: http://celloexpressions.com/plugins/featured-audio
 * Description: Add featured audio to your posts and pages, like featured images.
 * Version: 1.0
 * Author: Nick Halsey
 * Author URI: http://nick.halsey.co/
 * Tags: audio, music, podcast, media
 * License: GPL
 * Text Domain: featured-audio

=====================================================================================
Copyright (C) 2016 Nick Halsey

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WordPress; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
=====================================================================================
*/

// Load Translations
add_action( 'plugins_loaded', 'featured_audio_load_textdomain' );
function featured_audio_load_textdomain() {
	load_plugin_textdomain( 'featured-audio' );
}

// Set up post metabox for featured audio.
require( plugin_dir_path( __FILE__ ) . '/post-meta.php' );

// Load the featured audio playlist widget.
require( plugin_dir_path( __FILE__ ) . '/featured-audio-playlist-widget.php' );

// Load template for content display.
add_filter( 'the_content', 'featured_audio_template_filter' );
function featured_audio_template_filter( $the_content ) {
	$post_types = apply_filters( 'featured_audio_post_types', array( 'post', 'page' ) );
	if ( in_array( get_post_type(), $post_types ) && ! current_theme_supports( 'featured-audio' ) ) {
		$the_content = featured_audio_content_filter( $the_content );
	}
	return $the_content;
}

/**
 * Filter for `the_content()` to add featured audio.
 */
function featured_audio_content_filter( $the_content ) {
	return get_the_featured_audio() . $the_content;
}

/**
 * Filter the html attributes on the album art image.
 */
function featured_audio_filter_album_art_image_attrs( $attr ) {
	$attr['class'] .= ' alignleft featured-audio-art';
	return $attr;
}

/******************************
 * Public API functions
 ******************************/

/**
 * Display the featured audio, if it exists.
 *
 * @param $args              array   Display options.
 * @param $args['id']        int     Post id (optional). Defaults to current post id. 
 * @param $args['album_art'] boolean Whether to display the album art for the featured audio cycle. Default: false.
 * @param $args['title']     boolean Whether to display the title of the audio attachment. Default: false.
 */
function the_featured_audio( $args = array() ) {
	echo get_the_featured_audio( $args );
}

/**
 * Get the featured audio, if it exists.
 *
 * @param $args              array   Display options.
 * @param $args['id']        int     Post id (optional). Defaults to current post id. 
 * @param $args['album_art'] boolean Whether to display the album art for the featured audio cycle. Default: false.
 * @param $args['title']     boolean Whether to display the title of the audio attachment. Default: false.
 *
 * @return string The featured audio markup.
 */
 function get_the_featured_audio( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'id'        => null,
		'title'     => false,
		'album_art' => false,
	) );

	$audio_url = get_featured_audio_src( $args['id'] );
	$output = '';
	if ( $audio_url ) {
		$output = '<div class="featured-audio">';
		if ( $args['album_art'] ) {
			$thumb_id = get_post_thumbnail_id( $audio_attachment_id );
			if ( ! empty( $thumb_id ) ) {
				add_filter( 'wp_get_attachment_image_attributes', 'featured_audio_filter_album_art_image_attrs' );
				$output .= wp_get_attachment_image( $thumb_id, 'thumbnail' );
				remove_filter( 'wp_get_attachment_image_attributes', 'featured_audio_filter_album_art_image_attrs' );
			}
		}
		if ( $args['title'] ) {
			$output .= '<h3 class="featured-audio-title" style="clear: none;">' . get_the_title( $audio_attachment_id ) . '</h3>';
		}

		// Embedded audio player.
		$output .= wp_audio_shortcode( array( 'src' => $audio_url ) );
		$output .= '</div>';
	}
	return $output;
}

/**
 * Get the url of the featured audio file, if it exists.
 *
 * @param $id int Post id (optional). Defaults to current post id.
 *
 * @return string URL of the featured audio file.
 */
function get_featured_audio_src( $id ) {
	$audio_attachment_id = get_featured_audio_attachment_id( $id );
	return ( $audio_attachment_id ) ? wp_get_attachment_url( $audio_attachment_id ) : '';
}

/**
 * Get the id of the featured audio attachment, if it exists.
 *
 * @param $id int Post id (optional). Defaults to current post id.
 *
 * @return int ID of the featured audio attachment.
 */
 function get_featured_audio_attachment_id( $id = null ) {
	if ( ! absint( $id ) ) {
		$id = get_the_ID();
	}
	return absint( get_post_meta( $id, 'featured-audio', true ) );
}

/**
 * Display the featured audio playlist, if there are multiple posts with featured audio in the current query.
 */
 function the_featured_audio_playlist() {
	echo get_the_featured_audio_playlist();
}

/**
 * Get the featured audio playlist, if there are multiple posts with featured audio in the current query.
 *
 * @return string Markup of the featured audio playlist, or empty string.
 */
function get_the_featured_audio_playlist() {
	if ( have_posts() && ! is_singular() ) {
		rewind_posts(); // Reset the main query.
		$ids = array();
		while ( have_posts() ) : the_post();
			$audio_id = get_featured_audio_attachment_id();
			if ( $audio_id ) {
				$ids[] = $audio_id;
			}
		endwhile;
		rewind_posts(); // Reset the main query, in case it's used after this function.

		if ( empty( $ids ) ) {
			return '';
		} else {
			return wp_playlist_shortcode( array( 'type' => 'audio', 'ids' => $ids ) );
		}
	}
}

