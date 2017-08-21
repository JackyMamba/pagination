<?php

/**
 * General Pagination Tool
 * @author qingyu8@staff.weibo.com
 * @date 2017/8/17
 */
class GeneralPage {
	public static $_url = [];
	public static $_query = [];
	/**
	 * configurations
	 * @var array
	 */
	public static $_config = [
		//display current page's context pages. (imagine grep -C
		'contextNum' => 4,

		'method' => 'GET',

		//pagination param name
		'pageVar' => 'page',

		//Tag a'attribute href, whether jump or not
		'withHref' => true,

		//current page's tag a's CSS class
		'activeClassA' => '',

		//whether wrap a with li
		'withLi' => true,

		//current page's tag li's CSS class
		'activeClassLi' => 'active',

		//when withLi is false, this comes in handy
		'itemTheme' => '%item%',

		'showFirstLast' => true,
		'textFirst' => 'Home Page',
		'textLast' => 'Last Page',

		'showPrevNext' => true,
		'textPrev' => 'Next',
		'textNext' => 'Prev',
	];

	public static function initConfig(array $config = []) {
		foreach ($config as $k => $v) {
			(!is_null($v) && isset(self::$_config[$k])) && self::$_config[$k] = $v;
		}
	}

	/**
	 * @param int $totalRows Total reconds
	 * @param int $pageSize number of reconds displayed per page
	 * @param array $config
	 * @return string
	 */
	public static function show($totalRows, $pageSize = 0, $config = []) {
		if (empty($totalRows) || !is_numeric($totalRows)) {
			return '';
		}
		self::initConfig($config);

		$totalPages = ceil($totalRows / $pageSize); //总页数
		if (self::$_config['method'] == 'GET') {
			$curPage = $_GET[self::$_config['pageVar']];
		} else {
			$curPage = $_POST[self::$_config['pageVar']];
		}
		empty($curPage) && $curPage = 1;

		$startPage = max(1, $curPage - self::$_config['contextNum']);
		$endPage = min($curPage + self::$_config['contextNum'], $totalPages);

		self::$_url = parse_url($_SERVER['REQUEST_URI']);
		parse_str(self::$_url['query'], self::$_query);

		$pageStr = '';

		if ($curPage != $startPage) {
			if (self::$_config['showFirstLast']) {
				$item = '<a data-page="1" href="' . self::getScheme(1) . '">' . self::$_config['textFirst'] . '</a>';
				self::$_config['withLi'] && $item = '<li>' . $item . '</li>';
				$pageStr .= str_replace('%item%', $item, self::$_config['itemTheme']);
			}

			if (self::$_config['showPrevNext']) {
				$item = '<a data-page="' . ($curPage - 1) . '" href="' . self::getScheme($curPage - 1) . '">' . self::$_config['textPrev'] . '</a>';
				self::$_config['withLi'] && $item = '<li>' . $item . '</li>';
				$pageStr .= str_replace('%item%', $item, self::$_config['itemTheme']);
			}
		}

		for ($i = $startPage; $i <= $endPage; $i++) {
			$item = '<a data-page="' . $i . '" href="' . self::getScheme($i) . '" class="' . ($curPage == $i ? self::$_config['activeClassA'] : '') . '">' . $i . '</a>';
			self::$_config['withLi'] && $item = '<li class="' . ($curPage == $i ? self::$_config['activeClassLi'] : '') . '">' . $item . '</li>';
			$pageStr .= str_replace('%item%', $item, self::$_config['itemTheme']);
		}

		if ($curPage != $endPage) {
			if (self::$_config['showPrevNext']) {
				$item = '<a data-page="' . ($curPage + 1) . '" href="' . self::getScheme($curPage + 1) . '">' . self::$_config['textNext'] . '</a>';
				self::$_config['withLi'] && $item = '<li>' . $item . '</li>';
				$pageStr .= str_replace('%item%', $item, self::$_config['itemTheme']);
			}

			if (self::$_config['showFirstLast']) {
				$item = '<a data-page="' . $totalPages . '" href="' . self::getScheme($totalPages) . '">' . self::$_config['textLast'] . '</a>';
				self::$_config['withLi'] && $item = '<li>' . $item . '</li>';
				$pageStr .= str_replace('%item%', $item, self::$_config['itemTheme']);
			}
		}

		return $pageStr;
	}

	public static function getScheme($page) {
		if (self::$_config['withHref']) {
			$tmp_query = http_build_query(array_merge(self::$_query, [self::$_config['pageVar'] => $page]));
			return self::$_url['path'] . '?' . $tmp_query;
		} else {
			return 'javascript:void(0);';
		}
	}
}
