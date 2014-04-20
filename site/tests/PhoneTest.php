<?php

include_once '../services/Phone.php';

function test_find_phones($txt) {
    print_r($txt);
    print_r("\n");
    print_r(array_map("normalize_phone", find_phones($txt)));
    print_r("\n");
}
test_find_phones("ЧС. 952 356 48 82
");
test_find_phones('ЧС 575-16-80
<strong>Звонят мне второй раз. ');
test_find_phones('Добавлю от себя:<br>8-951-7464485 и 8-981-4443527<br>Стандартный развод с платежом на мобилу...');
test_find_phones('<div class="postbody">Надя 89627083868<br />перекупщица.<br />продает крошечных щенков у метро ветеранов, породистых и метисов. выпускает непривитых малявок на траву. В разговоре - практически не подкопаться... <img src="./images/smilies/sad.gif" alt=":(" title="" />  Получила телефон, пробила по яндексу. Меняла щенка папильона на взрослую девочку йорка или чиха. Отдавала щенков немецкой овчарки (почему-то написано &quot;отдам&quot;, правда). Сейчас вот цверги, мопс и метисы спаниеля с болонкой...<br /><br />Вот щены:<br /><a href="http://radikal.ru/F/s48.radikal.ru/i120/1005/df/c55c0179c974.jpg.html" class="postlink"><img src="http://s48.radikal.ru/i120/1005/df/c55c0179c974t.jpg" alt="Изображение" /></a><a href="http://radikal.ru/F/s39.radikal.ru/i085/1005/57/37623606329e.jpg.html" class="postlink"><img src="http://s39.radikal.ru/i085/1005/57/37623606329et.jpg" alt="Изображение" /></a><a href="http://radikal.ru/F/s54.radikal.ru/i145/1005/aa/129c5bd09f31.jpg.html" class="postlink"><img src="http://s54.radikal.ru/i145/1005/aa/129c5bd09f31t.jpg" alt="Изображение" /></a><br /><br />Этот мне показался больным...<br /><a href="http://radikal.ru/F/s57.radikal.ru/i156/1005/aa/2fd8caddaed1.jpg.html" class="postlink"><img src="http://s57.radikal.ru/i156/1005/aa/2fd8caddaed1t.jpg" alt="Изображение" /></a><br />На мое замечание стала заверять, что глаза текут исключительно от ветра. Но щен вялый очень, по виду явно нездоров.<br /><br />Может я и не права, но по моему все это говорит о том, что она перекупщица... <img src="./images/smilies/sad.gif" alt=":(" title="" /></div>');

test_find_phones("он 8911 989 30 28");
test_find_phones("8-906-250-78-72\n
8-960-285-84-82\n
8-905-264-60-33\n");
test_find_phones("7909-598-79-97");
test_find_phones("+79062760911");
test_find_phones("8906 276 09 11");
test_find_phones("921-755-72-46");
test_find_phones("ЧС: 8-921-904-11-02 и 8-904-338-97-14");
test_find_phones("Крыленко,29. Алла. 89522347749,89523697163. Люди,имейте сострадание-перестаньте нести ей своих и чужих животных..смерть");
test_find_phones("ЧС: (812) 296-49-70");
test_find_phones("ЧС: 785-35-73, 8-950-023-91-54");
test_find_phones("Именнно этой женщине я отвела Марфушу, идеальную собаку.viewtopic.php?f=2&t=63516\n" .
    "А на следующее утро, муж нашёл её около нашего дома ( за 2 км )\n" .
    "со срезанным поводком.Мне никаких звонков.\n" .
    "Когда ей позвонили, спокойно завтракала дома, сказала:\"Собака пугливая, поводок плохой.Убежала.\"\n" .
    "Как потом я выяснила, была привязана у магазина, И ЗАБЫТА НА НОЧЬ! Сотрудники магазина, уходившие домой, с поводком не справились и просто обрезали его. Собака осталась на улице.\n" .
    "Слава богу, нашла дорогу к моему дому.\n" .
    "ОНИ БУДУТ ПЫТАТЬСЯ СПАСТИ ЕЩЁ СОБАЧЕК.\n" .
    "БУДЬТЕ ВНИМАТЕЛЬНЫ!\n" .
        "ИХ ТЕЛЕФОНЫ:\n" .
"Жнещина - Юлия 8 952 232-43-32\n" .
"8 911 973-26-53\n" .
"Даниил (сын) 8 981-848-81-07\n" .
"Проживают на ул Мартыновской д.27\n");


test_find_phones('http://vkontakte.ru/id2260093</a><br>8-921-409-87-71');
test_find_phones('8-916-333-90-95 | 8-067-333-90-95 | 8 905 823 25 84');