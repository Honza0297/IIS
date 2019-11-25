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
        $product->manager = $_POST["manager"];
        $product->parent_product = $_POST["parent_product"];

        if($product->name == null or $product->manager)
        {
            echo "Vratte se tlacitkem zpet a vyplnte vsechny udaje prosim. \n";
            exit();
        }
        if(isset($_POST["id"]))
        {
            echo $product->id;
            echo $product->name;
            echo $product->parent_product;
            echo $product->description;
            echo $product->manager;

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
                $person->save(); //nova osoba ulozena
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
            echo "<form method=\"post\" action=\"product.php\">";
            echo "<label for=\"productname\">Product name:</label><input id=\"productname\" name=\"productname\" type=\"text\"><br>";
            echo "<label>Description:</label><br>";
            echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\"></textarea><br>";
            echo "<label for=\"parent\">Parent product:</label><input id=\"parent\"  name=\"parent\" type=\"text\"><br>";
            echo "<label for=\"manager\">Manager:</label><input id=\"manager\"  name=\"manager\" type=\"text\"><br>";
            echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
            echo "</form>";
        }

        else if ($_GET["action"]=="edit"){
            $product = \model\Product::getByID($_GET["productid"], $db->connection);
            echo "<form method=\"post\" action=\"product.php\">";
            echo "<label for=\"productname\">Product name:</label><input id=\"productname\" name=\"productname\" type=\"text\"><br>";
            echo "<label>Description:</label><br>";
            echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\"></textarea><br>";             echo "<label for=\"parent\">Parent product:</label><input id=\"parent\"  name=\"parent\" type=\"text\"><br>";
            echo "<label for=\"manager\">Manager:</label><input id=\"manager\"  name=\"manager\" type=\"text\"><br>";
            $id = $_GET["productid"];
            echo "<input id=\"id\"  name=\"id\" type=\"id\"  hidden=\"true\" value=$id /><br>";
            echo "<input type=\"submit\" value=\"Save changes\" name=\"submit\">";
            echo "</form>";
        }
    }
    else if (isset($_GET["id"])){

        $current_product = \model\Product::getByID($_GET["id"], $db->connection);
        if ($current_product == null)
        {
            echo "Nepodarilo se stahnout data. Prosim kontaktuje spravce.\n";
            exit();
        }

        echo "<label>Product name: $current_product->name</label><br>";
        echo "<label>Description: $current_product->description</label><br>";
        echo "<label>Parent product: $current_product->parent_product</label><br>";
        echo "<label>Manager: $current_product->manager</label><br>";
        {

            echo "<a href='product.php?action=edit&productid=";
            echo $_GET["id"];
            echo "'><button>EDIT</button></a><br>";
        }
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
