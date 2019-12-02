<?php
include_once "CustomElements.php";
setSession();
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

if (isset($_GET["productname"])){
    echo "<li><label for=\"productname\">Product name:</label><input name=\"productname\" value=\"";
    echo $_GET["productname"];
    echo "\" type=\"text\" /></li>";
    $product->name = $_GET["productname"];
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
    list($productIDs, $productLabels) = prepareProducts($db, true);
    array_unshift($productIDs, "any");
    array_unshift($productLabels, "any");
    ShowSelectElement($productIDs, $productLabels, $_GET["parentproduct"], "Parent product", "parentproduct");
    echo "<br>";
    //echo "<li><label for=\"parentproduct\">Parent product:</label><input name=\"parentproduct\" value=\"";
    //echo $_GET["parentproduct"];
    //echo "\" type=\"text\" /></li>";
    if($_GET["parentproduct"] == "any")
    {
        $parent = new \model\Product($db->connection);
        $parent->id = "any";
        $product->parent_product = $parent;
    }
    else
        $product->parent_product = \model\Product::getByID($_GET["parentproduct"], $db->connection);
}
else {

    list($productIDs, $productLabels) = prepareProducts($db, true);
    array_unshift($productIDs, "any");
    array_unshift($productLabels, "any");
    ShowSelectElement($productIDs, $productLabels, "any", "Parent product", "parentproduct");
    echo "<br>";
    $parent = new \model\Product($db->connection);
    $parent->id = "any";
    $product->parent_product = $parent;
    //echo "<li><label for=\"parentproduct\">Parent product:</label><input name=\"parentproduct\" type=\"text\" /></li>";
}

if (isset($_GET["manager"])){
    list($managerIDs, $managerLabels) = prepareManagers($db);
    array_unshift($managerIDs, "any");
    array_unshift($managerLabels, "any");
    if(isset($_SESSION["loggedin"]))
    {
        ShowSelectElement($managerIDs, $managerLabels, $_GET["manager"], "Manager", "manager");
        echo "<br>";
    }
    else
    {
       echo "<label>Manager: </label><input name=\"manager\" value=\"any\" type=\"text\" disabled/>";
    }
    //echo "<li><label for=\"manager\">Manager:</label></li>";
    $product->manager = \model\Person::getByID($_GET["manager"], $db->connection); //if managers ID is any, the method returns null anyway
}
else {
    list($managerIDs, $managerLabels) = prepareManagers($db);
    array_unshift($managerIDs, "any");
    array_unshift($managerLabels, "any");
    if(isset($_SESSION["loggedin"]))
    {
        ShowSelectElement($managerIDs, $managerLabels, "any", "Manager", "manager");
        echo "<br>";
    }
    else
    {
        echo "<label>Manager: </label><input name=\"manager\" value=\"any\" type=\"text\" disabled/>";
    }
    //$product->manager = null;
    //echo "<li><label for=\"manager\">Manager:</label><input name=\"manager\" type=\"text\" /></li>";
}

echo "<li><input class='button' type=\"submit\" value=\"search\"/></li>";
echo "</ul>";
echo "</form>";
echo "</div>";
echo "<div class=\"main\">";

$foundProducts = $product->findInDb();



foreach ($foundProducts as $pro) {
    print_product_basic($pro);

}
echo "</div>";
?>
</body>
    <?php logindiv(); ?>
</html>
