<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $TEXT['Lang']; ?>" lang="<?php echo $TEXT['Lang']; ?>">
	<head>
		 <title><?php echo $TEXT['Page_Title']; ?></title>
		 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		 <meta http-equiv="Content-Language" content="<?php echo $TEXT['Lang']; ?>" />
		 <meta http-equiv="Content-Script-Type" content="text/javascript" />
		 <link href="style.css" rel="stylesheet" type="text/css" />
         <script src="js/jquery-1.7.1.min.js"></script>
	</head>

	<body>
        <div id="UserBar">
            <span id="Flags">
	            <a href="lang/language.php?lang=fr"><img src="images/french_flag.jpg" alt="Français"/></a>
	            <a href="lang/language.php?lang=en"><img src="images/english_flag.jpg" alt="Anglais"/></a>
            </span>

	        <?php echo $_SESSION['logon'] ?> | Département
        </div>

        <div id="Header">
        	<a href="index.php"><img src="images/logo_polytech.png" alt="logo Semaine du Numérique"/></a>
        </div>
