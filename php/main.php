<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 27.10.2019
 * Time: 13:49
 */


//SOUBOR PRO POTREBY DEBUGU CISTE PRO DENNYHO
//NENI ZDE NIC CO BY SE POUZIVALO
//NEBO BY MOHLO BYT UZITECNE PRO KOHOKOLIV JINEHO
//CASEM SE TOTO SMAZE

include_once "model/Person.php";    //\model\Person::class;

$dsn = 'mysql:host=localhost;dbname=db-its';
$username = 'root';
$password = '';

$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
$pdo = new PDO($dsn, $username, $password, $options) or die('Unable to connect.');

$stmt = $pdo->query("select name, surname from persons");
while($row = $stmt->fetch())
{
    echo nl2br('osoba: ' . $row['name'] . ' ' . $row['surname'] . "\n");
}

echo 'tada';

//ulozeni noveho uzivatele

$person = \model\Person::getByID(4, $pdo);
echo "nalezeny clovek s id 4 je " . $person->name;
/*
$person->name = "Vlastislav";
$person->surname = "Zedník";
$person->role = "customer";
*/
$person->password = "noveheslopromarka";

if($person->save())
    echo "ulozenoooo";
else
    echo "nepovedlo se ulozir";


?>