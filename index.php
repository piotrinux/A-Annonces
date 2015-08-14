<?php

//On inclu le fichier de config
include("admin/config.php");
include("functions.inc.php");

// On se connecte à MySQL
$mysql_link = mysql_connect($MySQL_Hote, $MySQL_Login, $MySQL_Pass);
mysql_select_db($MySQL_Base);

// Définition des variables
global $Menu_HTML;

// Fonctin JavaScript pour image
echo "
<script language='JavaScript'>
	function Image_Popup(Adresse)
	{
		if (document.all)
			var xMax = screen.width, yMax = screen.height;
		else
			if (document.layers)
				var xMax = window.outerWidth, yMax = window.outerHeight;
			else
				var xMax = 640, yMax=480;
				var xOffset = (xMax - 300)/2, yOffset = (yMax - 300)/2;
				window.open(Adresse,'windowbis','width=300,height=300,scrollbars=yes,resizable=yes,screenX='+xOffset+',screenY='+yOffset+',top='+yOffset+',left='+xOffset+'');
	}
</script>
";

// Vérification des annonces
VerifTemps();

// Création du menu
$SQL = "SELECT * FROM $Table_Rub";
$mysql_result = mysql_query($SQL, $mysql_link);

while ($Rubriques = mysql_fetch_array($mysql_result)) {
	// On recherche le nombres d'annonces dans cette rubrique
	$SQL2 = "SELECT * FROM $Table_Annonces WHERE Rubrique='$Rubriques[ID]' AND Valid='1'";
	$mysql_result2 = mysql_query($SQL2, $mysql_link);
	$Nb_Annonces = mysql_num_rows($mysql_result2);

	$Rubriques[Nom] = stripslashes($Rubriques[Nom]);
	$Rubriques[Nb] = $Nb_Annonces;
	
	$id_parent = $Rubriques[Parent];
    if(!$id_parent) $RubriquessSujets[] = $Rubriques; 
    else $RubriquessFils[$id_parent][] = $Rubriques;
}

while(list(,$Titres) = each($RubriquessSujets)) {
	if ($Retour == 1) {
		$Menu_HTML .= "<br>";
	}
	
 	$Menu_HTML .= "- <a href=\"index.php?p=lire&rub=$Titres[ID]&nom_rubrique=$Titres[Nom]\">$Titres[Nom] ($Titres[Nb])</a><br>";
	CreateMenu($RubriquessFils,$Titres[ID],1);
	$Retour = 1;
}

// Formulaire de recherche
$Menu_HTML .= "<table border=\"0\">";
$Menu_HTML .= "<tr>";
$Menu_HTML .= "	<form action=\"index.php\" method=\"post\">";
$Menu_HTML .= "	<input type=\"hidden\" name=\"p\" value=\"recherche\">";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td align=\"center\"><b>.: Moteur de recherche :.</b></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td><input type=\"text\" name=\"Mot_Recherche\" size=\"17\"></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\".: Recherche :.\"></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	</form>";
$Menu_HTML .= "</tr>";
$Menu_HTML .= "</table><br>";
// Ajout du formulaire d'admin
$Menu_HTML .= "<table border=\"0\">";
$Menu_HTML .= "<tr>";
$Menu_HTML .= "	<form action=\"index.php\" method=\"post\">";
$Menu_HTML .= "	<input type=\"hidden\" name=\"p\" value=\"admin\">";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td align=\"center\" colspan=\"2\"><b>.: Administration :.</b></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td><b>ID :</b></td>";
$Menu_HTML .= "		<td><input type=\"text\" name=\"ID\" size=\"10\"></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td><b>Pass :</b></td>";
$Menu_HTML .= "		<td><input type=\"password\" name=\"Password\" size=\"10\"></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	<tr>";
$Menu_HTML .= "		<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\".: Admin :.\"></td>";
$Menu_HTML .= "	</tr>";
$Menu_HTML .= "	</form>";
$Menu_HTML .= "</tr>";
$Menu_HTML .= "</table><br>";
$Menu_HTML .= "<center><b><a href=\"http://www.mistersp.com\">MisterSP ©</a></b></center>";

