<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"><head>
    <title>Единый черный список для владельцев собак</title>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" href="/img/favicon.ico" />
    <link rel="stylesheet" href="css/fonts.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/common.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/main.css" type="text/css" media="all">
    <script src="js/jquery.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/main.js" type="text/javascript" charset="utf-8"></script>
    <?php include "ga.php"?>
</head>
<body class="main">

<section class="top-question">
    <ul>
        <li>Сомневаетесь в новых хозяевах?</li>
        <li>Сомневаетесь в передержке?</li>
        <li>Вам позвонили, будто бы найдя вашу собаку?</li>
    </ul>
</section>

<section class="top">
    <section class="find">
        <h1>Найти упоминания номера телефона в черных списках</h1>
        <form method="GET" action="search">
            <div class="field">
                <span>+7</span>
                <input name="phone" type="text" placeholder="-номер-телефона"/>
            </div>
            <input type="submit" value="Искать">
        </form>
    </section>
    <section class="sites">
        <div>Поиск ведется по сайтам
            <ul>
                <?php foreach ($sites as $site) { ?>
                    <li><strong><a href="http://<?php h($site->domain) ?>"><?php h($site->domain)?></a></strong> <em><?php h($site->info)?></em></li>
                <?php }?>
            </ul>
        </div>
    </section>

</section>

<section class="questions">

    <div class="question">
        <h3>Как добавить телефонный номер в этот список?</h3>
        <div>
            <strong>Никак.</strong>
            <br/>Данный сайт не содержит собственно списка телефонов, он только упрощает поиск по некоторым сайтам, где может быть опубликована жалоба.
            Вы можете зарегистрироваться на этих сайтах и разместить свою жалобу в соответствующей теме. Если модераторы пропустят ваше сообщение, то оно вскоре будет доступно и здесь.
        </div>
    </div>

    <div class="question">
        <h3>Как убрать телефонный номер из этого списка?</h3>
        <div>
            Телефоны собираются автоматически с указанных сайтов. Данный сайт ничем не отличается от поисковых систем Google и Yandex и показывает только то, что есть на других сайтах.
            <br/>
            Поэтому вам нужно связать с администрацией сайта, на котором упомянут ваш телефон - ссылка на него всегда есть в результатах поиска.
        </div>
    </div>

    <div class="question">
        <h3>Кого можно найти в этом черном списке?</h3>
        <div>
            В основном следующие категории граждан:
            <ul>
                <li>Мошенники, вымогающие деньги за якобы найденных животных</li>
                <li>Недобросовестные хозяева, не умеющие обращаться с животными</li>
                <li>Недобросовестные передержки</li>
                <li>Неадекватные граждане</li>
            </ul>
            <strong>Но</strong> бывают случаи, когда телефон попадает сюда случайно. Как это понять?
            Просто прочтите внимательно тему, которая покажется в результате поиска, из текста обычно ясно,
            где телефон <em>нехорошего человека</em>, а где контактный номер <em>хорошего</em>.
        </div>
    </div>



    <div class="question">
        <h3>Как пополняется черный список?</h3>
        <div>Черный список пополняется автоматически с указанных сайтов, несколько раз в день.
            На текущий момент мы насчитали <a href="<?php e(Site::$list)?>"><?php e($total_count)?> телефонных номеров</a>
            признанных мошенническими.
        </div>
    </div>


    <div class="question">
        <h3>Можно ли задать вопрос/внести предложение?</h3>
        <div>
            Конечно. Напишите письмо на <?php m(Site::$mail_contact)?>, или ответьте в форме комментариев здесь внизу.
        </div>
    </div>

    <div class="question">
        <h3>Можно ли встроить поиск по черному списку на другом сайте?</h3>
        <div>
            Конечно. Вы можете разместить на странице в нужном месте следующий html код:
            <pre>&lt;script src="http://mnezvonil.info/js/iframe.js">&lt;/script></pre>
            Результат будет выглядеть примерно вот так:
            <div class="example">
                <!--script src="http://mnezvonil.info/js/iframe.js"></script-->
            </div>

        </div>
    </div>

    <div id="mc-container"  class="question center"></div>



    <script type="text/javascript">
        var mcSite = '3644';
        (function() {
            var mc = document.createElement('script');
            mc.type = 'text/javascript';
            mc.async = true;
            mc.src = 'http://cackle.ru/mc.widget-min.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(mc);
        })();
    </script>

</section>

<br class="clear"/>

<footer>
    2012 - 2014
</footer>

</body>
</html>