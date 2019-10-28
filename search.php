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
                <a href="search.php"><li>Browse</li></a>
                <a href="ticket.php"><li>Create ticket</li></a>
            </nav>
            <personal>
                <li class="login" onclick="document.getElementById('id01').style.display='block'">Log in</li>
                <a href="register.php"><li class="register">Register</li></a>
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
        <div class="advanced_search">
            <form action="search.php" method="get">
                <ul>
					<?php
						if (isset($_GET["title"])){							
							echo "<li><label for=\"title\">Title:</label><input name=\"title\" value=\"";
							echo $_GET["title"];
							echo "\" type=\"text\" /></li>";
						}
						else {							
							echo "<li><label for=\"title\">Title:</label><input name=\"title\" type=\"text\" /></li>";
						}
						if (isset($_GET["status"])){							
							echo "<li><label for=\"status\">Status:</label><input name=\"status\" value=\"";
							echo $_GET["status"];
							echo "\" type=\"text\" /></li>";
						}
						else {							
							echo "<li><label for=\"status\">Status:</label><input name=\"status\" type=\"text\" /></li>";
						}
						if (isset($_GET["product"])){							
							echo "<li><label for=\"product\">Product:</label><input name=\"product\" value=\"";
							echo $_GET["product"];
							echo "\" type=\"text\" /></li>";
						}
						else {							
							echo "<li><label for=\"product\">Product:</label><input name=\"product\" type=\"text\" /></li>";
						}
					?>
                    <li><input type="submit" value="search"/></li>
                </ul>
            </form>
        </div>
        <div class="main">
            <div class="ticket">
				<ul>
					ID, Title, Product<br>
					kratkej popis problemu...........
				</ul>
			</div>
            <div class="ticket">
				<ul>
					ID, Title, Product<br>
					kratkej popis problemu...........
				</ul>
			</div>
            <div class="ticket">
				<ul>
					ID, Title, Product<br>
					kratkej popis problemu...........
				</ul>
			</div>
            <div class="ticket">
				<ul>
					ID, Title, Product<br>
					kratkej popis problemu...........
				</ul>
			</div>
            <div class="ticket">
				<ul>
					ID, Title, Product<br>
					kratkej popis problemu...........
				</ul>
			</div>
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
