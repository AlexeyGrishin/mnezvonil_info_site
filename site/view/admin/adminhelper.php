<?php


function for_each_known_site($fname) {
    array_walk($known_sited, $fname);
}

function error($error) {
    if (!array_key_exists('errors', $_SESSION)) {
        $_SESSION['errors'] = array();
    }
    $_SESSION['errors'][] = $error;
}

function notice($notice) {
    if (!array_key_exists('notices', $_SESSION)) {
        $_SESSION['notices'] = array();
    }
    $_SESSION['notices'][] = $notice;
}

function info() {
    show_info("notices", "notice");
    show_info("errors", "error");
}

function show_info($type, $class) {
    $info = $_SESSION[$type];
    foreach ($info as $n) {
        ?>
            <p class="<?php e($class);?>"><?php h($n)?></p>
            <?php
    }
    $_SESSION[$type] = array();
}

function phone_control($phone, $proofs, $class = "") {

    ?>

    <section class="config-part <?php e($class)?>" id="<?php e($phone)?>">
        <h3>
            <?php e($phone) ?>
        </h3>
        <span class="buttons">
            <button class="approve">Подтвердить</button>
            <button class="reject">Опровергнуть</button>
            <button class="invalid">Ошибка</button>
            <button class="contact">Контакт</button>
        </span>
        <div>
            <?php foreach($proofs as $proof) {
                phone_proof($proof);
            }?>
        </div>
    </section>
        <?php
}

function phone_proof($proof) {
    ?>
        <a class="proof-link" href="<?php e($proof->url)?>" id="<?php e($proof->id);?>">
            <span class="buttons">
            <?php e($proof->url)?>
            <button class="approve-all">Подтвердить все телефоны</button>
            <?php if (!$proof->removed) {?>
            <button class="remove">Удалить</button>
                <?php } else { ?>
            <button class="restore">Восстановить</button>
                <?php } ?>
            </span>
        </a>
        <div class="proof">
            <?php e(highlight_phone($proof->description, $proof->phone_id)); ?>
        </div>

        <?php
}

