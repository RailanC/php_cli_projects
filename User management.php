<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function userManagement(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('User Management');
    
    //Add
    $menuBuilder->addSubMenu('Add a new User', function (CliMenuBuilder $insert){
        $insert->disableDefaultItems();
        $insert->addItem('Add', function(CliMenu $menu){
            $users = json_decode(file_get_contents(FILE_USERS), true) ?? ['Users' => []];
            $newUser = [
            "id" => readline("id: "),
            "name" => readline("name: ")
            ];
            echo "\n";

            if (validate_user($newUser)){
                echo "\n✅ User has been successufly added!";
            }
            else{
                echo "Error, User not implemented!!\n";
            }
            fgets(STDIN);
            $menu->close();
        });
        $insert->addItem('Go Back!', new GoBackAction);
    });
    //Update
    $menuBuilder->addSubMenu('Update a user', function(CliMenuBuilder $update){
        $update->setTitle('List of users')
        ->disableDefaultItems();

        $users = json_decode(file_get_contents(FILE_USERS), true) ?? ['Users' => []];
        foreach($users['Users'] as $index => $user){
            $update->addSubMenu($user['id'] . " " . $user['name'], function(CliMenuBuilder $update2) use (&$users, $index){
                $update2->setTitle("Update: " . $users['Users'][$index]['name'] )
                ->disableDefaultItems();
                $update2->addItem("Update ", function (CliMenu $menu) use (&$users, $index, $field){
                    echo "Set a new value for name: ";
                    $newValue = trim(fgets(STDIN));

                    $users['Users'][$index][$field] = $newValue;
                    if(validate_user($users['Users'][$index], true))
                            file_put_contents(FILE_USERS, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    else{
                        echo "Press enter to continue...";
                        fgets(STDIN);
                        $menu->close();
                    }
                    echo "\n✅ $field updated with success!\n";
                    echo "Press enter to continue...";
                    fgets(STDIN);
                    $menu->close();
                });
                $update2->addItem('Go Back!', new GoBackAction);
            });
        }
        $update->addItem('Go Back!', new GoBackAction);
    });
    //Validate
    if (!function_exists('validate_user')) {
    function validate_user($user, $isUpdate = false){
        $users = json_decode(file_get_contents(FILE_USERS), true) ?? ['Users' => []];
        if($user['id'] == ''){
            echo "The name can't be empty\n";
            return false;
        }
        if($user['name'] == ''){
            echo "The id can't be empty\n";
            return false;
        }

        if(!isUpdate){
            foreach($users['Users'] as $u){
            if($u['id'] == $user['id']){
                echo "Already exist an Id with that number\n";
                return false;
            }
        }
        }

        $users['Users'][] = $user;

        file_put_contents(FILE_USERS, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }}
    return $menuBuilder->build();
}