<?php

//echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require "WSSDB.class.php";
require "WSSXMLMapping.class.php";

// Database 
/*$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);*/

if (file_exists('../config.php')) {
	require_once('../config.php');
}  

// VirtualQMOD
require_once('../vqmod/vqmod.php');
VQMod::bootup();

$db = new WSSDB(array(
	'host' => DB_HOSTNAME,
	'port' => '3306',
	'username' => DB_USERNAME,
	'password' => DB_PASSWORD,
	'dbName' => DB_DATABASE
));

$products = $db->fetch_table_array("
	SELECT
		oc_product.product_id,
		oc_product.model,
		oc_product.quantity,
		oc_product.stock_status_id,
		oc_product.image,
		oc_product.manufacturer_id,
		oc_product.price,

		oc_product_description.name as product_name,
		oc_product_description.description,

		oc_manufacturer.name as brand_name

		
	FROM oc_product

	LEFT JOIN oc_product_description ON oc_product.product_id = oc_product_description.product_id
	LEFT JOIN oc_manufacturer ON oc_manufacturer.manufacturer_id = oc_product.manufacturer_id

	
", 'product_id');//oc_product_description.description //LIMIT 0, 10



foreach($products as $p){

	$product_id = $p['product_id'];

	$p['sku'] = $p['model'];
	//$p['url'] = 'http://ghettre.com/'.WSSXMLMapping::remove_accents($p['product_name'], '-');
	$p['description'] =  WSSXMLMapping::aj_sub_string($p['description'], 250, false);
	$p['availability_instock'] = $p['stock_status_id'] == 7 ? true : false;
	$p['brand'] = $p['brand_name'];
	$p['category'] = $p['brand_name'];
	$p['price'] = WSSXMLMapping::currency($p['price']);
	$p['discount'] = 0;
	$p['discounted_price'] = $p['price'];
	$p['picture_url'] = 'http://ghettre.com/image/'.$p['image'];

	$tmp = $db->query_first("
		SELECT
			*

		FROM oc_url_alias
		WHERE query = 'product_id=".$product_id."'
	");

	$p['url'] = 'http://ghettre.com/'.$tmp['keyword'];

	/*$p['categories'] = $db->fetch_table_array("
		SELECT
			oc_category.category_id,
			oc_category_description.name,
			oc_category.parent_id

		FROM oc_product_to_category

		INNER JOIN oc_category ON (oc_product_to_category.category_id = oc_category.category_id AND oc_category.parent_id > 0)
		LEFT JOIN oc_category_description ON oc_category_description.category_id = oc_category.category_id

		WHERE oc_product_to_category.product_id = ".$product_id."

	", 'category_id');*/
	
	//print_r($p); exit;
	
	/*
	if($p['categories']){
		foreach($p['categories'] as $p_cat){
			$p['category_child'] = $db->fetch_table_array("
				SELECT
					oc_category.category_id,
					oc_category_description.name,
					oc_category.parent_id

				FROM oc_category
				LEFT JOIN oc_category_description ON oc_category_description.category_id = oc_category.category_id

				WHERE oc_category.parent_id = ".$p_cat['category_id']."

			", 'category_id');
		}
	}*/

	

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
		'price' => $p['price'],
		//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)								
		'discount' => $p['discount'],
		//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx) 												
		'discounted_price' => $p['discounted_price'],
		//Category1 cha của cha	
		'parent_of_parent_of_cat1' => '', 
		//Category1 cha	
		'parent_of_cat_1' => '',
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
		'picture_url' => $p['picture_url'],//$tmp['picture_url'] ? 'http://quatructuyen.com/wp-content/themes/organic_shop/timthumb.php?src='.$tmp['picture_url'] : '', 											//Ảnh sản phẩm 1
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

