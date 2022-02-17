<?php

namespace Zotlabs\Module;

use App;
use Zotlabs\Web\Controller;
use Zotlabs\Render\Comanche;

class Pdledit_gui extends Controller {

	function init() {

	}

	function post() {

	}

	function get() {

		if(! local_channel()) {
			return EMPTY_STR;
		}

		$module = argv(1);

		if (!$module) {
			goaway(z_root() . '/pdledit_gui/hq');
		}

		$pdl_path = 'mod_' . $module . '.pdl';

		$pdl = get_pconfig(local_channel(), 'system', $pdl_path);

		$modified = true;

		if(!$pdl) {
			$pdl_path = theme_include($pdl_path);
			if ($pdl_path) {
				$pdl = file_get_contents($pdl_path);
				$modified = false;
			}
		}

		if(!$pdl) {
			return t('Layout not found');
		}

		$template = self::get_template($pdl);

		$template_info = self::get_template_info($template);
		if(empty($template_info['contentregion'])) {
			return t('This template does not support pdledi_gui (no content regions defined)');
		}

		App::$page['template'] = $template;

		$regions = self::get_regions($pdl);

		foreach ($regions as $k => $v) {
			$region_str = '';
			if (is_array($v)) {
				ksort($v);
				foreach ($v as $entry) {
					$region_str .= replace_macros(get_markup_template('pdledit_gui_item.tpl'), ['$entry' => $entry]);
				}
			}

			App::$layout[$k] = $region_str;
		}

		$templates = self::get_templates();
		$templates_html = replace_macros(get_markup_template('pdledit_gui_templates.tpl'), ['$templates' => $templates]);

		App::$layout['region_content'] .= replace_macros(get_markup_template('pdledit_gui.tpl'), [
			'$content_regions' => $template_info['contentregion'],
			'$page_src' => base64_encode($pdl),
			'$templates' => base64_encode($templates_html),
			'$modules' => base64_encode(self::get_modules()),
			'$module_modified' => $modified,
			'$module' => $module

		]);

	}

	function get_template($pdl) {
		$ret = 'default';
		$cnt = preg_match("/\[template\](.*?)\[\/template\]/ism", $pdl, $matches);
		if($cnt && isset($matches[1])) {
			$ret = trim($matches[1]);
		}

		return $ret;
	}

	function get_templates() {
		$ret = [];

		$files = glob('view/php/*.php');
		if($files) {
			foreach($files as $f) {
				$name = basename($f, '.php');
				$x = get_template_info($name);
				if($x['contentregion']) {
					$ret[] = $name;
				}
			}
		}
		return $ret;
	}

	function get_modules() {
		$ret = '';

		$files = glob('Zotlabs/Module/*.php');
		if($files) {
			foreach($files as $f) {
				$name = lcfirst(basename($f,'.php'));
				$x = theme_include('mod_' . $name . '.pdl');
				if($x) {
					$ret .= '<a href="pdledit_gui/' . $name . '" >' . $name . '</a><br />';
				}
			}
		}
		return $ret;
	}

	function get_regions($pdl) {
		$ret = [];
		$supported_regions = ['aside', 'content', 'right_aside'];

		$cnt = preg_match_all("/\[region=(.*?)\](.*?)\[\/region\]/ism", $pdl, $matches, PREG_SET_ORDER);
		if($cnt) {
			foreach($matches as $mtch) {
				if (!in_array($mtch[1], $supported_regions)) {
					continue;
				}

				$ret['region_' . $mtch[1]] = self::parse_region($mtch[2]);
			}
		}

		return $ret;

	}

