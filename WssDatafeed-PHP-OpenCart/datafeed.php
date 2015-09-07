<?php

//echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require "WSSDB.class.php";
require "WSSXMLMapping.class.php";

if (file_exists('config.php')) {
	require_once('config.php');
}

if (file_exists('../config.php')) {
	require_once('../config.php');
} 

$db = new WSSDB(array(
	'host' => DB_HOSTNAME,
	'port' => '3306',
	'username' => DB_USERNAME,
	'password' => DB_PASSWORD,
	'dbName' => DB_DATABASE
));

$products = $db->fetch_table_array("
	SELECT
		".DB_PREFIX."product.product_id,
		".DB_PREFIX."product.model,
		".DB_PREFIX."product.quantity,
		".DB_PREFIX."product.stock_status_id,
		".DB_PREFIX."product.image,
		".DB_PREFIX."product.manufacturer_id,
		".DB_PREFIX."product.price,

		".DB_PREFIX."product_special.price as discounted_price,
		".DB_PREFIX."product_special.date_start,
		".DB_PREFIX."product_special.date_end,

		".DB_PREFIX."product_description.name as product_name,
		".DB_PREFIX."product_description.description,

		".DB_PREFIX."product_to_category.category_id,

		".DB_PREFIX."manufacturer.name as brand_name

	FROM ".DB_PREFIX."product

	LEFT JOIN ".DB_PREFIX."product_special ON ".DB_PREFIX."product.product_id = ".DB_PREFIX."product_special.product_id
	LEFT JOIN ".DB_PREFIX."product_description ON ".DB_PREFIX."product.product_id = ".DB_PREFIX."product_description.product_id
	LEFT JOIN ".DB_PREFIX."product_to_category ON ".DB_PREFIX."product.product_id = ".DB_PREFIX."product_to_category.product_id
	LEFT JOIN ".DB_PREFIX."manufacturer ON ".DB_PREFIX."manufacturer.manufacturer_id = ".DB_PREFIX."product.manufacturer_id

	
", 'product_id');//".DB_PREFIX."product_description.description //LIMIT 0, 10

//print_r($products); exit;

foreach($products as $p){

	$product_id = $p['product_id'];

	$p['sku'] = $p['model'];
	$p['description'] =  WSSXMLMapping::aj_sub_string($p['description'], 250, false);
	$p['availability_instock'] = $p['stock_status_id'] == 7 ? true : false;
	$p['brand'] = $p['brand_name'];

	$sql['category'] = $db->query_first("SELECT * FROM ".DB_PREFIX."category_description WHERE category_id = '".$p['category_id']."'");
	$p['category'] = $sql['category']['name'];

	$sql['category_parent_1_id'] = $db->query_first("SELECT * FROM ".DB_PREFIX."category WHERE category_id = '".$p['category_id']."'");
	$sql['category_parent_1'] = $db->query_first("SELECT * FROM ".DB_PREFIX."category_description WHERE category_id = '".$sql['category_parent_1_id']['parent_id']."'");
	$p['category_parent_1'] = $sql['category_parent_1']['name'];

	$p['price_currency'] = WSSXMLMapping::currency((int)$p['price']);

	if ($p['discounted_price'] > 0) {
		$date_now = date("Y-m-d");
		if (($p['date_start'] == 0 && $p['date_end'] == 0) || ($p['date_start'] < $date_now && $date_now < $p['date_end']) || ($p['date_start'] < $date_now && $p['date_end'] == 0) || ($p['date_start'] == 0 && $date_now < $p['date_end'])) {
			$p['discounted_price_currency'] = WSSXMLMapping::currency((int)$p['discounted_price']);
		} else {
			$p['discounted_price_currency'] = $p['price'];
		}
		// if ($p['date_start'] > $date_now || $p['date_end'] < $date_now) {
		// 	$p['discounted_price'] = $p['price'];
		// } else {
		// 	$p['discounted_price'] = WSSXMLMapping::currency((int)$p['discounted_price']);
		// }
		$p['discount_currency'] = WSSXMLMapping::currency((int)$p['price'] - (int)$p['discounted_price']);
	} else {
		$p['discounted_price_currency'] = WSSXMLMapping::currency((int)$p['price']);
		$p['discount_currency'] = 0;
	}

	$p['picture_url'] = HTTP_SERVER.'image/'.$p['image'];

	$tmp = $db->query_first("SELECT * FROM ".DB_PREFIX."url_alias WHERE query = 'product_id=".$product_id."'");

	if (empty($tmp['keyword'])) {
		$p['url'] = HTTP_SERVER.'index.php?route=product/product&product_id='.$p['product_id'];
	} else {
		$p['url'] = HTTP_SERVER.$tmp['keyword'];
	}

	$data[$product_id] = array(
		//SKU sản phẩm
		'simple_sku' => $p['sku'],
		//SKU sản phẩm cha nếu có
		'parent_sku' => '',
		//Có sẵn hàng hay không 											
		'availability_instock' => $p['availability_instock'], 								
		//Brand name
		'brand' => $p['brand_name'],
		//Tên sản phẩm						
		'product_name' => $p['product_name'],
		//Mô tả sản phẩm							
		'description' => $p['description'],
		//Mệnh giá tiền sử dụng VND/USD				
		'currency' => 'VND',
		//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx) 											
		'price' => $p['price_currency'],
		//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)								
		'discount' => $p['discount_currency'],
		//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx) 												
		'discounted_price' => $p['discounted_price_currency'],
		//Category1 cha của cha	
		'parent_of_parent_of_cat1' => '', 
		//Category1 cha	
		'parent_of_cat_1' => $p['category_parent_1'],
		//Category1 sản phẩm 
		'category_1' => $p['category'],
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
		'picture_url' => $p['picture_url'],
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

	$products[$product_id] = $p;
}

/* Exporting XML */
WSSXMLMapping::setData($data);
WSSXMLMapping::display();