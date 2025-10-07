<?php
require __DIR__ . '/vendor/autoload.php';
use PhpSchool\CliMenu\MenuItem\AsciiArtItem;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;

function printCenteredAscii($text) {
    $columns = exec('tput cols') ?: 80;
    $lines = explode("\n", $text);

    $maxLength = max(array_map('strlen', $lines));
    $textr = "";
    foreach ($lines as $line) {
        $lineTrimmed = rtrim($line);
        $padding = max(0, intval(($columns - $maxLength) / 2));
        $textr .= str_repeat(' ', $padding) . $lineTrimmed . "\n";
    }
    return $textr;
}

$asciiArt = <<<ART
          (+)
 __ __ _ _ _ _     _ _ _____ 
| '__/ _` | | |  / _` |  _  | 
| | | (_| | | |_| (_| | | | | 
|_|  \__,_|_|____\__,_|_| |_| 
ART;

$menuBuilder = ($builder = new CliMenuBuilder)
->setWidth($builder->getTerminal()->getWidth())
->setTitle(printCenteredAscii($asciiArt))
->setBackgroundColour('black')
->setForegroundColour('green')
;
$menuBuilder->addItem('Bibliothèque', function () {
    require_once __DIR__ . '/Bibliotheque.php';
    system('clear');
    $submenu = gestionDuCatalogueMenu();
    $submenu->open();
    $submenu->close();
});


$menu = $menuBuilder->build();
$menu->open();