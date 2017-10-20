<?php

namespace Filter;

class HttpXSSFilter {

	private $allowHttpValue = false;

	private $input = null;

	private $normalPattern = array(
		'\'' => '&apos;',
		'"' => '&quot;',
		'&' => '&amp;',
		'<' => '&lt;',
		'>' => '&gt;',
		//possible SQL injection remove from string with there is no '
		'SELECT * FROM' => '',
		'SELECT(' => '',
		'SLEEP(' => '',
		'AND (' => '',
		' AND' => '',
		'(CASE' => ''
	);

	private $pregPattern = array(
		// Fix &entity\n
		'!(&#0+[0-9]+)!' => '$1;',
		'/(&#*\w+)[\x00-\x20]+;/u' => '$1;>',
		'/(&#x*[0-9A-F]+);*/iu' => '$1;',
		//any attribute starting with "on" or xml name space
		'#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu' => '$1>',
		//javascript: and VB script: protocols
		'#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu' => '$1=$2nojavascript...',
		'#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu' => '$1=$2novbscript...',
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		'#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u' => '$1=$2nomozbinding...',
		'#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i' => '$1>',
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		'#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i' => '$1>',
		'#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu' => '$1>',
		// namespace elements
		'#</*\w+:\w[^>]*+>#i' => '',
		//unwanted tags
		'#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i' => '',
		// Remove any attribute starting with "on" or xmlns
		'#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu' => '$1>'        
	);


	public function filter($input) {
		$this->input = is_array($input) ? $input : html_entity_decode(trim($input), ENT_NOQUOTES, 'UTF-8');
		$this->replaceProcess();
		$this->grepCharacter();
		return $this->input;
	}

	public function allowHttp() {
		$this->allowHttpValue = true;
		return $this;
	}

	public function disAllowHttp() {
		$this->allowHttpValue = false;
		return $this;
	}

	public function removeParameters($url) {
		return preg_replace('/\?.*/', '', $url);
	}

	private function replaceProcess() {
		$this->input = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $this->input);
		
		if ($this->allowHttpValue === false){
			$this->input = str_replace(array('&', '%', 'script', 'http', 'localhost'), array('', '', '', '', ''), $this->input);
		} else {
			$this->input = str_replace(array('&', '%', 'script', 'localhost', '../'), array('', '', '', '', ''), $this->input);
		}
		
		foreach($this->normalPattern as $pattern => $replacement){
			$this->input = str_replace($pattern, $replacement, $this->input);
		}
	}

	private function grepCharacter() {
		foreach ($this->pregPattern as $pattern => $replacement) {
			$this->input = preg_replace($pattern, $replacement, $this->input);
		}
	}

}
