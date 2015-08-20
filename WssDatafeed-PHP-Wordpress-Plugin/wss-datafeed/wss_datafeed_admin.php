<?php

/* Saving options */
if(isset($_POST['_nonce']))
{
	if(wp_verify_nonce($_POST['_nonce'], 'wss_datafeed_setting_choosing_post_type'))
	{
		if(isset($_POST['wss_post_type'])){
			$wss_post_type = $_POST['wss_post_type'];
			update_option('wss_options_post_type', $wss_post_type);
		}
	}

	if(wp_verify_nonce($_POST['_nonce'], 'wss_datafeed_setting_mapping_attributes'))
	{
		if(isset($_POST['attributes'])){
			$attributes = $_POST['attributes'];
			update_option('wss_options_attributes', $attributes);
		}
	}

	if(wp_verify_nonce($_POST['_nonce'], 'wss_datafeed_setting_create_datafeed_page'))
	{
		if(isset($_POST['datafeed_page_slug'])){
			
			$datafeed_page = wss_create_datafeed_page($_POST['datafeed_page_slug']);
		}
	}
}

/* Choosing Post Type */
$wss_all_post_types = wss_get_registered_post_types();
$wss_options_post_type = get_option('wss_options_post_type');

if($wss_options_post_type){

	/* Attributes - Meta keys */
	$wss_all_meta_keys = wss_get_post_type_meta_keys($wss_options_post_type);
	$wss_attributes = get_option('wss_options_attributes', array());

	/* Attributes - Taxonomies */
	$wss_taxonomies = wss_get_post_type_taxonomies($wss_options_post_type);
}

/* Datafeed Page */
$wss_datafeed_page = get_option('wss_datafeed_page', array(
	'title' => 'Wss Datafeed',
	'slug' => 'wss-datafeed',
	'id' => 0,
	'url' => ''
));

$wss_datafeed_page_info = get_page_by_path($wss_datafeed_page['slug']);
if($wss_datafeed_page_info)
{
	$wss_datafeed_page['url'] = get_permalink($wss_datafeed_page_info->ID);
}

