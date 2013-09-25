<?php include_once 'adminhelper.php'?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../../admin/css/admin.css"/>
    <title>Admin - phones to check</title>
    <script src="../../js/jquery.js"></script>
    <script src="../../admin/js/admin.js"></script>
</head>
<body>
<?php if ($has_more) {?>
    <p class="notice">Показаны первые <?php e(count($phones))?> телефонов. После их разбора перезагрузите страницу чтобы увидеть остальные</p>
<?php } ?>
<?php if (count($phones) == 0) {?>
    <p class="notice">Необработанных телефонов нет</p>
<?php } ?>

<?php
$odd = true;
$lr = array(true => "left", false => "right");
foreach($phones as $phone => $proofs) {
    phone_control($phone, $proofs, $lr[$odd]);
    $odd = !$odd;
} ?>

</body>
</html>