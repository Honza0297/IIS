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
        echo "<nav>";
        echo "<a href=\"index.php\"><img src=\"BigDuckBugYellow.png\" alt=\"LOGO\" class=\"logo\"></a>";
        echo "<a href=\"search.php\"><li>Search</li></a>";
        session_start();
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
            echo "<a href=\"ticket.php?action=new\"><li>Create ticket</li></a>";
            if ($_SESSION["role"]=="admin"){
                echo "<a href=\"profile.php?action=new\"><li class=\"register\">Register</li></a>";
            }
            echo "</nav>";
            echo "<personal>";
            echo "<a href=\"profile.php\"><li class=\"profile\" >Profile</li></a>";
            echo "<a href=\"logout.php?page=index.php\"><li class=\"logout\">Log out</li></a>";
        }
        else {
            echo "</nav>";
            echo "<personal>";
            echo "<li class=\"login\" onclick=\"document.getElementById('id01').style.display='block'\">Log in</li>";
            echo "<a href=\"profile.php?action=new\"><li class=\"register\">Register</li></a>";
        }
        echo " </personal> "
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

$person = new \model\Person($db->connection);
echo "<div class=\"advanced_search\">";
echo "<form action=\"searchuser.php\" method=\"get\">";
echo "<ul>";

if (isset($_GET["username"])){
    echo "<li><label for=\"username\">Username:</label><input name=\"username\" value=\"";
    echo $_GET["username"];
    echo "\" type=\"text\" /></li>";
    $person->username = $_GET["username"];
}
else {
    echo "<li><label for=\"username\">Username:</label><input name=\"username\" type=\"text\" /></li>";
}
if (isset($_GET["name"])){
    echo "<li><label for=\"name\">Name:</label><input name=\"name\" value=\"";
    echo $_GET["name"];
    echo "\" type=\"text\" /></li>";
    $person->name = $_GET["name"];
}
else {
    echo "<li><label for=\"name\">Name:</label><input name=\"name\" type=\"text\" /></li>";
}
if (isset($_GET["surname"])){
    echo "<li><label for=\"surname\">Surname:</label><input name=\"surname\" value=\"";
    echo $_GET["surname"];
    echo "\" type=\"text\" /></li>";
    $person->surname = $_GET["surname"];
}
else {
    echo "<li><label for=\"surname\">Surname:</label><input name=\"surname\" type=\"text\" /></li>";
}
if (isset($_GET["role"])){
    echo "<li><label for=\"role\">Role:</label><input name=\"role\" value=\"";
    echo $_GET["role"];
    echo "\" type=\"text\" /></li>";
    $person->role = $_GET["role"];

}
else {
    echo "<li><label for=\"role\">Role:</label><input name=\"role\" type=\"text\" /></li>";
}

echo "<li><input type=\"submit\" value=\"search\"/></li>";
echo "</ul>";
echo "</form>";
echo "</div>";
echo "<div class=\"main\">";

$foundPersons = $person->findInDb();
foreach ($foundPersons as $per) {
    if($_SESSION["role"] == "admin")
    {
        echo "<a href=\"profile.php?id=$per->id\">";
    }
    echo "<div class=\"person\">";
    echo "<ul>";
    echo "ID:$per->id, Name:$per->name, Surname:$per->surname<br>";
    echo "Role: $per->role <br>";
    echo "Username: $per->username";
    echo "</ul>";
    echo "</div>";
    if($_SESSION["id"] == "admin")
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
