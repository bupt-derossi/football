<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/12/11
 * Time: 下午2:22
 * brief: 网易彩票数据抓取
 */
include_once '/private/var/www/football/phpQuery/phpQuery/phpQuery.php';
include_once '/private/var/www/football/Config/Caipiao163Config.php';
$url = Caipiao163Config::$URL;
use Pdo\PDOModel;
include_once "/private/var/www/football/Model/PDOModel.php";
$objPdo = new Pdo\PDOModel();
$handle = $objPdo->connect();
$beginTime = date('Ymd H:i:s',strtotime(date('Ymd')));
$endTime = date('Ymd H:i:s',strtotime(date('Ymd') . "+1 day"));


phpQuery::newDocumentFile($url);
$date = date('Ymd');
//按年分表,先查询,不存在则创建
$oddsTable = "odds_" . date('Y');
$descSql = "desc {$oddsTable}";
$check = $handle->query($descSql);
if ($check === false) {
    $createTableSql = "create table {$oddsTable} LIKE odds_2017";
    $handle->query($createTableSql);
}
//比赛是否结束的标志,结束前是数字,结束后是汉字
$overFlag = false;
foreach (pq(".gameSelect dl[gamedate={$date}] dd") as $index => $dd) {
    foreach (pq($dd)->find('span') as $key => $span) {
        if ($key == 0) {
            $arr['number'] = pq($span)->find('i')->text();
        }
        if ($key == 1) {
            $arr['type'] = pq($span)->find('a')->text();
        }
        if ($key == 2) {
            $arr['date'] = $date . ' ' . pq($span)->find('i')->text();
        }
        if ($key == 3) {
            if (pq($span)->find('a')->attr('class') == 'c_ffca6e') {
                $overFlag = true;
            } else {
                $overFlag = false;
            }
            $arr['host'] = trim(pq($span)->find('em:first')->text());
            $arr['guest'] = trim(pq($span)->find('em:last')->text());
        }
        if ($key == 4) {
            foreach (pq($span)->find('div') as $spanKey => $span5Div) {
                foreach (pq($span5Div)->find('em') as $emKey => $em) {
                    if ($spanKey == 0) {
                        if ($overFlag) {
                            if (pq($em)->find('strong')->attr('class') == 'c_f6c15a') {
                                $arr['result'] = Caipiao163Config::$RESULT_MAPPING[pq($em)->text()];
                            }
                        } else {
                            if ($emKey == 1) {
                                $arr['jc_s'] = pq($em)->text();
                            }
                            if ($emKey == 2) {
                                $arr['jc_p'] = pq($em)->text();
                            }
                            if ($emKey == 3) {
                                $arr['jc_f'] = pq($em)->text();
                            }
                        }
                    } elseif ($spanKey == 1) {
                        if ($emKey == 0) {
                            $arr['rq'] = pq($em)->text();
                        }
                        if ($overFlag) {
                            if (pq($em)->find('strong')->attr('class') == 'c_f6c15a') {
                                $arr['r_result'] = Caipiao163Config::$RESULT_MAPPING[pq($em)->text()];
                            }
                        } else {
                            if ($emKey == 1) {
                                $arr['jc_rs'] = pq($em)->text();
                            }
                            if ($emKey == 2) {
                                $arr['jc_rp'] = pq($em)->text();
                            }
                            if ($emKey == 3) {
                                $arr['jc_rf'] = pq($em)->text();
                            }
                        }
                    }
                }
            }
        }
        if ($key == 6) {
            $oddsUrl = pq($span)->find('a:last')->attr('href');
            $ch = curl_init($oddsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $arrRes = (curl_getinfo($ch));
            $oddsUrl = $arrRes['url'];
        }
    }

    if ($overFlag) {
        $sql = "update football_games set `result`={$arr['result']},`r_result`={$arr['r_result']}";
        $sql .= " where date between '{$beginTime}' and '{$endTime}' and number='{$arr['number']}'";
        //$handle->query($sql);
    } else {
        //先检索是否已经写入到football_games表中
        $checkSql = "select count(*) total from football_games where date between '{$beginTime}' and '{$endTime}' and number='{$arr['number']}'";
        $objRes = $handle->query($checkSql);
        $checkResult = $objRes->fetchAll(PDO::FETCH_ASSOC);
        if ($checkResult[0]['total'] > 0) {

        } else {
            $sql = "insert into football_games (`date`,`number`,`type`,`host`,`guest`,`rq`) VALUES ";
            $sql .= " ('{$arr['date']}','{$arr['number']}','{$arr['type']}','{$arr['host']}','{$arr['guest']}',{$arr['rq']})";
            $handle->query($sql);
            $id = $handle->lastInsertId();
            $arrOddsUrl[$id] = $oddsUrl;
        }

        $oddsSql = "insert into {$oddsTable} (`game_id`,`jc_s`,`jc_p`,`jc_f`,`jc_rs`,`jc_rp`,`jc_rf`) VALUES ";
        $oddsSql .= " ({$id},{$arr['jc_s']},{$arr['jc_p']},{$arr['jc_f']},{$arr['jc_rs']},{$arr['jc_rp']},{$arr['jc_rf']})";
        $handle->query($oddsSql);
    }

}
