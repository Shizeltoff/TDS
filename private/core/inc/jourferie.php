<?php
function reccuperationNomJourFerie($timestamp){
	$jour = date("d", $timestamp);
	$mois = date("m", $timestamp);
	$annee = date("Y", $timestamp);
	$EstFerie = 0;
	// dates fériées fixes
	
	if($jour == 1 && $mois == 1) $EstFerie = "Jour de l'an"; // 1er janvier
	if($jour == 1 && $mois == 5) $EstFerie = "Fête du travail"; // 1er mai
	if($jour == 8 && $mois == 5) $EstFerie = "Fête de la victoire 1945"; // 8 mai
	if($jour == 14 && $mois == 7) $EstFerie = "Fête nationale"; // 14 juillet
	if($jour == 15 && $mois == 8) $EstFerie = "Assomption"; // 15 aout
	if($jour == 1 && $mois == 11) $EstFerie = "Toussaint"; // 1 novembre
	if($jour == 11 && $mois == 11) $EstFerie = "Armistice 1918"; // 11 novembre
	if($jour == 25 && $mois == 12) $EstFerie = "Noël"; // 25 décembre
	
	// fetes religieuses mobiles
	$pak = paques(1, $annee);
	$jp = date("d", $pak);
	$mp = date("m", $pak);
	if($jp == $jour && $mp == $mois)		$EstFerie = "Lundi de Pâques";			 // Lundi de Pâques
	
	$dateascension = new Date($pak);
	$dateascension->addDays(38);
	$ascension = $dateascension->getTimestamp();
	$jp = date("d", $ascension);
	$mp = date("m", $ascension);
	if($jp == $jour && $mp == $mois)		$EstFerie = "Jeudi de l'ascenscion";			 // Jeudi de l'ascension
	
	$datelundipentecote = new Date($ascension);
	$datelundipentecote->addDays(11);
	$pentecote = $datelundipentecote->getTimestamp();
	$jp = date("d", $pentecote);
	$mp = date("m", $pentecote);
	if($jp == $jour && $mp == $mois)		$EstFerie = "Lundi de pentecote";			 // Lundi de pentecote
	
	return $EstFerie;
	}
	
function jour_ferie($timestamp){
	$jour = date("d", $timestamp);
	$mois = date("m", $timestamp);
	$annee = date("Y", $timestamp);
	$EstFerie = 0;
	// dates fériées fixes
	if($jour == 1 && $mois == 1) $EstFerie = 1; // 1er janvier
	if($jour == 1 && $mois == 5) $EstFerie = 1; // 1er mai
	if($jour == 8 && $mois == 5) $EstFerie = 1; // 8 mai
	if($jour == 14 && $mois == 7) $EstFerie = 1; // 14 juillet
	if($jour == 15 && $mois == 8) $EstFerie = 1; // 15 aout
	if($jour == 1 && $mois == 11) $EstFerie = 1; // 1 novembre
	if($jour == 11 && $mois == 11) $EstFerie = 1; // 11 novembre
	if($jour == 25 && $mois == 12) $EstFerie = 1; // 25 décembre
	
	// fetes religieuses mobiles
	$pak = paques(1, $annee);
	$jp = date("d", $pak);
	$mp = date("m", $pak);
	if($jp == $jour && $mp == $mois)		$EstFerie = 1;			 // Lundi de Pâques
	
	$dateascension = new Date($pak);
	$dateascension->addDays(38);
	$ascension = $dateascension->getTimestamp();
	$jp = date("d", $ascension);
	$mp = date("m", $ascension);
	if($jp == $jour && $mp == $mois)		$EstFerie = 1;			 // Jeudi de l'ascension
	
	$datelundipentecote = new Date($ascension);
	$datelundipentecote->addDays(11);
	$pentecote = $datelundipentecote->getTimestamp();
	$jp = date("d", $pentecote);
	$mp = date("m", $pentecote);
	if($jp == $jour && $mp == $mois)		$EstFerie = 1;			 // Lundi de pentecote
	
	return $EstFerie;
	}



function paques($Jourj=0, $annee=NULL){
    $annee=($annee==NULL) ? date("Y") : $annee;

    $G = $annee%19;
    $C = floor($annee/100);
    $C_4 = floor($C/4);
    $E = floor((8*$C + 13)/25);
    $H = (19*$G + $C - $C_4 - $E + 15)%30;

    if($H==29)
    {
        $H=28;
    }
    elseif($H==28 && $G>10)
    {
        $H=27;
    }
    $K = floor($H/28);
    $P = floor(29/($H+1));
    $Q = floor((21-$G)/11);
    $I = ($K*$P*$Q - 1)*$K + $H;
    $B = floor($annee/4) + $annee;
    $J1 = $B + $I + 2 + $C_4 - $C;
    $J2 = $J1%7; //jour de pâques (0=dimanche, 1=lundi....)
    $R = 28 + $I - $J2; // résultat final :)
    $mois = $R>30 ? 4 : 3; // mois (1 = janvier, ... 3 = mars...)
    $Jour = $mois==3 ? $R : $R-31;

    return mktime(0,0,0,$mois,$Jour+$Jourj,$annee);
	}
?>