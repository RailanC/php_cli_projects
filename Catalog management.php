<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function catalogManagement(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('Catalog Management');
    

    #Add
    $menuBuilder->addSubMenu('Add a new book', function (CliMenuBuilder $insert) {
        $insert->disableDefaultItems();
        $insert->addItem('Add', function(CliMenu $menu){
        $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];
        $title = readline("title: ");
        echo "$title \n";
        $author = readline("author: ");
        echo "$author \n";
        $isbn = readline("ISBN: ");
        echo "$isbn \n";
        $category = readline("category: ");
        echo "$category \n";
        $available = readline("available (0/1): ") != "0";
        echo "$available \n";
        
        $newBook = [
        "title" => $title,
        "author" => $author,
        "isbn" => $isbn,
        "category" => $category,
        "available" => $available
        ];

        $books['Books'][] = $newBook;

        file_put_contents(FILE_BOOKS, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "\n✅ The book has been updated with success!\n";
        fgets(STDIN);
        $menu->close();
        });
        $insert->addItem("Go Back", new GoBackAction);
    });   
    
    #Update
    $menuBuilder->addSubMenu('Update a book', function (CliMenuBuilder $update) {
    $update->setTitle('List of available books')
    ->disableDefaultItems();

    $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];
    foreach ($books['Books'] as $index => $book) {
        $update->addSubMenu($book['title'], function (CliMenuBuilder $update2) use (&$books, $index) {
            $update2->setTitle("Update : " . $books['Books'][$index]['title'])
            ->disableDefaultItems();

            $fields = ['title', 'author', 'isbn', 'category', 'available'];

            foreach ($fields as $field) {
                $update2->addItem("Update $field", function (CliMenu $menu) use (&$books, $index, $field) {
                    echo "Set a new value for $field: ";
                    $newValue = trim(fgets(STDIN));

                    if ($field === 'available') {
                        $newValue = $newValue == 0 ? false : true;
                    }

                    $books['Books'][$index][$field] = $newValue;
                    file_put_contents(FILE_BOOKS, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                    echo "\n✅ $field updated with success!\n";
                    echo "Press enter to continue...";
                    fgets(STDIN);
                    $menu->close();
                });
            }
            $update2->addItem('Go Back', new GoBackAction);
        });
    }
    $update ->addItem('Go Back', new GoBackAction);
    });
    
    #Delete
    $menuBuilder->addSubMenu('Delete a Books', function (CliMenuBuilder $delete) {
        $delete->setTitle('List of books')
        ->disableDefaultItems();

        $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];

        foreach ($books['Books'] as $index => $book) {
            $delete->addSubMenu($book['title'], function (CliMenuBuilder $supprimer2) use (&$books, $index){
                $supprimer2->setTitle("Delete")
                ->disableDefaultItems();
                $supprimer2->addItem("Yes", function(CliMenu $menu) use (&$books, $index){
                    array_splice($books['Books'], $index , 1);
                    file_put_contents(FILE_BOOKS, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    echo "\n✅ Book has been deleted with success!\n";
                    fgets(STDIN);
                    $menu->close();
                });
                $supprimer2->addItem('No', new GoBackAction);
            });
        }
        $delete->addItem('Go Back', new GoBackAction);
        
    });
    
    #Search 
    $menuBuilder->addSubMenu('Search a book', function(CliMenuBuilder $search){
        $search->disableDefaultItems();
        $books = json_decode(file_get_contents(FILE_BOOKS), true) ?? ['Books' => []];
        $found = false;
        $search->addItem("Search", function(CliMenu $menu) use (&$books, $FILE_BOOKS, &$found){
            $request_rechercher = strtolower(readline("Search un livre: "));
            echo $request_rechercher . "\n";
            foreach($books['Books'] as $book){
                if(str_contains(strtolower($book['title']), $request_rechercher)){
                    $found = true;
                    echo $book['title'] . "\n";
                }
            }

            if(!$found){
                echo "Nothing found...\n";
            }
            echo 'Press enter to continue!';
            fgets(STDIN);
            $menu->close();
        });
        $search->addItem('Go Back', new GoBackAction);
    });
    return $menuBuilder->build();
}