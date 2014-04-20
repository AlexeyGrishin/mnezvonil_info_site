<?php

include_once dirname(__FILE__).'/../package.php';

class PoteryashkaCollector extends ForumCollector {

    private $url = "http://poteryashka.spb.ru/forum/111-1520-1";


    /**
     * @return String
     */
    protected function get_next_page_selector() {
        return ".switchNext";
    }

    /**
     * @return String
     */
    protected function get_post_selector() {
        return ".posttdMessage";
    }

    /**
     * @param phpQueryObject $post_element
     * @return String
     */
    protected function get_post_url($post_element) {
        return null;//$post_element->parent()->parent()->find("a.postNumberLink")->eq(1)->attr("href");
    }


    function __construct() {
        $this->logger = new KLogger("poteryashka.txt", KLogger::INFO);
    }


    function do_collect(CollectorFW $fw) {
        $this->process_forum_page($this->url, $fw);

    }


}
