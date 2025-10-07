<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function userManagement(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('User Management');
    return $menuBuilder->build();
}