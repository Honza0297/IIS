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
    <br>
        <div class="main">		
		<?php
			include_once "php/Database.php";
			include_once "php/model/Ticket.php";
			include_once "php/model/Product.php";
			include_once "php/model/Person.php";
            include_once "php/model/Comment.php";
            include_once "php/model/Task.php";

			$db = new Database();
			$db->getConnection();
            $logged = isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true &&$_SESSION["role"]!="customer";
			if (isset($_POST["submit"])&&$logged){
                $ticketID;
                ////////////////////////////
                //Task was created or edited
                ////////////////////////////
				if (isset($_POST["type"])&&isset($_GET["ticketID"])&&isset($_POST["description"])){
					$task = new \model\Task($db->connection);
                    //ticket was edited
                    if (isset($_POST["edit"])) $task->id = $_POST["edit"];
					$task->type = $_POST["type"];
                    $task->state = $_POST["state"];          
                    if (isset($_POST["estimated_time"])) $task->remaining_time = $_POST["estimated_time"];
                    else  $task->remaining_time = 0;//TODO author
                    if (isset($_POST["total_time"])) $task->total_time = $_POST["total_time"];
                    else  $task->remaining_time = 0;//TODO author
                    $task->ticket = \model\Ticket::getByID($_GET["ticketID"],$db->connection);   
                    $task->description = $_POST["description"];                
                    if ($task->save()) header( "Location: task.php?id=".$task->id); 
                    //else header( "Location: task.php");
				}
			}
            /////////////////////
            //Edit or New task
            /////////////////////
			else if (isset($_GET["action"])&&isset($_GET["ticketID"])&&$logged){	
                ///////////////////
                //New task
                /////////////////		
				if ($_GET["action"]=="new"){
                    echo "<form method=\"post\" action=\"task.php?ticketID=";echo ($_GET["ticketID"]);echo "\">";
					echo "<label for=\"type\">type:</label><input id=\"type\" name=\"type\" value=\"Todo\" type=\"text\"><br>";
					echo "<label for=\"state\">State:</label><input id=\"state\"  name=\"state\" readonly=\"true\" value=\"pending\" type=\"text\"><br>";
                    echo "<label for=\"estimated_time\">Estimated time:</label><input id=\"estimated_time\" value=\"0\" name=\"estimated_time\" type=\"text\"><br>";
                    echo "<label for=\"total_time\">Total time:</label><input id=\"total_time\" readonly=\"true\" value=\"0\" name=\"total_time\" type=\"text\"><br>";
					echo "<label>Description:</label><br>";
					echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\"></textarea><br>";
					echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
					echo "</form>";
				}
                //////////////////
                //EDIT task
                //////////////////
				if ($_GET["action"]=="edit"&&isset($_GET["id"])){
                    $task = \model\Task::getByID($_GET["id"],$db->connection);
                    if ($task!=null){                        
                        echo "<form method=\"post\" action=\"task.php?ticketID=";echo ($_GET["ticketID"]);echo "\">";
                        echo "<label for=\"type\">type:</label><input id=\"type\" name=\"type\" value=\"$task->type\" type=\"text\"><br>";
                        echo "<label for=\"state\">State:</label><input id=\"state\"  name=\"state\" value=\"$task->state\" type=\"text\"><br>";
                        echo "<label for=\"estimated_time\">Estimated time:</label><input id=\"estimated_time\" value=\"$task->estimated_time\" name=\"estimated_time\" type=\"text\"><br>";
                        echo "<label for=\"total_time\">Total time:</label><input id=\"total_time\" value=\"$task->total_time\" name=\"total_time\" type=\"text\"><br>";
                        echo "<label>Description:</label><br>";
                        echo "<textarea id=\"description\" name=\"description\" rows=\"10\" cols=\"50\">$task->description</textarea><br>";
                        echo "<input type=\"text\" value=\""; echo$_GET["id"]; echo "\" id=\"edit\" style=\"display: none;\" name=\"edit\">";
                        echo "<input type=\"submit\" value=\"Edit\" name=\"submit\">";
                        echo "</form>";
                    }
                    else echo "Task nenalezen";
				}
			}
            //////////////
            //Show task
            //////////////
			else if (isset($_GET["id"])){
				$task = \model\Task::getByID($_GET["id"], $db->connection);
				echo "<label>Type: $task->type</label><br>";
				echo "<label>State: $task->state</label><br>";
				echo "<label>Estimated time: $task->estimated_time</label><br>";
				echo "<label>Total_time: $task->total_time</label><br>";
                echo "<label>Description: $task->description</label><br>";
                $task->loadModels();
                if ($logged&&$_SESSION["role"]!="customer"){
                    $temp = $task->ticket->id;
                    echo "<a href=\"task.php?action=edit&id=";echo $_GET["id"]; echo "&ticketID=";echo ($temp); echo "\"><button>Edit</button></a>";
                    echo "<a href=\"task.php?id=";echo $_GET["id"]; echo "\"><button>Assign task</button></a>";//TODO
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
