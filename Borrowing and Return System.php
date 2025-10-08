<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function borrowingReturnSystem(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('Borrowing and Return System');
    if (!function_exists('returnIndexBook')){
        function returnIndexBook($isbn){
            $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];
            foreach($books['Books'] as $index => $book){
                if($book['isbn'] == $isbn)
                return $index;
            }
            return -1;
        }
    }
    //Add
    $menuBuilder->addSubMenu('New borrow', function (CliMenuBuilder $create){
        $create->disableDefaultItems();
        $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];
        $users = json_decode(file_get_contents(FILE_USERS), true) ?? ['Users' => []];
        foreach($books['Books'] as $index => $book){
            if($book['available']){
                $create->addSubMenu($book['title'], function(CliMenuBuilder $choseBook) use (&$users, &$books, $index){
                $choseBook->disableDefaultItems();

                foreach($users['Users'] as $index2 => $user){
                    $choseBook->addItem($user['id'] . " " . $user['name'], function(CliMenu $menu) use (&$users, &$books, $index, $index2){
                        $newBorrow = [
                            "user_id" => $users['Users'][$index2]['id'],
                            "book_isbn" => $books['Books'][$index]['isbn'],
                            "borrowed_at" => date("d/m/Y"),
                            "returned_at" => "",
                            "duration" => 0
                        ];
                        $borrows = json_decode(file_get_contents(FILE_BORROWS), true) ?? ['Borrows' => []];
                        $borrows['Borrows'][] = $newBorrow;
                        $books['Books'][$index]['available'] = false;
                        file_put_contents(FILE_BORROWS, json_encode($borrows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        file_put_contents(FILE_BOOKS, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        echo "\n✅ Created with success!\n Press enter to continue...";
                        fgets(STDIN);
                        $menu->close();
                    });
                }
                $choseBook->addItem('Go Back!', new GoBackAction);
                });
            }
        
        }
        $create->addItem('Go Back!', new GoBackAction);
    });
    //Update
    $menuBuilder->addSubMenu('Return a book', function (CliMenuBuilder $return){
        $return->disableDefaultItems();
        $borrows = json_decode(file_get_contents(FILE_BORROWS), true) ?? ['Borrows' => []];
        $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];
        foreach($borrows['Borrows'] as $index => $borrow){
            $bookIndex = returnIndexBook($borrow['book_isbn']);
            if(!$borrow['returned_at']){
                $return->addSubMenu($books['Books'][$bookIndex]['title'], function(CliMenuBuilder $confirm) use (&$books, &$borrows, $index, $bookIndex){
                $confirm->disableDefaultItems()
                ->setTitle('Do you want to return the book: ' . $books['Books'][$bookIndex]['title']);
                $confirm->addItem('Yes', function(CliMenu $menu) use (&$books, &$borrows, $index, $bookIndex){
                    $borrows['Borrows'][$index]['returned_at'] = date("d/m/Y");
                    $borrowed_at = DateTime::createFromFormat('d/m/Y', $borrows['Borrows'][$index]['borrowed_at']);
                    if (!$borrowed_at) {
                        echo "Erro ao ler data: " . $borrows['Borrows'][$index]['borrowed_at'];
                        return;
                    }
                    $returned_at = new DateTime();
                    $duration = $borrowed_at->diff($returned_at);
                    $borrows['Borrows'][$index]['duration'] = $duration->days;
                    $books['Books'][$bookIndex]['available'] = true;
                    file_put_contents(FILE_BORROWS, json_encode($borrows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    file_put_contents(FILE_BOOKS, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    echo "\n✅ Created with success!\n Press enter to continue...";
                    fgets(STDIN);
                    $menu->close();
                });
                $confirm->addItem('No', new GoBackAction);
            });
            }
            
        }
        $return->addItem('Go Back!', new GoBackAction);
    });
    return $menuBuilder->build();
}