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
    <center>
        <div>
            <div class="imagebox"><img src="BigDuckBugBlack.png" alt="kacenka"></div>
            <form class="search" action="search.php" method="get">
                <input class="indexinput" type="text" name="title" placeholder="Search title..." multiple />
                <input type="submit" class="indexbutton" value="Search">
            </form>
        </div>
    </center>
</body>

    <?php logindiv(); ?>

</html>
