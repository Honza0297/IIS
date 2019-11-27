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
    include_once "php/model/Work_on_tasks.php";

    $logged = isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true;
    $db = new Database();
    $db->getConnection();
    if ($logged&&$_SESSION["role"]!="customer"){
        if (isset($_GET["id"])){
            $foundTasks;
            $ticket = \model\Ticket::getByID($_GET["id"],$db->connection);
            if ($ticket!=NULL){
                echo "<a href=\"task.php?ticketID=";echo $_GET["id"];echo "&action=new\"><button>New task</button></a>";
                    ////////////////////////////////////////
                    /// All current tasks under this ticket
                    //////////////////////////////////////////
                    $searchtask = new \model\Task ($db->connection);
                    $searchtask->ticket= $ticket;
                    $foundTasks = $searchtask->findInDb(); 
            }
        }
        else if (isset($_GET["assignee"])){
            $foundTasks = \model\Work_on_tasks::getTasks($_GET["assignee"],$db->connection);
        }
        else {
            $searchtask = new \model\Task ($db->connection);
            $foundTasks = $searchtask->findInDb();       
        }
        echo "<div class=\"main\">";
        foreach ($foundTasks as $task) {
                echo "<a href=\"task.php?id=$task->id\">";
                    echo "<div class=\"task\">";
                        echo "<ul>";
                            $task->loadModels();
                            $temp = $task->ticket;
                            $temp->loadModels();
                            $temp = $temp->product->name;
                            echo "Type:$task->type  Status:$task->state  Product:$temp<br>";
                            echo "$task->description";
                        echo "</ul>";
                    echo "</div>";
                echo "</a>";
        }   
        echo "</div>";  

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
