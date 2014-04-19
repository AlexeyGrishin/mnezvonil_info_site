<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"><head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" href="/img/favicon.ico" />
	<link rel="stylesheet" href="/css/common.css?1" type="text/css" media="all">
	<link rel="stylesheet" href="/css/result.css?1" type="text/css" media="all">
    <title>Единый черный список для владельцев собак - полный список</title>
    <?php include "ga.php"?>
</head>
<body>
    <section class="find">
        <a class="home" href="<?php e(Site::index())?>">&lt;&lt; На главную</a>
    </section>

<section class="full-list">
    <ul>
    <?php foreach ($phones as $phone) { ?>
            <li>
                <a href="<?php e(Site::phone($phone))?>"> <?php e($phone)?> </a>
            </li>
    <?php } ?>
    </ul>
</section>

</body>

</html>