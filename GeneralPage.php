<?php

/**
 * 通用分页工具
 * @author qingyu8@staff.weibo.com
 * @date 2017/8/17
 */
class GeneralPage {
	public static $_url = [];
	public static $_query = [];
	/**
	 * 配置项
	 * @var array
	 */
	public static $_config = [
		//显示当前页的前后n页
		'contextNum' => 4,

		'method' => 'GET',

		//分页跳转的参数名
		'pageVar' => 'page',

		//a标签href,否则禁止跳转
		'withHref' => true,

		//当前页 a标签 css类
		'activeClassA' => '',

		//是否以li包裹a标签
		'withLi' => true,

		//当前页 li标签 css类
		'activeClassLi' => 'active',

		//withLi为false时，可于此设置a标签外围
		'itemTheme' => '%item%',

		'showFirstLast' => true,
		'textFirst' => '首页',
		'textLast' => '最后一页',

		'showPrevNext' => true,
		'textPrev' => '上一页',
		'textNext' => '下一页',
	];

	public static function initConfig(array $config = []) {
		foreach ($config as $k => $v) {
			(!is_null($v) && isset(self::$_config[$k])) && self::$_config[$k] = $v;
		}
	}

	/**
	 * @param int $totalRows 总的记录数
	 * @param int $pageSize 每页显示记录数
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