// On regarde si il veut que l'on lise
if ($p == "lire") {
$Page_HTML .= "<b><center><font size=\"3\">LECTURE D'ANNONCES</font></center></b><br>";
	// Ajout du lien pour ajouter une annonce dans cette rubrique
	$Page_HTML .= "<center><a href=\"index.php?p=ajouter&rub=$rub&nom_rubrique=$nom_rubrique\">&gt; Ajout d'une annonce dans cette rubrique &lt;</a></center><br>";
	
	// On lit
	$SQL = "SELECT * FROM $Table_Annonces WHERE Rubrique='$rub' AND Valid='1'";
	$mysql_result = mysql_query($SQL, $mysql_link);
	$Nb_Enregistrements = mysql_num_rows($mysql_result);
	
	if(empty($page)) {
		$page = "1";
	}
	$Debut_Lecture = (($page * $Nb_Annonces_Page) - ($Nb_Annonces_Page));
	
	$SQL = "SELECT * FROM $Table_Annonces WHERE Rubrique='$rub' AND Valid='1' ORDER BY ID DESC LIMIT $Debut_Lecture, $Nb_Annonces_Page";
	
	// print "<br><br>$SQL<br><br>";
	
	$mysql_result = mysql_query($SQL, $mysql_link);
	
	while($row = mysql_fetch_row($mysql_result)) {
		// Affichage de chaque annonce
		$A_ID = $row[0];
		$A_Rubrique = $row[1];
		$A_Temps = $row[2];
		$A_Image = $row[3];
		$A_Nom = $row[4];
		$A_Prenom = $row[5];
		$A_Telephone = $row[6];
		$A_Mail = $row[7];
		$A_Annonce = $row[8];
		$A_Password = $row[9];
		$A_NbContact = $row[11];
		
		$A_Nom = stripslashes($A_Nom);
		$A_Prenom = stripslashes($A_Prenom);
		$A_Password = stripslashes($A_Password);
		$A_Annonce = stripslashes($A_Annonce);
		$A_Mail = stripslashes($A_Mail);
		
		// On récupère la template pour afficher afficher l'annonce suivant cette dernière
		$SQL2 = "SELECT * FROM $Table_Template WHERE ID='2'";	
		$mysql_result2 = mysql_query($SQL2, $mysql_link);
		
		while ($row2 = mysql_fetch_row($mysql_result2)) {
			$T_HTML = $row2[1];
		}
		$T_HTML2 = $T_HTML;
		
		$T_HTML2 = stripslashes($T_HTML2);
		
		// On modifie
		$T_HTML2 = str_replace('[Nom]', $A_Nom, $T_HTML2);
		$T_HTML2 = str_replace('[Prenom]', $A_Prenom, $T_HTML2);
		$T_HTML2 = str_replace('[Mail]', "<a href=\"mailto:$A_Mail\">$A_Mail</a>", $T_HTML2);
		$T_HTML2 = str_replace('[Telephone]', $A_Telephone, $T_HTML2);
		if ($A_Image != "") {
			$T_HTML2 = str_replace('[Image]', "<a href='#' Onclick='Image_Popup(\"images/$A_Image\")'>Voir l'image</a>", $T_HTML2);
		} else {
			$T_HTML2 = str_replace('[Image]', "Inexistant...", $T_HTML2);
		}
		$T_HTML2 = str_replace('[Message]', $A_Annonce, $T_HTML2);
		
		// Affichage de la page
		$Page_HTML .= $T_HTML2;
	}
	
	$Page_HTML .= "<div align='center'>";
	
	if ($Nb_Enregistrements > $Nb_Annonces_Page) {
		$Nb_Pages = ceil($Nb_Enregistrements / $Nb_Annonces_Page);
		
		for ($i=1; $i<=$Nb_Pages; $i++) {
			if ($i != $page) {
				$Page_HTML .= "<b><a href='index.php?page=".($i)."&rub=".$rub."&p=lire'> [Page $i]</a></b>";
			} else {
				$Page_HTML .= "<b> [Page $i]</b>";
			}
		}
	}
	
	$Page_HTML .= "</div>";
	
	if ($Nb_Enregistrements == 0) {
		$Page_HTML .= "Aucune annonce trouvée dans cette rubrique actuellement.";
	}
	
	// Ajout du lien pour ajouter une annonce dans cette catégorie
	$Page_HTML .= "<br><br><center><a href=\"index.php?p=ajouter&rub=$rub&nom_rubrique=$nom_rubrique\">&gt; Ajout d'une annonce dans cette rubrique &lt;</a></center>";
}	

