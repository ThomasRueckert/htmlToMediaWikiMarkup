<?php

require_once("simple_html_dom.php");

$files = file_get_contents("input.files");
$filesArray = explode("\n", $files);
$outBuffer = '';

foreach ($filesArray as $htmlFile) {

    echo 'Processing ' . $htmlFile . '...' . PHP_EOL;
    $html = file_get_html($htmlFile);
    $body = $html->find('body', 0);

    $bodyHtml = str_get_html($body);

    foreach ($bodyHtml->childNodes()[0]->childNodes() as $childs) {

        //echo $childs . PHP_EOL;
        //create processable child
        $childsHtml = str_get_html($childs);



        $outBuffer .= elementHandler($childsHtml, $outBuffer);

    }
    $outBuffer = htmlspecialchars_decode($outBuffer);

    $outputName = 'out' . str_replace('.html', '.txt', $htmlFile);

    file_put_contents($outputName, $outBuffer);

    //
    //echo parse()$bodyHtml;
    /**
     * process body
     */

}

echo PHP_EOL . '...done. Created ' . $outputName . PHP_EOL;

/**
 * @param simple_html_dom $childsHtml
 * @param string $tmpBuffer
 * @return string
 */
function elementHandler($childsHtml, $tmpBuffer) {

    $lis = $childsHtml->find('li');
    foreach ($lis as $li) {
        /** @var simple_html_dom_node $li */
        $c = -1;
        $tmp = $li;
        while (!is_null($tmp->parentNode()) && strpos($tmp->parentNode(),"<ul>")==0) {
            $c++;
            $tmp = $tmp->parentNode();
        }
        for ($i = 0; $i < $c; $i++) {
            $tmpBuffer .= '*' . $li->innertext();
        }


        /** @var simple_html_dom_node $next */
        $next = $li->nextSibling();
        if (strpos($next,"</p>") !== false) {
            $tmpBuffer .= $next->innertext() . "\n";
        }
    }
    $tmpBuffer .= "\n";
    return $tmpBuffer;
}
