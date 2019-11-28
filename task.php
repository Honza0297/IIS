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
                    if (isset($_POST["deadline"])) $task->deadline = $_POST["deadline"];
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
                    echo "<label for=\"deadline\">Estimated time:</label><input id=\"deadline\" value=\"0\" name=\"deadline\" type=\"text\"><br>";
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
                        $task->loadModels();
                        $temp = $task->ticket;
                        echo "<a href=\"ticket.php?id=$temp->id\">Ticket: $temp->title</a><br>";
                        $temp->loadModels();
                        $temp = $temp->product;
                        echo "<a href=\"product.php?id=$temp->id\">Product: $temp->name</a><br>";
                        echo "<label for=\"deadline\">Estimated time:</label><input id=\"deadline\" value=\"$task->deadline\" name=\"deadline\" type=\"text\"><br>";
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
			else if (isset($_GET["id"])&&$logged&&$_SESSION["role"]!="customer"){
				$task = \model\Task::getByID($_GET["id"], $db->connection);
				echo "<label>Type: $task->type</label><br>";
				echo "<label>State: $task->state</label><br>";                  
                $task->loadModels();
                $temp = $task->ticket;
                echo "<a href=\"ticket.php?id=$temp->id\">Ticket: $temp->title</a><br>";
                $temp->loadModels();
                $temp = $temp->product;
                echo "<a href=\"product.php?id=$temp->id\">Product: $temp->name</a><br>";
				echo "<label>Estimated time: $task->deadline</label><br>";
                echo "<label>Description: $task->description</label><br>";
                $task->loadModels();
                $temp = $task->ticket->id;
                echo "<a href=\"task.php?action=edit&id=";echo $_GET["id"]; echo "&ticketID=";echo ($temp); echo "\"><button>Edit</button></a>";
                echo "<button onclick=\"showhide()\">Assign task</button>";   
                $searchperson = new \model\Person ($db->connection);
                $persons = $searchperson->findInDB ();
                /*
                echo "<div id=\"persons\" style=\"display:none\">";
                foreach ($persons as $person){
                    if ($person->role!="admin"){
                        echo "<a href=\"task.php?action=assign&assignee=\"" TODO
                    }
                } 
                */
                echo "</div>";
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

    function showhide(){
        if (document.getElementById("persons").style.display == 'block'){
            document.getElementById("persons").style.display = 'none';
        }
        else {
            document.getElementById("persons").style.display = 'block';
        }
    }
</script>

</html>
