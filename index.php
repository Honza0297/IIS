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
                            echo "<a href=\"searchuser.php\"><li class=\"searchuser\" >Search user</li></a>";
                        }
                        echo "</nav>";
                        echo "<personal>";
                        $ref = "profile.php?id=".$_SESSION["id"];
                        echo "<a href=$ref><li class=\"profile\" >Profile</li></a>";
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
    <center>
        <div>
            <form class="search" action="search.php" method="get">
                <input type="text" name="title" placeholder="Search title..." multiple />
                <input type="submit" value="Search">
            </form>
        </div>
    </center>
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
