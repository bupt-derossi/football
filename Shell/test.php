<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/4/14
 * Time: 上午6:31
 */
include_once '/private/var/www/football/phpQuery/phpQuery/phpQuery.php';
include_once '/private/var/www/football/Config/OkoooConfig.php';
$url = "http://www.okooo.com/jingcai/";
phpQuery::newDocumentFile($url);
$week = date('w');
$weekName = '周' . OkoooConfig::$WEEK_MAP[$week];
//抓取当天的比赛
$objTz = pq('div.touzhu:eq(1)');
$var = $objTz->find('touzhu_1')->html();

var_dump($var);exit;


use Pdo\PDOModel;
include_once "/private/var/www/football/Model/PDOModel.php";
$objPdo = new Pdo\PDOModel();
$handle = $objPdo->connect();
var_dump($handle);

