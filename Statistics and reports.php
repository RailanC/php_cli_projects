<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function statisticsReports(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('Statistics and Reports');
    return $menuBuilder->build();
}