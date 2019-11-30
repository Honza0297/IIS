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

                use model\Work_on_tasks;

                include_once "CustomElements.php";
                ShowHeader();
                ?>
        </ul>
    </header>
    <br>
    <div class="main">
		<?php


        include_once "php/Database.php";
        include_once "php/model/Person.php";
        include_once "php/model/Work_on_tasks.php";
        include_once "php/model/Task.php";
        include_once "php/model/Ticket.php";
        $db = new Database();
        $db->getConnection();
        
			if (isset($_POST["submit"])){
                $person =  new model\Person($db->connection);
                if(isset($_POST["id"]))
                {
                 $person->id = $_POST["id"];
                }
                $person->name = $_POST["name"];
                $person->surname = $_POST["surname"];
                $person->username = $_POST["username"];
                if(isset($_POST["password"]))
                {
                    $person->password =  $_POST["password"];
                }
                if(isset($_POST["role"]) && isset($_SESSION["role"]) && $_SESSION["role"] == "admin")
                {
                    $person->role = $_POST["role"];
                }
                else
                {
                    $person->role = "customer";
                }

                if($person->username == null or $person->name == null or $person->surname == null or $person->password == null)
                {
                    echo "Vratte se tlacitkem zpet a vyplnte vsechny udaje prosim. \n";
                    exit();
                }
                if(isset($_POST["id"]))
                {
                    echo $person->id;
                    echo $person->name;
                    echo $person->surname;
                    echo $person->username;
                    echo $person->password;
                    echo $person->role;

                    if ($person->save()) //nova osoba ulozena
                        echo "Zmeny byly ulozeny\n";
                    else
                        echo "Nepovedlo ulozit novou osobu do databaze.";
                }
                else
                {
                    $same_username = \model\Person::getByName($person->username, $db->connection);
                    if ($same_username == null)
                    {
                        if($person->save()) //nova osoba ulozena
                            echo "Osoba registrovana uspesne. Prihlaste se prosim. :)\n";
                        else

                            echo "Registration failed.";

                        exit();
                    }
                    else
                    {
                        echo "Toto uzivatelske jmeno je jiz pouzivane. Prosim, vyberte jine.\n";

                    }
                }

			}
			else if (isset($_GET["action"])){			
				if ($_GET["action"]=="new"){			
					echo "<form method=\"post\" action=\"profile.php\">";
					echo "<label for=\"username\">Username:</label><input id=\"username\" name=\"username\" type=\"text\"><br>";
					echo "<label for=\"name\">Name:</label><input id=\"name\"  name=\"name\" type=\"text\"><br>";
					echo "<label for=\"surname\">Surname:</label><input id=\"surname\"  name=\"surname\" type=\"text\"><br>";
                    echo "<label for=\"password\">Password:</label><input id=\"password\"  name=\"password\" type=\"password\"><br>";
                    if(isset($_SESSION['role']) and $_SESSION['role'] == "admin")
                    {
                        ShowSelectElement($rolesNoEmpty, $rolesNoEmpty, "customer", "Role", "role"); echo "<br>";
                    }
                    else
                    {
                        echo "<label for=\"role\">Role:</label><input id=\"role\" value='customer' name=\"role\" type=\"text\" disabled><br>";
                    }

					echo "<input type=\"submit\" class='button' value=\"Create\" name=\"submit\">";
					echo "</form>";
				}

				else if ($_GET["action"]=="edit"){
				    if(isset($_GET["userid"]))
                    {
                        echo "Editing other user<br>";
                        $person = \model\Person::getByID($_GET["userid"], $db->connection);
                    }
				    else
                    {
                        $person = \model\Person::getByID($_SESSION["id"], $db->connection);
                    }
                    echo "<form method=\"post\" action=\"profile.php\">";
				    if($_SESSION["role"] == "admin")
                    {
                        echo "<label for=\"username\">Username:</label><input id=\"username\"  name=\"username\" type=\"text\" value=$person->username /><br>";
                    }
                    echo "<label for=\"name\">Name:</label><input id=\"name\"  name=\"name\" type=\"text\" value=$person->name /><br>";
                    echo "<label for=\"surname\">Surname:</label><input id=\"surname\"  name=\"surname\" type=\"text\" value=$person->surname /> <br>";
                    echo "<label for=\"password\">Password:</label><input id=\"password\"  name=\"password\" type=\"password\" value=$person->password /><br>";

                    if($_SESSION["role"] == "admin")
                    {
                        ShowSelectElement($roles, $roles, $person->role, "Role", "role"); echo "<br>";
                    }
                    else
                    {
                        echo "<label for=\"role\">Role:</label><input id=\"role\"  name=\"role\" type=\"text\" disabled='true' value=$person->role /> <br>";
                    }
                    if (isset($_GET["userid"]))
                        $id = $_GET["userid"];
                    else
                        $id = $_SESSION["id"];

                    echo "<input id=\"id\"  name=\"id\" type=\"id\"  hidden=\"true\" value=$id /><br>";
                    echo "<input class='button' type=\"submit\" value=\"Save changes\" name=\"submit\">";
                    echo "</form>";
				}
			}		
			else if (isset($_GET["id"])){

                $current_person = \model\Person::getByID($_GET["id"], $db->connection);
                if ($current_person == null)
                {
                    echo "Nepodarilo se stahnout data. Prosim kontaktuje spravce.\n";
                    exit();
                }

                echo "<label class='info' >Username:</label><label class='info' >$current_person->username</label><br>";
                echo "<label class='info' >Name:</label><label class='info' >$current_person->name</label><br>";
                echo "<label class='info' >Surname:</label><label class='info' >$current_person->surname</label><br>";
                echo "<label class='info' >Role:</label><label class='info' >$current_person->role</label><br>";
                if($_SESSION["id"] == $_GET["id"])
                {
                    echo "<a href='profile.php?action=edit'><button>EDIT</button></a><br>";
                }
			    else if($_SESSION["role"] == "admin")
                {

                    echo "<a href='profile.php?action=edit&userid=";
                    echo $_GET["id"];
                    echo "'><button>EDIT</button></a><br>";
                }
                echo "<label class='showlabel' >My tasks:</label><br>";
			    $tasks = Work_on_tasks::getTasks($current_person->id, $db->connection);
			    if($tasks != null)
                {
                    foreach($tasks as $item) {
                        print_task($item);
                    }
                }
		if($_SESSION["role"] == "manager")
                {
                    echo "<label class='info' >My products and its tickets:</label><br>";
                    $product = new \model\Product($db->connection);
                    $product->manager = new \model\Person($db->connection);
                    $product->manager->id = $_SESSION["id"];
                    $products = $product->findInDb();
                    if($products != null)
                    {
                        foreach ($products as $pro) {
                        print_product_basic($pro);
                        $ticket = new \model\Ticket($db->connection);
                        $ticket->product = $pro;
                        $tickets = $ticket->findInDb();
                        if($tickets != null)
                        {
                            echo"<div style='margin-left: 50px'>";
                            foreach ($tickets as $tic) {
                                print_ticket_simple($tic);
                            }
                            echo"</div>";
                        }
                        }

                    }

                }
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
