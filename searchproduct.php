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
<!--<center>
    <nav>
        <ul>
            <a href="search.php"><li>Show all</li></a>
        </ul>
    </nav>
</center>-->
<br>

<?php
include_once "php/Database.php";
include_once "php/model/Person.php";
include_once "php/model/Product.php";
include_once "php/model/Task.php";
include_once "php/model/Ticket.php";
include_once "php/model/Attachment.php";
include_once "php/model/Comment.php";

$db = new Database();
$db->getConnection();

$product= new \model\Product($db->connection);

echo "<div class=\"advanced_search\">";
echo "<form action=\"searchproduct.php\" method=\"get\">";
echo "<ul>";

if (isset($_GET["name"])){
    echo "<li><label for=\"productname\">Product name:</label><input name=\"productname\" value=\"";
    echo $_GET["name"];
    echo "\" type=\"text\" /></li>";
    $product->name = $_GET["name"];
}
else {
    echo "<li><label for=\"productname\">Product name:</label><input name=\"productname\" type=\"text\" /></li>";
}

if (isset($_GET["description"])){
    echo "<li><label for=\"description\">Description:</label><input name=\"description\" value=\"";
    echo $_GET["description"];
    echo "\" type=\"text\" /></li>";
    $product->description = $_GET["description"];
}
else {
    echo "<li><label for=\"description\">Description:</label><input name=\"description\" type=\"text\" /></li>";
}

if (isset($_GET["parentproduct"])){
    echo "<li><label for=\"parentproduct\">Parent product:</label><input name=\"parentproduct\" value=\"";
    echo $_GET["parentproduct"];
    echo "\" type=\"text\" /></li>";
    $product->parent_product = \model\Product::getByID($_GET["parentproduct"], $db->connection);
}
else {
    echo "<li><label for=\"parentproduct\">Parent product:</label><input name=\"parentproduct\" type=\"text\" /></li>";
}

if (isset($_GET["manager"])){
    echo "<li><label for=\"manager\">Manager:</label><input name=\"manager\" value=\"";
    echo $_GET["manager"];
    echo "\" type=\"text\" /></li>";
    $product->manager = \model\Person::getByID($_GET["manager"], $db->connection);
}
else {
    $product->manager = null;
    echo "<li><label for=\"manager\">Manager:</label><input name=\"manager\" type=\"text\" /></li>";
}

echo "<li><input type=\"submit\" value=\"search\"/></li>";
echo "</ul>";
echo "</form>";
echo "</div>";
echo "<div class=\"main\">";

$foundProducts = $product->findInDb();

foreach ($foundProducts as $pro) {
    if(!$pro->loadModels())
    {
    echo  "nepodarilo se nacist modely...";
    }

    if($_SESSION["role"] == "admin" || $_SESSION["role"] == "senior manager" )
    {
        echo "<a href=\"product.php?id=$pro->id\">";
    }
    echo "<div class=\"product\">";
    echo "<ul>";
    echo "ID: $pro->id, Name:$pro->name<br>";
    echo "Description: $pro->description <br>";
    $pro->loadModels();
    $name = $pro->manager->username;
    echo "Manager: $name";
    echo "</ul>";
    echo "</div>";
    if($_SESSION["id"] == "admin"|| $_SESSION["role"] == "senior manager")
    {
        echo "</a>";
    }

}
echo "</div>";
?>
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
