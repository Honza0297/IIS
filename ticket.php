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
        <div class="main">		
		<?php
			include_once "php/Database.php";
			include_once "php/model/Ticket.php";
			include_once "php/model/Product.php";
			include_once "php/model/Person.php";
			include_once "php/model/Attachment.php";
            include_once "php/model/Comment.php";

			$db = new Database();
			$db->getConnection();
			if (isset($_POST["submit"])&&isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true){
                //echo $_FILES["file"]["tmp_name"];
				if (isset($_POST["title"])&&$_POST["product"]&&$_POST["info"]){
					$ticket = new \model\Ticket($db->connection);
                    if (isset($_POST["edit"])) $ticket->id = $_POST["edit"];
					$ticket->title = $_POST["title"];
					$ticket->product = \model\Product::getByID($_POST["product"],$db->connection);
					$ticket->state = "In progress";
					$ticket->date_posted = date("Y-m-d");                    
                    if (isset($_POST["author"])) $ticket->author = \model\Person::getByID($_POST["author"],$db->connection);
                    else $ticket->author = \model\Person::getByID($_SESSION["id"],$db->connection); //TODO author
					$ticket->info = $_POST["info"];
					if ($ticket->save()) header( "Location: ticket.php?id=".$ticket->id);
				}
                if(!empty($_FILES["file"]["name"]))
                {
                    echo "Ukladam soubor";
                    $name = $_FILES["file"]["name"];
                    $dest = "uploads/". $ticket->id . "/";
                    $name_in_dest = $dest. $_FILES["file"]["name"];
                    mkdir($dest);
                    if(move_uploaded_file($_FILES["file"]["tmp_name"],$name_in_dest))
                    {

                        echo "Soubor ulozen";
                        //echo "<a href=\"$name_in_dest\">$name</a>";
                    }

                    $attachment = new \model\Attachment($db->connection);
                    $attachment->ticket = $ticket;
                    $attachment->filename = $_FILES["file"]["name"];
                    if ($attachment->save()){
                        echo "yespico";
                    }  else {
                        echo "fuck";
                    }
                }
			}
			else if (isset($_GET["action"])){			
				if ($_GET["action"]=="new"){
                    echo "<form method=\"post\" action=\"ticket.php\" enctype=\"multipart/form-data\">";
					echo "<label for=\"title\">Title:</label><input id=\"title\" name=\"title\" type=\"text\"><br>";
					echo "<label for=\"status\">Status:</label><input id=\"status\"  name=\"status\" type=\"text\"><br>";
					echo "<label for=\"product\">Product:</label><input id=\"product\"  name=\"product\" type=\"text\"><br>";
                    echo "<label for=\"attachment\">Attachment:</label> <input type=\"file\" name=\"file\"><br>"; //TODO: more files
					echo "<label>Info:</label><br>";
					echo "<textarea id=\"info\" name=\"info\" rows=\"10\" cols=\"50\"></textarea><br>";
					echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
					echo "</form>";
				}
				if ($_GET["action"]=="edit"&&isset($_GET["id"])){
                    $ticket = \model\Ticket::getByID($_GET["id"],$db->connection);
                    if ($ticket!=null){                        
                        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && (($ticket->author==$_SESSION["id"])||$_SESSION["role"]=="admin")){  
                                echo "<form method=\"post\" action=\"ticket.php\" enctype=\"multipart/form-data\">";
                                echo "<label for=\"title\">Title:</label><input value=\"$ticket->title\" id=\"title\" name=\"title\" type=\"text\"><br>";
                                echo "<label for=\"status\">Status:</label><input value=\"$ticket->state\" readonly=\"true\" id=\"status\"  name=\"status\" type=\"text\"><br>";
                                $ticket->loadModels();
                                $temp = $ticket->product->id;
                                echo "<label for=\"product\">Product:</label><input value=\"$temp\" readonly=\"true\" id=\"product\"  name=\"product\" type=\"text\"><br>";
                                echo "<label for=\"attachment\">Attachment:</label> <input type=\"file\" name=\"file\"><br>"; //TODO: more files
                                echo "<label>Info:</label><br>";
                                echo "<textarea id=\"info\" name=\"info\" rows=\"10\" cols=\"50\">$ticket->info</textarea><br>";
                                $temp=$_GET["id"];
                                echo "<input type=\"text\" value=\"$temp\" id=\"edit\" style=\"display: none;\" name=\"edit\">";
                                $temp = $ticket->author->id;
                                echo "<input type=\"text\" value=\"$temp\" style=\"display: none;\" id=\"author\" name=\"author\" >";
                                echo "<input type=\"submit\" value=\"Edit\" name=\"submit\">";
                                echo "</form>";
                                // 
                        }
                    }
                    else echo "Ticket nenalezen";
				}
			}		
			else if (isset($_GET["id"])){
				//$test =  new \model\Ticket($db->connection);
				$ticket = \model\Ticket::getByID($_GET["id"], $db->connection);
				if (isset($_POST["comment"])&&isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true){
					$comment = new \model\Comment($db->connection);
                    $comment->ticket = $ticket;
                    $comment->datePosted = date("Y-m-d");
                    $comment->author = \model\Person::getByID($_SESSION["id"],$db->connection);
                    $comment->text = $_POST["comment"];
                    if ($comment->save()) header( "Location: ticket.php?id=".$ticket->id);
                    else "oooops";
				}
				echo "<label>$ticket->title</label><br>";
				echo "<label>$ticket->state</label><br>";
				echo "<label>$ticket->product</label><br>";
				echo "<label>$ticket->info</label><br>";
				$temp = new \model\Attachment($db->connection);
				$attachments = $temp->getByTicketID($ticket->id);
				if(!empty($attachments))
                {
                    foreach ($attachments as $att)
                    {
                        $dest = "uploads/". $ticket->id . "/".$att->filename;
                        echo "<a href=\"$dest\">$att->filename</a>";
                    }
                }
                echo "<a href=\"ticket.php?action=edit&id=";echo $_GET["id"]; echo "\"><button>Edit</button></a>";
				echo "<form method=\"post\" action=\"ticket.php?id=";echo $_GET["id"]; echo "\">";
             
                $comment = new \model\Comment($db->connection);
                $comment->ticket = $ticket;
                $comments = $comment->findInDb();
                foreach ($comments as $comment){
                    echo "<label>Date:$comment->datePosted:$comment->text</label><br>";
                }
				echo "<textarea id=\"comment\" name=\"comment\" rows=\"10\" cols=\"50\"></textarea><br>";
				echo "<input type=\"submit\" value=\"Add comment\">";
				echo "</form>";
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
