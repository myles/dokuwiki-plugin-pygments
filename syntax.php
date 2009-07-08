<?php
	/**
	 *	Pygments: replaces DokuWiki's syntax highliting with pygments.
	 *	@author		Myles Braithwaite <me@mylesbraithwaite.com>
	 */
	
	function pygments_highlight_code($code, $lexer) {
		
		if (!preg_match('/^[a-zA-Z_+]+$/', $lexer)) {
			return null;
		}
		
		$pipes = null;
		$process = proc_open(
			$this->getConf('pygmentize_path') .
			'-l' .
			$lexer .
			' -fhtml -Ostyle=' .
			$this->getConf('pygmentize_styles'), array(
				array('pipe', 'r'),
				array('pipe', 'w'),
				array('pipe', 'w')
			),
			$pipes
		);
		
		fwrite($pipes[0], $code);
		fclose($pipes[0]);
		
		$highlighted = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		if (trim($errors))
			return NULL;
		
		proc_close($process);
		return $highlighted;
	}
	
	if(!defined('DOKU_INC')) die();
	
	if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
	require_once(DOKU_PLUGIN . 'syntax.php');
	
	class syntax_plugin_pygments extends DokuWiki_Syntax_Plugin {
		
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
		
		function getType() {
			return 'protected';
		}
		
		function getPType() {
			return 'block';
		}
		
		function getSort() {
			return 194;
		}
		
		function connectTo($mode) {
			$this->Lexer->addEntryPattern('<syntax(?=[^\r\n]*?>.*?</syntax>)', $mode, 'plugin_pygments');
		}
		
		function postConnect() {
			$this->Lexer->addExitPattern('</syntax>', 'plugin_pygments');
		}
		
		function handle($match, $state, $pos, &$handler) {
			switch ($state) {
				case DOKU_LEXER_ENTER:
					$this->syntax = substr($match, 1);
					return false;
				
				case DOKU_LEXER_UNMATCHED:
					list($attr, $content) = preg_split('/>/u', $match, 2);
					list($lang, $title) = preg_split('/\|/u', $attr, 2);
				
					if ($this->syntax == 'pre') {
						$lang = trim($lang);
						if (!$lang) $lang = 'txt';
					} else {
						$lang = 'txt';
					}
					
					return array($this->syntax, $lang, trim($title), $content);
			}
			return false;
		}
		
		function render($mode, &$renderer, $data) {
			if (count($data) == 4) {
				list($syntax, $lang, $title, $content) = $data;
				
				if ($mode == 'xhtml') {
					$renderer->doc .= '<pre class="syntax">';
					$renderer->doc .= pygments_highlight_code($content, $lang);
					$renderer->doc .= '</pre>';
				} else {
					pygments_highlight_code($content, $lang);
				}
				
				return true;
			}
			return false;
		}
	}
?>