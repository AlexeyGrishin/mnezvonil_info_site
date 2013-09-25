<?php include_once 'adminhelper.php'?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../../admin/css/admin.css"/>
    <title>Admin - phone <?php e($phone->id)?></title>
    <script src="../../js/jquery.js"></script>
    <script src="../../admin/js/admin.js"></script>
</head>
<body>

<?php info(); ?>


<section class="config-part whole">
    <h3>
        <form method="GET" action="../phone/">
            По номеру: <input name="phone"> <input type="submit"/>
        </form>
    </h3>
</section>

<?php
if ($phone != null) {
    $proofs = $phone->proofs();
    ?>
<section class="config-part whole">
    <h3>
        <a href="../<?php e($phone->id)?>">
        <?php e($phone->id)?>
        </a>
    </h3>
    <div>

        <?php foreach($proofs as $proof) {
            phone_proof($proof);
        }
        ?>

    </div>
    <div>
        <form method="POST">
            <input type="hidden" name="phone" value="<?php e($phone->id);?>"/>
            <label>
                <input name="resolution" type="radio" value="postpone" <?php if (!$phone->reviewed) e("checked") ?>/>
                Отложить
            </label>
            <br>
            <label>
                <input name="resolution" type="radio" value="bad" <?php if ($phone->reviewed && !$phone->marked_as_good) e("checked") ?>/>
                Подтвердить
            </label>
            <br>
            <label>
                <input name="resolution" type="radio" value="good" <?php if ($phone->reviewed && $phone->marked_as_good) e("checked") ?>/>
                Опровергнуть.
            </label>
            Доказательство: <input name="proof_of_good" value="<?php e($phone->proof_of_good)?>" />
            <br>
            <br>
            <input type="submit">
            <br>
        </form>
    </div>
</section>
    <?php
}
else {
    ?>
Телефон не существует
    <?php
}
?>

</body>
</html>