// On regarde si il veut que l'on recherche des annonces
if ($p == "recherche") {
$Page_HTML .= "<b><center><font size=\"3\">RECHERCHE D'ANNONCES</font></center></b><br>";	
	// On lit
	$SQL = "SELECT * FROM $Table_Annonces WHERE Annonce LIKE '%$Mot_Recherche%' ";
	$mysql_result = mysql_query($SQL, $mysql_link);
	$Nb_Enregistrements = mysql_num_rows($mysql_result);
	
	if(empty($page)) {
		$page = "1";
	}
	$Debut_Lecture = (($page * $Nb_Annonces_Page) - ($Nb_Annonces_Page));
	
	$SQL = "SELECT * FROM $Table_Annonces WHERE Annonce LIKE '%$Mot_Recherche%' AND Valid='1' ORDER BY ID DESC LIMIT $Debut_Lecture, $Nb_Annonces_Page";
	$mysql_result = mysql_query($SQL, $mysql_link);
	
	while($row = mysql_fetch_row($mysql_result)) {
		// Affichage de chaque annonce
		$A_ID = $row[0];
		$A_Rubrique = $row[1];
		$A_Temps = $row[2];
		$A_Image = $row[3];
		$A_Nom = $row[4];
		$A_Prenom = $row[5];
		$A_Telephone = $row[6];
		$A_Mail = $row[7];
		$A_Annonce = $row[8];
		$A_Password = $row[9];
		
		$A_Nom = stripslashes($A_Nom);
		$A_Prenom = stripslashes($A_Prenom);
		$A_Password = stripslashes($A_Password);
		$A_Annonce = stripslashes($A_Annonce);
		$A_Mail = stripslashes($A_Mail);
		
		// On récupère la template pour afficher afficher l'annonce suivant cette dernière
		$SQL2 = "SELECT * FROM $Table_Template WHERE ID='2'";	
		$mysql_result2 = mysql_query($SQL2, $mysql_link);
		
		while ($row2 = mysql_fetch_row($mysql_result2)) {
			$T_HTML = $row2[1];
		}
		$T_HTML2 = $T_HTML;
		
		$T_HTML2 = stripslashes($T_HTML2);
		
		// On modifie
		$T_HTML2 = str_replace('[Nom]', $A_Nom, $T_HTML2);
		$T_HTML2 = str_replace('[Prenom]', $A_Prenom, $T_HTML2);
		$T_HTML2 = str_replace('[Mail]', "<a href=\"mailto:$A_Mail\">$A_Mail</a>", $T_HTML2);
		$T_HTML2 = str_replace('[Telephone]', $A_Telephone, $T_HTML2);
		if ($A_Image != "") {
			$T_HTML2 = str_replace('[Image]', "<a href='#' Onclick='Image_Popup(\"images/$A_Image\")'>Voir l'image</a>", $T_HTML2);
		} else {
			$T_HTML2 = str_replace('[Image]', "Inexistant...", $T_HTML2);
		}
		$T_HTML2 = str_replace('[Message]', $A_Annonce, $T_HTML2);
		
		// Affichage de la page
		$Page_HTML .= $T_HTML2;
	}
	
	$Page_HTML .= "<div align='center'>";
	
	if ($Nb_Enregistrements > $Nb_Annonces_Page) {
		$Nb_Pages = ceil($Nb_Enregistrements / $Nb_Annonces_Page);
		
		for ($i=1; $i<=$Nb_Pages; $i++) {
			if ($i != $page) {
				$Page_HTML .= "<b><a href='index.php?page=".($i)."&p=recherche&Mot_Recherche=$Mot_Recherche'> [Page $i]</a></b>";
			} else {
				$Page_HTML .= "<b> [Page $i]</b>";
			}
		}
	}
	
	$Page_HTML .= "</div>";
	
	if ($Nb_Enregistrements == 0) {
		$Page_HTML .= "Aucune annonce trouvée pour ces mots.";
	}
}	

