<?php
include( dirname( __FILE__ ) . '/wp-load.php' );

require get_template_directory()."/wss/WSSXMLMapping.class.php";

/**TEST**/
if(isset($_GET['test']) && $_GET['test'] == 'true'){

	$data = array();

	$posts_array = get_posts(array(
		'posts_per_page'   => -1,
		'post_type'        => 'product',
		'post_status'      => 'publish'
	));

	foreach($posts_array as $p){
		$post_id = $p->ID;

		$tmp = array();
		$tmp['post_id'] = $post_id;
		$tmp['price'] = get_post_meta( $post_id, 'regular_price', true);
		$tmp['URL'] = get_permalink($post_id);

		if(!$tmp['price']){
			//$meta = get_post_meta($post_id);
			//print_r($tmp);
			//print_r($meta);
			//http://quatructuyen.com/wp-admin/post.php?post=34335&action=edit&message=1
			echo $tmp['post_id'].'.'.$tmp['URL'].'<br />';
		}

		if($post_id == 20209){
			print_r(get_post_meta( $post_id, 'stock_status', true));
		}

	}
	exit;
}
/**END-TEST**/

$data = array();

$posts_array = get_posts(array(
	'posts_per_page'   => -1,
	'post_type'        => 'product',
	'post_status'      => 'publish'
));

foreach($posts_array as $p)
{
	$post_id = $p->ID;

	$tmp = array();
	$tmp['product_name'] = $p->post_title;
	$tmp['simple_sku'] = get_post_meta( $post_id, 'Model', true);
	$tmp['price'] = get_post_meta( $post_id, 'regular_price', true);
	$tmp['discounted_price'] = $tmp['price'];//get_post_meta( $post_id, 'sale_price', true);
	$tmp['discount'] = 0;//$tmp['discounted_price'] ? $tmp['price'] - $tmp['discounted_price'] : '';
	$tmp['URL'] = get_permalink($post_id);
	$tmp['product_status'] = get_post_meta( $post_id, 'stock_status', true);
	$tmp['product_status'] = $tmp['product_status'] == 'instock' ? true : false;
	$tmp['description'] = WSSXMLMapping::aj_sub_string(strip_tags($p->post_content), 300, '', true);

	/* Thumb */
	$tmp['picture_url'] = wp_get_attachment_url( get_post_thumbnail_id($post_id));

	/* Images */
	$attachments = get_attached_media('image/jpeg', $post_id);
	if($attachments)
	{
		if(!$tmp['picture_url'])
		{
			$first_attachment = reset($attachments);
			$tmp['picture_url'] = $first_attachment->guid;
		}
		$stt_picture = 0;
		foreach($attachments as $att)
		{
			if($att->guid != $tmp['picture_url']){
				$tmp['picture_url'.($stt_picture+2)] = $att->guid;
				$stt_picture++;
			}
		}
	}
	
	/* Categories */
	$terms = get_the_terms( $post_id, 'product_cat');
	if($terms)
	{
		$tmp['parents'][0] = $terms[0];
		if($tmp['parents'][0]->parent > 0)
		{
			$tmp['parents'][1] = get_term($tmp['parents'][0]->parent, 'product_cat');
			if($tmp['parents'][1] && $tmp['parents'][1]->parent > 0)
			{
				$tmp['parents'][2] = get_term($tmp['parents'][1]->parent, 'product_cat');
			}
		}
	}

	/* Mapping data */
	$data[$post_id] = array(
		//SKU sản phẩm
		'simple_sku' => $tmp['simple_sku'],
		//SKU sản phẩm cha nếu có
		'parent_sku' => '',
		//Có sẵn hàng hay không 											
		'availability_instock' => $tmp['product_status'], 								
		//Brand name
		'brand' => '',
		//Tên sản phẩm						
		'product_name' => $tmp['product_name'],
		//Mô tả sản phẩm							
		'description' => $tmp['description'],
		//Mệnh giá tiền sử dụng VND/USD				
		'currency' => 'VND',
		//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx) 											
		'price' => WSSXMLMapping::currency($tmp['price']),
		//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)								
		'discount' => WSSXMLMapping::currency($tmp['discount']),
		//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx) 												
		'discounted_price' => $tmp['discounted_price'] ? WSSXMLMapping::currency($tmp['discounted_price']) : null,
		//Category1 cha của cha	
		'parent_of_parent_of_cat1' => isset($tmp['parents'][2]) && $tmp['parents'][2] ? $tmp['parents'][2]->name : '', 
		//Category1 cha	
		'parent_of_cat_1' => isset($tmp['parents'][1]) && $tmp['parents'][1] ? $tmp['parents'][1]->name : '',
		//Category1 sản phẩm 
		'category_1' => isset($tmp['parents'][0]) && $tmp['parents'][0] ? $tmp['parents'][0]->name : '',
		//Category2 cha của cha (nếu có)								
		'parent_of_parent_of_cat2' => '',
		//Category2 cha (nếu có)			
		'parent_of_cat_2' => '', 
		//Category2 sản phẩm (nếu có)						
		'category_2' => '',
		//Category3 cha của cha (nếu có)											
		'parent_of_parent_of_cat3' => '',
		//Category3 cha (nếu có)
		'parent_of_cat3' => '', 
		//Category3 sản phẩm (nếu có)
		'category_3' => '',
		//Ảnh sản phẩm (Ảnh đại diện)
		'picture_url' => isset($tmp['picture_url2']) ? $tmp['picture_url2'] : '',//$tmp['picture_url'] ? 'http://quatructuyen.com/wp-content/themes/organic_shop/timthumb.php?src='.$tmp['picture_url'] : '', 											//Ảnh sản phẩm 1
		//Ảnh sản phẩm 2
		'picture_url2' => isset($tmp['picture_url2']) ? $tmp['picture_url2'] : '', 	
		//Ảnh sản phẩm 3
		'picture_url3' => isset($tmp['picture_url3']) ? $tmp['picture_url3'] : '', 
		//Ảnh sản phẩm 4
		'picture_url4' => isset($tmp['picture_url4']) ? $tmp['picture_url4'] : '', 
		//Ảnh sản phẩm 5											
		'picture_url5' => isset($tmp['picture_url5']) ? $tmp['picture_url5'] : '', 
		//Đường dẫn đến bài viết sản phẩm											
		'URL' => $tmp['URL'],
		//Thông tin khuyến mãi
		'promotion' => '',
		//Thời gian giao hàng
		'delivery_period' => '' 
	);
}
WSSXMLMapping::setData($data);
WSSXMLMapping::display(); exit();