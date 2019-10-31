<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta name="robots" content="noindex; nofollow">
    <link type="text/css" rel="stylesheet" href="style.css">
    <title>ITS</title>
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
                <a href="profile.php"><li class="profil" >Profil</li></a>
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
			if (isset($_POST["submit"])){
				echo $_POST["title"];
				echo $_POST["product"];
				echo $_POST["info"];
			}
			else if (isset($_GET["action"])){			
				if ($_GET["action"]=="new"){			
					echo "<form method=\"post\" action=\"profil.php\">";
					echo "<label for=\"title\">Title:</label><input id=\"title\" name=\"title\" type=\"text\"><br>";
					echo "<label for=\"status\">Status:</label><input id=\"status\"  name=\"status\" type=\"text\"><br>";
					echo "<label for=\"product\">Product:</label><input id=\"product\"  name=\"product\" type=\"text\"><br>";
					echo "<label>Info:</label><br>";
					echo "<textarea id=\"info\" name=\"info\" rows=\"10\" cols=\"50\"></textarea><br>";
					echo "<input type=\"submit\" value=\"Create\" name=\"submit\">";
					echo "</form>";
				}
				else if ($_GET["action"]=="edit"){
					//TODO
				}
			}		
			else if (isset($_GET["id"])){//TODO kdyz prihlasenej
				echo "<label>Title:Ziskam pozdeji</label><br>";
				echo "<label>Status:Ziskam pozdeji</label><br>";
				echo "<label>Product:Ziskam pozdeji</label><br>";
				echo "<label>Info:Ziskam pozdeji</label><br>";
				echo "<label>Ziskam pozdeji</label><br>";			
				echo "<form method=\"post\" action=\"ticket.php?id=";echo $_GET["id"]; echo "\">";	
				echo "<textarea id=\"comment\" name=\"comment\" rows=\"10\" cols=\"50\"></textarea><br>";
				echo "<input type=\"submit\" value=\"Add comment\">";
				echo "</form>";
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

    <form class="modal-content animate" action="/action_page.php" method="post">
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
