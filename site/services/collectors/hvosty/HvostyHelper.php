<?php

include_once dirname(__FILE__).'/../package.php';

class Hvosty {

     function urls() {
        return array("http://hvosty.ru/forum/viewforum.php?f=15");
    }

    /**
     * @
     * @param $first_page
     * @param $nr page nr from 0 (1st page = 0)
     */
     function page($first_page, $nr) {
        return $first_page . '&start=' . (50*$nr);
    }

     function is_post_link($url) {
        return preg_match("/.*viewtopic\\.php\\?.*/", $url);
    }

     function post_id($url) {
        $m = array();
        preg_match("/.*viewtopic\\.php\\?.*&t=([0-9]*)/", $url, $m);
        return $m[1];
    }

     function absolutize($url_part) {
        return "http://hvosty.ru/forum/" . $url_part;
    }

     function findNext($doc) {
        $topic_links = phpQuery::makeArray($doc[".gensmall b a"]);
        if (count($topic_links) > 0) {
            $topic_links = $topic_links[0];
        }
        if (count($topic_links) > 0) {
            $last = $topic_links[count($topic_links) - 1];
            if (pq($last)->text() == "След.") {
                $href = $this->removeSid($this->absolutize(pq($last)->attr("href")));
                return $href;
            }
        }
        return null;
    }

    public  function removeSid($url) {
        $pos = strpos($url, "&sid=");
        if (!$pos) return $url;
        $before = substr($url, 0, $pos);
        $after = substr($url, $pos + 1);
        $pasfter = strpos($after, "&");
        if ($pasfter) {
            return $before . substr($after, $pasfter);
        }
        else {
            return $before;
        }
    }

    public  function is_blacklist_link($url) {
        return strpos($url, "?f=15");
    }

    public function get_title(phpQueryObject $doc) {
        $hdr = $doc->find("#pageheader a.titles");
        return $hdr->eq(0)->html();
    }

    public function get_posts_htmls($doc, $include_1st_post = true) {
        $htmls = array();
        $hdr_text = "";
        if ($include_1st_post) {
            $hdr_text = $this->get_title($doc);
        }
        //parse posts
        $posts = $doc[".tablebg .postbody:first-child"];
        $include_post = $include_1st_post;
        foreach ($posts as $post) {
            if ($include_post) {
                $htmls[] = $hdr_text . "\r\n" . pq($post)->html();
            }
            $include_post = true;
            $hdr_text = ""; //not required for next after first
        }
        return $htmls;
    }

    function shall_ignore($url) {
        return false;
    }

    public function remove_links($text) {
        return preg_replace("/<a[^>]+>[^<]+<\\/a>/", "", $text);
    }

}
