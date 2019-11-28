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
			include_once "php/model/Ticket.php";
			include_once "php/model/Product.php";
			include_once "php/model/Person.php";
            include_once "php/model/Comment.php";
            include_once "php/model/Task.php";
            include_once "php/model/Work_on_tasks.php";

			$db = new Database();
			$db->getConnection();
            $logged = isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true &&$_SESSION["role"]!="customer";
            if ($logged){
                if (isset($_POST["submit"])){
                    if (isset($_POST["time"])){                                             
                        $work = new \model\Work_on_tasks ($db->connection);
                        $work->taskID=$_GET["taskID"];
                        $work->personID=$_GET["ID"];
                        $work->total_time=$_POST["time"];
                        if ($work->update()) redirect ("task.php?id=".$_GET["taskID"]);
                        else redirect ("task.php?id=".$_GET["taskID"]."&error");
                    }
                    $ticketID;
                    ////////////////////////////
                    //Task was created or edited
                    ////////////////////////////
                    if (isset($_POST["type"])&&isset($_GET["ticketID"])&&isset($_POST["description"])){
                        $task = new \model\Task($db->connection);
                        //ticket was edited
                        if (isset($_POST["edit"])) $task->id = $_POST["edit"];
                        $task->title = $_POST["type"];
                        $task->state = $_POST["state"];          
                        if (isset($_POST["deadline"])) $task->deadline = $_POST["deadline"];
                        else  $task->remaining_time = 0;//TODO author
                        $task->ticket = \model\Ticket::getByID($_GET["ticketID"],$db->connection);   
                        $task->description = $_POST["description"];                
                        if ($task->save()) redirect("task.php?id=".$task->id); 
                        else redirect ("task.php");
                    }
                }
                /////////////////////
                //Edit or New task
                /////////////////////
                else if (isset($_GET["action"])){ 
                    ///////////////////
                    //New task
                    /////////////////       
                    if ($_GET["action"]=="new"&&isset($_GET["ticketID"])){
                        echo "<form method=\"post\" action=\"task.php?ticketID=";echo ($_GET["ticketID"]);echo "\">";
                        echo "<label for=\"type\">title:</label><input id=\"type\" name=\"type\" value=\"Todo\" type=\"text\"><br>";
                        echo "<label for=\"state\">State:</label><input id=\"state\"  name=\"state\" readonly=\"true\" value=\"pending\" type=\"text\"><br>";
                        echo "<label for=\"deadline\">Expected completion date (yyyy-mm-dd):</label><input id=\"deadline\" value=\"00-00-0000\" name=\"deadline\" type=\"text\"><br>";
                        echo "<label>Description:</label><br>";
                        echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\"></textarea><br>";
                        echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
                        echo "</form>";
                    }
                    //////////////////
                    //EDIT task
                    //////////////////
                    else if ($_GET["action"]=="edit"&&isset($_GET["id"])&&isset($_GET["ticketID"])){
                        $task = \model\Task::getByID($_GET["id"],$db->connection);
                        if ($task!=null){                        
                            echo "<form method=\"post\" action=\"task.php?ticketID=";echo ($_GET["ticketID"]);echo "\">";
                            echo "<label for=\"type\">title:</label><input id=\"type\" name=\"type\" value=\"$task->title\" type=\"text\"><br>";
                            echo "<label for=\"state\">State:</label><input id=\"state\"  name=\"state\" value=\"$task->state\" type=\"text\"><br>";               
                            $task->loadModels();
                            $temp = $task->ticket;
                            echo "<a href=\"ticket.php?id=$temp->id\">Ticket: $temp->title</a><br>";
                            $temp->loadModels();
                            $temp = $temp->product;
                            echo "<a href=\"product.php?id=$temp->id\">Product: $temp->name</a><br>";
                            echo "<label for=\"deadline\">Expected completion date (yyyy-mm-dd):</label><input id=\"deadline\" value=\"$task->deadline\" name=\"deadline\" type=\"text\"><br>";
                            echo "<label>Description:</label><br>";
                            echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\">$task->description</textarea><br>";
                            echo "<input type=\"text\" value=\""; echo$_GET["id"]; echo "\" id=\"edit\" style=\"display: none;\" name=\"edit\">";
                            echo "<input type=\"submit\" value=\"Edit\" name=\"submit\">";
                            echo "</form>";
                        }
                        else echo "Task nenalezen";
                    }
                    else if ($_GET["action"]=="unassign"&&isset($_GET["assignee"])&&isset($_GET["taskID"])){
                        \model\Work_on_tasks::delete($_GET["assignee"],$_GET["taskID"],$db->connection);
                        redirect("task.php?id=".$_GET["taskID"]);
                    }
                    else if (isset($_GET["action"])&&$_GET["action"]=="assign"&&isset($_GET["assignee"])&&isset($_GET["assignee"])){
                        $work_on_task = new \model\Work_on_tasks ($db->connection);
                        $work_on_task->personID=$_GET["assignee"];
                        $work_on_task->taskID=$_GET["taskID"];
                        $work_on_task->saveNew();
                        redirect("task.php?id=".$_GET["taskID"]);
                    }
                }
                //////////////
                //Show task
                //////////////
                else if (isset($_GET["id"])&&$_SESSION["role"]!="customer"){
                    $task = \model\Task::getByID($_GET["id"], $db->connection);
                    echo "<label>Title: $task->title</label><br>";
                    echo "<label>State: $task->state</label><br>";                  
                    $task->loadModels();
                    $temp = $task->ticket;
                    echo "<a href=\"ticket.php?id=$temp->id\">Ticket: $temp->title</a><br>";
                    $temp->loadModels();
                    $temp = $temp->product;
                    echo "<a href=\"product.php?id=$temp->id\">Product: $temp->name</a><br>";
                    echo "<label>Expected completion date (yyyy-mm-dd): $task->deadline</label><br>";
                    echo "<label>Description: $task->description</label><br>";
                    $task->loadModels();
                    $temp = $task->ticket->id;
                    echo "<a href=\"task.php?action=edit&id=";echo $_GET["id"]; echo "&ticketID=";echo ($temp); echo "\"><button>Edit</button></a><br>";
                    $assignees = \model\Work_on_tasks::getPersons($_GET["id"],$db->connection);                
                    echo "Assigned:<br><br>";
                    if ($assignees!=null){
                        foreach ($assignees as $assignee) {
                            $temp = \model\Work_on_tasks::getByIDs ($assignee->id,$_GET["id"],$db->connection);
                            if ($assignee->id!=$_SESSION["id"]){
                                echo "<a href=\"task.php?action=unassign&assignee=$assignee->id&taskID=";echo ($_GET["id"]);echo "\"><font color=\"red\">X</font></a> $assignee->name $assignee->surname   Time:$temp->total_time<br>";
                            }
                            else {
                                echo "<a href=\"task.php?action=unassign&assignee=$assignee->id&taskID=";echo ($_GET["id"]);echo "\"><font color=\"red\">X</font></a> $assignee->name $assignee->surname   <form action=\"task.php?taskID=$task->id&ID=$assignee->id\" method=\"post\">Time:<input type=\"text\" value=\"$temp->total_time\" name=\"time\"/><input type=\"submit\" name=\"submit\" value=\"update\"></form><br>";
                            }
                        }
                    }
                    echo "<button onclick=\"showhide()\">Assign task</button>";   
                    $searchperson = new \model\Person ($db->connection);
                    $persons = $searchperson->findInDB();
                    $persons = RemoveAlreadyAssigned($persons,$assignees);
                    echo "<div id=\"persons\" style=\"display:none\">";
                    foreach ($persons as $person){
                        if ($person->role!="admin"){
                            echo "<a href=\"task.php?action=assign&assignee=$person->id&taskID=";echo ($_GET["id"]); echo "\">$person->name $person->surname</a><br>";
                        }
                    }                
                    echo "</div>";
                }
            }
            else echo "LOG IN";
		?>
	   </div>
</body>

<?php logindiv(); ?>

</html>