?>
<div class="wrap">

	<h2>Websosanh.vn's Datafeed Options</h2>

	<h3>Choosing "Products" Post Type</h3>

	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" id="_nonce" name="_nonce" value="<?php echo wp_create_nonce('wss_datafeed_setting_choosing_post_type'); ?>">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="blogname">Choose one of these: </label></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span>Choose one of these:</span></legend>
							<?php
							foreach($wss_all_post_types as $k => $v):
							?>
							<label title="<?php echo $v; ?>">
								<input type="radio" name="wss_post_type" value="<?php echo $v; ?>" <?php echo $v == $wss_options_post_type ? 'checked' : ''; ?> />
								<span><?php echo $v; ?></span>
							</label><br>
							<?php
							endforeach;
							?>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
	</form>

	<?php if($wss_options_post_type): ?>

	<h3>Mapping Attributes to Post Meta Keys</h3>

	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" id="_nonce" name="_nonce" value="<?php echo wp_create_nonce('wss_datafeed_setting_mapping_attributes'); ?>">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="simple_sku">SKU sản phẩm <br><code>simple_sku</code></label></th>
					<td>
						<?php wss_html_select('attributes[simple_sku][key]', $wss_all_meta_keys, isset($wss_attributes['simple_sku']['key']) ? $wss_attributes['simple_sku']['key'] : ''); ?>
					</td>
				</tr>
				<!-- <tr valign="top">
					<th scope="row"><label for="blogname">SKU sản phẩm cha (parent_sku)</label></th>
					<td><input name="blogname" type="text" id="parent_sku" value="" class="regular-text"></td>
				</tr> -->
				<tr valign="top">
					<th scope="row"><label for="product_name">Tên sản phẩm <br><code>product_name</code></label></th>
					<td><p class="description">Post title</p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="product_name">Ảnh sản phẩm <br><code>picture_url</code></label></th>
					<td><p class="description">Featured image</p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="product_name">Đường dẫn sản phẩm <br><code>URL</code></label></th>
					<td><p class="description">Post Permalink</p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="description">Mô tả sản phẩm <br><code>description</code></label></th>
					<td>
						<select name="attributes[description][key]">
							<option value="">--Lựa chọn--</option>
							<option value="post_content" <?php echo isset($wss_attributes['description']['key']) && $wss_attributes['description']['key'] == 'post_content' ? 'selected' : ''; ?>>excerpt</option>
							<option value="post_content" <?php echo isset($wss_attributes['description']['key']) && $wss_attributes['description']['key'] == 'post_content' ? 'selected' : ''; ?>>content</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="currency">Tiền tệ <br><code>currency</code></label></th>
					<td>
						<?php wss_html_select('attributes[currency][key]', $wss_all_meta_keys, isset($wss_attributes['currency']['key']) ? $wss_attributes['currency']['key'] : ''); ?>
						Hoặc giá trị: <?php wss_html_select('attributes[currency][value]', array('VND'=>'VND', 'USD'=>'USD'), isset($wss_attributes['currency']['value']) ? $wss_attributes['currency']['value'] : ''); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="price">Giá sản phẩm <br><code>price</code></label></th>
					<td><?php wss_html_select('attributes[price][key]', $wss_all_meta_keys, isset($wss_attributes['price']['key']) ? $wss_attributes['price']['key'] : ''); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="discounted_price">Giá KM <br><code>discounted_price</code></label></th>
					<td><?php wss_html_select('attributes[discounted_price][key]', $wss_all_meta_keys, isset($wss_attributes['discounted_price']['key']) ? $wss_attributes['discounted_price']['key'] : ''); ?></td>
				</tr>
				<!-- <tr valign="top">
					<th scope="row"><label for="discount">Giá chênh lệch với giá KM <br>(discount)</label></th>
					<td><?php wss_html_select('attributes[discount][key]', $wss_all_meta_keys, isset($wss_attributes['discount']['key']) ? $wss_attributes['discount']['key'] : ''); ?></td>
				</tr> -->
				<tr valign="top">
					<th scope="row"><label for="promotion">Thông tin khuyến mãi <br><code>promotion</code></label></th>
					<td>
						<?php wss_html_select('attributes[promotion][key]', $wss_all_meta_keys, isset($wss_attributes['promotion']['key']) ? $wss_attributes['promotion']['key'] : ''); ?>
						Hoặc giá trị: <input name="attributes[promotion][value]" value="<?php echo isset($wss_attributes['promotion']['value']) ? $wss_attributes['promotion']['value'] : ''; ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="delivery_period">Thời gian giao hàng <br><code>delivery_period</code></label></th>
					<td>
						<?php wss_html_select('attributes[delivery_period][key]', $wss_all_meta_keys, isset($wss_attributes['delivery_period']['key']) ? $wss_attributes['delivery_period']['key'] : ''); ?>
						Hoặc giá trị: <input name="attributes[delivery_period][value]" value="<?php echo isset($wss_attributes['delivery_period']['value']) ? $wss_attributes['delivery_period']['value'] : ''; ?>">
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="blogname">Có sẵn hàng <br><code>availability_instock</code></label></th>
					<td>
						<?php wss_html_select('attributes[availability_instock][key]', $wss_all_meta_keys, isset($wss_attributes['availability_instock']['key']) ? $wss_attributes['availability_instock']['key'] : ''); ?>
						Giá trị trả về <code>true</code> (>=): <input name="attributes[availability_instock][true_value]" value="<?php echo isset($wss_attributes['availability_instock']['true_value']) ? $wss_attributes['availability_instock']['true_value'] : ''; ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="blogname">Brand name <br><code>brand</code></label></th>
					<td>
						<?php wss_html_select('attributes[brand][key]', $wss_all_meta_keys, isset($wss_attributes['brand']['key']) ? $wss_attributes['brand']['key'] : ''); ?>
						Hoặc Taxonomy: <?php wss_html_select('attributes[brand][taxonomy]', $wss_taxonomies, isset($wss_attributes['brand']['taxonomy']) ? $wss_attributes['brand']['taxonomy'] : ''); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="blogname">Category <br><code>category</code></label></label></th>
					<td>
						<?php wss_html_select('attributes[category][key]', $wss_all_meta_keys, isset($wss_attributes['category']['key']) ? $wss_attributes['category']['key'] : ''); ?>
						Hoặc Taxonomy: <?php wss_html_select('attributes[category][taxonomy]', $wss_taxonomies, isset($wss_attributes['category']['taxonomy']) ? $wss_attributes['category']['taxonomy'] : ''); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
	</form>

	<?php if(isset($wss_attributes) && $wss_attributes): ?>

	<h3>Building XML Datafeed Page</h3>

	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" id="_nonce" name="_nonce" value="<?php echo wp_create_nonce('wss_datafeed_setting_create_datafeed_page'); ?>">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="blogname">ID</label></th>
					<td><input name="datafeed_page_title" type="text" id="datafeed_page_title" value="<?php echo $wss_datafeed_page['id']; ?>" disabled class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="blogname">Title</label></th>
					<td><input name="datafeed_page_title" type="text" id="datafeed_page_title" value="<?php echo $wss_datafeed_page['title']; ?>" disabled class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="blogname">Slug</label></th>
					<td><input name="datafeed_page_slug" type="text" id="datafeed_page_slug" value="<?php echo $wss_datafeed_page['slug']; ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="blogname">URL</label></th>
					<td>
						<input name="datafeed_page_url" type="text" id="datafeed_page_url" value="<?php echo $wss_datafeed_page['url']; ?>" disabled class="regular-text">
						<p class="description">Copy this URL and send to Websosanh's datafeed supporter.</p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
		<p class="description">Plugin will create a page with that slug if no page exists.</p>
	</form>
	
	<?php endif; ?>

	<?php endif; ?>
</div>