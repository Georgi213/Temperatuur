<?php
$yhendus=new mysqli("localhost", "blinov", "12345", "blinov");
function kysiKaupadeAndmed($sorttulp="temperatuur", $otsisona=""){
    global $yhendus;
    $lubatudtulbad=array("temperatuur", "maakonnanimi","maakonnakeskus", "kuupaev");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }
    //addslashes - striplashes -lisab langjoone - kustutab langjoo

    $otsisona=addslashes(stripslashes($otsisona));
    $kask=$yhendus->prepare("SELECT temperatuuri.id, temperatuur, maakonnanimi, kuupaev
       FROM temperatuuri, maakonna
       WHERE temperatuuri.maakonna_id=maakonna.id
        AND (temperatuur LIKE '%$otsisona%' OR maakonnanimi LIKE '%$otsisona%')
       ORDER BY $sorttulp");
    $kask->bind_result($id, $temperatuur, $maakonnanimi, $kuupaev);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $kaup=new stdClass();
        $kaup->id=$id;
        $kaup->nimetus=htmlspecialchars($temperatuur);
        $kaup->grupinimi=htmlspecialchars($maakonnanimi);
        $kaup->hind=$kuupaev;
        array_push($hoidla, $kaup);
    }
    return $hoidla;
}
?>
<?php
function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}
//lisab uue kaubagrupi
function lisaGrupp($maakonnanimi){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO maakonna (maakonnanimi)
                      VALUES (?)");
    $kask->bind_param("s", $maakonnanimi);
    $kask->execute();
}
//lisa andmed tabeli Kauab
function lisaKaup($temperatuur, $maakonna_id, $kuupaev){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO
       temperatuuri (temperatuur, maakonna_id, kuupaev)
       VALUES (?, ?, ?)");
    $kask->bind_param("iis", $temperatuur, $maakonna_id, $kuupaev);
    $kask->execute();
}
//kustutab kaudab tabelist kaudab
function kustutaKaup($teavet_id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM temperatuuri WHERE id=?");
    $kask->bind_param("i", $teavet_id);
    $kask->execute();
}
//muudab andmed tabelis kaudab
function muudaKaup($teavet_id, $temperatuur, $maakonna_id, $kuupaev){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE temperatuuri SET temperatuur=?, maakonna_id=?, kuupaev=?
                      WHERE id=?");
    $kask->bind_param("iisi", $temperatuur, $maakonna_id, $kuupaev, $teavet_id);
    $kask->execute();
}
?>