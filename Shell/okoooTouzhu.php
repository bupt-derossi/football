<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/7/23
 * Time: 下午1:50
 */
include_once '/private/var/www/football/phpQuery/phpQuery/phpQuery.php';
include_once '/private/var/www/football/Config/OkoooConfig.php';
error_reporting(E_ALL & ~E_NOTICE);
$url = "http://www.okooo.com/soccer/match/932875/exchanges/";
phpQuery::newDocumentFile($url);

use Pdo\PDOModel;
include_once "/private/var/www/football/Model/PDOModel.php";
$objPdo = new Pdo\PDOModel();
$handle = $objPdo->connect();
$staticUrl = "http://www.okooo.com/soccer/match/%s/exchanges/";
$dt = date('Y-m-d');
$sql = "select * from games where dt='{$dt}'";
$arrGame = [];
foreach ($handle->query($sql) as $row) {
    $arrGame[$row['ok_number']] = $row['id'];
}

foreach ($arrGame as $okNumber => $gameId) {
    $url = sprintf($staticUrl,$okNumber);
    phpQuery::newDocumentFile($url);
    foreach (pq('.noBberBottom:even:fist > tr') as $key => $item) {
        if ($key < 2) {
            continue;
        }
        $value = str_replace(["\r", " "], '', $item->nodeValue);
        $arrValue = explode("\n", $value);
        unset($arrValue[0]);
        array_pop($arrValue);

        $arrJc['game_id'] = $arrBf['game_id'] = $gameId;
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
    if ($arrBf) {
        $strBfSql = "insert into bf_real_time_data (" . implode(",",array_keys($arrBf)) . ") values ('";
        $strBfSql .= implode("','", array_values($arrBf)) . "')";
        echo $strBfSql . "\n";
        $handle->exec($strBfSql);
    }

    if ($arrJc) {
        $strJcSql = "insert into jc_real_time_data (" . implode(",",array_keys($arrJc)) . ") values ('";
        $strJcSql .= implode("','", array_values($arrJc)) . "')";
        echo $strJcSql . "\n";
        $handle->exec($strJcSql);
    }
} 