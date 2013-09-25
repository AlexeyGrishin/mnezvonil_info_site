<?php include_once 'adminhelper.php'?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../../admin/css/admin.css"/>
    <title>Admin - logs</title>
    <script src="../../js/jquery.js"></script>
    <script src="../../admin/js/admin.js"></script>
</head>
<body>

<h3>Журнал работы для <?php e($site_name);?></h3>

<table class="per-site">
    <tr>
        <th>Задача</th>
        <th>Дата запуска</th>
        <th>Время обработки</th>
        <th>Ошибок</th>
    </tr>
    <?php foreach ($logs as $log) {
    ?>
    <tr>
        <td><?php e($log->action)?></td>
        <td><?php e(strftime("%d %b, %A, %H:%M:%S", strtotime($log->start_date)))?></td>
        <td><?php e($log->duration/60)?> минут</td>
        <td class="<?php if ($log->warnings > 0) e("error");?>">
            <?php e($log->warnings);?>
        </td>
    </tr>
    <?php } ?>
</table>


</body>
</html>