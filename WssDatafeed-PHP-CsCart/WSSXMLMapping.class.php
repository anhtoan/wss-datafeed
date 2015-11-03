<?php

/* *
 * WSSXMLMapping.class.php
 * @author ToanNguyen
 * @date Tue Jan 20 11:18:45 PM
 */

class WSSXMLMapping{

	private static $data = array();
	private static $xmlTableTag = 'Products';
	private static $xmlRowTag = 'Product';
	private static $xmlHeader = '<?xml version="1.0" encoding="UTF-8"?>';
	private static $xml = '';

	/**
	 * Set input data
	 * @param Array $data
	 * @param String $xmlTableTag
	 * @param String $xmlRowTag
	 * @return Response
	 */
	public static function setData($data = array(), $xmlTableTag = '', $xmlRowTag = ''){

		if($data){
			self::$data = $data;
		}

		if($xmlTableTag){
			self::$xmlTableTag = $xmlTableTag;
		}

		if($xmlRowTag){
			self::$xmlRowTag = $xmlRowTag;
		}

		self::buildXML();
		
		return true;
	}

	/**
	 * Display XML
	 * @return Response
	 */
	public static function display(){

		if(self::$data){

			header('Content-type: application/xml');

			echo self::$xmlHeader;
			echo self::$xml;

			return;
		}
		echo 'Data is empty.';
	}

	/**
	 * Get XML
	 * @return Response XML
	 */
	public static function getXML(){
		return self::$xmlHeader.self::$xml;
	}

	/**
	 * Force Object to Array
	 * @param Array/Object $data
	 * @return Array
	 */
	private static function objectToArray($data){

		if(is_object($data)){

			$result = array();

			foreach($data as $key => $value){
				$result[$key] = $value;
			}

			return $result;
		}

		return $data;
	}

	/**
	 * Build XML
	 * @return Bool
	 */
	private static function buildXML(){
		
		foreach(self::$data as $row){

			$row = self::objectToArray($row);

			$xml = '';
			foreach($row as $key => $value){

				if(is_bool($value)){
					$value = $value === true ? 'true' : 'false';
				}

				$xml .= self::tag($key, '<![CDATA['.$value.']]>');
			}

			self::$xml .= self::tag(self::$xmlRowTag, $xml);
		}

		self::$xml = self::tag(self::$xmlTableTag, self::$xml);

		return true;
	}

	/**
	 * Build Tag
	 * @return String
	 */
	private static function tag($tagName = '', $value = ''){
		return '<'.$tagName.'>'.$value.'</'.$tagName.'>';
	}

	/**
	 * Format Currentcy
	 * @return number
	 */
	public static function currency($number = 0, $seperator = ',', $fseperator = '.') {
	    return number_format((int) $number, 0, $fseperator, $seperator);
	}

	/**
	 * Remove accents
	 * @param  [type] $str       [description]
	 * @param  string $separator [description]
	 * @return [type]            [description]
	 */
	public static function remove_accents($str, $separator = ' ') {
	    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
	    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	    $str = preg_replace("/(đ)/", 'd', $str);
	    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
	    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
	    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	    $str = preg_replace("/(Đ)/", 'D', $str);
	    $str = str_replace(array("&quot;", ":", ".", "'", ",", ";", ")", "(", "?", "@", "%", "*", "&", "^", "!", "=", "{", "}", "\\", '"', '-', '‘', '’', '•'), " ", $str);
	    $str = trim($str);
	    $str = preg_replace('/\s+/', ' ', $str);
	    $str = stripslashes($str);
	    $str = str_replace(array(' ', '--', '|', "/", '_', "[", "]", "+"), $separator, $str);
	    $str = strtolower($str);
	    return $str;
	}

	/**
	 * [aj_sub_string description]
	 * @param  [type]  $str     [description]
	 * @param  [type]  $len     [description]
	 * @param  boolean $more    [description]
	 * @param  string  $charset [description]
	 * @return [type]           [description]
	 */
	public static function aj_sub_string($str, $len, $more = '', $trim_space = false, $charset = 'UTF-8') {
	    $str = html_entity_decode($str, ENT_QUOTES, $charset);
	    $str = strip_tags($str);
	    if (mb_strlen($str, $charset) > $len && mb_strpos($str, ' ') !== false) {
	        $arr = explode(' ', $str);
	        $str = mb_substr($str, 0, $len, $charset);
	        $arrRes = explode(' ', $str);
	        $last = $arr[count($arrRes) - 1];
	        unset($arr);
	        if (strcasecmp($arrRes[count($arrRes) - 1], $last)) {
	            unset($arrRes[count($arrRes) - 1]);
	        }
	        $str = strip_tags(implode(' ', $arrRes)).$more;
	        return $trim_space ? self::trim_space($str) : $str;
	    }
	    return $str;
	}

	/**
	 * [trim_space description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	public static function trim_space($string) {
	    $string = preg_replace('/\s+/', ' ', $string);
	    return $string;
	}
}