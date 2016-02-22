<?php

echo ('
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>PROTOVIZ JS</title>
	<link rel="stylesheet" href="/protoviz-js/style.css">
        <link rel="stylesheet" href="/protoviz-js/infobulles.css">
        <script src="//d3js.org/d3.v3.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
</head>
<body>
	<header><h1>PROTO204 DAVATIZ</h1>
        <center id="sous_titre">100% JS !</center></header>
        
	<p class="description">
		ceci <a rel="tooltip" title="ragraphs generally range three to seven sentences all combined in a single paragraphed statement. In prose fiction successive details, for example; but it is just as common for the point of a prose paragraph to occur in the middle or the end. A paragraph can be as short as one word or run the length of multiple pages, and may consist of one or many sentences. When dialogue is being quoted in fiction, a new paragraph is used each time the person being quoted changed. "> rzqtr </a>
	</p>
');

require_once 'PHPExcel/IOFactory.php';

// Chargement du fichier Excel
$objPHPExcel = PHPExcel_IOFactory::load("dataviz.xlsx");
 
// Parsage du fichier
$sheet = $objPHPExcel->getSheet(0);

include "visiteurs_cumules_1.php";
//include "visiteurs_horaire_1.php";

//include

/*
// On boucle sur les lignes
foreach($sheet->getRowIterator() as $row) {
 

   echo ' <br /> ';
 
   // On boucle sur les cellule de la ligne
   foreach ($row->getCellIterator() as $cell) {
      echo ' ';
      print_r($cell->getValue());
      echo ' ';
   }
 
   echo ' ';
}
 
 */


echo ('
</body>

</html>
');
?>