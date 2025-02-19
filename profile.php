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
        include_once "php/model/Product.php";
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
                    /*echo $person->id;
                    echo $person->name;
                    echo $person->surname;
                    echo $person->username;
                    echo $person->password;
                    echo $person->role;*/
                    echo "Go back and fill in all required arguments please. \n";
                }
                if(isset($_POST["id"]))
                {
                    /*echo $person->id;
                    echo $person->name;
                    echo $person->surname;
                    echo $person->username;
                    echo $person->password;
                    echo $person->role;*/

                    if ($person->save()) //nova osoba ulozena
                        echo "Changes were saved successfuly.\n";
                    else
                        echo "Changes were NOT saved! Please go back and try again.\n";
                }
                else
                {
                    $same_username = \model\Person::getByName($person->username, $db->connection);
                    if ($same_username == null)
                    {
                        if($person->save()) //nova osoba ulozena
                            echo "User registered successfully. :)\n";
                        else

                            echo "Registration failed.";
                    }
                    else
                    {
                        echo "This username is used already. Please, go back and choose another.\n";

                    }
                }

			}
			else if (isset($_GET["action"])){			
				if ($_GET["action"]=="new"){			
					echo "<form method=\"post\" action=\"profile.php\">";
					echo "<label for=\"username\">Username*:</label><input id=\"username\" name=\"username\" type=\"text\"><br>";
					echo "<label for=\"name\">Name*:</label><input id=\"name\"  name=\"name\" type=\"text\"><br>";
					echo "<label for=\"surname\">Surname*:</label><input id=\"surname\"  name=\"surname\" type=\"text\"><br>";
                    echo "<label for=\"password\">Password*:</label><input id=\"password\"  name=\"password\" type=\"password\"><br>";
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
					echo "All fields with * are required.\n";
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
				    else
                    {
                        echo "<label for=\"username\">Username:</label><input id=\"username\"  name=\"username\" class='hidden' type=\"text\" value=$person->username /><br>";
                    }
                    echo "<label for=\"name\">Name:</label><input id=\"name\"  name=\"name\" type=\"text\" value=$person->name /><br>";
                    echo "<label for=\"surname\">Surname:</label><input id=\"surname\"  name=\"surname\" type=\"text\" value=$person->surname /> <br>";
                    echo "<label for=\"password\">Password:</label><input id=\"password\"  name=\"password\" type=\"password\" value=$person->password /><br>";

                    if($_SESSION["role"] == "admin")
                    {
                        ShowSelectElement($rolesNoEmpty, $rolesNoEmpty, $person->role, "Role", "role"); echo "<br>";
                    }
                    else
                    {
                        echo "<label for=\"role\">Role:</label><input id=\"role\"  name=\"role\" type=\"text\" class='hidden' value=$person->role /> <br>";
                    }
                    if (isset($_GET["userid"]))
                        $id = $_GET["userid"];
                    else
                        $id = $_SESSION["id"];

                    echo "<input id=\"id\"  name=\"id\" type=\"id\"  class='hidden'  value=$id /><br>";
                    echo "<input class='button' type=\"submit\" value=\"Save changes\" name=\"submit\">";
                    echo "</form>";
				}
				else if ($_GET["action"]=="delete")
                {
                    if(isset($_GET["userid"]))
                    {
                        $person = \model\Person::getByID($_GET["userid"], $db->connection);

                    }
                    else
                    {
                        $person = \model\Person::getByID($_SESSION["id"], $db->connection);
                    }
                    if($person != null and $person->delete())
                    {
                        echo "Person deleted successfully.\n";
                        if(!isset($_GET["userid"])) //mažu sám sebe
                        {
                            unset($_SESSION["loggedin"]);
                            unset($_SESSION["id"]);
                            unset($_SESSION["role"]);
                            redirect("index.php");
                        }

                    }
                    else
                    {
                        echo "Something happened, person cannot be deleted.\n";
                    }

                }
			}		
			else if (isset($_GET["id"])){

                $current_person = \model\Person::getByID($_GET["id"], $db->connection);
                if ($current_person == null)
                {
                    echo "Cannot download data. Please contact admin..\n";
                }

                echo "<label class='info' >Username:</label><label class='info' >$current_person->username</label><br>";
                echo "<label class='info' >Name:</label><label class='info' >$current_person->name</label><br>";
                echo "<label class='info' >Surname:</label><label class='info' >$current_person->surname</label><br>";
                echo "<label class='info' >Role:</label><label class='info' >$current_person->role</label><br>";
                if($_SESSION["id"] == $_GET["id"])
                {
                    echo "<a href='profile.php?action=edit'><button>EDIT</button></a><br>";
                    echo "<button onclick=\"showhideremove()\" style='background: #f44336'>REMOVE</button><br>";
                    echo "<div id=\"idremove\" style=\"display:none\">ARE YOU SURE?<br>";
                    echo "<a href='profile.php?action=delete'><button style='background: #f44336'>REMOVE</button></a><br>";
                    echo "<button onclick=\"showhideremove()\">cancel</button><br>";
                    echo "</div>";
                }
			    else if($_SESSION["role"] == "admin")
                {
                    echo "<a href='profile.php?action=edit&userid=";
                    echo $_GET["id"];
                    echo "'><button>EDIT</button></a><br>";

                    echo "<a href='profile.php?action=delete&userid=";
                    echo $_GET["id"];
                    echo "'><button style='background: #f44336'>REMOVE</button></a><br>";
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
    </div>
</body>
<?php logindiv(); ?>
<script>
function showhideremove(){
    if (document.getElementById("idremove").style.display == 'block'){
        document.getElementById("idremove").style.display = 'none';
    }
    else {
        document.getElementById("idremove").style.display = 'block';
    }
}
</script>
</html>
