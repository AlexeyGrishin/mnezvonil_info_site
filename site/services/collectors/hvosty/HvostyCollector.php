<?php

include_once dirname(__FILE__) . '/../package.php';
include_once dirname(__FILE__) . '/HvostyHelper.php';

class HvostyCollector extends  Collector {

    function __construct(Hvosty $helper) {
        $this->logger = new KLogger("collector_hvosty.txt", KLogger::INFO);
        $this->logger->consoleOutput = KLogger::INFO;
        $this->helper = $helper;
    }

    function do_collect(CollectorFW $fw) {
        $this->logger->logInfo("Collecting from hvosty.ru started");

        $urls = $this->helper->urls();
        foreach ($urls as $page) {
            $this->process_page($page, $fw, true);
        }

        $this->logger->logInfo("Collecting from hvosty.ru finished");
        $this->logger->logInfo("");
    }

    private function collect_post_links($doc) {
        $links = $doc['a.topictitle'];
        $urls = array();
        $this->logger->logDebug("Found links " . count($links));
        foreach ($links as $dlink) {
            $dlink = pq($dlink);
            if ($this->helper->is_blacklist_link($dlink->attr("href"))) {
                $urls[] = $dlink->attr("href");
            }
        }
        return array_unique($urls);
    }

    private function process_page($url, CollectorFW $fw, $always_collect = false) {

        $this->logger->logDebug("Navigate to page ".$url);
        $doc = phpQuery::newDocumentFileHTML($url);
        $post_links = $this->collect_post_links($doc);
        $this->logger->logDebug("Collected links: " . count($post_links));
        foreach($post_links as $post_link) {
            $this->logger->logDebug("Check link " . $post_link);
            if ($this->helper->is_post_link($post_link)) {
                $post_link = $this->helper->removeSid($this->helper->absolutize($post_link));
                if ($this->helper->shall_ignore($post_link)) {
                    $this->logger->logDebug("Post " . $post_link . " shall be ignored");
                }
                else if ($always_collect || !$fw->isPostStored($post_link)) {
                    $this->logger->logDebug("Post " . $post_link . " to collect");
                    $id = $fw->storePost($post_link);
                    $fw->addCollector(new PostCollector($post_link, $id, $this->logger, $this->helper));
                }
                else {
                    $this->logger->logDebug("Post " . $post_link . " skipped");
                }
            }
            else {
                $this->logger->logWarn("Url was rejected as non-post one: " . $post_link);
            }
        }

        $next_url = $this->helper->findNext($doc);
        if ($next_url) {
            $this->process_page($next_url, $fw);
        }
    }

    function check_link($url, $phone_to_find) {
        $doc = phpQuery::newDocumentFileHTML($url);
        $htmls = $this->helper->get_posts_htmls($doc, true);
        foreach ($htmls as $html) {
            if (has_phone($html, $phone_to_find)) return true;
        }
        return false;
    }


}

class PostCollector extends  Collector {
    private $url, $logger;
    function __construct($url, $post_id, KLogger $logger, Hvosty $helper, $ignore_header = false) {
        $this->url = $url;
        $this->post_id = $post_id;
        $this->logger = $logger;
        $this->ignore_header = $ignore_header;
        $this->helper = $helper;
    }

    protected function find_phones($text) {
        return parent::find_phones($this->helper->remove_links($text));
    }


    function do_collect(CollectorFW $fw) {
        $doc = phpQuery::newDocumentFileHTML($this->url);
        $phones_info = array();
        $url = $this->helper->removeSid($this->url);
        $phones_in_title = $this->ignore_header ? array() : find_phones($this->helper->get_title($doc));

        $htmls = $this->helper->get_posts_htmls($doc, !$this->ignore_header);
        foreach ($htmls as $html) {
            $this->process_post($html, $url, $this->post_id,
                $fw,
                $this->logger, $phones_info, new InCollection($phones_in_title));
        }
        if (count($phones_info) == 0) {
            $this->logger->logWarn("There is no phones on page " . $this->url);
        }
        $this->logger->logDebug("Collected: " . $this->url . " found phone infos: " . count($phones_info));
        $fw->store($phones_info);
        $next_url =$this->helper->findNext($doc);
        if ($next_url) {
            $this->logger->logDebug("Next page url = " . $next_url);
            $fw->addCollector(new PostCollector($next_url, $this->post_id, $this->logger, $this->helper, true));
        }
        else {
            $this->logger->logDebug("No more pages");
        }
    }

    function check_link($url, $phone_to_find) {
    }

}

class InCollection implements PhoneEnsurer {
    function __construct($phones) {
        $this->phones = $phones;
    }

    function before_post() {}

    function sure($phone) {
        return in_array($phone, $this->phones);
    }

    function after_post() {}

}


