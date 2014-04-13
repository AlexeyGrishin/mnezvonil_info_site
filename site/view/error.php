<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"><head>
    <title>Единый черный список для владельцев собак - неизвестная ошибка</title>
    <link rel="shortcut icon" href="/img/favicon.ico" />
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/common.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/main.css" type="text/css" media="all">
    <?php include "ga.php"?>
</head>
<body class="error">

<p>Произошла неизвестная ошибка.</p>
<p>Мы уже разбираемся. Извините за досадную оплошность.</p>

<?php if ($dev) { ?>
<pre>
    <?php e($error); ?>
</pre>
<?php } ?>

</body>
</html>