<?php
//lisame oma kasutajanimi, parooli, ja ab_nimi
$yhendus=new mysqli("localhost", "blinov", "12345", "blinov");
session_start();
$error= $_SESSION["error"] ?? "";

function puhastaAndmed($data){
    //trim()- eemaldab tühikud
    $data=trim($data);
    //htmlspecialchars - ignoreerib <käsk>
    $data=htmlspecialchars($data);
    //stripslashes - eemaldab \
    $data=stripslashes($data);
    return $data;
}
if(isset($_REQUEST["knimi"])&& isset($_REQUEST["psw"])) {

    $login = puhastaAndmed($_REQUEST["knimi"]);
    $pass = puhastaAndmed($_REQUEST["psw"]);
    $sool = 'vagavagatekst';
    $krypt = crypt($pass, $sool);

//kasutajanimi kontroll
    $kask = $yhendus->prepare("SELECT id, knimi, psw,admin FROM uuedkasutajad
WHERE knimi=?");
    $kask->bind_param("s", $login);
    $kask->bind_result($id,$kasutajanimi, $onAdmin, $koduleht);
    $kask->execute();
    if ($kask->fetch()) {
        $_SESSION["error"] = "Kasutaja on juba olemas";
        header("Location: $_SERVER[PHP_SELF]");
        $yhendus->close();
        exit();

    } else {
        $_SESSION["error"] = " ";
    }


// uue kasutaja lisamine andmetabeli sisse
    $kask = $yhendus->prepare("
INSERT INTO uuedkasutajad(knimi, psw, isadmin) 
VALUES (?,?,?)");
    $kask->bind_param("ssi", $login, $krypt, $_REQUEST["admin"]);
    $kask->execute();
    $_SESSION['knimi'] = $login;
    $_SESSION['admin'] = true;
//header("location: kaubahaldus.php");
//$yhendus->close();
//exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registreerimisvorm</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="CSS/login.css" type="text/css">
</head>
<body>
<h1>Uue kasutaja registreerimine</h1>
<form action="registr.php" method="post">
    <label for="knimi">Kasutajanimi</label>
    <input type="text" placeholder="Sisesta kasutajanimi"
           name="knimi" id="knimi" required>
    <br>
    <label for="psw">Parool</label>
    <input type="password" placeholder="Sisesta parool"
           name="psw" id="psw" required>
    <br>
    <label for="admin">Kas teha admin?</label>
    <input type="checkbox" name="admin" id="admin" value="1">
    <br>
    <input type="submit" value="Loo kasutaja">

    <strong> <?=$error ?></strong>

</form>

</body>
</html>