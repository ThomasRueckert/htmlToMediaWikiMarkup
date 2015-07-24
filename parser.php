<?php

require_once("simple_html_dom.php");

$files = file_get_contents("input.files");
$filesArray = explode("\n", $files);
$outBuffer = '';

foreach ($filesArray as $htmlFile) {

    if ($htmlFile == '') {
        continue;
    }

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

    $outBuffer = '';

    echo '> Created ' . $outputName . PHP_EOL;

}

echo '...done.' . PHP_EOL;

/**
 * @param simple_html_dom_node $child
 * @param string $tmpBuffer
 * @return string
 */
function elementHandler($child, $tmpBuffer, $depth = 0, $break = true) {

    //foreach ($childsHtml->children as $child) {
        /** @var simple_html_dom_node $child */


        if ($child->tag == 'ul') {
            foreach ($child->children as $grandchild) {
                $tmpBuffer = elementHandler($grandchild, $tmpBuffer, ($depth+1));
            }
            return $tmpBuffer;
        }

        $inner = $child->innertext();

        $break2 = true;
        if ($child->tag == 'li') {
            //$tmpBuffer.='*';
            for ($i = 0; $i < $depth; $i++) {
                $tmpBuffer.='*';
            }
            if ($inner=='') {
                $break2 = false;
            } else {
                $tmpBuffer.=$child->innertext();
            }
        } else if ($child->tag == 'p') {
            $inner = str_replace("\t", '', $inner);
            $inner = str_replace("\n", ' ', $inner);
            //$tmpBuffer .= $inner;
            $tmpBuffer = depthTest($inner, $child, $tmpBuffer);
        } else if ($child->tag == 'b') {
            $tmpBuffer .= '===';
            $tmpBuffer = depthTest($inner, $child, $tmpBuffer);
            //$tmpBuffer .= $inner;
            $tmpBuffer .= "===";
        } else if ($child->tag == 'u') {
            $tmpBuffer .= '<u>';
            $tmpBuffer = depthTest($inner, $child, $tmpBuffer);
            //$tmpBuffer .= $inner;
            $tmpBuffer .= '</u>';
        } else if ($child->tag == 'i') {
            $tmpBuffer .= '\'\'';
            $tmpBuffer = depthTest($inner, $child, $tmpBuffer);
            //$tmpBuffer .= $inner;
            $tmpBuffer .= '\'\'';
        } else if ($child->tag == 'text') {
            $tmpBuffer .= $child->innertext();
        } else if ($child->tag == 'font') {
            $tmpBuffer .= $child->innertext();
        }
        if ($break && $break2) {
            $tmpBuffer .= "\n";
        }
    //}
    return $tmpBuffer;
}

function depthTest($inner, $child, $tmpBuffer) {
    $inner = str_get_html($inner);
    if ($inner) {
        $firstChild = $inner->firstChild();
        if ($firstChild) {
            $tag = $firstChild->tag;
            if (in_array($tag, ['u','b','i','p','li','ul','font'])) {
                $tmpBuffer = elementHandler($firstChild, $tmpBuffer, 0, false);
            }
        } else {
            $tmpBuffer .= $inner;
        }
    }
    return $tmpBuffer;
}
