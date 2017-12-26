<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/12/11
 * Time: 下午2:22
 * brief: 网易彩票数据抓取
 */
include_once __DIR__ . '/../phpQuery/phpQuery/phpQuery.php';
include_once __DIR__ . '/../Config/Caipiao163Config.php';
$url = Caipiao163Config::$URL;
use Pdo\PDOModel;
include_once __DIR__ . "/../Model/PDOModel.php";
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
    $overTime = (pq($dd)->attr('endtime') / 1000);
    if (time() > $overTime) {
        $overFlag = true;
    }
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
            $arr['host'] = trim(preg_replace('/\[\d+\]/',pq($span)->find('em:first')->text(),''));
            $arr['guest'] = trim(preg_replace('/\[\d+\]',pq($span)->find('em:last')->text(),''));
        }
        if ($key == 4) {
            foreach (pq($span)->find('div') as $spanKey => $span5Div) {
                foreach (pq($span5Div)->find('em') as $emKey => $em) {
                    if ($spanKey == 0) {
                        //如果比赛结束,更新结果
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
            $content = curl_exec($ch);
            $arrRes = (curl_getinfo($ch));
            $oddsUrl = str_replace('sjfx', 'oydz', $arrRes['redirect_url']);
        }
    }

    //如果比赛结束,更新结果
    if ($overFlag && $arr['result']) {
        $sql = "update games set `result`={$arr['result']},`r_result`={$arr['r_result']}";
        $sql .= " where date between '{$beginTime}' and '{$endTime}' and number='{$arr['number']}'";
        $handle->query($sql);
    } else {
        //先检索是否已经写入到games表中
        $checkSql = "select id from games where date between '{$beginTime}' and '{$endTime}' and number='{$arr['number']}'";
        $objRes = $handle->query($checkSql);
        $checkResult = $objRes->fetchAll(PDO::FETCH_ASSOC);
        if ($checkResult[0]['id'] > 0) {
            $sql = "update {$oddsTable} set jc_s={$arr['jc_s']},jc_p={$arr['jc_p']},jc_f={$arr['jc_f']},jc_rs={$arr['jc_rs']},jc_rp={$arr['jc_rp']},jc_rf={$arr['jc_rf']}";
            $sql .= " where id={$checkResult[0]['id']}";
            $handle->query($sql);
            $arrOddsUrl[$checkResult[0]['id']] = $oddsUrl;
        } else {
            $sql = "insert into games (`date`,`number`,`type`,`host`,`guest`,`rq`) VALUES ";
            $sql .= " ('{$arr['date']}','{$arr['number']}','{$arr['type']}','{$arr['host']}','{$arr['guest']}',{$arr['rq']})";
            $handle->query($sql);
            $id = $handle->lastInsertId();
            $arrOddsUrl[$id] = $oddsUrl;
            $arrOver[$id] = $overFlag;
        }

        $oddsSql = "insert into {$oddsTable} (`game_id`,`jc_s`,`jc_p`,`jc_f`,`jc_rs`,`jc_rp`,`jc_rf`) VALUES ";
        $oddsSql .= " ({$id},{$arr['jc_s']},{$arr['jc_p']},{$arr['jc_f']},{$arr['jc_rs']},{$arr['jc_rp']},{$arr['jc_rf']})";
        $handle->query($oddsSql);
    }
}
/*
$arrOddsUrl[1] = 'http://bisai.caipiao.163.com/283/13830/2478940/oydz.html?revert=';
if ($arrOddsUrl && is_array($arrOddsUrl)) {
    foreach ($arrOddsUrl as $id => $url) {


        phpQuery::newDocumentFile($url);
        var_dump(pq("section[class=g-r m-loss m-oydz]")->html());exit;
        foreach (pq(".e-tbWrap") as $tr) {
            var_dump(pq($tr)->html());exit;
        }
    }
}
*/

