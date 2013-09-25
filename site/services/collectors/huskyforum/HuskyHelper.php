<?php

class HuskyHelper {

    function get_url() {
        return "http://husky.forum.ru/index.php/topic,16117.0.html";
    }

    public function get_posts($doc) {
        return $doc[".post"];
    }

    public function get_next_page(phpQueryObject $doc) {
        return $doc->find(".navPages")->parent()->find("b")->next("a.navPages");
    }

}
