<?php
require __DIR__ . '/vendor/autoload.php';
use PhpSchool\CliMenu\MenuItem\AsciiArtItem;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
define('FILE_USERS', 'json/users.json');
define('FILE_BOOKS', 'json/books.json');
define('FILE_BORROWS', 'json/borrows.json');
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
██      ██ ██████  ██████   █████  ██████  ██    ██ 
██      ██ ██   ██ ██   ██ ██   ██ ██   ██  ██  ██  
██      ██ ██████  ██████  ███████ ██████    ████   
██      ██ ██   ██ ██   ██ ██   ██ ██   ██    ██    
███████ ██ ██████  ██   ██ ██   ██ ██   ██    ██                                                                                                                                                            
ART;

$menuBuilder = ($builder = new CliMenuBuilder)
->setWidth($builder->getTerminal()->getWidth())
->setTitle(printCenteredAscii($asciiArt))
->setBackgroundColour('black')
->setForegroundColour('green')
;

$menuBuilder->addItem('Catalog management', function () {
    require_once __DIR__ . '/Catalog management.php';
    system('clear');
    $submenu = catalogManagement();
    $submenu->open();
    $submenu->close();
});

$menuBuilder->addItem('User management', function () {
    require_once __DIR__ . '/User management.php';
    system('clear');
    $submenu = userManagement();
    $submenu->open();
    $submenu->close();
});

$menuBuilder->addItem('Borrowing and Return System', function () {
    require_once __DIR__ . '/Borrowing and Return System.php';
    system('clear');
    $submenu = borrowingReturnSystem();
    $submenu->open();
    $submenu->close();
});

$menuBuilder->addItem('Statistics and reports', function () {
    require_once __DIR__ . '/Statistics and reports.php';
    system('clear');
    $submenu = statisticsReports();
    $submenu->open();
    $submenu->close();
});





$menu = $menuBuilder->build();
$menu->open();