<?php
/**
 * Created by PhpStorm.
 * User: dalong
 * Date: 17/4/13
 * Time: 下午9:48
 */
namespace Pdo;
use DBConfig;
include_once '/private/var/www/football/Config/DBConfig.php';
class PDOModel {
    public function connect() {
        $params['host'] = DBConfig\DBConfig::$MYSQL_CONFIG['host'];
        $params['user'] = DBConfig\DBConfig::$MYSQL_CONFIG['user'];
        $params['password'] = DBConfig\DBConfig::$MYSQL_CONFIG['password'];
        $dsn = "mysql:dbname=football;host={$params['host']}";
        $user = $params['user'];
        $password = $params['password'];
        try {
            $dbHandle = new \PDO($dsn,$user,$password);
        } catch (PDOException $e) {
            echo "connection failed: " . $e->getMessage();
        }
        return $dbHandle;
    }
}