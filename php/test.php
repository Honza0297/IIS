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


$marek = \model\Person::getByID(-5, $db->connection);
assert($marek == null, "getbyid -1 ma byt null");

$marek = \model\Person::getByID(5, $db->connection);
assert($marek->name == "Marek", "getbyid 5 ma byt jmeno Marek, ale je $marek->name");

$searchPerson = new \model\Person($db->connection);
//$searchPerson->name = "Mar";
$searchPerson->role = "adm";
$admins = $searchPerson->findInDb();
foreach ($admins as $admin)
{
    echo "name of admin: " . $admin->name . "\n";
}

$novy = new \model\Person($db->connection);
$novy->username = "novak01";
$novy->name = "Pepa";
$novy->surname = "Novák";
$novy->role = "manager";
assert($novy->save() == false, "ulozeni se nema povest kvuli chybejicimu heslu");
$novy->password = "hashheslo";
assert($novy->save() == true, "ulozeni se ma povest");
assert($novy->id != null, "id ma byt vyplnene");


$product = new \model\Product($db->connection);
$product->manager = $novy;
assert($product->save() == false, "ulozeni se nema povest kvuli chybejicimu name");
$product->name = "Fedora";
assert($product->save() == true, "ulozeni productu se ma povest");
assert($product->id != null, "id produktu ma byt vyplnene");
$product->manager = $marek;
assert($product->save() == true, "aktualizace productu se ma povest");
assert($product->loadModels() == true, "nacteni modelu productu se ma povest");


$ticket = new \model\Ticket($db->connection);
$ticket->product = $product;
$ticket->state = "pending";
$ticket->date_posted = date('Y-m-d');
$ticket->title = "nefaka externi monitor";
$ticket->info = "funguje jen behem bootu nebo vypinani";
assert($ticket->save() == false, "ulozeni tiketu se nema povest kvuli chybejicimu autorovi");
$ticket->author = $novy;
assert($ticket->save() == true, "ulozeni tiketu se ma povest");

$searchTicket = new \model\Ticket($db->connection);
$searchTicket->title = "je";
$searchTicket->author = \model\Person::getByID(1, $db->connection);
$tickets = $searchTicket->findInDb();
foreach ($tickets as $ticket) {
    echo "tiket: $ticket->title: $ticket->info\n";
}


assert($product->delete() == true, "smazani product ma byt ok");
assert($novy->delete() == true, "smazani uzivatele ma byt ok");
