<?php

include_once __DIR__.'/../../site/services/Phone.php';
include_once __DIR__.'/../../site/services/collectors/hvosty/HvostyHelper.php';


class PhoneParserTest extends PHPUnit_Framework_TestCase  {

    public function assert_phones($phones, $text) {
        if (!is_array($phones))
            $phones = array($phones);
        $found_phones = array_values(array_filter(array_map("normalize_phone_or_false", find_phones($text))));
        $this->assertEquals($phones, $found_phones);
    }

    public function assert_no_phones($text) {
        $found_phones = array_filter(array_map("normalize_phone_or_false", find_phones($text)));
        $this->assertEquals(array(), $found_phones);
    }

    public function test_10digits_spaces() {
        $this->assert_phones("9523564882", "ЧС. 952 356 48 82
                ");
    }

    public function test_7digits() {
        $this->assert_phones("5751680", "5751680");
    }

    /*
    public function test_not_a_phone() {
        $this->assert_phones(array(), "asdsda5751680bsfsdf");
    }
    */

    public function test_vkontakte_photo_link() {
        $this->assert_no_phones('<a href="http://vkontakte.ru/photo-1298562_122645109" class="postlink">http://vkontakte.ru/photo<strong class="phone">-1298562</strong>_122645109</a>
        <br><a href="http://vkontakte.ru/photo-1298562_123465818" class="postlink">http://vkontakte.ru/photo<strong class="phone">-1298562</strong>_123465818</a>
        <br>посмотри этих девочек        </div>
        ');
    }

    public function test_vkontakte_link() {
        $this->assert_no_phones('<a href="http://vkontakte.ru/id1433138" class="postlink">http://vkontakte.ru/id<strong class="phone">1433138</strong></a>');
    }

    public function test_hvosty_link() {
        $helper = new Hvosty();
        $this->assert_no_phones(
            $helper->remove_links(
                '<a href="http://vsehvosty.ru/forum/viewtopic.php?f=32&amp;t=4424&amp;p=1249380#p1249380" class="postlink-local">viewtopic.php?f=32&amp;t=4424&amp;p=1249380#p1249380</a>'
            )
        );
    }

    public function test_long_link_from_hvosty() {
        $helper = new Hvosty();
        $this->assert_no_phones(
            $helper->remove_links(
                '<a href="http://www.chance.ru/classifieds/show/index.html/16-1000?arsx1994398" class="postlink">http://www.chance.ru/classifieds/show/i ... rsx1994398</a>'
            )
        );
    }

    public function test_10digits() {
        $this->assert_phones("8125556677", "8125556677");
    }

    public function test_7digits_cr() {
        $this->assert_phones("5751680", "ЧС 575-16-80
                <strong>Звонят мне второй раз. ");
    }

    public function test_7digits_several() {
        $this->assert_phones(
            array(
                "1236789",
                "3235678",
                "3234567",
                "1234445"
            ),
            "    123-67-89
                323-56-78
                323-45-67
                123-44-45"
        );
    }


    public function test_lot_of_phones_in_text() {
        $this->assert_phones(
            array("8017059", "0297051980", "0295121152"),
            '<div class="postbody">1.Перекупщики:<br>+375 29 334 14 17, Елена. Купленные ею щенки крупных пород пропадают в неизвестном направлении, а последнее свое "приобретение" потеряла, забыв на лестничной клетке в подъезде. <br>2.Вот подозрительный номер. 8017059<br>по тому номеру всегда хотят взять щенков.<br>3.Оксана Малей: злостный перекуп. Собак держит в невыносимых условиях.<br><br>8 - 029 - 578 - 78 - 49 <!-- e --><a href="mailto:Fr-oksa@mail.ru">Fr-oksa@mail.ru</a><!-- e --> Оксана <br>4.Минске есть перекупшица Юлия Кабанова<br>В РКСС она ведет секцию РОСов, у неё питомник "hutorokujulii"<br>Человек явно невменяемый!<br>У неё живет 10 спаниелей<br>Спаниели в сарае, только щенные суки и щенки в коридоре, который даже не отопляется.<br>Щенков она продает по 200-400 долларов.<br>Щенки с глистами, вяжут собак на каждую течку.<br>Продает помесь англичанина с русским за РУССКИХ с документами!!! Остерегайтесь! Мы чуть непопали на эту удочку, до сих пор в шоке после приезда к ней смотреть перекупанных стаффов, которым по желанию она собиралась делать отл. документы! <br>Принимает в дар всех породистых собак, обещает шикарную жизнь и отличный уход.<br><br>Живет эта дамочка на чижевских. в частном секторе!<br><br>Вот её контакты!<br><!-- e --><a href="mailto:hutorokujulii@yandex.ru">hutorokujulii@yandex.ru</a><!-- e --><br>80297051980<br>+375447412091 <br>5.Приму в дар разных зверюшек.<br>Приму в дар или куплю дешево котят и щенков (простых красивых, помесь или породистых), декоративных кроликов(можно взрослую особь); хомяков, декоративных крыс, морских свинок, песчанок и прочих грызунов (можно взрослую особь или весь помет).<br><br><!-- e --><a href="mailto:8-029-512-11-52Katya.tochka18@mail.ru">8-029-512-11-52Katya.tochka18@mail.ru</a><!-- e --><br><br>6.Возьму щенка. хозяева всех пород если у вас есть щенки и вы желаете их отдать в заботливые руки то прошу позвонить.<br>Денис<br><br>Тел.: +375299238906<br>Страна: БЕларусь<br>Город: Минск<br>7.[NaturalBornBitch]<br>Статус: Гуру<br>Страна: [Беларусь]<br>Город: Минск<br>Сообщений: 7635<br>Мои блоги<br>ICQ: 478787771<br>Рег. 13 Августа 2007<br>Пол: Женщина<br>Подарки: 56<br>Репутация: 115<br>	+37529 6317749 Елена<br><br>Взяла на передержку Дэна хохлатого. Через неделю забрали собаку с гноящейся необработанной лапой. Похудевшего в 2 раза. Фотографировала лапу и посылала фото ветеринару. Онлайн консультации.</div>'
        );
    }

    public function test_title_hvosty() {
        $h = new Hvosty();
        $this->assert_phones("9217567794",
            $h->remove_links("ЧС: (921) 756-7794")
        );
    }



    public function test_2_10digi_phones() {
        $this->assert_phones(
            array("9517464485", "9814443527"),
            "Добавлю от себя:<br>8-951-7464485 и 8-981-4443527<br>Стандартный развод с платежом на мобилу..."
        );
    }

    public function test_big_html_text() {
        $this->assert_phones(
            array("9627083868"),
            '<div class="postbody">Надя 89627083868<br />перекупщица.<br />продает крошечных щенков у метро ветеранов, породистых и метисов. выпускает непривитых малявок на траву. В разговоре - практически не подкопаться... <img src="./images/smilies/sad.gif" alt=":(" title="" />  Получила телефон, пробила по яндексу. Меняла щенка папильона на взрослую девочку йорка или чиха. Отдавала щенков немецкой овчарки (почему-то написано &quot;отдам&quot;, правда). Сейчас вот цверги, мопс и метисы спаниеля с болонкой...<br /><br />Вот щены:<br /><a href="http://radikal.ru/F/s48.radikal.ru/i120/1005/df/c55c0179c974.jpg.html" class="postlink"><img src="http://s48.radikal.ru/i120/1005/df/c55c0179c974t.jpg" alt="Изображение" /></a><a href="http://radikal.ru/F/s39.radikal.ru/i085/1005/57/37623606329e.jpg.html" class="postlink"><img src="http://s39.radikal.ru/i085/1005/57/37623606329et.jpg" alt="Изображение" /></a><a href="http://radikal.ru/F/s54.radikal.ru/i145/1005/aa/129c5bd09f31.jpg.html" class="postlink"><img src="http://s54.radikal.ru/i145/1005/aa/129c5bd09f31t.jpg" alt="Изображение" /></a><br /><br />Этот мне показался больным...<br /><a href="http://radikal.ru/F/s57.radikal.ru/i156/1005/aa/2fd8caddaed1.jpg.html" class="postlink"><img src="http://s57.radikal.ru/i156/1005/aa/2fd8caddaed1t.jpg" alt="Изображение" /></a><br />На мое замечание стала заверять, что глаза текут исключительно от ветра. Но щен вялый очень, по виду явно нездоров.<br /><br />Может я и не права, но по моему все это говорит о том, что она перекупщица... <img src="./images/smilies/sad.gif" alt=":(" title="" /></div>'
        );
    }

    public function test_10digits_spaces_starting_8() {
        $this->assert_phones(
            "9119893028",
            "он 8911 989 30 28"
        );
    }

    public function test_3_10digits_phones_one_by_line() {
     $this->assert_phones(
         array("9062507872", "9602858482", "9052646033"),
         "8-906-250-78-72\n
                 8-960-285-84-82\n
                 8-905-264-60-33\n"
     );
    }

    public function test_10digits_spaces_starting_7() {
        $this->assert_phones(
            "9095987997",
            "7909-598-79-97"
        );
    }

    public function test_10digits_spaces_starting_plus7() {
        $this->assert_phones(
            "9062760911",
            "+79062760911"
        );
    }

    public function test_2_10digits_phones_on_same_line() {
     $this->assert_phones(
         array("9219041102", "9043389714"),
         "ЧС: 8-921-904-11-02 и 8-904-338-97-14"
     );
    }

    public function test_2_10digits_phones_divided_by_space() {
        $this->assert_phones(
            array("9522347749", "9523697163"),
            "Крыленко,29. Алла. 89522347749 89523697163. Люди,имейте сострадание-перестаньте нести ей своих и чужих животных..смерть"
        );
    }

    public function test_10digit_parenthesis() {
        $this->assert_phones("8122964970", "ЧС: (812) 296-49-70");
    }

    public function test_7_and_10_digits_phones() {
        $this->assert_phones(
            array("7853573", "9500239154"),
            "ЧС: 785-35-73, 8-950-023-91-54"
        );
    }

    public function test_big_text() {

        $this->assert_phones(
            array(
                "9522324332",
                "9119732653",
                "9818488107"
            ),
            "Именнно этой женщине я отвела Марфушу, идеальную собаку.viewtopic.php?f=2&t=63516\n" .
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
    }

    public function test_hvosty_style1() {
        $this->assert_phones(
            array("9112872765", "9811269380", "9811269375"),
            "89112872765, 89811269380, 75,85, 89811269375 ЧС"
        );
    }

    public function test_hvosty_style2() {
        $this->assert_phones(
            array("9213572835", "3179337"),
            "ЧС: 8-921-357-28-35 317-93-37"
        );
    }

    public function test_punctuation() {
        $this->assert_phones(
            array("9111234567", "9032281043"),
            "8-911=1234567 !!!!!!!!!  8/903/2281043"
        );
    }

    public function test_hvosty_case1() {
        $this->assert_phones(array("9214098771"),
            'Анюта Азаренко (Рублева) <br><!-- m --><a class="postlink" rel="nofollow" target="_blank" href="http://vkontakte.ru/id2260093" onclick="window.open(this.href);return false;">http://vkontakte.ru/id2260093</a><!-- m --><br>8-921-409-87-71 <br>Ищет кошку для утех своему британскому коту "на совсем или на вязку", британку или русскую голубую.<br>Очередная малолетняя разведенка, блин... <img src="./images/smilies/yucky.gif" alt=":sick:" title="">'
        );
    }

    public function test_hc1() {
        $this->assert_phones(array("9214098771"),
            'http://vkontakte.ru/id2260093</a><br>8-921-409-87-71');
    }

    public function test_hc2() {
        $this->assert_phones(array("9214098771"),
            'http://vkontakte.ru/id2260093</a><BR />8-921-409-87-71');
    }


    public function test_is_cell() {
        $this->assertTrue(is_cell("9217775566"));
    }

    public function test_is_cell_7digits() {
        $this->assertFalse(is_cell("9775566"));
    }

    public function test_is_cell_city() {
        $this->assertFalse(is_cell("8127775566"));
    }

    public function test_is_cell_7digits_city() {
        $this->assertFalse(is_cell("1075566"));
    }

    public function test_get_local_phone_10digits() {
        $this->assertEquals("1234567", get_local_phone("9211234567"));
    }

    public function test_get_local_phone_7digits() {
        $this->assertEquals("1234567", get_local_phone("1234567"));
    }

    public function test_get_city_code_10digits() {
        $this->assertEquals("921", get_city_code("9211234567"));
    }

    public function test_get_city_code_7digits() {
        $this->assertEquals("", get_city_code("1234567"));
    }


    public function test_are_equal_phones_equal() {
        $this->assertTrue(are_equal_phones("1234567", "1234567"));
    }

    public function test_are_equal_phones_equal_first_not_normalized() {
        $this->assertTrue(are_equal_phones("+1(234) 56-7", "1234567"));
    }

    public function test_are_equal_phones_equal_both_not_normalized() {
        $this->assertTrue(are_equal_phones("+1(234) 56-7", "(123) 45-67"));
    }

    public function test_are_equal_phones_city_code_absent() {
        $this->assertTrue(are_equal_phones("8-812-1234567", "1234567"));
    }

    public function test_are_equal_phones_city_code_different() {
        $this->assertFalse(are_equal_phones("8-812-1234567", "8-495-1234567"));
    }

    public function test_are_equal_phones_mobile_and_city() {
        $this->assertFalse(are_equal_phones("8-921-1234567", "1234567"));
    }

    public function test_are_equal_phones_mobile() {
        $this->assertTrue(are_equal_phones("8-921-1234567", "+7(921)1234567"));
    }

}
