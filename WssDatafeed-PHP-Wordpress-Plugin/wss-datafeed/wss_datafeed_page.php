<?php
require plugin_dir_path( __FILE__ )."library/WSSXMLMapping.class.php";

$wss_options_post_type = get_option('wss_options_post_type', '');
$wss_attributes = get_option('wss_options_attributes', array());
//print_r($wss_attributes);

if(!$wss_options_post_type){
	exit;
}

$data = array();

$posts_array = get_posts(array(
	'posts_per_page'   => -1,
	'post_type'        => $wss_options_post_type,
	'post_status'      => 'publish'
));

//print_r($wss_attributes);
//print_r($posts_array);

foreach($posts_array as $p)
{
	//print_r($p); exit;
	$post_id = $p->ID;

	$tmp = array();

	$tmp['product_name'] = $p->post_title;
	$tmp['picture_url'] = wp_get_attachment_url( get_post_thumbnail_id($post_id));
	$tmp['URL'] = get_permalink($post_id);

	foreach($wss_attributes as $k => $v)
	{
		if($k == 'description')
		{
			$tmp[$k] = $p->$v['key'];
		}else
		{
			if(isset($v['key']) && $v['key'])
			{
				$tmp[$k] = get_post_meta($post_id, $v['key'], true);

				if(isset($v['true_value']) && $v['true_value'])
				{
					$tmp[$k] = $tmp[$k] >= $v['true_value'] ? true : false;
				}
			}

			if(isset($v['value']) && $v['value'])
			{
				$tmp[$k] = $v['value'];
			}

			if(isset($v['taxonomy']) && $v['taxonomy'])
			{
				$terms = get_the_terms( $post_id, $v['taxonomy']);
				if($terms){
					$first_term = reset($terms);
					$tmp[$k] = $first_term->name;
				}
			}
		}
	}

	$data[$post_id] = array(
		//SKU sản phẩm
		'simple_sku' => isset($tmp['simple_sku']) ? $tmp['simple_sku'] : '',
		//SKU sản phẩm cha nếu có
		'parent_sku' => '',
		//Có sẵn hàng hay không 											
		'availability_instock' => isset($tmp['availability_instock']) ? $tmp['availability_instock'] : true,							
		//Brand name
		'brand' => isset($tmp['brand']) ? $tmp['brand'] : '',
		//Tên sản phẩm						
		'product_name' => isset($tmp['product_name']) ? $tmp['product_name'] : '',
		//Mô tả sản phẩm							
		'description' => isset($tmp['description']) ? WSSXMLMapping::aj_sub_string($tmp['description'], 250, false) : '',
		//Mệnh giá tiền sử dụng VND/USD				
		'currency' => isset($tmp['currency']) ? $tmp['currency'] : 'VND',
		//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx) 											
		'price' => isset($tmp['price']) ? WSSXMLMapping::currency($tmp['price']) : 0,
		//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)								
		'discount' => isset($tmp['price']) && isset($tmp['discounted_price']) && $tmp['price'] > $tmp['discounted_price'] ? WSSXMLMapping::currency($tmp['price'] - $tmp['discounted_price']) : 0,
		//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx) 												
		'discounted_price' => isset($tmp['discounted_price']) ? WSSXMLMapping::currency($tmp['discounted_price']) : 0,
		//Category1 cha của cha	
		'parent_of_parent_of_cat1' => isset($tmp['category_parent_parent']) ? $tmp['category_parent_parent'] : '',
		//Category1 cha	
		'parent_of_cat_1' => isset($tmp['category_parent']) ? $tmp['category_parent'] : '',
		//Category1 sản phẩm 
		'category_1' => isset($tmp['category']) ? $tmp['category'] : '',
		//Category2 cha của cha (nếu có)								
		'parent_of_parent_of_cat2' => isset($tmp['category_parent_parent2']) ? $tmp['category_parent_parent2'] : '',
		//Category2 cha (nếu có)			
		'parent_of_cat_2' => isset($tmp['category_parent2']) ? $tmp['category_parent2'] : '', 
		//Category2 sản phẩm (nếu có)						
		'category_2' => isset($tmp['category2']) ? $tmp['category2'] : '',
		//Category3 cha của cha (nếu có)											
		'parent_of_parent_of_cat3' => isset($tmp['category_parent_parent3']) ? $tmp['category_parent_parent3'] : '',
		//Category3 cha (nếu có)
		'parent_of_cat3' => isset($tmp['category_parent3']) ? $tmp['category_parent3'] : '', 
		//Category3 sản phẩm (nếu có)
		'category_3' => isset($tmp['category3']) ? $tmp['category3'] : '',
		//Ảnh sản phẩm (Ảnh đại diện)
		'picture_url' => isset($tmp['picture_url']) ? $tmp['picture_url'] : '',
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
		'promotion' => isset($tmp['promotion']) ? $tmp['promotion'] : '',
		//Thời gian giao hàng
		'delivery_period' => isset($tmp['delivery_period']) ? $tmp['delivery_period'] : '',
	);
	
	//print_r($tmp); exit;
}

WSSXMLMapping::setData($data);
WSSXMLMapping::display();