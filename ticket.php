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
            <nav>
                <a href="index.php"><img src="BigDuckBugYellow.png" alt="LOGO" class="logo"></a>
                <a href="search.php"><li>Search</li></a>
                <a href="ticket.php?action=new"><li>Create ticket</li></a>
            </nav>
            <personal>
                <li class="login" onclick="document.getElementById('id01').style.display='block'">Log in</li>
                <a href="profile.php?action=new"><li class="register">Register</li></a>
                <a href="profile.php"><li class="profile" >Profile</li></a>
            </personal>
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

			$db = new Database();
			$db->getConnection();
			if (isset($_POST["submit"])){
                //echo $_FILES["file"]["tmp_name"];
				if (isset($_POST["title"])&&$_POST["product"]&&$_POST["info"]){
					$ticket = new \model\Ticket($db->connection);
					$ticket->title = $_POST["title"];
					$ticket->product = \model\Product::getByID($_POST["product"],$db->connection);
					$ticket->state = "In progress";
					$ticket->date_posted = date("Y-m-d");
					$ticket->author = \model\Person::getByID(1,$db->connection);
					$ticket->info = $_POST["info"];
					if ($ticket->save()) echo "yespico"; else "fuck";
					header( "Location: ticket.php?id=".$ticket->id);
				}
                if(!empty($_FILES["file"]["name"]))
                {
                    $name = $_FILES["file"]["name"];
                    $dest = "uploads/". rtrim($_POST["title"]) . "/". $_FILES["file"]["name"]; //rtrim because spaces in the end makes mess
                    mkdir("uploads/". $_POST["title"] . "/");
                    if(move_uploaded_file($_FILES["file"]["tmp_name"],$dest))
                    {

                        echo "Soubor ulozen";
                        echo "<a href=\"$dest\">$name</a>";
                    }

                }
			}
			else if (isset($_GET["action"])){			
				if ($_GET["action"]=="new"||($_GET["action"]=="edit"&&isset($_GET["id"]))){
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
					//TODO
				}
			}		
			else if (isset($_GET["id"])){
				//$test =  new \model\Ticket($db->connection);
				$ticket = \model\Ticket::getByID($_GET["id"], $db->connection);
				if (isset($_POST["comment"])){
					echo $_POST["comment"];
				}
				echo "<label>$ticket->title</label><br>";
				echo "<label>$ticket->state</label><br>";
				echo "<label>$ticket->product</label><br>";
				echo "<label>$ticket->info</label><br>";
				echo "<label>Ziskam pozdeji</label><br>";			
				echo "<form method=\"post\" action=\"ticket.php?id=";echo $_GET["id"]; echo "\">";	
				echo "<textarea id=\"comment\" name=\"comment\" rows=\"10\" cols=\"50\"></textarea><br>";
				echo "<input type=\"submit\" value=\"Add comment\">";
				echo "</form>";
			}
		?>
	   </div>
</body>

<div id="id01" class="modal">

    <form class="modal-content animate" action="/action_page.php" method="post">
        <div class="imgcontainer">
            <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
            <img src="img_avatar2.png" alt="Avatar" class="avatar">
        </div>

        <div class="container">
            <label for="uname"><b>Username</b></label>
            <input type="text" class="UsPa" placeholder="Enter Username" name="uname" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" class="UsPa" placeholder="Enter Password" name="psw" required>

            <button type="submit">Login</button>
            <label>
                <input type="checkbox" checked="checked" name="remember"> Remember me
            </label>
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
            <span class="psw">Forgot <a href="#">password?</a></span>
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
