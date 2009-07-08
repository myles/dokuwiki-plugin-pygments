<?php
	/**
	 *	@author		Myles Braithwaite <me@mylesbraithwaite.com>
	 */
	
	if (!defined('DOKU_INC')) die();
	
	class helper_plugin_pygments extends DokuWiki_Plugin {
		function getInfo() {
			return array(
				'name'		=> 'Pygments',
				'author'	=> 'Myles Braithwaite',
				'email'		=> 'me@mylesbraithwaite.com',
				'date'		=> '2009-07-07',
				'desc'		=> "Replaces DokuWiki's syntax highlight with Pygments.",
				'url'		=> 'http://github.com/myles/dokuwiki-plugin-pygments'
			);
		}
		
		function getMethods() {
			return array(
				'name'		=> 'pygmentize',
				'desc'		=> 'code highlighting',
				'params'	=> array(),
				'return'	=> array()
			)
		}
		
		function pygmentize($returns=false) {
			/*
				TODO 
			*/
			return ''
		}
	}
?>