if ($p == "ajouter") {
// 1j : 86400
// 2j : 172800
// 3j : 259200
// 1s : 604800
// 2s : 120900
// 3s : 1814400
// 1m : 2592000
	$Page_HTML .= "<b><center><font size=\"3\">AJOUT D'UNE ANNONCE</font></center></b><br>";
	$Page_HTML .= "<div align=\"center\">";
	$Page_HTML .= "<table border=\"0\">";
	$Page_HTML .= "	<form action=\"index.php\" ENCTYPE='multipart/form-data' method='post'>";
	$Page_HTML .= "	<input type=\"hidden\" name=\"p\" value=\"ajouter_go\">";
	$Page_HTML .= "	<input type=\"hidden\" name=\"rub\" value=\"$rub\">";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Nom :</b></td>";
	$Page_HTML .= "		<td><input type=\"text\" name=\"Nom\" size=\"20\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Prénom :</b></td>";
	$Page_HTML .= "		<td><input type=\"text\" name=\"Prenom\" size=\"20\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Mail :</b></td>";
	$Page_HTML .= "		<td><input type=\"text\" name=\"Mail\" size=\"20\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Téléphone :</b></td>";
	$Page_HTML .= "		<td><input type=\"text\" name=\"Telephone\" size=\"20\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Password :<br><small>Vous permet de modifier votre annonce.</small></b></td>";
	$Page_HTML .= "		<td><input type=\"password\" name=\"Password\" size=\"20\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Image :<br><small>Format : GIF, JPG, PNG</small></b></td>";
	$Page_HTML .= "		<td><input type=\"file\" name=\"Image\" size=\"20\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td><b>Temps de diffusion :</b></td>";
	$Page_HTML .= "		<td>";
	$Page_HTML .= "			<select name=\"Temps\">";
	$Page_HTML .= "				<option value=\"86400\"> 1 jour</option>";
	$Page_HTML .= "				<option value=\"172800\"> 2 jours</option>";
	$Page_HTML .= "				<option value=\"259200\"> 3 jours</option>";
	$Page_HTML .= "				<option value=\"604800\"> 1 semaine</option>";
	$Page_HTML .= "				<option value=\"120900\"> 2 semaines</option>";
	$Page_HTML .= "				<option value=\"1814400\"> 3 semaines</option>";
	$Page_HTML .= "				<option value=\"2592000\"> 1 mois</option>";
	$Page_HTML .= "				<option value=\"5184000\"> 2 mois</option>";
	$Page_HTML .= "				<option value=\"7776000\"> 3 mois</option>";
	$Page_HTML .= "				<option value=\"10368000\"> 4 mois</option>";
	$Page_HTML .= "				<option value=\"12960000\"> 5 mois</option>";
	$Page_HTML .= "				<option value=\"15552000\"> 6 mois</option>";
	$Page_HTML .= "			</select>";
	$Page_HTML .= "	</td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td colspan=\"2\"><b>Annonce :</b></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td colspan=\"2\"><textarea name=\"Annonce\" rows=\"7\" cols=\"50\"></textarea></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	<tr>";
	$Page_HTML .= "		<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\".: Ajouter votre annonce :.\"></td>";
	$Page_HTML .= "	</tr>";
	$Page_HTML .= "	</form>";
	$Page_HTML .= "</table>";
	$Page_HTML .= "</div>";
}	

