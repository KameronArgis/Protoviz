<?php

/* Ce fichier sert à générer le fichier de données necessaire à l'affichage du graphique. */

setlocale (LC_TIME, 'fr_FR.utf8','fra'); 

require_once '../vendor/PHPExcel/IOFactory.php';

// Chargement du fichier Excel
$objPHPExcel = PHPExcel_IOFactory::load("../data/dataviz.xlsx");
 
// Parsage du fichier
$sheet = $objPHPExcel->getSheet(0);

unlink ('data.json');
$visiteurs_cumules_tsv = fopen('data.json', 'a+');

$nb_prs = 0;
$nb_traite = 262;

//Calculateur de temps
function excelDateToStandardDate($days) {
    return date("Y-m-d",  strtotime (date("Y-m-d", $days * 86400 - 2209118400)));
}

/*
//Calculateur d'URL
function calculateur_URL($parametres, $plus, $moins) {
    $url = "http://production.action-creation.eu/protoviz-js/index.html.php?";
    $i = 0;
    foreach ($parametres as $p){
        if (!is_null($moins)){
            if ($p["valeur"] != $moins["valeur"]) {
                $url = $url.$p["type"]."_".$i."=".$p["valeur"]."&";
            }
        } else {
            $url = $url.$p["type"]."_".$i."=".$p["valeur"]."&";
        }
        $i++;
    }
    if (!is_null($plus)){
        $url = $url.$plus["type"]."_".$i."=".$plus["valeur"];
    }
    return $url;
}
 
 */

$public_select=Array();$thematique_select=Array();$format_select=Array();
// CRÉATION DES 3 TABLEAUX PUBLIC / THEMATIQUE / FORMAT
foreach ($_GET as $id_parametre => $item_parametre) {
    if (explode ("_", $id_parametre)[0] == "p") {
        $public_select[] = mb_strtolower($item_parametre,'UTF-8');
        $tous_parametres_select[] = array("type" => "p" , "valeur" => mb_strtolower($item_parametre,'UTF-8'));
    }
    else if (explode ("_", $id_parametre)[0] == "t") {
        $thematique_select[] = mb_strtolower($item_parametre,'UTF-8');
        $tous_parametres_select[] = array("type" => "t" , "valeur" => mb_strtolower($item_parametre,'UTF-8'));
    }
    else if (explode ("_", $id_parametre)[0] == "f") {
        $format_select[] = mb_strtolower($item_parametre,'UTF-8');
        $tous_parametres_select[] = array("type" => "f" , "valeur" => mb_strtolower($item_parametre,'UTF-8'));
    }
}

/*
// Écriture du tableau de tous les types de public trouvés dans le XLS
$types_public_evt = Array(Array());
$types_public = Array();
$effectif_types_public = Array();
for ($i = 3; $i <= $nb_traite; $i++) {
    $types_public_evt [$i] = explode (", ", $sheet->getCell("H".$i)->getValue());
    foreach ($types_public_evt [$i] as $type) {
        if (!in_array ($type, $types_public, 1)) {
            $types_public[] = $type;
            $effectif_types_public [$type] = 0;
        }
    }
}

// Écriture du tableau de toutes les types de thematiques trouvées dans le XLS
$types_thematique_evt = Array(Array());
$types_thematique = Array();
$effectif_types_thematique = Array();
for ($i = 3; $i <= $nb_traite; $i++) {
    $types_thematique_evt [$i] = explode (", ", $sheet->getCell("I".$i)->getValue());
    foreach ($types_thematique_evt [$i] as $type) {
        if (!in_array ($type, $types_thematique, 1)) {
            $types_thematique[] = $type;
            $effectif_types_thematique [$type] = 0;
        }
    }
}

// Écriture du tableau de tous les types de formats trouvés dans le XLS
$types_format_evt = Array(Array());
$types_format = Array();
$effectif_types_format = Array();
for ($i = 3; $i <= $nb_traite; $i++) {
    $types_format_evt [$i] = explode (", ", $sheet->getCell("G".$i)->getValue());
    foreach ($types_format_evt [$i] as $type) {
        if (!in_array ($type, $types_format, 1)) {
            $types_format[] = $type;
            $effectif_types_format [$type] = 0;
        }
    }
}
*/
//var_dump($types_public);

// Écriture de la chaîne d'entête
$visiteurs_cumules_chaine = "{\n";
$visiteurs_cumules_types = Array();
$nb_cumule = 0;

