<?php

/* *
 * sample.php
 * @author ToanNguyen
 * @date Tue Jan 20 11:18:45 PM
 */

/*
 * Hard code sample data to test
 */

$sampleArray = array(
	array(
		'simple_sku' => 'Tủ Nhựa Duy Tân',
		'parent_sku' => '',
		'availability_instock' => true,
		'brand' => 'Duy Tân',
		'URL' => 'http://alobuy.vn/san-pham/tu-nhua-duy-tan-tabi-3-tang-4-ngan-3768.html?utm_source=Partner'
	),
	array(
		'simple_sku' => 'Máy hút khói và khử mùi Sanko',
		'parent_sku' => '',
		'availability_instock' => false,
		'brand' => 'Sanko',
		'URL' => 'http://alobuy.vn/san-pham/may-hut-khoi-va-khu-mui-sanko-spn-3502c-3769.html?utm_source=Partner'
	),
);


/*
 * Sample code to use the library
 */

require "DB/Db.class.php";
require "WSSXMLMapping.class.php";

$db = new Db(array(
	'dbname' => '',
	'host' => '',
	'user' => '',
	'password' => ''
));

$products = $db->query("
	SELECT 
		toc_products.*,
		toc_products_description.products_name,
		toc_products_description.products_short_description,
		toc_manufacturers.manufacturers_name
	FROM toc_products
	LEFT JOIN toc_products_description
		ON toc_products.products_id = toc_products_description.products_id
	LEFT JOIN toc_manufacturers
		ON toc_products.manufacturers_id = toc_manufacturers.manufacturers_id
");

$data = array();

foreach($products as $p){
	$data[] = array(
		'simple_sku' => $p['products_sku'], 							//SKU sản phẩm
		'parent_sku' => '', 											//SKU sản phẩm cha nếu có
		'availability_instock' => true, 								//Có sẵn hàng hay không
		'brand' => $p['manufacturers_name'], 							//Brand name
		'product_name' => $p['products_name'], 							//Tên sản phẩm
		'description' => $p['products_short_description'], 					//Mô tả sản phẩm
		'currency' => 'VND', 											//Mệnh giá tiền sử dụng VND/USD
		'price' => WSSXMLMapping::currency($p['products_price']),								//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx)
		'discount' => '', 												//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)
		'discounted_price' => '', 										//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx)
		'parent_of_parent_of_cat1' => '', 								//Category1 cha của cha
		'parent_of_cat_1' => '', 										//Category1 cha
		'category_1' => '', 											//Category1 sản phẩm
		'parent_of_parent_of_cat2' => '', 								//Category2 cha của cha (nếu có)
		'parent_of_cat_2' => '', 										//Category2 cha (nếu có)
		'category_2' => '', 											//Category2 sản phẩm (nếu có)
		'parent_of_parent_of_cat3' => '', 								//
		'parent_of_cat3' => '', 										//
		'category_3' => '', 											//
		'picture_url' => '', 											//Ảnh sản phẩm 1
		'picture_url2' => '', 											//Ảnh sản phẩm 2
		'picture_url3' => '', 											//Ảnh sản phẩm 3
		'picture_url4' => '', 											//Ảnh sản phẩm 4
		'picture_url5' => '', 											//Ảnh sản phẩm 5
		'URL' => 'http://sieuthithanhcong.com/products.php?'.$p['products_id'],//Url
		'delivery_period' => '' 
	);
}

//print_r($products);




WSSXMLMapping::setData($data);

WSSXMLMapping::display();