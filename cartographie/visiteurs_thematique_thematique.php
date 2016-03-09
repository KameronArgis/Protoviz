<?php

/* Ce fichier sert à générer le fichier de données necessaire à l'affichage du graphique. */

require_once '../vendor/PHPExcel/IOFactory.php';

// Chargement du fichier Excel
$objPHPExcel = PHPExcel_IOFactory::load("../data/dataviz.xlsx");
 
// Parsage du fichier
$sheet = $objPHPExcel->getSheet(0);

$nb_prs = 0;
$nb_traite = 250;
unlink ('visiteurs_thematique_thematique.csv');
$visiteurs_public_public_csv = fopen('visiteurs_thematique_thematique.csv', 'a+');


// CRÉATION DU TABLEAU PUBLIC
$effectif_public_public = Array(Array());
$publics = Array();
$publics_evt = Array(Array());
for ($i = 3; $i <= $nb_traite; $i++) {
    $publics_evt [$i] = explode (", ", $sheet->getCell("I".$i)->getValue());
    foreach ($publics_evt [$i] as $type) {
        if (!in_array ($type, $publics, 1)) {
            $publics[] = $type;
        }
    }
}

/*
//Initialisation de public_public
$d = 1;
foreach ($publics as $p1) {
    $e=1;
    foreach ($publics as $p2) {
        if (($p1 != $p2) AND ($e >= $d)) {
            $effectif_public_public [$p1] [$p2] = 0;
        }
        $e++;
    }
    $d++;
}
 * 
 */

// Écriture du tableau de tous les types de public trouvés dans le XLS
//$effectif_public_public = array("étudiants" => Array("étudiants" => 0));
//$types_public = Array();
//$effectif_types_public = Array();
for ($i = 3; $i <= $nb_traite; $i++) {
    $publics_evt = explode (", ", $sheet->getCell("I".$i)->getValue());
    $d = 1;
    foreach ($publics_evt as $indice_a => $public_evt_a) {
        $e = 1;
        foreach ($publics_evt as $indice_b => $public_evt_b) {
            if (($indice_a != $indice_b) AND ($e >= $d)) {
                if (isset($effectif_public_public[$public_evt_a][$public_evt_b])) {
                    $effectif_public_public[$public_evt_a][$public_evt_b] = $effectif_public_public[$public_evt_a][$public_evt_b] + $sheet->getCell("E".$i)->getValue();
                }
                else {
                    $effectif_public_public[$public_evt_a][$public_evt_b] = $sheet->getCell("E".$i)->getValue();
                }
            }
            $e++;
        }
        $d++;
    }
}

//var_dump ($effectif_public_public);

// Écriture de la chaîne d'entête
$visiteurs_public_public_chaine = "source,target,value\n";
$visiteurs_public_public_types = Array(Array());

// Écriture du contenu du tableau

foreach ($effectif_public_public as $indice_a => $a) {
    foreach ($a as $indice_b => $effectif) {
        $visiteurs_public_public_chaine = $visiteurs_public_public_chaine . $indice_a . "," . $indice_b . "," . $effectif/100 . "\n";
    }
}


//echo $visiteurs_public_public_chaine;
fputs($visiteurs_public_public_csv, $visiteurs_public_public_chaine);
fclose($visiteurs_public_public_csv);

// Renvoi vers la page d'affichage
require "visiteurs_thematique_thematique.html";
