<?php

include_once dirname(__FILE__).'/../Collector.php';

class HuskyCollector extends  Collector {


    function __construct(HuskyHelper $helper) {
        $this->logger = new KLogger("collector_husky.txt", KLogger::INFO);
        $this->logger->consoleOutput = KLogger::INFO;
        $this->helper = $helper;
    }

    function do_collect(CollectorFW $fw) {
        $this->process_topic($this->helper->get_url(), $fw);
    }

    function process_topic($topic_url, CollectorFW $fw) {
        $post_id = $fw->storePost($topic_url);
        $doc = phpQuery::newDocumentFileHTML($topic_url);
        $posts = $this->helper->get_posts($doc);
        $phones_info = array();
        foreach ($posts as $post) {
            $html = iconv("windows-1251", "utf-8", pq($post)->html());
            $url = pq($post)->parent()->find("a")->eq(1)->attr("href");
            $this->process_post($html, $url, $post_id, $fw, $this->logger, $phones_info);
        }
        $fw->store($phones_info);
        $next = $this->helper->get_next_page($doc);
        if ($next->length != 0) {
            $this->logger->logInfo("Process next page");
            $this->process_topic(pq($next->eq(1))->attr("href"), $fw);
        }
    }

    function check_link($url, $phone) {
        //TODO
    }

}

