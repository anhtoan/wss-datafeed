<?php

/* *
 * Sample code
 * @author ToanNguyen
 * @date Tue Jan 20 11:18:45 PM
 */

require "WSSDB.class.php";
require "WSSXMLMapping.class.php";

/* Retrieving products from database */
$db = new WSSDB(array(
	'host' => 'localhost',
	'port' => '3306',
	'username' => 'username_here',
	'password' => 'password_here',
	'dbName' => 'db_name_here'
));
$products = $db->fetch_table_array("
	SELECT
		product.id_product, 
		product.name, 
		product.image, 
		product.ngay_dang, 
		product.price_vnd,
		product.price,
		product.nha_sx,
		product.price_km,
		product.noi_dung,
		product.het_hang,

		catpd.id_catpd,
		catpd.name as name_catpd,
		catpd.parentid as parentid_catpd

	FROM product
	LEFT JOIN catpd ON product.id_catpd = catpd.id_catpd
	WHERE product.active = 1
", 'id_product');


$data = array();
foreach($products as $p){

	/* Smoothing the attributes */
	$post_id = $p['id_product'];
	$p['image'] = 'http://suabim.vn/upload/images/'.$p['image'];
	$p['url'] = 'http://suabim.vn/detail/'.WSSXMLMapping::remove_accents($p['name_catpd'], '-').'/'.WSSXMLMapping::remove_accents($p['name'], '-').'-'.$p['id_product'].'.html';
	$p['noi_dung'] = WSSXMLMapping::trim_space(strip_tags($p['noi_dung']));
	$p['parent'] = $db->query_first('
		SELECT id_catpd, name as name_catpd, parentid as parentid_catpd
		FROM catpd
		WHERE id_catpd = '.$p['parentid_catpd'].'
	');
	$products[$post_id] = $p;

	/* Mapping to data */
	$data[$post_id] = array(
			//SKU sản phẩm
			'simple_sku' => $p['id_product'],
			//SKU sản phẩm cha nếu có
			'parent_sku' => '',
			//Có sẵn hàng hay không 											
			'availability_instock' => $p['het_hang'] == 1 ? false : true, 								
			//Brand name
			'brand' => $p['nha_sx'],
			//Tên sản phẩm						
			'product_name' => $p['name'],
			//Mô tả sản phẩm							
			'description' => $p['noi_dung'],
			//Mệnh giá tiền sử dụng VND/USD				
			'currency' => 'VND',
			//Giá sản phẩm khi chưa khuyến mãi (format US currency -> xx,xxx,xxx.xx) 											
			'price' => WSSXMLMapping::currency($p['price']),
			//Số tiền khuyến mãi (format US currency -> xx,xxx,xxx.xx)								
			'discount' => 0,
			//Giá sau khi khuyến mãi (nếu không có khuyến mãi thì để bằng giá ban đầu, hoặc không điền (format US currency -> xx,xxx,xxx.xx) 												
			'discounted_price' => WSSXMLMapping::currency($p['price']),
			//Category1 cha của cha	
			'parent_of_parent_of_cat1' => isset($tmp['parents'][2]) && $tmp['parents'][2] ? $tmp['parents'][2]->name : '', 
			//Category1 cha	
			'parent_of_cat_1' => isset($p['parent']) ? $p['parent']['name_catpd'] : '',
			//Category1 sản phẩm 
			'category_1' => $p['name_catpd'],
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
			'picture_url' => $p['image'],//$tmp['picture_url'] ? 'http://quatructuyen.com/wp-content/themes/organic_shop/timthumb.php?src='.$tmp['picture_url'] : '', 											//Ảnh sản phẩm 1
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
}

/* Exporting XML */
WSSXMLMapping::setData($data);
WSSXMLMapping::display();