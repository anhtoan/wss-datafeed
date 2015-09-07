# wss-datafeed

Sample code and library for installing websosanh's datafeed for PHP websites.

The original library was written by [@ToanNguyen](https://twitter.com/anhtoandev).

#Setup the path
Native PHP
----------
 - The datafeed path will be like: `http://example.com/wss/datafeed.php`.
 - Or something like: `http://example.com/wss-datafeed`.
 - In that executable PHP file we will do the next steps below.

Wordpress
---------
 - Path from root: `http://example.com/wss/datafeed.php`. (Need to include `wp_load.php`)
 - Or using page template: `http://example.com/wss-datafeed`.

#Connect to DB & retrieve products from DB
 - Using your own library.
 - Or using wss' library `WSSDB.class.php`.
 
#Building XML file
 - Using wss' library `WSSXMLMapping.class.php`.

#Wordpress Plugin

 - Download and copy to `/wp-content/plugins/`.
 - Fill information step by step in Settings page.
