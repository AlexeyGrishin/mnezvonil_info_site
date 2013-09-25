<?php

class TestCollector extends  Collector {
    function do_collect(CollectorFW $fw) {
        $doc = phpQuery::newDocumentFileHTML("http://localhost:8090/blacklist/test/index.html");
        $links = $doc["a"];
        foreach ($links as $link) {
            $link = "http://localhost:8090/blacklist/test/" . pq($link)->attr("href");
            $post_id = $fw->storePost($link);
            $page = phpQuery::newDocumentFileHTML($link);
            $records = $page["p"];
            foreach ($records as $post) {
                $html = pq($post)->html();
                $phones = find_phones($html);
                $info = array();
                foreach ($phones as $phone) {
                    try {
                        $info[] = new CollectedPhoneInfo($phone, $link, $html, $post_id, false);
                    }
                    catch (Exception $e) {
                        echo("Invalid phone " . $phone . " found on page " . $link);
                    }
                }
                $fw->store($info);
            }
        }
    }

    function check_link($url, $phone_to_find) {
        $page = phpQuery::newDocumentFileHTML($url);
        $records = $page["p"];
        foreach ($records as $post) {
            $html = pq($post)->html();
            if (has_phone($html, $phone_to_find)) return true;
        }
        return false;
    }

}

