<?php
/**
 * Created by PhpStorm.
 * User: danbu
 * Date: 28.10.2019
 * Time: 12:39
 */

include_once "Database.php";
include_once "model/Person.php";
include_once "model/Product.php";
include_once "model/Task.php";
include_once "model/Ticket.php";
include_once "model/Attachment.php";
include_once "model/Comment.php";

$db = new Database();
$db->getConnection();


$marek = \model\Person::getByID(5, $db->connection);
assert($marek->name == "Marek", "getbyid 5 ma byt jmeno Marek, ale je $marek->name");

$marek = \model\Person::getByID(-5, $db->connection);
assert($marek == null, "getbyid -1 ma byt null");


$novy = new \model\Person($db->connection);
$novy->name = "Pepa";
$novy->surname = "Novák";
$novy->role = "worker";
assert($novy->save() == false, "ulozeni se nema povest kvuli chybejicimu heslu");
$novy->password = "hashheslo";
assert($novy->save() == true, "ulozeni se ma povest");
assert($novy->id != null, "id ma byt vyplnene");

assert($novy->delete() == true, "bla");
