<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="robots" content="noindex; nofollow">
    <link type="text/css" rel="stylesheet" href="style.css">
    <title>ITS</title>
    <link rel="icon" href="BigDuckBugBlack.png">
    <!--<link rel="icon" href="imgs/icon.png">-->

</head>
<body>
<header>
    <ul>
        <?php
        include_once "CustomElements.php";
        ShowHeader();
        ?>
    </ul>
</header>
<br>
<div class="main">
    <?php


    include_once "php/Database.php";
    include_once "php/model/Product.php";
    include_once "php/model/Person.php";
    include_once "php/model/Ticket.php";
    $db = new Database();
    $db->getConnection();




    if (isset($_POST["submit"])){
        $product =  new model\Product($db->connection);
        if(isset($_POST["id"]))
        {
            $product->id = $_POST["id"];
        }
        $product->name = $_POST["productname"];
        $product->description = $_POST["description"];
        if($_POST["manager"] != null)
        {
            $product->manager = new \model\Person($db->connection);
            $product->manager->id = $_POST["manager"];
        }
        else
        {
            $product->manager = null;
        }
        if($_POST["parent"] != null)
        {
            echo $_POST["parent"];
            $product->parent_product = new \model\Product($db->connection);
            $product->parent_product->id = $_POST["parent"];
        }

        if($product->name == null or $product->manager == null)
        {
            echo "Vratte se tlacitkem zpet a vyplnte vsechny udaje prosim. \n";
            exit();
        }

        if(isset($_POST["id"])) //todo zkontrolovat isset u post metody (mozna ma byt != null)?
        {
            echo $product->id;
            echo $product->name;
            echo $product->parent_product == null ? "" : $product->parent_product->id;
            echo $product->description;
            echo $product->manager->name;

            if ($product->save()) //novy produkt ulozen
                echo "Zmeny byly ulozeny\n";
            else
                echo "fuck";
        }
        else
        {
            $same_name = \model\Product::getByName($product->name, $db->connection);
            if ($same_name == null)
            {
                if(!$product->save())
                    echo "fuck";//nova osoba ulozena
                else
                    echo "Produkt zapsan uspesne. :)\n";
                exit();
            }
            else
            {
                echo "Produkt stejneho jmena jiz existuje. vyberte prosim jine jmeno \n";

            }
        }
    }
    else if (isset($_GET["action"])){
        if ($_GET["action"]=="new"){
            //preparing arrays of managers and products to show in select element
            list($managerIDs, $managerLabels) = prepareManagers($db);
            list($productIDs, $productLabels) = prepareProducts($db, true);

            echo "<form method=\"post\" action=\"product.php\">";
            echo "<label for=\"productname\">Product name:</label><input id=\"productname\" name=\"productname\" type=\"text\"><br>";
            echo "<label>Description:</label><br>";
            echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\"></textarea><br>";
            //echo "<label for=\"parent\">Parent product:</label><input id=\"parent\"  name=\"parent\" type=\"text\"><br>";
            ShowSelectElement($productIDs, $productLabels, "", "Parent product", "parent"); echo "<br>";
            ShowSelectElement($managerIDs, $managerLabels, "", "Manager", "manager"); echo "<br>";
            //echo "<label for=\"manager\">Manager:</label><input id=\"manager\"  name=\"manager\" type=\"text\"><br>";
            echo "<input type=\"submit\" class='button' value=\"Create\" name=\"submit\">";
            echo "</form>";
        }

        else if ($_GET["action"]=="edit"){
            //preparing arrays of managers and products to show in select element
            list($managerIDs, $managerLabels) = prepareManagers($db);
            list($productIDs, $productLabels) = prepareProducts($db, true);

            $product = \model\Product::getByID($_GET["productid"], $db->connection);
            if(!$product->loadModels())
            {
                echo "nepodarilo se ancist modely\n"; //todo neco s tim udelej berry
            }
            echo "<form method=\"post\" action=\"product.php\">";
            echo "<label for=\"productname\">Product name:</label><input id=\"productname\" name=\"productname\" value=\"$product->name\" type=\"text\"><br>";
            echo "<label>Description:</label><br>";
            echo "<textarea id=\"description\" name=\"description\"  rows=\"10\" cols=\"50\">$product->description</textarea><br>";
            //$parent_product_name = $product->parent_product == null ? "" : $product->parent_product->name;
            ShowSelectElement($productIDs, $productLabels, $product->parent_product == null ? "" : $product->parent_product->id, "Parent product", "parent"); echo "<br>";
            ShowSelectElement($managerIDs, $managerLabels, $product->manager->id, "Manager", "manager"); echo "<br>";
            echo "<input type=\"submit\" class='button' value=\"Save changes\" name=\"submit\">";
            $manager_id = $product->manager->id;
            echo "<input class='hidden'id=\"managername\"  name=\"managername\" value=\"$manager_id\" hidden=\"true\" type=\"text\"><br>";
            $id = $_GET["productid"];
            echo "<input class='hidden' id=\"id\"  name=\"id\" type=\"id\"  hidden=\"true\" value=$id /><br>";

            echo"</form>";
        }
    }
    else if (isset($_GET["id"])){

        $current_product = \model\Product::getByID($_GET["id"], $db->connection);
        if ($current_product == null)
        {
            echo "Nepodarilo se stahnout data. Prosim kontaktuje spravce.\n";
            exit();
        }
        if(!$current_product->loadModels())
        {
            echo "Nepodarilo se nacist cizi modely\n";
        }
        echo "<label class='title'>$current_product->name</label><br>";
        echo "<label class='info'>Description: </label>";
        echo"<p class='description' >$current_product->description asjkdfha dskfhals kjdfasdlk alsdig faoidihg oashid fgiahdof gihadsogih adso igaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1gigaidhga aosidh gfoasdihg oaihdg oiahd g oiahdgo iahsdogha dsg sdg 23asd1 g3ads1g 321ads3g 1dag 3asdg   </p><br>";
        echo"<br>";
        $parent_product_name = $current_product->parent_product == null? "" : $current_product->parent_product->name;
        echo "<label class='info'>Parent product:</label><label class='info'>$parent_product_name</label><br>";
        $username = $current_product->manager->username;
        echo "<label class='info'>Manager:</label><label class='info'>$username</label><br>";
        if($_SESSION["role"] == "senior manager" || $_SESSION["role"] == "admin")
        {

            echo "<a href='product.php?action=edit&productid=";
            echo $_GET["id"];
            echo "'><button>EDIT</button></a><br>";
        }

        echo "<label class='info'>Products´s tickets:</label><br>";
        $ticket = new \model\Ticket($db->connection);
        $ticket->product = $current_product;
        $tickets = $ticket->findInDb();
        if($tickets != null)
        {
            foreach ($tickets as $ticket) {
                print_ticket($ticket);
            }
        }
    }
    function print_ticket($tik)
    {
        echo "<a href=\"ticket.php?id=$tik->id\">";
        echo "<div class=\"ticket\">";
        echo "<ul>";
        echo "ID:$tik->id, Title:$tik->title<br>";
        echo "$tik->info";
        echo "</ul>";
        echo "</div>";
        echo"<hr>";
        echo "</a>";

    }

    ?>
</div>
</body>

<div id="id01" class="modal">

    <form class="modal-content animate" action=<?php echo ("\"login.php?page=".$_SERVER["REQUEST_URI"]."?".$_SERVER["QUERY_STRING"]."\""); ?> method="post">
        <div class="container">
            <label for="uname"><b>Username</b></label>
            <input type="text" class="UsPa" placeholder="Enter Username" name="uname" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" class="UsPa" placeholder="Enter Password" name="psw" required>

            <button type="submit">Login</button>
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
        </div>
    </form>
</div>

<script>
    // Get the modal
    var modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</html>
