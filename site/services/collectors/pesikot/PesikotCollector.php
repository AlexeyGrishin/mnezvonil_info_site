<?php

class PesikotCollector extends ForumCollector {

    private $topics = array("134476", "88213", "148275", "10530");
    private $base_url = "http://pesikot.org/forum/index.php?showtopic=";


    /**
     * @return String
     */
    protected function get_next_page_selector() {
        return ".pagelink a:contains('>')";
    }

    /**
     * @return String
     */
    protected function get_post_selector() {
        return "div.postcolor";
    }

    /**
     * @param phpQueryObject $post_element
     * @return String
     */
    protected function get_post_url($post_element) {
        return $post_element->parent()->parent()->parent()->find(".postdetails a")->eq(0)->attr("href");
    }

    protected function toUtf($html) {
        return iconv("windows-1251", "utf-8", $html);
    }

    function __construct() {
        $this->logger = new KLogger("pesikot.txt", KLogger::INFO);
    }


    function do_collect(CollectorFW $fw) {
        foreach ($this->topics as $topic) {
            $this->process_forum_page($this->base_url . $topic, $fw);
        }

    }

    function check_link($url, $phone) {
        // TODO: Implement check_link() method.
    }



}
