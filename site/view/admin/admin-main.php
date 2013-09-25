<?php include_once 'adminhelper.php'?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../admin/css/admin.css"/>
    <title>Admin - main</title>
    <script src="../js/jquery.js"></script>
    <script src="../admin/js/admin.js"></script>
</head>
<body>
<section class="config-part left">
    <h3>Телефоны</h3>
    <div>
        <?php if ($phones_count == 0) { ?>
        <span>Нет непроверенных телефонов</span>
        <?php } else { ?>
        <a href="<?php e(Site::$admin_phones);?>">Непроверенных телефонов - <?php e($phones_count);?></a>
        <?php } ?>
    </div>
    <?php if ($phones_without_proofs > 0) {?>
    <div>
        Телефонов без ссылок - <?php e($phones_without_proofs)?>.
        <span class="buttons">
            <button class="delete-without-proofs">Удалить телефоны без ссылок</button>
        </span>
    </div>
    <?php } ?>
    <div>
        <form method="GET" action="phone/">
            По номеру: <input name="phone"> <input type="submit"/>
        </form>
    </div>
</section>

<section class="config-part right">
    <h3>Журнал работы</h3>
    <div>
        <table class="per-site">
            <tr>
                <th>Сайт</th>
                <th>Дата последнего запуска</th>
                <th>Последняя задача</th>
                <th>Последнее время обработки</th>
            </tr>
            <?php foreach ($logs as $log_entry) {
            $log = $log_entry[1];
            $site = $log_entry[0];
            $site_id = $log_entry[2];
            ?>
            <tr>
                <td class="site">
                    <a href="<?php e(Site::logs($site_id));?>">
                        <?php e($site)?>
                    </a>
                </td>
                <td><?php e(strftime("%d %b, %A, %H:%M:%S", strtotime($log->start_date)))?></td>
                <td><?php e($log->action)?></td>
                <td><?php e($log->duration/60)?> минут</td>
            </tr>
            <?php } ?>
        </table>
    </div>
</section>

</body>
</html>