<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 13:49
 */

$user = 'root';
$password = '';
$dbName = 'db-its';

$db = new mysqli('localhost', $user, $password, $dbName) or die('Unable to connect');

echo 'tada';

?>