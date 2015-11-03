<?php

define('BOOTSTRAP', 'abc');

require "../config.local.php";


/* *
 * Sample code
 * @author ToanNguyen
 * @date Tue Jan 20 11:18:45 PM
 */

require "WSSDB.class.php";
require "WSSXMLMapping.class.php";

/* Retrieving products from database */
$db = new WSSDB(array(
	'host' => $config['db_host'],
	'port' => '3306',
	'username' => $config['db_user'],
	'password' => $config['db_password'],
	'dbName' => $config['db_name']
));

$count = $db->query_first("SELECT COUNT(*) FROM ".$config['table_prefix']."products WHERE status = 'A'");
$total_page = (int)($count['COUNT(*)'] / 100) + 1;
$page_param = 'page';

if (!isset($_GET[$page_param])) {
	$result = array(
		'feed_url' => $protocol.$_SERVER['SERVER_NAME'].'/wss/datafeed.php',
		'total_page' => $total_page,
		'page_param' => $page_param
	);

	echo json_encode($result); exit;
}
if ($_GET[$page_param] == 0) {
	echo 'Data is empty.'; exit;
}

$page = $_GET[$page_param] ? $_GET[$page_param] : 1;
$limit = 100;
$start = ($page-1)*$limit;

$products = $db->fetch_table_array("
	SELECT
		".$config['table_prefix']."products.product_id,
		".$config['table_prefix']."products.product_code,
		".$config['table_prefix']."products.amount,
		".$config['table_prefix']."products.status,

		".$config['table_prefix']."product_descriptions.product as name,
		".$config['table_prefix']."product_descriptions.short_description,

		".$config['table_prefix']."product_prices.price,
		".$config['table_prefix']."product_prices.percentage_discount,

		".$config['table_prefix']."products_categories.category_id,

		".$config['table_prefix']."seo_names.name as slug

	FROM ".$config['table_prefix']."products 

	LEFT JOIN ".$config['table_prefix']."product_descriptions ON ".$config['table_prefix']."products.product_id = ".$config['table_prefix']."product_descriptions.product_id
	LEFT JOIN ".$config['table_prefix']."product_prices ON ".$config['table_prefix']."products.product_id = ".$config['table_prefix']."product_prices.product_id
	LEFT JOIN ".$config['table_prefix']."products_categories ON ".$config['table_prefix']."products.product_id = ".$config['table_prefix']."products_categories.product_id
	LEFT JOIN ".$config['table_prefix']."seo_names ON ".$config['table_prefix']."products.product_id = ".$config['table_prefix']."seo_names.object_id
	WHERE ".$config['table_prefix']."products.status = 'A'
	LIMIT ".$start.", ".$limit."
", 'product_id');

$site_url = "http://phunu24h.com.vn";

$data = array();
foreach($products as $p){

	/* Smoothing the attributes */
	$post_id = $p['product_id'];
	$p['description'] =  WSSXMLMapping::aj_sub_string($p['short_description'], 250, true);
	$p['url'] = $site_url.'/'.$p['slug'].'.html';

	// Lấy tên của CAT
	$sql_category = $db->query_first("
		SELECT ".$config['table_prefix']."categories.*, ".$config['table_prefix']."category_descriptions.*  FROM ".$config['table_prefix']."category_descriptions 
		LEFT JOIN 
			".$config['table_prefix']."categories ON ".$config['table_prefix']."categories.category_id=".$config['table_prefix']."category_descriptions.category_id
		WHERE ".$config['table_prefix']."categories.category_id = '".$p['category_id']."' 
	");

	if(isset($sql_category['status']) && $sql_category['status'] == 'D'){
		continue;
	}

	// Lấy parent_id của CAT (id của CAT_1)
	$sql_parent_of_cat_1_id = $db->query_first("SELECT * FROM ".$config['table_prefix']."categories WHERE category_id = '".$p['category_id']."'");

	// Lấy tên của CAT_1
	$sql_parent_of_cat_1 = $db->query_first("SELECT * FROM ".$config['table_prefix']."category_descriptions WHERE category_id = '".$sql_parent_of_cat_1_id['parent_id']."'");

	// Lấy parent_id của CAT_1 (id của CAT_2)
	$sql_parent_of_parent_of_cat1_id = $db->query_first("SELECT * FROM ".$config['table_prefix']."categories WHERE category_id = '".$sql_parent_of_cat_1_id['parent_id']."'");

	// Lấy tên của CAT_2
	$sql_parent_of_parent_of_cat1 = $db->query_first("SELECT * FROM ".$config['table_prefix']."category_descriptions WHERE category_id = '".$sql_parent_of_parent_of_cat1_id['parent_id']."'");

	$sql_image = $db->fetch_table_array("
		SELECT 
			".$config['table_prefix']."images_links . * , 
			".$config['table_prefix']."images . * 
		FROM  ".$config['table_prefix']."images_links 
		LEFT JOIN ".$config['table_prefix']."images ON ".$config['table_prefix']."images.image_id = ".$config['table_prefix']."images_links.detailed_id
		WHERE ".$config['table_prefix']."images_links.object_id = '".$p['product_id']."'
		AND ".$config['table_prefix']."images_links.object_type LIKE 'product' AND ".$config['table_prefix']."images_links.type LIKE 'M'", 'object_id'
	);
	
	if(isset($sql_image[$p['product_id']])){
		//print_r($sql_image[$p['product_id']]); exit;
		$p['image'] = $site_url.'/images/detailed/'.substr($sql_image[$p['product_id']]['image_id'], 0, 1).'/'.$sql_image[$p['product_id']]['image_path'];//preg_replace('/jpg/', 'gif', $sql_image[$p['product_id']]['image_path']);
		//http://streetstyle.vn/images/thumbnails/400/300/detailed/1/Gi__y_Nike_Sb_Hi_533d6c4061072.png
	}else{
		$p['image'] = '';
	}
	
	/* Mapping to data */
	$data[$post_id] = array(
			//SKU sản phẩm
			'simple_sku' => $p['product_code'],
			//SKU sản phẩm cha nếu có
			'parent_sku' => $post_id,
			//Có sẵn hàng hay không 											
			'availability_instock' => $p['amount'] >= 1 ? true : false, 								
			//Brand name
			'brand' => '',
			//Tên sản phẩm						
			'product_name' => $p['name'],
			//Mô tả sản phẩm							
			'description' => $p['description'],
			//Mệnh giá tiền sử dụng VND/USD				
			'currency' => 'VND',
			//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx) 											
			'price' => WSSXMLMapping::currency($p['price']),
			//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)								
			'discount' => $p['percentage_discount'] > 0 ? WSSXMLMapping::currency($p['price'] - $p['percentage_discount']) : 0,
			//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx) 												
			'discounted_price' => $p['percentage_discount'] > 0 ? WSSXMLMapping::currency($p['percentage_discount']) : WSSXMLMapping::currency($p['price']),
			//Category1 cha của cha	
			'parent_of_parent_of_cat1' => !empty($sql_parent_of_parent_of_cat1['category']) ? $sql_parent_of_parent_of_cat1['category'] : '', 
			//Category1 cha	
			'parent_of_cat_1' => !empty($sql_parent_of_cat_1['category']) ? $sql_parent_of_cat_1['category'] : '',
			//Category1 sản phẩm 
			'category_1' => $sql_category['category'],
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
			'picture_url' => $p['image'], 											//Ảnh sản phẩm 1
			//Ảnh sản phẩm 2
			'picture_url2' => isset($tmp['picture_url2']) ? $tmp['picture_url2'] : '', 	
			//Ảnh sản phẩm 3
			'picture_url3' => isset($tmp['picture_url3']) ? $tmp['picture_url3'] : '', 
			//Ảnh sản phẩm 4
			'picture_url4' => isset($tmp['picture_url4']) ? $tmp['picture_url4'] : '', 
			//Ảnh sản phẩm 5											
			'picture_url5' => isset($tmp['picture_url5']) ? $tmp['picture_url5'] : '', 
			//Đường dẫn đến bài viết sản phẩm											
			'URL' => $p['url'],
			//Thông tin khuyến mãi
			'promotion' => '',
			//Thời gian giao hàng
			'delivery_period' => '' 
		);
		//print_r($data); exit;
}

/* Exporting XML */
WSSXMLMapping::setData($data);
WSSXMLMapping::display();