if ($p == "ajouter_go") {
$Page_HTML .= "<b><center><font size=\"3\">AJOUT D'UNE ANNONCE</font></center></b><br>";
	if ($Nom != "" AND $Prenom != "" AND $Mail != "" AND $Password != "" AND $Annonce != "" AND $Temps <= 15552000 AND $rub != "") {
		// Ok
		$Date_Limite = date("d-m-Y",time()+$Temps);
		
		// Création d'un nom de fichier aléatoire
		$Pool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$Pool .= "abcdefghijklmnopqrstuvxyz";
		$Pool .= "0123456789";
		
		// Initialisation du compteur
		srand(time());
		
		for ($i=0; $i <= 8; $i++) {
			$Nom_Image .= substr($Pool, (rand()%(strlen($Pool))), 1);
		}
		
		if ($Image != "") {
			if(file_exists("$Image_name")) { $Nom_Image = ""; } else {
				if ($Image_type == "image/gif") { $Nom_Image .= ".gif"; }
				if ($Image_type == "image/pjpeg") { $Nom_Image .= ".jpg"; }
				if ($Image_type == "image/x-png") { $Nom_Image .= ".png"; }
				move_uploaded_file($Image,"images/$Nom_Image");
				//unlink($Image);
			}
		} else { $Nom_Image = ""; }
		
		// Traitement des données
		$Nom = addslashes($Nom);
		$Prenom = addslashes($Prenom);
		$Password = addslashes($Password);
		$Annonce = addslashes($Annonce);
		$Annonce = ereg_replace("<", "&lt;", $Annonce);
		$Annonce = ereg_replace(">", "&gt;", $Annonce);
		$Annonce = nl2br($Annonce);
		
		// On modifie la requete SQL en fonction de l'option de moderation choisi par l'administrateur
		if ($Moderation_Annonce == 1) {
			// Modération active
			$SQL = "INSERT INTO $Table_Annonces (Rubrique, Temps, Image, Nom, Prenom, Telephone, Mail, Annonce, Password, Valid) VALUES ('$rub', '$Date_Limite', '$Nom_Image', '$Nom', '$Prenom', '$Telephone', '$Mail', '$Annonce', '$Password', '0')";
			$mysql_result = mysql_query($SQL, $mysql_link);
		
			// On récupére l'ID
			$SQL = "SELECT * FROM $Table_Annonces WHERE Password='$Password' AND Mail='$Mail'";
			$mysql_result = mysql_query($SQL, $mysql_link);
		
			while($row = mysql_fetch_row($mysql_result)) {
				$A_ID = $row[0];
			}
			
			// On envoi un mail à l'administrateur
			$Msg_Mail = "Bonjour,<br><br>Nous vous écrivons car une personne vient de déposer l'annonce suivante sur le site :<br>";
			$Msg_Mail .= "<b>Nom :</b> $Nom<br>";
			$Msg_Mail .= "<b>Prenom :</b> $Prenom<br>";
			$Msg_Mail .= "<b>Telephone :</b> $Telephone<br>";
			$Msg_Mail .= "<b>Mail :</b> $Mail<br>";
			$Msg_Mail .= "<b>Annonce :</b><br>";
			$Msg_Mail .= "$Annonce";
			$Msg_Mail .= "<br><br><b>Que souhaitez vous faire de cette annonce :</b><br>";
			$Msg_Mail .= "<b><a href=\"$Adresse_AA/index.php?p=valid_annonce&ID=$A_ID&MailAnnonceur=$Mail&MailAdm=$Mail_Admin&Password=$Password_Admin&Login=$Login_Admin&Valid=1\">Je VALIDE l'annonce.</a></b><br><br><br>";
			$Msg_Mail .= "<b><a href=\"$Adresse_AA/index.php?p=valid_annonce&ID=$A_ID&MailAnnonceur=$Mail&MailAdm=$Mail_Admin&Password=$Password_Admin&Login=$Login_Admin&Valid=0\">Je SUPPRIME l'annonce.</a></b><br><br>";
			$Msg_Mail .= "A-Annonce $Version_Script";
			
			$Header_Mail = "From: $Mail_Admin \n"; 
			$Header_Mail .= "Reply-To: $Mail_Admin \n"; 
			$Header_Mail .= "X-Priority: 1 \n";
			$Header_Mail .= "Content-Type: text/html; \n";
			
			@mail($Mail_Admin, "[A-Annonce] - Moderation d'annonce",$Msg_Mail,$Header_Mail);  
			
			$Page_HTML .= "Votre annonce à bien été ajoutée. Nous attendons la confirmation de l'administrateur du site pour rendre cette dernière visible. Elle sera affichée jusqu'au <b>$Date_Limite inclus</b>.<br>";
			$Page_HTML .= "Vous pouvez à tout moment modifier et/ou supprimer cette annonce en allant dans votre administration gràce au formulaire se trouvant dans le menu.<br><br>Voici les informations :<br>";
			$Page_HTML .= "<b>ID :</b> $A_ID<br>";
			$Page_HTML .= "<b>Password :</b> $Password<br><br>";
			$Page_HTML .= "Nous vous remercions de votre participation.";
		} else {
			// Aucune modération
			$SQL = "INSERT INTO $Table_Annonces (Rubrique, Temps, Image, Nom, Prenom, Telephone, Mail, Annonce, Password, Valid) VALUES ('$rub', '$Date_Limite', '$Nom_Image', '$Nom', '$Prenom', '$Telephone', '$Mail', '$Annonce', '$Password', '1')";
			$mysql_result = mysql_query($SQL, $mysql_link);
		
			// On récupére l'ID
			$SQL = "SELECT * FROM $Table_Annonces WHERE Password='$Password' AND Mail='$Mail'";
			$mysql_result = mysql_query($SQL, $mysql_link);
		
			while($row = mysql_fetch_row($mysql_result)) {
				$A_ID = $row[0];
			}
			
			// On envoi un mail à l'administrateur
			@mail("");
			
			$Page_HTML .= "Votre annonce à bien été ajoutée dans cette annuaire. Elle sera affichée jusqu'au <b>$Date_Limite inclus</b>.<br>";
			$Page_HTML .= "Vous pouvez à tout moment modifier et/ou supprimer cette annonce en allant dans votre administration gràce au formulaire se trouvant dans le menu.<br><br>Voici les informations :<br>";
			$Page_HTML .= "<b>ID :</b> $A_ID<br>";
			$Page_HTML .= "<b>Password :</b> $Password<br><br>";
			$Page_HTML .= "Nous vous remercions de votre participation.";
		}
	}
	else {
		// Erreur
		$Page_HTML .= "<b>Erreur :</b><br>";
		$Page_HTML .= "- Vous devez correctement remplir le formulaire.<br>";
		$Page_HTML .= "- Le temps d'apparition pour l'annonce n'est pas correct.";
	}
}	

