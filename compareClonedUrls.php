#!/usr/bin/php
<?php

$urlList = file('example-urls.txt', FILE_IGNORE_NEW_LINES);

$compare = new CompareClonedUrls;
$compare->processList($urlList);

class CompareClonedUrls
{
    private $_currentUrl;
    private $_outputFile;

    public function __construct()
    {
        date_default_timezone_set("GMT");
        $this->_outputFile = 'output_' . date('Y-m-d_H:i.s') . '.log';
    }

    public function processList(Array $urlList)
    {
        file_put_contents($this->_outputFile, "URLs with differences:\n\n");

        foreach ($urlList as $url) {

            $this->_currentUrl = $url;

            $from_text = $this->getPageSource($url);
            $from_text = $this->normaliseForDiff($from_text);

            $url = str_replace('.co.uk/sport', '.co.uk/clone/sport', $url);
            $url = str_replace('.com/sport', '.com/clone/sport', $url);

            $to_text = $this->getPageSource($url);
            $to_text = $this->normaliseForDiff($to_text);

            if ($from_text != $to_text)
                echo $url . " has differences\n";
                $this->postToDiffChecker($from_text, $to_text);
        }
    }

    private function getPageSource($url)
    {
        // set URL and other appropriate options  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  

        // grab URL and pass it to the browser  
        $source = curl_exec($ch);  

        // close curl resource, and free up system resources  
        curl_close($ch);

        $ch = curl_init();

        return $source;
    }

    private function postToDiffChecker($a, $b)
    {
        $url = 'https://www.diffchecker.com/diff';
        
        $fields = array (
                'file1' => $a,
                'file2' => $b,
                'storage-options' => 'month'
            );
        $fields_string = http_build_query($fields);

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt( $ch, CURLOPT_HEADER, false);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec( $ch );

        preg_match_all('|data-href="(https:\/\/www\.diffchecker\.com\/\w+)">|i', $response, $matches);
        file_put_contents($this->_outputFile, $this->_currentUrl . ' - ' . $matches[1][0] . "\n", FILE_APPEND);
        return $matches[1][0];
    }

    private function normaliseForDiff($html)
    {
        preg_match("/<body.*\/body>/s", $html, $matches);
        $html = preg_replace('|static\.test\.bbci\.co.uk\/sport\/ui\/\d+\.\d+\.\d+|i', 'static.sandbox.dev.bbc.co.uk/sport/ui/dev', $matches[0]);
        $html = preg_replace('|<script[\d\D]*?>[\d\D]*?</script>|i', '', $html);
        $html = preg_replace('|<!--[\d\D]*?-->|i', '', $html);
        $html = preg_replace('|<noscript[\d\D]*?noscript>|i', '', $html);
        $html = preg_replace('|>[ ]+?<|i', '><', $html);
        $html = preg_replace('|blq-gel">[\d\D]*<div id="blq-nav|i', 'blq-gel"><div id="blq-nav', $html);
        $html = str_ireplace('en-gb', 'en-gb', $html);
        $html = preg_replace('|barlesque/\d+\.\d+\.\d+|i', 'barlesque/2.71.0', $html);
        $html = preg_replace('|[ ]*<div id="blq-global">|i', '<div id="blq-global">', $html);
        $html = preg_replace('$(sport|onesport|sportui)/cps/(\d+)/media$i', 'onesport/cps/$2/media', $html);
        $html = preg_replace('$(sport|onesport|sportui)/cps/(\d+)/(mcs|aws)/media$i', 'onesport/cps/$2/media', $html);
        $html = preg_replace('$(sport|onesport|sportui)/\d+.\d+.\d+$i', '$1/dev', $html);
        $html = str_replace('sportui/images/ic/', 'onesport/', $html);
        $html = str_replace('sandbox.dev.', '', $html);
        $html = str_replace('bbci.co.uk', 'bbc.co.uk', $html);
        $html = str_replace('test.', '', $html);

        return $html;
    }
}