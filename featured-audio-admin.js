/**
 * Sheet music library admin script. Primarily handles file upload UI.
 */

var featured_audio = {};
( function( $ ) {
	featured_audio = {
		container: '',
		frame: '',
		settings: featuredAudioOptions || {},

		init: function() {
			featured_audio.container = $( '#featured-audio' );
			featured_audio.initFrame();

			// Bind events, with delegation to facilitate re-rendering.
			featured_audio.container.on( 'click', '#featured-audio-upload', featured_audio.openAudioFrame );
			featured_audio.container.on( 'click', '#featured-audio-remove', featured_audio.removeAudio );
			featured_audio.initAudioPreview();
		},

		/**
		 * Open the featured audio media modal.
		 */
		openAudioFrame: function( event ) {
			if ( ! featured_audio.frame ) {
				featured_audio.initFrame();
			}
			featured_audio.frame.open();
		},

		/**
		 * Create a media modal select frame, and store it so the instance can be reused when needed.
		 */
		initFrame: function() {
			featured_audio.frame = wp.media({
				button: {
					text: featured_audio.settings.l10n.select
				},
				states: [
					new wp.media.controller.Library({
						title:     featured_audio.settings.l10n.featuredAudio,
						library:   wp.media.query({ type: 'audio' }),
						multiple:  false,
						date:      false
					})
				]
			});

			// When a file is selected, run a callback.
			featured_audio.frame.on( 'select', featured_audio.selectAudio );
		},

		/**
		 * Callback handler for when an attachment is selected in the media modal.
		 * Gets the selected attachment information, and sets it within the control.
		 */
		selectAudio: function() {
			// Get the attachment from the modal frame.
			var attachment = featured_audio.frame.state().get( 'selection' ).first().toJSON();
			$( '#audio-attachment-id' ).val( attachment.id );
			$( '#audio-attachment-title' ).text( attachment.title );
			featured_audio.audioEmbed( attachment );
		},

		/**
		 * Embed the audio player preview.
		 */
		audioEmbed: function( attachment ) {
			wp.ajax.send( 'parse-embed', {
				data : {
					post_ID: wp.media.view.settings.post.id,
					shortcode: '[audio src="' + attachment.url + '"][/audio]'
				}
			} ).done( function( response ) {
				var html = ( response && response.body ) || '';
				$( '#audio-preview-container' ).html( html );
				$( '#featured-audio-remove' ).show();
				$( '#featured-audio-upload' ).text( featured_audio.settings.l10n.change );
			} );
		},

		/**
		 * Remove the selected audio.
		 */
		removeAudio: function() {
			$( '#audio-attachment-id' ).val( 0 );
			$( '#audio-attachment-title' ).text( '' );
			$( '#audio-preview-container' ).html( '' );
			$( '#featured-audio-upload' ).text( featured_audio.settings.l10n.select );
			$( '#featured-audio-remove' ).hide();
		},

		/**
		 * Initialize featured audio preview.
		 */
		initAudioPreview: function() {
			var attachment = initialAudioAttachment;
			if ( attachment ) {
				featured_audio.audioEmbed( attachment );
				$( '#featured-audio-upload' ).text( featured_audio.settings.l10n.change );
				$( '#featured-audio-remove' ).show();
			}
		}
	}

	$(document).ready( function() { featured_audio.init(); } );

} )( jQuery );