if ($p == "valid_annonce") {
	if ($ID == "" OR $MailAnnonceur == "" OR $MailAdm != $Mail_Admin OR $Password != $Password_Admin OR $Login != $Login_Admin OR $Valid == "") {
		$Page_HTML .= "<b>Erreur :</b><br>";
		$Page_HTML .= "- Vous n'êtes pas autorisé à executer cette page.<br>";
		$Page_HTML .= "- L'ID de l'annonce n'est pas valide.<br>";
		$Page_HTML .= "- Vous devez choisir une action à faire.";
	} else {
		if ($Valid == 1) {
			// On valide l'annonce
			$SQL = "UPDATE $Table_Annonces SET Valid='1' WHERE ID='$ID'";
			$mysql_result = mysql_query($SQL, $mysql_link);
			
			// On envoi un mail à l'annonceur
			$Msg_Mail = "Bonjour,<br><br>Nous vous écrivons pour confirmer la validation de votre annonce sur le site : $Adresse_AA.<br><br> Nous vous remercions de votre confiance est de votre participation.<br><br>";
			$Msg_Mail .= "A-Annonce $Version_Script<br><a href=\"http://www.mistersp.com\">http://www.mistersp.com -> Scripts PHP gratuits Et scripts Perl Et Services pour webmasters...</a>";
			
			$Header_Mail = "From: $Mail_Admin \n"; 
			$Header_Mail .= "Reply-To: $Mail_Admin \n"; 
			$Header_Mail .= "X-Priority: 1 \n";
			$Header_Mail .= "Content-Type: text/html; \n";
			
			@mail($MailAnnonceur, "[A-Annonce] - Validation d'annonce",$Msg_Mail,$Header_Mail);  
			
			$Page_HTML .= "L'annonce $ID à bien été valildée.";
		} else {
			// On valide l'annonce
			$SQL = "DELETE FROM $Table_Annonces WHERE ID='$ID'";
			$mysql_result = mysql_query($SQL, $mysql_link);
			
			// On envoi un mail à l'annonceur
			$Msg_Mail = "Bonjour,<br><br>Nous vous écrivons pour vous signaler que votre annonce sur le site : $Adresse_AA à été refusé par l'administrateur. Vous pouvez toujours recommencer en vous rendant de nouveau sur le site..<br><br> Nous vous remercions de votre confiance est de votre participation.<br><br>";
			$Msg_Mail .= "A-Annonce $Version_Script<br><a href=\"http://www.mistersp.com\">http://www.mistersp.com -> Scripts PHP gratuits Et scripts Perl Et Services pour webmasters...</a>";
			
			$Header_Mail = "From: $Mail_Admin \n"; 
			$Header_Mail .= "Reply-To: $Mail_Admin \n"; 
			$Header_Mail .= "X-Priority: 1 \n";
			$Header_Mail .= "Content-Type: text/html; \n";
			
			@mail($MailAnnonceur, "[A-Annonce] - Validation d'annonce",$Msg_Mail,$Header_Mail);
			
			$Page_HTML .= "L'annonce $ID à bien été supprimée.";
		}
	}
}

