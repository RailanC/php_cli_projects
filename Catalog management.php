<?php
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Action\ExitAction;
use PhpSchool\CliMenu\Action\GoBackAction;

function gestionDuCatalogueMenu(): CliMenu {
    $menuBuilder = new CliMenuBuilder();
    $menuBuilder->setTitle('Gestion du Catalogue');
    define('FILE', 'json/GDU_Livre.json');

    #Ajouter
    $menuBuilder->addSubMenu('Ajouter un livre', function (CliMenuBuilder $ajouter) {
        $ajouter->setTitle('Ajouter un livre')
        ->disableDefaultItems();
        $ajouter->addItem('Ajouter', function(CliMenu $menu){
        $data = json_decode(file_get_contents(FILE), true) ?? ['Livres' => []];
        $titre = readline("Titre: ");
        echo "$titre \n";
        $auteur = readline("Auteur: ");
        echo "$auteur \n";
        $isbn = readline("ISBN: ");
        echo "$isbn \n";
        $categorie = readline("Categorie: ");
        echo "$categorie \n";
        $disponible = readline("Disponible (0/1): ") != "0";
        echo "$disponible \n";
        
        $newBook = [
        "titre" => $titre,
        "auteur" => $auteur,
        "isbn" => $isbn,
        "categorie" => $categorie,
        "disponible" => $disponible
        ];

        $data['Livres'][] = $newBook;

        file_put_contents(FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "\n✅ Le livre a été ajouté avec succès!\n";
        fgets(STDIN);
        $menu->close();
        });
        $ajouter->addItem("Go Back", new GoBackAction);
    });   
    
    #Modifier
    $menuBuilder->addSubMenu('Modifier des livres', function (CliMenuBuilder $modifer) {
    $modifer->setTitle('Liste des livres disponibles')
    ->disableDefaultItems();

    $data = json_decode(file_get_contents(FILE), true) ?? ['Livres' => []];
    $file = FILE;
    foreach ($data['Livres'] as $index => $book) {
        $modifer->addSubMenu($book['titre'], function (CliMenuBuilder $modifer2) use (&$data, $index, $file) {
            $modifer2->setTitle("Modifier le livre: " . $data['Livres'][$index]['titre'])
            ->disableDefaultItems();

            $fields = ['titre', 'auteur', 'isbn', 'categorie', 'disponible'];

            foreach ($fields as $field) {
                $modifer2->addItem("Modifier $field", function (CliMenu $menu) use (&$data, $index, $file, $field) {
                    echo "Nouvelle valeur pour $field: ";
                    $newValue = trim(fgets(STDIN));

                    if ($field === 'disponible') {
                    $newValue = $newValue == 0 ? false : true;
                    }

                    $data['Livres'][$index][$field] = $newValue;
                    file_put_contents(FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                    echo "\n✅ $field modifié avec succès!\n";
                    echo "Appuyez sur Entrée pour revenir au menu...";
                    fgets(STDIN);
                    $menu->close();
                });
            }
            $modifer2->addItem('Go Back', new GoBackAction);
        });
    }
    $modifer ->addItem('Go Back', new GoBackAction);
    });
    
    #Supprimer
    $menuBuilder->addSubMenu('Supprimer des Livres', function (CliMenuBuilder $supprimer) {
        $supprimer->setTitle('Liste des livres disponibles')
        ->disableDefaultItems();

        $data = json_decode(file_get_contents(FILE), true) ?? ['Livres' => []];
        $file = FILE;

        foreach ($data['Livres'] as $index => $book) {
            $supprimer->addSubMenu($book['titre'], function (CliMenuBuilder $supprimer2) use (&$data, $index, $file){
                $supprimer2->setTitle("Supprimer")
                ->disableDefaultItems();
                $supprimer2->addItem("Oui", function(CliMenu $menu) use (&$data, $index, $file){
                    array_splice($data['Livres'], $index , 1);
                    file_put_contents(FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    echo "\n✅ Livre supprimé avec succès!\n";
                    fgets(STDIN);
                    $menu->close();
                });
                $supprimer2->addItem('Non', new GoBackAction);
            });
        }
        $supprimer->addItem('Go Back', new GoBackAction);
        
    });
    
    #Rechercher 
    $menuBuilder->addSubMenu('Rechercher des Livres', function(CliMenuBuilder $rechercher){
        $rechercher->setTitle('Rechercher un livre')
        ->disableDefaultItems();
        $data = json_decode(file_get_contents(FILE), true) ?? ['Livres' => []];
        $file = FILE;
        $found = false;
        $rechercher->addItem("Rechercher", function(CliMenu $menu) use (&$data, $file, &$found){
            $request_rechercher = strtolower(readline("Rechercher un livre: "));
            echo $request_rechercher . "\n";
            foreach($data['Livres'] as $book){
                if(str_contains(strtolower($book['titre']), $request_rechercher)){
                    $found = true;
                    echo $book['titre'] . "\n";
                }
            }

            if(!$found){
                echo "Nothing found...\n";
            }
            echo 'Press enter to continue!';
            fgets(STDIN);
            $menu->close();
        });
        $rechercher->addItem('Go Back', new GoBackAction);
    });
    return $menuBuilder->build();
}