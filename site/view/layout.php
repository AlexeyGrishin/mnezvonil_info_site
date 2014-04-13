<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"><head>
    <title><?php e($phone)?> - единый черный список для владельцев собак</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="../css/common.css" type="text/css" media="all">
	<link rel="stylesheet" href="../css/result.css" type="text/css" media="all">
	<script src="../js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/main.js" type="text/javascript" charset="utf-8"></script>
    <?php include "ga.php"?>
</head>
<body class="results">
    <section class="find">
        <a href="<?php e(Site::index()) ?>" class="home">&lt;&lt; На главную</a>
        <h1>Проверьте номер телефона в черном списке</h1>
        <form method="GET" action="<?php e(Site::index())?>search">
            <div class="field">
                <span>+7</span>
                <input name="phone" type="text" size="16" maxlength="20" value="<?php h($phone) ?>" />
            </div>
            <input type="submit" value="Проверить">
        </form>
    </section>

    <h1>Результаты поиска <q><?php h($phone) ?></q> по черным спискам</h1>
    <?php Templator::insert("body"); ?>
    <section class="questions">

        <?php Templator::insert("questions"); ?>
    </section>
    <br class="clear"/>
    <footer>
        2012 - 2014
    </footer>

</body>
</html>