if ($p == "admin") {
$Page_HTML .= "<b><center><font size=\"3\">ADMINISTRATION DE VOTRE ANNONCE</font></center></b><br>";
	if ($ID != "" OR $Password != "") {
		// Ok
		$SQL = "SELECT * FROM $Table_Annonces WHERE ID='$ID'";
		$mysql_result = mysql_query($SQL, $mysql_link);
		$Nb_Enregistrements = mysql_num_rows($mysql_result);
		
		if($Nb_Enregistrements != 0) {
			// Ok
			while($row = mysql_fetch_row($mysql_result)) {
				$A_ID = $row[0];
				$A_Rubrique = $row[1];
				$A_Temps = $row[2];
				$A_Image = $row[3];
				$A_Nom = $row[4];
				$A_Prenom = $row[5];
				$A_Telephone = $row[6];
				$A_Mail = $row[7];
				$A_Annonce = $row[8];
				$A_Password = $row[9];
				
				$A_Nom = stripslashes($A_Nom);
				$A_Prenom = stripslashes($A_Prenom);
				$A_Password = stripslashes($A_Password);
				$A_Annonce = stripslashes($A_Annonce);
				$A_Mail = stripslashes($A_Mail);
			}
			
			if($Password == $A_Password) {
				// Ok
				if ($Nom != "") {
					// On modifie l'annonce
					if ($Nom != "" AND $Prenom != "" AND $Mail != "" AND $Annonce != "") {
						$Nom = addslashes($Nom);
						$Prenom = addslashes($Prenom);
						$Password = addslashes($Password);
						$Annonce = addslashes($Annonce);
						$Annonce = ereg_replace("<", "&lt;", $Annonce);
						$Annonce = ereg_replace(">", "&gt;", $Annonce);
						$Annonce = nl2br($Annonce);
						
						// Création d'un nom de fichier aléatoire
						$Pool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
						$Pool .= "abcdefghijklmnopqrstuvxyz";
						$Pool .= "0123456789";
						
						// Initialisation du compteur
						srand(time());
						
						for ($i=0; $i <= 8; $i++) {
							$Nom_Image .= substr($Pool, (rand()%(strlen($Pool))), 1);
						}
						
						if ($Image != "none") {
							if(file_exists("$Image_name")) { $Nom_Image = ""; } else {
								if ($Image_type == "image/gif") { $Nom_Image .= ".gif"; }
								if ($Image_type == "image/pjpeg") { $Nom_Image .= ".jpg"; }
								if ($Image_type == "image/x-png") { $Nom_Image .= ".png"; }
								move_uploaded_file($Image,"images/$Nom_Image");
								//unlink($Image);
							}
						} else { $Nom_Image = ""; }
						
						$SQL = "UPDATE $Table_Annonces SET Image='$Nom_Image',Nom='$Nom',Prenom='$Prenom',Telephone='$Telephone',Mail='$Mail',Annonce='$Annonce' WHERE ID='$ID'";
						$mysql_result = mysql_query($SQL, $mysql_link);
						
?>
						<script language="JavaScript">
						window.alert("Annonce modifiée.");
						</script>
<?
						// On met à jour les informations de l'annonce
						$SQL = "SELECT * FROM $Table_Annonces WHERE ID='$ID'";
						$mysql_result = mysql_query($SQL, $mysql_link);
						// Ok
						while($row = mysql_fetch_row($mysql_result)) {
							$A_ID = $row[0];
							$A_Rubrique = $row[1];
							$A_Temps = $row[2];
							$A_Image = $row[3];
							$A_Nom = $row[4];
							$A_Prenom = $row[5];
							$A_Telephone = $row[6];
							$A_Mail = $row[7];
							$A_Annonce = $row[8];
							$A_Password = $row[9];
							
							$A_Nom = stripslashes($A_Nom);
							$A_Prenom = stripslashes($A_Prenom);
							$A_Password = stripslashes($A_Password);
							$A_Annonce = stripslashes($A_Annonce);
							$A_Mail = stripslashes($A_Mail);
						}
					} else {
						// Erreur
?>
						<script language="JavaScript">
						window.alert("Formulaire mal remplis.");
						</script>
<?
					}
				}
				$A_Annonce = str_replace('<br />', ' ',$A_Annonce);
				
				$Page_HTML .= "<div align=\"center\">";
				$Page_HTML .= "<table border=\"0\">";
				$Page_HTML .= "	<form action=\"index.php\" method=\"post\" ENCTYPE='multipart/form-data' method='post'>";
				$Page_HTML .= "	<input type=\"hidden\" name=\"ID\" value=\"$ID\">";
				$Page_HTML .= "	<input type=\"hidden\" name=\"Password\" value=\"$Password\">";
				$Page_HTML .= "	<input type=\"hidden\" name=\"p\" value=\"admin\">";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td><b>Nom :</b></td>";
				$Page_HTML .= "		<td><input type=\"text\" name=\"Nom\" size=\"20\" value=\"$A_Nom\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td><b>Prénom :</b></td>";
				$Page_HTML .= "		<td><input type=\"text\" name=\"Prenom\" size=\"20\" value=\"$A_Prenom\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td><b>Mail :</b></td>";
				$Page_HTML .= "		<td><input type=\"text\" name=\"Mail\" size=\"20\" value=\"$A_Mail\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td><b>Téléphone :</b></td>";
				$Page_HTML .= "		<td><input type=\"text\" name=\"Telephone\" size=\"20\" value=\"$A_Telephone\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td><b>Image (format gif) :</b></td>";
				$Page_HTML .= "		<td><input type=\"file\" name=\"Image\" size=\"20\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td><b>Temps de diffusion :</b></td>";
				$Page_HTML .= "		<td>";
				$Page_HTML .= "			Jusqu'au : $A_Temps";
				$Page_HTML .= "		</td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td colspan=\"2\"><b>Annonce :</b></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td colspan=\"2\"><textarea name=\"Annonce\" rows=\"7\" cols=\"50\">$A_Annonce</textarea></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\".: Modifier votre annonce :.\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	</form>";
				$Page_HTML .= "</table>";
				$Page_HTML .= "<table border=\"0\">";
				$Page_HTML .= "	<form action=\"index.php\" method=\"post\">";
				$Page_HTML .= "	<input type=\"hidden\" name=\"p\" value=\"admin_supp\">";
				$Page_HTML .= "	<input type=\"hidden\" name=\"ID\" value=\"$ID\">";
				$Page_HTML .= "	<input type=\"hidden\" name=\"Password\" value=\"$Password\">";
				$Page_HTML .= "	<tr>";
				$Page_HTML .= "		<td align=\"center\"><input type=\"submit\" value=\".: Supprimer votre annonce :.\"></td>";
				$Page_HTML .= "	</tr>";
				$Page_HTML .= "	</form>";
				$Page_HTML .= "</table>";
				$Page_HTML .= "</div>";
			} else {
				// Erreur
				$Page_HTML .= "<b>Erreur :</b><br>";
				$Page_HTML .= "- Le mot de passe est incorrect.";
			}
			
		} else {
			// Erreur
			$Page_HTML .= "<b>Erreur :</b><br>";
			$Page_HTML .= "- Le login est inexistant.";
		}
	} else {
		// Erreur
		$Page_HTML .= "<b>Erreur :</b><br>";
		$Page_HTML .= "- Vous devez donner un ID et un mot de passe.";
	}
}
		
