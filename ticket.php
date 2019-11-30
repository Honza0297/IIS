<?php
include_once "CustomElements.php";
setSession();
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
			include_once "php/model/Attachment.php";
            include_once "php/model/Comment.php";

			$db = new Database();
			$db->getConnection();
            $logged = isset($_SESSION["loggedin"])&&$_SESSION["loggedin"]===true;

			if (isset($_POST["submit"])&&$logged){
                $ticketID;
                //////////////////////
                //Ticket was submitted
                //////////////////////
				if (isset($_POST["title"])&&isset($_POST["product"])&&isset($_POST["info"])){
					$ticket = new \model\Ticket($db->connection);
                    //ticket was edited
                    if (isset($_POST["edit"])) $ticket->id = $_POST["edit"];
					$ticket->title = $_POST["title"];
					$ticket->product = \model\Product::getByID($_POST["product"],$db->connection);
					$ticket->state = $_POST["status"];
					$ticket->date_posted = date("Y-m-d");
                    if (isset($_POST["author"])) $ticket->author = \model\Person::getByID($_POST["author"],$db->connection);
                    else $ticket->author = \model\Person::getByID($_SESSION["id"],$db->connection); //TODO author
					$ticket->info = $_POST["info"];
                    $ticket->save();
                    $ticketID=$ticket->id;
				}
                if(!empty($_FILES["file"]["name"]))
                {
                    echo "Ukladam soubor";
                    $name = $_FILES["file"]["name"];
                    $dest = "uploads/". $ticket->id . "/";
                    $name_in_dest = $dest. $_FILES["file"]["name"];
                    mkdir($dest);
                    chmod($dest, 0755);
		    if(move_uploaded_file($_FILES["file"]["tmp_name"],$name_in_dest))
                    {

                        echo "Soubor ulozen";
			chmod($name_in_dest, 0644);
                        //echo "<a href=\"$name_in_dest\">$name</a>";
                    }

                    $attachment = new \model\Attachment($db->connection);
                    $attachment->ticket = $ticket;
                    $attachment->filename = $_FILES["file"]["name"];
		    $attachment->save();
                }
                if ($ticketID>0) redirect ("ticket.php?id=".$ticketID);
                else redirect ("ticket.php");
			}
            /////////////////////
            //Edit or New ticket
            /////////////////////
			else if (isset($_GET["action"])){	
                ///////////////////
                //New ticket
                /////////////////		
				if ($_GET["action"]=="new"){
				    list($productIDs, $productLabels) = prepareProducts($db, false);
                    echo "<form method=\"post\" action=\"ticket.php\" enctype=\"multipart/form-data\">";
					echo "<label for=\"title\">Title:</label><input id=\"title\" name=\"title\" type=\"text\">";
					echo "<label for=\"status\"></label><input id=\"status\" value=\"pending\" name=\"status\"style=\"display: none; type=\"text\"><br>";
					//nahrazeno listem echo "<label for=\"product\">Product:</label><input id=\"product\"  name=\"product\" type=\"text\"><br>";
                    ShowSelectElement($productIDs, $productLabels, "", "Product", "product"); echo "<br>";
                    echo "<label for=\"attachment\">Attachment:</label> <input type=\"file\" name=\"file\"><br>";
					echo "<label>Info:</label><br>";
					echo "<textarea id=\"info\" name=\"info\" rows=\"10\" cols=\"50\"></textarea><br>";
					echo "<input type=\"submit\" value=\"Create\" class='button' name=\"submit\">";
					echo "</form>";
				}
                //////////////////
                //EDIT Ticket
                //////////////////
				if ($_GET["action"]=="edit"&&isset($_GET["id"])){
                    $ticket = \model\Ticket::getByID($_GET["id"],$db->connection);
                    $ticket->loadModels();
                    if ($ticket!=null){
                        ///////////////////////////////////////
                        //Only author or admin can edit ticket
                        //////////////////////////////////////           
                        if ($logged&& (($ticket->author==$_SESSION["id"])||$_SESSION["role"]=="admin")){
                            list($productIDs, $productLabels) = prepareProducts($db, false);
                            echo "<form method=\"post\" action=\"ticket.php\" enctype=\"multipart/form-data\">";
                            echo "<label for=\"title\">Title:</label><input value=\"$ticket->title\" id=\"title\" name=\"title\" type=\"text\"><br>";
                            //echo "<label for=\"status\">Status:</label><input value=\"$ticket->state\" readonly=\"true\" id=\"status\"  name=\"status\" type=\"text\"><br>";
                            ShowSelectElement($statesNoEmpty, $statesNoEmpty, $ticket->state, "Status", "status"); echo "<br>";
                            $temp = $ticket->product->id;
                            //echo "<label for=\"product\">Product:</label><input value=\"$temp\" readonly=\"true\" id=\"product\"  name=\"product\" type=\"text\"><br>";
                            ShowSelectElement($productIDs, $productLabels, $ticket->product->id, "Product", "product"); echo "<br>";
                            echo "<label for=\"attachment\">Attachment:</label> <input type=\"file\" name=\"file\"><br>"; //TODO: more files
                            echo "<label>Info:</label><br>";
                            echo "<textarea id=\"info\" name=\"info\" rows=\"10\" cols=\"50\">$ticket->info</textarea><br>";
                            $temp=$_GET["id"];
                            echo "<input type=\"text\" value=\"$temp\" id=\"edit\" style=\"display: none;\" name=\"edit\">";
                            $temp = $ticket->author->id;
                            echo "<input type=\"text\" value=\"$temp\" style=\"display: none;\" id=\"author\" name=\"author\" >";
                            echo "<input type=\"submit\" value=\"Edit\" class=\"button\" name=\"submit\">";
                            echo "</form>";
                                // 
                        }
                    }
                    else echo "Ticket not found";
				}
			}
            /////////////////////
            //Show ticket
            ////////////////////
			else if (isset($_GET["id"])){
				$ticket = \model\Ticket::getByID($_GET["id"], $db->connection);
				$ticket->loadModels();
                /////////////////////////
                //Comment was posted
                ////////////////////////
				if (isset($_POST["comment"])&&$logged){
					$comment = new \model\Comment($db->connection);
                    $comment->ticket = $ticket;
                    $comment->datePosted = date("Y-m-d");
                    $comment->author = \model\Person::getByID($_SESSION["id"],$db->connection);
                    $comment->text = $_POST["comment"];
                    if ($comment->save()) redirect("ticket.php?id=".$ticket->id);
                    else redirect("ticket.php");
				}
				echo "<label>Title: $ticket->title</label><br>";
				echo "<label >Status: $ticket->state</label><br>";
				echo "<label >Product: " . $ticket->product->name . "</label><br>";
				echo "<label >Info: $ticket->info</label><br>";
				$temp = new \model\Attachment($db->connection);
				$attachments = $temp->getByTicketID($ticket->id);
				if(!empty($attachments))
                {
                    foreach ($attachments as $att)
                    {
                        $dest = "uploads/". $ticket->id . "/".$att->filename;
                        echo "<a style=\"margin-left:20px\" href=\"$dest\"><font color='blue'>$att->filename</font></a>";
                    }
                }
		echo"<br>";
                echo "<a href=\"ticket.php?action=edit&id=";echo $_GET["id"]; echo "\"><button>Edit</button></a>";
                if ($logged&&$_SESSION["role"]!="customer"){
                    echo "<a href=\"tasks.php?id=";echo $_GET["id"]; echo "\"><button>Tasks</button></a>";
                }
				echo "<form method=\"post\" action=\"ticket.php?id=";echo $_GET["id"]; echo "\">";
             
                ///////////////////////////
                //Print all comments
                //////////////////////
                $comment = new \model\Comment($db->connection);
                $comment->ticket = $ticket;
                $comments = $comment->findInDb();
                foreach ($comments as $comment){
                    $comment->loadModels();
                    $name = $comment->author->name;
                    $surname = $comment->author->surname;
                    echo "<label class='showlabel'>$comment->datePosted $name $surname:$comment->text</label><br>";
                }
				echo "<textarea id=\"comment\" name=\"comment\" rows=\"10\" cols=\"50\"></textarea><br>";
				echo "<input type=\"submit\" class='button' value=\"Add comment\">";
				echo "</form>";
			}
		?>
	   </div>
</body>

<?php logindiv(); ?>

</html>
