<?php

/**
* 1st,get data list from Database
* assume we have a $_GET param named p, means current page
* define page size $k
* get total number $n of data list from Database
*
*/
$n = DB::total_count();
$k = 10;
$curPage = $_GET['p'];

// here we got data & pagination
$datalist = DB::get_data($curPage, $k);
$pagination = GeneralPage::show($n, $k, $curPage, ['pageVar' => 'p']);
