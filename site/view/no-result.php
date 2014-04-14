<?php $class="not-found"; ?>
<?php Templator::capture("body") ?>

<section class="result-group good">
    <h3>
        нет упоминаний
    </h3>
    <section class="single-result no-result">
         <div class="not-found">Упоминаний на известных нам сайтах не обнаружено</div>
    </section>
</section>
<section class="bottom">
    &nbsp;
</section>

<?php Templator::capture("questions") ?>

<section class="question">
    <h3>Что из этого следует?</h3>
    <div>
        Лишь то, что никто еще не разместил жалобу на этот номер на известных нам сайтах.
    </div>
</section>
<section class="question right">
    <h3>Что делать, если номер все-таки оказался мошенническим</h3>
    <div>
        Разместите информацию об этом на одном из известных нам сайтов. Вскоре после этого номер окажется здесь.
        <ul>
            <?php foreach ($sites as $site) { ?>
            <li><?php h($site->domain)?> &mdash; <?php h($site->info)?></li>
            <?php }?>
        </ul>
    </div>
</section>


<?php Templator::capture() ?>
