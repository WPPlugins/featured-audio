=== Featured Audio ===
Contributors: celloexpressions
Tags: audio, music, podcast, media
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.0
License: GPLv2

Add featured audio to your posts and pages, like featured images.

== Description ==
WordPress supports featured images out of the box, allowing images to represent posts in various ways defined by the theme. This plugin adds similar support for audio, allowing musicians, podcasters, and anyone who publishes audio with WordPress to feature audio files on posts and pages in a structured way. Each post and page gets a featured audio metabox where an audio file can be uploaded or selected from the media library.

By default, featured audio is displayed at the top of posts and pages `(within the_content)`. Developers can change this by adding theme support for `featured-audio`, via several API functions listed below.

Why use featured audio instead of embeding audio directly into posts? Featured audio organizes the content in a structured way, alowing infinite possibilities to customize the way users experience audio content on your site. The plugin ships with one example of this - the featured audio playlist widget. Add this widget to your sidebar and it'll automatically display a playlist of all of the audio files featured on posts shown on the current view, on views with more than one post such as the main blog page or a category page.

== Developer API Functions ==
= Add Theme Support =

`add_theme_support( 'featured-audio' )`

Adding theme support for featured audio tells the plugin not to add the featured audio to the content automatically. Instead, you can add featured audio exactly where you want it with `the_featured_content()` (see below for details).

= Change Supported Post Types =
By default, the `post` and `page` post types are supported. You can use the `featured_audio_post_types` filter to modify this list. For example:

`add_filter( 'featured_audio_post_types', 'prefix_featured_audio_post_types' );
function prefix_featured_audio_post_types( $post_types ) {
	// Add support to the sheet_music post type.
	$post_types[] = 'sheet_music';

	// Overwrite the entire list to remove support on pages.
	$post_types = array( 'post' );

	return $post_types;
}`

= `the_featured_audio( $args )` =
Display the featured audio, if it exists.

Parameters:
`$args              array   Display options.
$args['id']        int     Post id (optional). Defaults to current post id. 
$args['album_art'] boolean Whether to display the album art for the featured audio cycle. Default: false.
$args['title']     boolean Whether to display the title of the audio attachment. Default: false.`

= `get_the_featured_audio( $args )` =
Get the featured audio, if it exists, as a string. Has the same arguments as `the_featured_audio()`.

= `get_featured_audio_src( $id )` =
Returns the url of the featured audio file, if it exists.

Parameter:
`$id int Post id (optional). Defaults to current post id.`

= `get_featured_audio_attachment_id( $id )` =
Returns the id of the featured audio attachment, if it exists.

Parameter:
`$id int Post id (optional). Defaults to current post id.`

= `get_the_featured_audio_playlist()` =
Get the featured audio playlist, if there are multiple posts with featured audio in the current query. Used by the featured audio playlist widget.

= `the_featured_audio_playlist()` =
Displays (echoes) `get_the_featured_audio_playlist()`.


== Installation ==
1. Take the easy route and install through the WordPress plugin adder OR
1. Download the .zip file and upload the unzipped folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add featured audio to your posts and pages, and optionally add the featured audio playlist widget to a sidebar.
1. Developers can add support for additional post types and customize the display of featured audio in themes.


== Frequently Asked Questions ==
= How do I change where featured audio is displayed? =
See the "Developer API Functions" section for information on how to change where featured audio is displayed in your theme's code.

= How does the playlist widget work? =
The featued audio playlist widget pulls in the featured audio associated with all of the posts displayed on the current view (auch as the blog index, a taxonomy archive, or an author archive). It won't display on single post or page views or on archive views where none of the posts have featured audio selected.

== Screenshots ==
1. Featured audio metabox on the post edit screen in the admin.
2. Default featured audio dislpay with the Twenty Fifteen theme.
3. Custom featured audio display using `the_featured_audio( array( 'title' => true, 'album_art' => true ) );` in a theme.
4. Featured Audio Playlist Widget display with the Twenty Fifteen theme.
5. Featured Audio Playlist Widget display in the customizer.

== Changelog ==
= 1.0 =
* Initial public release.

== Upgrade Notice ==
= 1.0 =
* Initial public release.
