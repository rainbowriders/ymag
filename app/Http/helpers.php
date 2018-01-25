<?php


function get_rss_media(\Zend\Feed\Reader\Entry\Rss $item, $key)
{
    $media = null;

    if ($item->getType() == \Zend\Feed\Reader\Reader::TYPE_RSS_20) {
        try {
            $nodeList = $item->getXpath()->query('//item[' . $key . ']/media:thumbnail');

            if ($nodeList->length > 0) {
                $media = new \stdClass();
                $media->url = $nodeList->item(0)->getAttribute('url');
            }
            $nodeList = $item->getXpath()->query('//item[' . $key . ']/media:content');

            if ($nodeList->length > 0) {
                $media = new \stdClass();
                $media->url = $nodeList->item(0)->getAttribute('url');
            }
        } catch (Exception $e) {
            
        }
    }

    return $media;
}