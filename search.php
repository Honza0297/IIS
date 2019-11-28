﻿<!DOCTYPE HTML>
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
    include_once "CustomElements.php";


    $db = new Database();
    $db->getConnection();

    $ticket = new \model\Ticket($db->connection);
    echo "<div class=\"advanced_search\">";
    echo "<form action=\"search.php\" method=\"get\">";
        echo "<ul>";
            /////////////////
            //search fields
            ////////////////
            if (isset($_GET["title"])){
                echo "<li><label  for=\"title\">Title:</label><input name=\"title\" value=\"";
                echo $_GET["title"];
                echo "\" type=\"text\" /></li>";
                $ticket->title = $_GET["title"];
            }
            else {
                echo "<li><label for=\"title\">Title:</label><input name=\"title\" type=\"text\" /></li>";
            }
            if (isset($_GET["info"])){
                echo "<li><label  for=\"info\">Info:</label><input name=\"info\" value=\"";
                echo $_GET["info"];
                echo "\" type=\"text\" /></li>";
                $ticket->info = $_GET["info"];
            }
            else {
                echo "<li><label for=\"info\">Info:</label><input name=\"info\" type=\"text\" /></li>";
            }

            if (isset($_GET["status"])){
                echo "<li>";
                ShowSelectElement($states, $states, $_GET["status"], "Status", "status");
                echo "</li>";
                $ticket->state = $_GET["status"];
            }
            else {
                echo "<li>";
                ShowSelectElement($states, $states, "", "Status", "status");
                echo "</li>";
            }
            if (isset($_GET["product"])){
                /*echo "<li><label for=\"product\">Product:</label><input name=\"product\" value=\"";
                echo $_GET["product"];
                echo "\" type=\"text\" /></li>";*/
                list($productIDs, $productLabels) = prepareProducts($db, false);
                array_unshift($productIDs, "any");
                array_unshift($productLabels, "any");
                echo "<li>";
                ShowSelectElement($productIDs, $productLabels, $_GET["product"], "Product", "product");
                echo "</li>";
                $ticket->product = \model\Product::getByID($_GET["product"], $db->connection);

            }
            else {
                //echo "<li><label for=\"product\">Product:</label><input name=\"product\" type=\"text\" /></li>";
                list($productIDs, $productLabels) = prepareProducts($db, false);
                array_unshift($productIDs, "any");
                array_unshift($productLabels, "any");
                echo "<li>";
                ShowSelectElement($productIDs, $productLabels, "any", "Product", "product");
                echo "</li>";
            }

            echo "<li><input type=\"submit\" class=\"button\" value=\"search\"/></li>";
        echo "</ul>";
    echo "</form>";
    echo "</div>";
    echo "<div class=\"main\">";
        /////////////////////
        /// Search result
        ////////////////////
        $foundTickets = $ticket->findInDb();            
        foreach ($foundTickets as $tik) {
            echo "<a href=\"ticket.php?id=$tik->id\">";
                echo "<div class=\"ticket\">";
                    echo "<ul>";
                        echo "ID:$tik->id, Title:$tik->title<br>";
                        echo "$tik->info";
                    echo "</ul>";
                echo "</div>";
            echo "</a>";
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
