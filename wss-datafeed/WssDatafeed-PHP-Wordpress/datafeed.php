<?php
/*
Template Name: Wss Datafeed
*/
require get_template_directory()."/wss/WSSXMLMapping.class.php";

$data = array();

$posts_array = get_posts(array(
	'posts_per_page'   => -1,
	'post_type'        => 'product',
	'post_status'      => 'publish'
));


$tax = get_taxonomies();

foreach($posts_array as $p){
	$post_id = $p->ID;

	$tmp = array();
	$tmp['product_name'] = $p->post_title;
	$tmp['simple_sku'] = get_post_meta( $post_id, 'Model', true);
	$tmp['price'] = get_post_meta( $post_id, 'regular_price', true);
	$tmp['discounted_price'] = $tmp['price'];//get_post_meta( $post_id, 'sale_price', true);
	$tmp['discount'] = 0;//$tmp['discounted_price'] ? $tmp['price'] - $tmp['discounted_price'] : '';
	$tmp['URL'] = get_permalink($post_id);
	$tmp['picture_url'] = wp_get_attachment_url( get_post_thumbnail_id($post_id));
	$tmp['product_status'] = get_post_meta( $post_id, 'stock_status', true) == 'instock' ? true : true;
	$tmp['description'] = WSSXMLMapping::aj_sub_string(strip_tags($p->post_content), 300, '', true);

	$terms = get_the_terms( $post_id, 'product_cat');

	if($terms){
		$tmp['parents'][0] = $terms[0];

		if($tmp['parents'][0]->parent > 0){

			$tmp['parents'][1] = get_term($tmp['parents'][0]->parent, 'product_cat');

			if($tmp['parents'][1] && $tmp['parents'][1]->parent > 0){

				$tmp['parents'][2] = get_term($tmp['parents'][1]->parent, 'product_cat');

			}
		}
	}

	$data[] = array(
		'simple_sku' => $tmp['simple_sku'], //SKU sản phẩm
		'parent_sku' => '', 											//SKU sản phẩm cha nếu có
		'availability_instock' => $tmp['product_status'] == 1 ? true : false, 								//Có sẵn hàng hay không
		'brand' => '', 							//Brand name
		'product_name' => $tmp['product_name'], 							//Tên sản phẩm
		'description' => $tmp['description'], 					//Mô tả sản phẩm
		'currency' => 'VND', 											//Mệnh giá tiền sử dụng VND/USD
		'price' => WSSXMLMapping::currency($tmp['price']),								//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx)
		'discount' => WSSXMLMapping::currency($tmp['discount']), 												//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)
		'discounted_price' => WSSXMLMapping::currency($tmp['discounted_price']),									//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx)
		'parent_of_parent_of_cat1' => isset($tmp['parents'][2]) && $tmp['parents'][2] ? $tmp['parents'][2]->name : '', 								//Category1 cha của cha
		'parent_of_cat_1' => isset($tmp['parents'][1]) && $tmp['parents'][1] ? $tmp['parents'][1]->name : '', 										//Category1 cha
		'category_1' => isset($tmp['parents'][0]) && $tmp['parents'][0] ? $tmp['parents'][0]->name : '', 											//Category1 sản phẩm
		'parent_of_parent_of_cat2' => '', 								//Category2 cha của cha (nếu có)
		'parent_of_cat_2' => '', 										//Category2 cha (nếu có)
		'category_2' => '', 											//Category2 sản phẩm (nếu có)
		'parent_of_parent_of_cat3' => '', 								//
		'parent_of_cat3' => '', 										//
		'category_3' => '', 											//
		'picture_url' => $tmp['picture_url'], 											//Ảnh sản phẩm 1
		'picture_url2' => '', 											//Ảnh sản phẩm 2
		'picture_url3' => '', 											//Ảnh sản phẩm 3
		'picture_url4' => '', 											//Ảnh sản phẩm 4
		'picture_url5' => '', 											//Ảnh sản phẩm 5
		'URL' => $tmp['URL'],//Url
		'delivery_period' => '' 
	);
}

print_r($data);

WSSXMLMapping::setData($data);
WSSXMLMapping::display(); exit();