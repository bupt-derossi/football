<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/7/23
 * Time: 上午11:31
 * brief : 抓取澳客网
 */
include_once '/private/var/www/football/phpQuery/phpQuery/phpQuery.php';
include_once '/private/var/www/football/Config/OkoooConfig.php';
$url = "http://www.okooo.com/jingcai/";
$staticUrl = "http://www.okooo.com/soccer/match/%s/exchanges/";
use Pdo\PDOModel;
include_once "/private/var/www/football/Model/PDOModel.php";
$objPdo = new Pdo\PDOModel();
$handle = $objPdo->connect();


phpQuery::newDocumentFile($url);
$data = [];
$week = date('w');
$weekName = '周' . OkoooConfig::$WEEK_MAP[$week];
$arrGame = [];
$date = date("Y-m-d");
foreach (pq('.touzhu:odd:first > .touzhu_1') as $game) {
    $okNumber = $game->getAttribute('data-mid');
    $jcNumber = str_replace($weekName,'',iconv('gb2312','UTF-8//IGNORE',$game->getAttribute('data-ordercn')));
    $dat['jc_number'][] = $jcNumber;
    //$data['host'][] = iconv('utf-8','gb2312',iconv('utf-8','ISO-8859-1',$game->getAttribute('data-hname')));
    //$data['visit'][] = $game->getAttribute('data-aname');
    $sql = "insert into games (number,ok_number,dt) values ('{$jcNumber}','{$okNumber}','{$date}')";
    echo $sql . "\n";
    $handle->exec($sql);
    $id = $handle->lastInsertId();
    $arrGame[$jcNumber] = $id;
}
exit;
sleep(3);
foreach ($data['ok_number'] as $index => $value) {
    phpQuery::newDocumentFile($url,$value);
    foreach (pq('.noBberBottom:even:fist > tr') as $key => $item) {
        if ($key < 2) {
            continue;
        }
        $value = str_replace(["\r", " "], '', $item->nodeValue);
        $arrValue = explode("\n", $value);
        unset($arrValue[0]);
        array_pop($arrValue);

        $arrJc['game_id'] = $arrBf['game_id'] = $arrGame[$data['jc_number'][$index]];
        if ($key == 2) {
            $arrBf['vict_odds'] = $arrValue[5];
            $arrBf['bet_vict'] = $arrValue[6];
            $arrBf['profit_vict'] = $arrValue[7];
            $arrJc['vict_odds'] = $arrValue[8];
            $arrJc['bet_vict'] = $arrValue[9];
            $arrJc['profit_vict'] = $arrValue[10];
        } elseif ($key == 3) {
            $arrBf['draw_odds'] = $arrValue[5];
            $arrBf['bet_draw'] = $arrValue[6];
            $arrBf['profit_draw'] = $arrValue[7];
            $arrJc['draw_odds'] = $arrValue[8];
            $arrJc['bet_draw'] = $arrValue[9];
            $arrJc['profit_draw'] = $arrValue[10];
        } elseif ($key == 4) {
            $arrBf['fail_odds'] = $arrValue[5];
            $arrBf['bet_fail'] = $arrValue[6];
            $arrBf['profit_fail'] = $arrValue[7];
            $arrJc['fail_odds'] = $arrValue[8];
            $arrJc['bet_fail'] = $arrValue[9];
            $arrJc['profit_fail'] = $arrValue[10];
        }
    }
    $strBfSql = "insert into bf_real_time_data (" . implode(array_keys($arrBf)) . ") values ('";
    $strBfSql .= implode("','",array_values($arrBf)) . "'";
    echo $strBfSql . "\n";
    $handle->exec($strBfSql);

    $strJcSql = "insert into jc_real_time_data (" . implode(array_keys($arrJc)) . ") values ('";
    $strJcSql .= implode("','",array_values($arrJc)) . "'";
    echo $strJcSql . "\n";
    $handle->exec($strJcSql);
}