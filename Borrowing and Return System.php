<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function borrowingReturnSystem(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('Borrowing and Return System');
    return $menuBuilder->build();
}