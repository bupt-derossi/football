<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/12/11
 * Time: 下午2:30
 * brief:网易彩票抓取配置
 */
class Caipiao163Config {
    public static $URL = "http://caipiao.163.com/order/jczq/?betDate=";

    public static $ODDS_URL_PRE = "http://bisai.caipiao.163.com/";

    public static $DL_ATTR = 'gamedate';

    public static $RESULT_MAPPING = [
        '胜' => 3,
        '平' => 1,
        '负' => 0
    ];

}