if ($p == "admin_supp") {
$Page_HTML .= "<b><center><font size=\"3\">SUPPRESION D'UNE ANNONCE</font></center></b><br>";
	if ($ID != "" OR $Password != "") {
		// Ok
		$SQL = "SELECT * FROM $Table_Annonces WHERE ID='$ID'";
		$mysql_result = mysql_query($SQL, $mysql_link);
		$Nb_Enregistrements = mysql_num_rows($mysql_result);
		
		if($Nb_Enregistrements != 0) {
			// Ok
			while($row = mysql_fetch_row($mysql_result)) {
				$A_ID = $row[0];
				$A_Rubrique = $row[1];
				$A_Temps = $row[2];
				$A_Image = $row[3];
				$A_Nom = $row[4];
				$A_Prenom = $row[5];
				$A_Telephone = $row[6];
				$A_Mail = $row[7];
				$A_Annonce = $row[8];
				$A_Password = $row[9];
				
				$Nom = stripslashes($Nom);
				$Prenom = stripslashes($Prenom);
				$Password = stripslashes($Password);
				$Annonce = stripslashes($Annonce);
				$Mail = stripslashes($Mail);
			}
			
			if($Password == $A_Password) {
				// Ok
				// WARNING : injection sql possible pour tout supprimer. 
				$ID = addslashes($ID);
				// On risque plus rien 
				$SQL = "DELETE FROM $Table_Annonces WHERE ID='$ID'";
				$mysql_result = mysql_query($SQL, $mysql_link);
				if ($A_Image != "") {
					unlink("images/$A_Image");
				}
				$Page_HTML .= "Votre annonce à bien été supprimée sur votre demande.";
			} else {
				// Erreur
				$Page_HTML .= "<b>Erreur :</b><br>";
				$Page_HTML .= "- Le mot de passe est incorrect.";
			}
			
		} else {
			// Erreur
			$Page_HTML .= "<b>Erreur :</b><br>";
			$Page_HTML .= "- Le login est inexistant.";
		}
	} else {
		// Erreur
		$Page_HTML .= "<b>Erreur :</b><br>";
		$Page_HTML .= "- Vous devez donner un ID et un mot de passe.";
	}
}		
		
// Affichage des page avec la template
$SQL = "SELECT * FROM $Table_Template WHERE ID='1'";	
$mysql_result = mysql_query($SQL, $mysql_link);

while ($row = mysql_fetch_row($mysql_result)) {
	$T_HTML = $row[1];
}
$T_HTML2 = $T_HTML;

$T_HTML2 = stripslashes($T_HTML2);

// On modifie
$T_HTML2 = str_replace('[Menu]', $Menu_HTML,$T_HTML2);
$T_HTML2 = str_replace('[Page]', $Page_HTML,$T_HTML2);
	
// Affichage de la page
echo "
$T_HTML2
";

?>
