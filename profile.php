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
    <div class="main">
		<?php


        include_once "php/Database.php";
        include_once "php/model/Person.php";

        $db = new Database();
        $db->getConnection();
        
			if (isset($_POST["submit"])){

			    $person =  new model\Person($db->connection);
                $person->name = $_POST["name"];
                $person->surname = $_POST["surname"];
                $person->username = $_POST["username"];
                $person->password =  $_POST["password"];
                $person->role = "customer";
                if($person->username == null or $person->name == null or $person->surname == null or $person->password == null)
                {
                    echo "Vratte se tlacitkem zpet a vyplnte vsechny udaje prosim. \n";
                    exit();
                }
                $testperson = new model\Person($db->connection);
                $testperson->username = $person->username;
                $same_username_array = $testperson->findInDb();
                if (empty($same_username_array))
                {
                    $person->save(); //nova osoba ulozena
                    echo "Osoba registrovana uspesne. Prihlaste se prosim. :)\n";
                    exit();
                }
                else
                {
                    echo "Toto uzivatelske jmeno je jiz pouzivane. Prosim, vyberte jine.\n";

                }
			}
			else if (isset($_GET["action"])){			
				if ($_GET["action"]=="new"){			
					echo "<form method=\"post\" action=\"profile.php\">";
					echo "<label for=\"username\">Username:</label><input id=\"username\" name=\"username\" type=\"text\"><br>";
					echo "<label for=\"name\">Name:</label><input id=\"name\"  name=\"name\" type=\"text\"><br>";
					echo "<label for=\"surname\">Surname:</label><input id=\"surname\"  name=\"surname\" type=\"text\"><br>";
                    echo "<label for=\"password\">Password:</label><input id=\"password\"  name=\"password\" type=\"password\"><br>";
					echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
					echo "</form>";
				}

				else if ($_GET["action"]=="edit"){
                    echo "<form method=\"post\" action=\"profile.php\">";
                    echo "<label for=\"name\">Name:</label><input id=\"name\"  name=\"name\" type=\"text\"><br>";
                    echo "<label for=\"surname\">Surname:</label><input id=\"surname\"  name=\"surname\" type=\"text\"><br>";
                    echo "<label for=\"password\">Password:</label><input id=\"password\"  name=\"password\" type=\"password\"><br>";
                    echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
                    echo "</form>";
                   echo "todo";
				}
			}		
			else if (isset($_GET["id"])){//TODO kdyz prihlasenej
			    $current_person = \model\Person::getByID($_GET["id"], $db->connection);
			    if ($current_person == null)
                {
                    echo "Nepodarilo se stahnout data. Prosim kontaktuje spravce.\n";
                    exit();
                }

				echo "<label>Username: $current_person->username</label><br>";
				echo "<label>Name: $current_person->name</label><br>";
				echo "<label>Surname: $current_person->surname</label><br>";
				echo "<label>Role: $current_person->role</label><br>";
			}
		?>
        <!--<form class="profile">
            <li><label for="name">Name:</label><input name="name" type="text" /></li>
            <li><label for="role">Role:</label><input name="role" type="text" /></li>
            <li><label for="product">Product:</label><input name="product" type="text" /></li>
            <li><input type="submit" value="change" /></li>
        </form>-->
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
