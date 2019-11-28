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
echo "<div class=\"main\">";


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

        if ($foundTasks!=NULL){            
            foreach ($foundTasks as $task) {
                print_task($task);
            } 
        } else{
            echo "<div><label class='info'>No tasks found.</label></div>";
        }
        echo "</div>";  

    }  
?>
</body>
<?php logindiv(); ?>
</html>
