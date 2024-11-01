<?php

/*
Plugin Name:  Shorten Sub-category's Link
Plugin URI:   http://www.jamviet.com
Description:  If your website has many sub-category like Category/sub-category/sub-2-category/sub-3-category, that the time you install this plugin, all category link will be shorten like "domain.com/any-category.html", it's better for SEO
Version:      1.2
Author:       Jam Việt
Author URI:   http://www.jamviet.com
*/

class Subcategory_shorten_link {
	var $ending;
// start it !
	function __construct() {
		$this->ending = get_option('suffix_category');
		if ( get_option('suffix_category') == '')
			return;
		add_filter( 'category_rewrite_rules', array( $this, 'jamviet_category_rewrite_rules'), 99, 1 );
		add_filter( 'category_link', array( $this, 'jamviet_category_link'), 10, 2 );
		
	}

function jamviet_category_rewrite_rules( $rules ) {
	unset( $rules );
	$rules = array();
	$ending = $this->ending;
	$rules["(.+?)$ending/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$"] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
	$rules["(.+?)$ending/page/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
	$rules["(.+?)$ending/?$"] = 'index.php?category_name=$matches[1]';
	return $rules;
}


function jamviet_category_link( $link, $term_id ) {
	$data = get_category( $term_id);
	return home_url('/' . $data->slug . $this->ending);
}


} // endclass

$start_class_shortening = new Subcategory_shorten_link();

/* add option */

function jam_load_options_permalink() {

	if ( isset( $_POST['suffix_category'] ) ) {

		$suffix_category = $_POST['suffix_category'];
		jam_set_author_base( $suffix_category );
	}

	add_settings_field(
		'suffix_category',
		__( 'Suffix Category' ),
		'jam_settings_field',
		'permalink',
		'optional',
		array( 'label_for' => 'suffix_category' )
	);
}
add_action( 'load-options-permalink.php', 'jam_load_options_permalink' );

/**
 * Displays author base settings field
 *
 * @since 1.1
 */
function jam_settings_field() {
	echo '<input name="suffix_category" id="suffix_category" type="text" value="' . esc_attr( get_option( 'suffix_category' ) ) . '" class="regular-text code" placeholder=".html or .xyz is better :D">';
	echo '<p>If your main structure is "/%postname%.html", you must choose other suffix than ".html" or it will show error 404.</p>';
}

/**
 * Set the base for the author permalink
 *
 * @since 1.1
 *
 * @param string $author_base Author permalink structure base
 */
function jam_set_author_base( $suffix_category ) {
	if ( $suffix_category != '' ) {
		update_option( 'suffix_category', $suffix_category );
	} else {
		delete_option('suffix_category');
	}
}
