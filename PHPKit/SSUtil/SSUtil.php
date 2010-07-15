<?php
require_once 'api_config.php';
include('class.rss.php');

/* This is the PHP class that can be used for accessing the API 
You can use memcached etc if you want to*/
class SSUtil {
	private $key;
	private $secret;
	private $user;
	private $password;
	private $apiurl;
	private function XMLtoArray($data)
	{
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_free($parser);
		foreach ($tags as $key=>$val) {
			if(strtoupper($key) == "SLIDESHARESERVICEERROR") {
				$finarr[0]["Error"]="true";
				$finarr[0]["Message"]=$values[$tags["MESSAGE"][0]]["value"];
				return $finarr;
			}     
			if ((strtolower($key) != "slideshow") &&  (strtolower($key) != "slideshows") && (strtolower($key) != "slideshowdeleted") && (strtolower($key) != "slideshowuploaded") && (strtolower($key) != "tags")  && (strtolower($key) != "group") && (strtolower($key) != "name") && (strtolower($key) != "count") && (strtolower($key) != "user")) {
                for($i = 0;$i < count($val);$i++) {
                      $finarr[$i][$key]=$values[$val[$i]]["value"];
                }
			}
			else {
				continue;
			}
		}
	return $finarr;
	}
	private function RSStoArray($feed) {
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $feed, $values, $tags);
		xml_parser_free($parser);
		$count=1;
		foreach($tags as $key=>$val) {
			if((strtolower($key)=='title')&&(strtolower($key)=='link')&&(strtolower($key)=='pubDate')&&(strtolower($key)=='description')) {
				for($i = 1;$i < count($val);$i++) {
                      $data[$i-1][$key]=$values[$val[$i-1]]["value"];
                }
			} else if((strtolower($key)!='rss')&&(strtolower($key)!='channel')&&(strtolower($key)!='item')&&(strtolower($key)!='slideshare:meta')) {
				if(strtolower(substr($key,0,10))=="slideshare")
					$key=substr($key,11);
				for($i = 0;$i < count($val);$i++) {
                      $data[$i][$key]=$values[$val[$i]]["value"];
                }
			}
		}
		return $data;
	}
	public function SSUtil() {
		$this->key=$GLOBALS['key'];
		$this->secret=$GLOBALS['secret'];
		$this->apiurl=$GLOBALS['apiurl'];
	}
	private function get_data($call,$params) {
		$ts=time();
		$hash=sha1($this->secret.$ts);
		try {
			$res=file_get_contents($this->apiurl.$call."?api_key=$this->key&ts=$ts&hash=$hash".$params);
		} catch (Exception $e) {
		// Log the exception and return $res as blank
		}
		return utf8_encode($res);
	}
	/* Get all the slide information in a simple array */
	public function get_slideInfo($id) {
		$data=$this->XMLtoArray($this->get_data("get_slideshow","&slideshow_id=$id"));
		return $data[0];
	}
	public function count_slideUser($user) {
		$xml=new SimpleXMLElement($this->get_data("get_slideshow_by_user","&username_for=$user&offset=0&limit=1"));
		return $xml->count;
	}
	/* Get all the user's slide information  in a simple multi-dimensional array */
	public function get_slideUser($user,$offset=0,$limit=0) {
		return $this->XMLtoArray($this->get_data("get_slideshow_by_user","&username_for=$user&offset=$offset&limit=$limit"));
	}
	public function count_slideTag($tag) {
		$xml=new SimpleXMLElement($this->get_data("get_slideshow_by_tag","&tag=$tag&offset=0&limit=1"));
		return $xml->count;
	}
	/* Get all the tags's slide information  in a simple multi-dimensional array */
	public function get_slideTag($tag,$offset=0,$limit=0) {
		return $this->XMLtoArray($this->get_data("get_slideshow_by_tag","&tag=$tag&offset=$offset&limit=$limit"));
	}
	public function count_slideGroup($group) {
		$xml=new SimpleXMLElement($this->get_data("get_slideshow_from_group","&group_name=$group&offset=0&limit=1"));
		return $xml->count;
	}
	/* Get all the group's slide information  in a simple multi-dimensional array */
	public function get_slideGroup($group,$offset=0,$limit=0) {
		return $this->XMLtoArray($this->get_data("get_slideshow_from_group","&group_name=$group&offset=$offset&limit=$limit"));
	}
	/* pull any slideshare feed and retrieve that in  a multi-dimensional array */
	public function get_RSS($feed) {
		try {
			$res=file_get_contents($feed);
		} catch (Exception $e) {
		// Log the exception and return $res as blank
		}
		$feedxml=utf8_encode($res);
		return $this->RSStoArray($feedxml);
	}
	/* Generate your own slideshow RSS enter a multi-dimensional slide */
	public function make_RSS($title,$description,$date,$slides,$location='.',$filename='rss') {
		$rss = new rss('utf-8');
		$rss->channel($title, 'http://www.slideshare.net', $description);
		$rss->language('en-us');
		$rss->copyright('Copyright by SlideShare 2006');
		$rss->managingEditor('support.slideshare@gmail.com');
		$rss->startRSS($location,$filename);
		
		for($i = 0; $i < count($slides); $i++){
			$rss->itemTitle($slides[$i]['TITLE']);
			$rss->itemLink($slides[$i]['PERMALINK']);
			$rss->itemDescription(
			'<![CDATA[
				<img style="border: 1px solid rgb(195, 230, 216);" src="'.$slides[$i]['THUMBNAIL'].'" align="right" border="0" width="120" height="90" vspace="4" hspace="4" />
				<p>
				'.$slides[$i]['DESCRIPTION'].'
				</p>
			]]>'
			);
			$rss->itemGuid($slides[$i]['PERMALINK'],true);
			$rss->itemComments($slides[$i]['PERMALINK']);
			$rss->itemSource('Slideshare', 'http://www.slideshare.net');
			$rss->addItem();
		}
		$rss->RSSdone();
	}
}

?>