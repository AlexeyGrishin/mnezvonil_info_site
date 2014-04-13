<?php $class="found"; ?>
<?php Templator::capture("body") ?>

<?php
    $single_group = count($grouped) == 1;
    foreach ($grouped as $group) {
        $infos = $group['info'];
        $has_info = count($infos);
        $klass = $has_info ? 'bad' : 'good';
        ?>
        <section class="result-group <?php e($klass) ?>">
            <h3>
                <?php iff($group['scope'], "<span>" . $group['scope']. "</span>") ?>
                <?php e($group['result']) ?>
            </h3>
            <!--
            <?php if (!$single_group) { ?>
            <h3>
                <?php iff($group['code'], "<code>" . $group['code']. "</code>") ?>
                <?php h($group['phone']) ?>
                <?php iff($group['code'], "<span>" . $group['scope']. "</span>") ?>
            </h3>
            <?php }?>
            -->
            <?php
                if ($has_info) {
                    foreach ($infos as $proof) {
                        ?>
                        <section class="single-result">
                            <a class="source" href="<?php h($proof->url)?>" target="_blank">Ссылка на источник</a>
                            <h4><a href="<?php h($proof->url)?>" target="_blank">Черный список на сайте <?php h($proof->site_name) ?></a></h4>
                            <?php if (could_be_cut($proof->description)) { ?>
                            <div class="summary">
                                <?php e(highlight_phone_and_cut($proof->description, $proof->phone_id)) ?>
                            </div>
                            <?php } ?>
                            <div class="full">
                                <?php e(highlight_phone($proof->description, $proof->phone_id)) ?>
                            </div>
                        </section>
                        <?php
                    }
                }
            else {
                ?>
                <section class="single-result no-result">
                    <div class="not-found">Упоминания не найдены</div>
                </section>
            <?php
            }
            ?>
        </section>
        <?php
    }
?>

<section class="bottom">
    &nbsp;
</section>

<?php Templator::capture("questions") ?>

<section class="question left">
    <h3>Что из этого следует?</h3>
    <div>
        Лишь то, что данный номер засветился на одном из сайтов в теме для жалоб. Внимательно прочтите показанные сообщения,
        перейдите по ссылкам на соответствующие сайты и прочтите дискуссию. Возможно, для указанного обвинения уже есть опровержение.
        <br/>
        Однако вероятность ошибки довольно низка, и если вас одолевают сомнения, как минимум отнеситесь к звонку с этого номера настороженно.
    </div>
</section>
<section class="question right">
    <h3>Что делать, если это ваш номер/номер вашего знакомого/номер уважаемого человека!</h3>
    <div>
        Если ваш телефон был собран по ошибке, или на одном из сайтов указана неверная информация о вас, то добейтесь удаления записей на тех сайтах или публикации опровержения. Данный сайт ничем не отличается от поисковых систем Google и Yandex и показывает только то, что есть на других сайтах.
        Если вы предприняли эти действия, а телефон по-прежнему отмечен как мошеннический, то пришлите письмо на ящик <?php m(Site::$mail_delete)?>. В письме обязательно укажите номер телефона и причину, по которой он должен быть удален.
    </div>
</section>
<section class="question left">
    <h3>Что делать если поступил звонок с этого номера!</h3>
    <div>
        Не волнуйтесь. Прочтите то, что написано в сообщениях, и ответьте на вызов. Если поведение соответствует описанию, прерывайте разговор,
        сославшись на важные дела или невозможность удовлетворения просьбы звонящего. Не вступайте в дискуссию - "разводилы" могут-таки вас развести, это их особый талант,
        а неадекватные личности могут испортить вам настроение.
    </div>
</section>


<?php Templator::capture() ?>