// Écriture du contenu du tableau
for ($i = 1; $i <= $nb_traite; $i++) {
    $dte_evt = excelDateToStandardDate($sheet->getCell("B" . $i)->getValue());
    $nb_prs = $sheet->getCell("E" . $i)->getValue();
    $nom_evt = $sheet->getCell("C" . $i)->getValue();
    $nb_cumule = $nb_cumule + $nb_prs;
    $visiteurs_cumules_chaine = $visiteurs_cumules_chaine . "	{date: \"" . $dte_evt . "\", name: \"" . $nom_evt . "\", visteurs_cumules: " . $nb_cumule . ", visiteurs: " . $nb_prs . "},\n";
    /*foreach ($types_public_evt[$i] as $type) {
        $visiteurs_cumules_chaine = $visiteurs_cumules_chaine . "	" . $type . ",";
    }*/
    //$visiteurs_cumules_chaine = $visiteurs_cumules_chaine . "\n";
}
$visiteurs_cumules_chaine = $visiteurs_cumules_chaine . "}";

fputs($visiteurs_cumules_tsv, $visiteurs_cumules_chaine);
fclose($visiteurs_cumules_tsv);

echo ('
<h2>Visiteurs cumulés croissants </h2>
<style>

body {
  font: 12px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.browser text {
  text-anchor: end;
}

</style>
<script>

  var margin = {top: 20, right: 100, bottom: 30, left: 50},
    width = 1000 - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

var parseDate = d3.time.format("%d-%b-%y").parse,
    formatPercent = d3.format(".0%");

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var color = d3.scale.category20();

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var area = d3.svg.area()
    .x(function(d) { return x(d.date); })
    .y0(function(d) { return y(d.y0); })
    .y1(function(d) { return y(d.y0 + d.y); });

var stack = d3.layout.stack()
    .values(function(d) { return d.values; });

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.tsv("visiteurs_cumules.tsv", function(error, data) {
  if (error) throw error;

  color.domain(d3.keys(data[0]).filter(function(key) { return key !== "date"; }));

  data.forEach(function(d) {
    d.date = parseDate(d.date);
  });

  var browsers = stack(color.domain().map(function(name) {
    return {
      name: name,
      values: data.map(function(d) {
        return {date: d.date, y: d[name] / 100};
      })
    };
  }));

  x.domain(d3.extent(data, function(d) { return d.date; }));

  var browser = svg.selectAll(".browser")
      .data(browsers)
    .enter().append("g")
      .attr("class", "browser");

  browser.append("path")
      .attr("class", "area")
      .attr("d", function(d) { return area(d.values); })
      .style("fill", function(d) { return color(blue); });

  browser.append("text")
      .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
      .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.y0 + d.value.y / 2) + ")"; })
      .attr("x", 100)
      .attr("dy", ".35em");

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);


});

</script>

<script type="text/javascript">
 
$(document).ready(function() {
 
    // Sélectionner tous les liens ayant l attribut rel valant tooltip
    $(\'a[rel=tooltip]\').mouseover(function(e) {
 
        // Récupérer la valeur de l attribut title et l assigner à une variable
        var tip = $(this).attr(\'title\');   
 
        // Supprimer la valeur de l attribut title pour éviter l infobulle native
        $(this).attr(\'title\',"");
 
        // Insérer notre infobulle avec son texte dans la page
        $(this).append("<div id="tooltip"><div class="tipHeader"></div><div class="tipBody">" + tip + "</div><div class="tipFooter"></div></div>");    
 
        // Ajuster les coordonnées de l infobulle
        $("#tooltip").css("top", e.pageY + 10 );
        $("#tooltip").css("left", e.pageX + 20 );
 
        // Faire apparaitre l infobulle avec un effet fadeIn
        $("#tooltip").fadeIn("500");
        $("#tooltip").fadeTo("10",0.8);
 
    }).mousemove(function(e) {
 
        // Ajuster la position de l infobulle au déplacement de la souris
        $("#tooltip").css("top", e.pageY + 10 );
        $("#tooltip").css("left", e.pageX + 20 );
 
    }).mouseout(function() {
 
        // Réaffecter la valeur de l attribut title
        $(this).attr("title",$(".tipBody").html());
 
        // Supprimer notre infobulle
        $(this).children("div#tooltip").remove();
 
    });
 
});
 
</script>
<style>
body {font-family:arial;font-size:12px;text-align:center;}
div#paragraph {width:300px;margin:0 auto;text-align:left}
a {color:#aaa;text-decoration:none;cursor:pointer;cursor:hand}
a:hover {color:#000;text-decoration:none;}
 
/* Infobulle */
 
#tooltip {
    position:absolute;
    z-index:9999;
    color:#fff;
    font-size:10px;
    width:180px;
 
}
 
#tooltip .tipHeader {
    height:8px;
    background:url(images/tipHeader.gif) no-repeat;
}
 
/* hack IE */
*html #tooltip .tipHeader {margin-bottom:-6px;}
 
#tooltip .tipBody {
    background-color:#000;
    padding:5px;
}
 
