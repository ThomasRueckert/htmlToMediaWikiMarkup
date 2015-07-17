<?php

require_once("simple_html_dom.php");

$files = file_get_contents("input.files");
$filesArray = explode("\n", $files);
$outBuffer = '';

foreach ($filesArray as $htmlFile) {

    echo 'Processing ' . $htmlFile . '...' . PHP_EOL;
    $html = file_get_html($htmlFile);
    $body = $html->find('body', 0);

    foreach ($body->children as $childs) {


        $tmpBuffer = '';
        $tmpBuffer = elementHandler($childs, $tmpBuffer);


        $outBuffer .= $tmpBuffer;

    }
    $outBuffer = htmlspecialchars_decode($outBuffer);

    $outputName = 'out' . str_replace('.html', '.txt', $htmlFile);

    file_put_contents($outputName, $outBuffer);


}

echo PHP_EOL . '...done. Created ' . $outputName . PHP_EOL;

/**
 * @param simple_html_dom_node $childsHtml
 * @param string $tmpBuffer
 * @return string
 */
function elementHandler($childsHtml, $tmpBuffer, $depth = 0) {

    foreach ($childsHtml->children as $child) {
        /** @var simple_html_dom_node $child */


        if ($child->tag == 'ul') {
            $tmpBuffer = elementHandler($child, $tmpBuffer, ($depth+1));
            continue;
        }

        if ($child->tag == 'li') {
            $tmpBuffer.='*';
            for ($i = 0; $i < $depth; $i++) {
                $tmpBuffer.='*';
            }
        } else if ($child->tag == 'p') {
            $inner = $child->innertext();
            $inner = str_replace("\t", '', $inner);
            $inner = str_replace("\n", ' ', $inner);
            $tmpBuffer .= $inner . "\n";
        } else if ($child->tag == 'b') {
            $tmpBuffer .= '===';
            $tmpBuffer .= $child->innertext();
            $tmpBuffer .= "===\n";
        }


    }
    $tmpBuffer .= "\n";
    return $tmpBuffer;
}
