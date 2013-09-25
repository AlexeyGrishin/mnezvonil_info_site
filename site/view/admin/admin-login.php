<?php include_once 'adminhelper.php'?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../../admin/css/admin.css"/>
    <title>Admin - login</title>
</head>
<body class="login">
<section class="config-part">
    <h3>Вход в систему</h3>
    <div>
        <?php if ($error != "") { ?>
        <p class="error"><?php e($error)?></p>
        <?php }?>
        <form method="POST">
            <table>
                <tr>
                    <th>Имя</th><td><input name="name"/></td>
                </tr>
                <tr>
                    <th>Пароль</th><td><input name="password" type="password"/></td>
                </tr>
                <tr>
                    <td></td><td><input type="submit" value="Войти"/></td>
                </tr>
            </table>
        </form>
    </div>
</section>
</body>

</html>