#tooltip .tipFooter {
    height:8px;
    background:url(images/tipFooter.gif) no-repeat;
}
 
</style>
<br />
<div id="selection">
    <div id="publics" class="choix">
        <h3 class="public">PUBLICS</h3>
        <div class="items public">
');
/*
foreach ($types_public as $p) {
    if (in_array($p, $public_select)) {
        echo ('
            <div class="public item on">
                <a rel="tooltip" href="'.calculateur_URL($tous_parametres_select, NULL, array("type" => "p", "valeur" => $p)).'">
                    <img src="images/publics/'.$p.'.png" alt="'.$p.'" width="200px" class="item_image">
                </a>
            </div>
        ');
    }
    else {
        echo ('
            <div class="public item off">
                <a rel="tooltip" href="'.calculateur_URL($tous_parametres_select, array("type" => "p", "valeur" => $p), NULL).'">
                    <img src="images/publics/'.$p.'.png" alt="'.$p.'" width="200px" class="item_image">
                </a>
            </div>
        ');
    }
}
echo ('
        </div>
    </div>
<!--
    <div id="thematiques" class="choix">
        <h3 class="thematique">THÉMATIQUES</h3>
        <div class="items thematique">
');
foreach ($types_thematique as $t) {
    if (in_array($t, $thematique_select)) {
        echo ('
            <div class="thematique item on">
                <a href="'.calculateur_URL($tous_parametres_select, NULL, array("type" => "t", "valeur" => $t)).'">
                    <img src="images/thematiques/startups.png" alt="'.$t.'" width="50px" class="item_image">
                </a>
            </div>
        ');
    }
    else {
        echo ('
            <div class="thematique item off">
                <a href="'.calculateur_URL($tous_parametres_select, array("type" => "t", "valeur" => $t), NULL).'">
                    <img src="images/thematiques/startups.png" alt="'.$t.'" width="100px" class="item_image">
                </a>
            </div>
        ');
    }
}
echo ('
        </div>
    </div>

    <div id="formats" class="choix">
        <h3 class="format">FORMATS</h3>
        <div class="items format">
');
foreach ($types_format as $f) {
    if (in_array($f, $format_select)) {
        echo ('
            <div class="format item on">
                <a href="'.calculateur_URL($tous_parametres_select, NULL, array("type" => "f", "valeur" => $f)).'">
                    <p class="item_texte format"><br />'."&nbsp;&nbsp;&nbsp;".$f.'</p>
                </a>
            </div>
        ');
    }
    else {
        echo ('
            <div class="format item off">
                <a href="'.calculateur_URL($tous_parametres_select, array("type" => "f", "valeur" => $f), NULL).'">
                    <p class="item_texte format"><br />'."&nbsp;&nbsp;&nbsp;".$f.'</p>
                </a>
            </div>
        ');
    }
}
echo (' </div>
    </div>
</div>
-->


<!-- ITEMS NON SELECTIONNÉS -->
<!--
<div id="selection">
    <div id="publics" class="choix">
');
foreach (array_diff($types_public, $public_select) as $p) {
    echo ('
        <div class="item off">
            <a href="'.calculateur_URL($tous_parametres_select, array("type" => "p", "valeur" => $p), NULL).'">
                <img src="images/publics/'.$p.'.png" alt="'.$p.'" width="50px" class="item_image">
                <p class="item_texte public"><br />'."&nbsp;&nbsp;&nbsp;".$p.'</p>
            </a>
        </div>
    ');
}
echo ('
    </div>

    <div id="thematiques" class="choix">
');
foreach (array_diff($types_thematique, $thematique_select) as $t) {
    echo ('
        <div class="item off">
            <a href="'.calculateur_URL($tous_parametres_select, array("type" => "t", "valeur" => $t), NULL).'">
                <img src="images/thematiques/startups.png" alt="'.$t.'" width="50px" class="item_image">
                <p class="item_texte thematique"><br />'."&nbsp;&nbsp;&nbsp;".$t.'</p>
            </a>
        </div>
    ');
}
echo ('
    </div>

    <div id="formats" class="choix">
');
foreach (array_diff($types_format, $format_select) as $f) {
    echo ('
        <div class="item off">
            <a href="'.calculateur_URL($tous_parametres_select, array("type" => "f" , "valeur" => $f), NULL).'">
                <p class="item_texte format"><br />'."&nbsp;&nbsp;&nbsp;".$f.'</p>
            </a>
        </div>
    ');
}
echo ('
    </div>
</div>
-->
');


*/