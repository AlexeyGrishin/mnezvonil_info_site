<?php

interface PhoneEnsurer {
    function before_post();
    function sure($phone);
    function after_post();
}

class FirstSure implements PhoneEnsurer {

    private $isure = false;

    function before_post() {
        $this->isure = true;
    }

    function sure($phone) {
        $s = $this->isure;
        $this->isure = false;
        return $s;
    }

    function after_post() {

    }
}

class AllSure implements PhoneEnsurer {
    public static $INSTANCE;
    function before_post() {}
    function sure($phone) { return true; }
    function after_post() {}
}

class NoSure extends AllSure {
    public static $INSTANCE;
    function sure($phone) {return false;}
}

AllSure::$INSTANCE = new AllSure();
NoSure::$INSTANCE = new NoSure();

abstract class Collector {

    abstract function do_collect(CollectorFW $fw);

    abstract function check_link($url, $phone);

    protected function find_phones($text) {
        return find_phones($text);
    }

    protected function process_post($html, $url, $post_id, CollectorFW $fw, KLogger $logger, &$phones_info, PhoneEnsurer $ensurer = null) {
        if ($ensurer == null) $ensurer = NoSure::$INSTANCE;
        $text = $html;
        $phones = $this->find_phones($text);
        $ensurer->before_post();
        $text_id = null;
        if (count($phones) > 0) {
            foreach ($phones as $phone) {
                try {
                    $phones_info[] = new CollectedPhoneInfo($phone, $url, $text, $post_id, $ensurer->sure($phone));
                }
                catch (Exception $e) {
                    if ($logger != null)
                        $logger->logWarn("Invalid phone " . $phone . " found on page " . $url);
                }
            }
        }
        $ensurer->after_post();
    }

}


abstract class ForumCollector extends Collector {

    /**
     * @var KLogger $logger
     */
    protected $logger = null;

    /**
     * @abstract
     * @return String
     */
    protected abstract function get_next_page_selector();
    /**
     * @abstract
     * @return String
     */
    protected abstract function get_post_selector();
    /**
     * @abstract
     * @param phpQueryObject $post_element
     * @return String
     */
    protected abstract function get_post_url($post_element);

    /**
     * @param $html html from post
     * @return String html encoded in UTF-8
     */
    protected function toUtf($html) {
        return $html;
    }

    protected function process_forum_page($url, CollectorFW $fw) {
        $doc = phpQuery::newDocumentFileHTML($url);
        $posts = $doc[$this->get_post_selector()];
        $phones_info = array();
        $post_id = $fw->storePost($url);
        foreach ($posts as $post) {
            $post_url = $this->get_post_url(pq($post));
            if ($post_url == null) $post_url = $url;
            $this->process_post($this->toUtf(pq($post)->html()), $post_url, $post_id, $fw, $this->logger, $phones_info);
        }
        $next = phpQuery::makeArray($doc[$this->get_next_page_selector()]);
        $fw->store($phones_info);
        if (count($next) > 0) {
            $this->logger->logInfo("Go to the next page: " . pq($next[0])->attr("href"));
            $this->process_forum_page(pq($next[0])->attr("href"), $fw);
        }
    }

};