	function parse_region($pdl) {

		$ret = [];

		$cnt = preg_match_all('/\$content\b/ism', $pdl, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
		if($cnt) {
			foreach($matches as $mtch) {
				$offset = intval($mtch[0][1]);
				$name = trim($mtch[0][0]);
				//$src = base64url_encode(preg_replace(['/\s*\[/', '/\]\s*/'], ['[', ']'], $mtch[0][0]));
				$src = base64_encode($mtch[0][0]);
				$ret[$offset] = [
					'type' => 'content',
					'name' => t('Main content'),
					'src' => $src
				];
			}
		}

		$cnt = preg_match_all("/\[menu\](.*?)\[\/menu\]/ism", $pdl, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
		if($cnt) {
			foreach($matches as $mtch) {
				$offset = intval($mtch[1][1]);
				$name = trim($mtch[1][0]);
				//$src = base64url_encode(preg_replace(['/\s*\[/', '/\]\s*/'], ['[', ']'], $mtch[0][0]));
				$src = base64_encode($mtch[0][0]);

				$ret[$offset] = [
					'type' => 'menu',
					'name' => $name,
					'src' => $src
				];
			}
		}

		// menu class e.g. [menu=horizontal]my_menu[/menu] or [menu=tabbed]my_menu[/menu]
		// allows different menu renderings to be applied

		//$cnt = preg_match_all("/\[menu=(.*?)\](.*?)\[\/menu\]/ism", $s, $matches, PREG_SET_ORDER);
		//if($cnt) {
			//foreach($matches as $mtch) {
				//$s = str_replace($mtch[0],$this->menu(trim($mtch[2]),$mtch[1]),$s);
			//}
		//}


		$cnt = preg_match_all("/\[block\](.*?)\[\/block\]/ism", $pdl, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
		if($cnt) {
			foreach($matches as $mtch) {
				$offset = intval($mtch[1][1]);
				$name = trim($mtch[1][0]);
				//$src = base64url_encode(preg_replace(['/\s*\[/', '/\]\s*/'], ['[', ']'], $mtch[0][0]));
				$src = base64_encode($mtch[0][0]);
				$ret[$offset] = [
					'type' => 'block',
					'name' => $name,
					'src' => $src
				];
			}
		}

		//$cnt = preg_match_all("/\[block=(.*?)\](.*?)\[\/block\]/ism", $s, $matches, PREG_SET_ORDER);
		//if($cnt) {
			//foreach($matches as $mtch) {
				//$s = str_replace($mtch[0],$this->block(trim($mtch[2]),trim($mtch[1])),$s);
			//}
		//}

		//$cnt = preg_match_all("/\[js\](.*?)\[\/js\]/ism", $s, $matches, PREG_SET_ORDER);
		//if($cnt) {
			//foreach($matches as $mtch) {
				//$s = str_replace($mtch[0],$this->js(trim($mtch[1])),$s);
			//}
		//}

		//$cnt = preg_match_all("/\[css\](.*?)\[\/css\]/ism", $s, $matches, PREG_SET_ORDER);
		//if($cnt) {
			//foreach($matches as $mtch) {
				//$s = str_replace($mtch[0],$this->css(trim($mtch[1])),$s);
			//}
		//}

		// need to modify this to accept parameters

		$cnt = preg_match_all("/\[widget=(.*?)\](.*?)\[\/widget\]/ism", $pdl, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

		if($cnt) {
			foreach($matches as $mtch) {
				$offset = intval($mtch[1][1]);
				$name = trim($mtch[1][0]);
				//$src = base64url_encode(preg_replace(['/\s*\[/', '/\]\s*/'], ['[', ']'], $mtch[0][0]));
				$src = base64_encode($mtch[0][0]);
				$ret[$offset] = [
					'type' => 'widget',
					'name' => $name,
					'src' => $src
				];
			}
		}

		return $ret;

	}


	/**
	 * @brief Parse template comment in search of template info.
	 *
	 * like
	 * \code
	 *   * Name: MyWidget
	 *   * Description: A widget
	 *   * Version: 1.2.3
	 *   * Author: John <profile url>
	 *   * Author: Jane <email>
	 *   * ContentRegionID: some_id
	 *   * ContentRegionID: some_other_id
	 *   *
	 *\endcode
	 * @param string $template the name of the template
	 * @return array with the information
	 */
	function get_template_info($template){
		$m = array();
		$info = array(
			'name' => $template,
			'description' => '',
			'author' => array(),
			'maintainer' => array(),
			'version' => '',
			'contentregion' => []
		);

		$checkpaths = [
			'view/php/' . $template . '.php',
		];

		$template_found = false;

		foreach ($checkpaths as $path) {
			if (is_file($path)) {
				$template_found = true;
				$f = file_get_contents($path);
				break;
			}
		}

		if(! ($template_found && $f))
			return $info;

		$f = escape_tags($f);
		$r = preg_match("|/\*.*\*/|msU", $f, $m);

		if ($r) {
			$ll = explode("\n", $m[0]);
			foreach( $ll as $l ) {
				$l = trim($l, "\t\n\r */");
				if ($l != ""){
					list($k, $v) = array_map("trim", explode(":", $l, 2));
					$k = strtolower($k);
					if (in_array($k, ['author', 'maintainer'])){
						$r = preg_match("|([^<]+)<([^>]+)>|", $v, $m);
						if ($r) {
							$info[$k][] = array('name' => $m[1], 'link' => $m[2]);
						} else {
							$info[$k][] = array('name' => $v);
						}
					}
					elseif (in_array($k, ['contentregion'])){
						$info[$k][] = $v;
					}
					else {
						$info[$k] = $v;
					}
				}
			}
		}

		return $info;
	}

}
