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
                        echo "<a href=\"searchuser.php\"><li class=\"searchuser\" >Search user</li></a>";
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

    $logged = isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true;
    if ($logged&&$_SESSION!="customer"&&isset($_GET["id"])){
        $db = new Database();
        $db->getConnection();

        $ticket = \model\Ticket::getByID($_GET["id"],$db->connection);
        if ($ticket!=NULL){
            echo "<a href=\"task.php?ticketID=";echo $_GET["id"];echo "&action=new\"><button>New task</button></a>";
            echo "<div class=\"main\">";
                ////////////////////////////////////////
                /// All current tasks under this ticket
                //////////////////////////////////////////
                $searchtask = new \model\Task ($db->connection);
                $searchtask->ticket= $ticket;
                $foundTasks = $searchtask->findInDb();            
                foreach ($foundTasks as $task) {
                    echo "<a href=\"task.php?id=$task->id\">";
                        echo "<div class=\"task\">";
                            echo "<ul>";
                                echo "Type:$task->type Status:$task->state<br>";
                                echo "$task->description";
                            echo "</ul>";
                        echo "</div>";
                    echo "</a>";
                }       
            echo "</div>";             
        }    
    }
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
