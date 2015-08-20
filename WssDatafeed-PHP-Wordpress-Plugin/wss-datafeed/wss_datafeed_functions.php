<?php

/**
 * Get all registered post types
 * @return [type] [description]
 */
function wss_get_registered_post_types() {
    global $wp_post_types;
    return array_keys( $wp_post_types );
}

function wss_get_post_type_meta_keys($post_type = 'post'){

	$post = get_posts(array(
		'posts_per_page'   => 1,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_type'        => $post_type,
		'post_status'      => 'publish'
	));

	if($post){
		$p = reset($post);
		$post_metas = get_post_meta($p->ID);
		$results = array();
		foreach($post_metas as $pmk => $pm){
			$results[$pmk] = $pmk;
		}
		return $results;
	}

	return array();
}

function wss_get_post_type_taxonomies($post_type = 'post'){
	$taxs = get_object_taxonomies($post_type);
	$results = array();
	foreach($taxs as $tax){
		$results[$tax] = $tax;
	}
	return $results;
}

function wss_html_select($name, $options = array(), $current = ''){
	?>
	<select name="<?php echo $name; ?>">
		<option value="">--Lựa chọn--</option>
		<?php foreach($options as $k => $v): ?>
			<option value="<?php echo $k; ?>" <?php echo $current == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}

function wss_create_datafeed_page($slug='wss-datafeed'){
	global $wpdb;

    $the_page_title = 'Wss Datafeed';
    $the_page_slug = $slug;

    $the_page = get_page_by_path($the_page_slug);

    if ( ! $the_page )
    {
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "Websosanh.vn's Datafeed Page.";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';

        $the_page_id = wp_insert_post( $_p );
    }
    else
    {
        $the_page_id = $the_page->ID;
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
    }

    if($the_page_id > 0){
	    update_option('wss_datafeed_page', array(
	    	'title' => $the_page_title,
	    	'slug' => $slug,
	    	'id' => $the_page_id
	    ));
    }

    return get_page_by_path($slug);
}