<?php

$db = new PDO('mysql:host=localhost;dbname=wikitabb_wiki2;charset=utf8mb4', 'USUARIO', 'CONTRASEÑA'); // Cambiar

try {
    $rows = $db->query('Select id from tab_rev');
} catch(PDOException $ex) {
    echo "An Error occurred!"; //user friendly message
}

foreach ($rows as $row) {
	$id = $row['id'];
	$sqlQuery = 'select TAB_REV.ID as ID, TAB.ID as TAB_ID, TAB_REV.NOTATION as NOTATION, TAB_REV.TAB as TAB,SONG.TITLE as TITLE 
					from TAB_REV,TAB inner join SONG on TAB.SONG_ID = SONG.ID
						where TAB_REV.TAB_ID=' . $id . '
						and TAB.ID=' . $id . '
						 order by TAB_REV.ID desc limit 1';
	try {
		$tabs = $db->query($sqlQuery);
	} catch(PDOException $ex) {
		echo "An Error occurred!"; //user friendly message
	}
	foreach ($tabs as $tab) {
		$title = $tab['TITLE'];
		
		// Hacer un nombre de fichero aceptable para Windows
		$filename = iconv("UTF-8", "ISO-8859-9//TRANSLIT", $title);
		$filename = str_replace("/", "-", $filename);
		$filename = str_replace("?", "", $filename);
		$filename = str_replace("¿", "", $filename);
		
		@$f = fopen("$filename.html", "w");
		
		if ($f == FALSE) {
			echo "Fallo al intentar crear el fichero $filename\n";
			continue;
		}
		
		fwrite($f, 
'<html>'."\n".
'  <head>'."\n".
'    <meta charset="UTF-8">'."\n".
"    <title>$title</title>"."\n".
'    <link rel="stylesheet" title="Kamikazes" type="text/css" href="../Estilo_kamikazes.css">'."\n".
'   <link rel="alternate stylesheet" title="Imprimir canción" type="text/css" href="../Estilo_imprimir.css">'."\n".
'  </head>'."\n".
'  <body>'."\n".
'    <div id="flag">'."\n".
'    <h1>Y sangrando los dedos</h1>'."\n".
'    <div id="borde_inf"></div></div><div id="contenido">'."\n".
"    <h2>$title</h2>"."\n".
'    <pre>');
		
		fwrite($f, "$title\n");
		
		$chordpro = $tab['TAB'];
		fwrite($f, "$chordpro\n");
		
		fwrite($f,
'    </pre>'."\n".
'    </div>'."\n".
'    <div id="pie">'."\n\n".
'    <p><a href="../index.html">Volver a la página inicial</a></p>'."\n".
'    </div>'."\n".
'  </body>'."\n".
'</html>');
	}
}

?>