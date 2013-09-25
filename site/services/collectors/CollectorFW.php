<?php

interface CollectorFW {

    function addCollector(Collector $collector);

    function store($collectedPhoneInfo);

    /**
     * @abstract
     * @param $post_url
     * @return Integer id
     */
    function storePost($post_url);

    /**
     * @abstract
     * @param $text .
     * @return Integer id
     */
    function storeText($text);

    function isPostStored($post_url);

}
