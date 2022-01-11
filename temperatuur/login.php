<?php
$yhendus=new mysqli("localhost", "blinov", "12345", "blinov");
//login vorm Andmebaasis salvestatud kasutajanimega ja prooliga
session_start();
$error= $_SESSION["error"] ?? "";
// kontroll kas login vorm on täidetud?
if(isset($_REQUEST['knimi']) && isset($_REQUEST['psw'])) {
    $login = htmlspecialchars($_REQUEST['knimi']);
    $pass = htmlspecialchars($_REQUEST['psw']);

    $sool = 'vagavagatekst';
    $krypt = crypt($pass, $sool);
    // kontrollime kas andmebaasis on selline kasutaja
    $kask = $yhendus->prepare("
SELECT id, knimi, psw, isadmin FROM uuedkasutajad WHERE knimi=?");
    $kask->bind_param("s", $login);
    $kask->bind_result($id, $kasutajanimi, $parool, $onadmin);
    $kask->execute();

    if ($kask->fetch() && $krypt == $parool) {
        $_SESSION['knimi'] = $login;
        if ($onadmin == 1) {
            $_SESSION['admin'] = true;
            header("Location: lisamine.php");
        }
        echo "kasutaja $login või parool $krypt on vale";
        $yhendus->close();
    }
}
$kask = $yhendus->prepare("
INSERT INTO uuedkasutajad(knimi, psw, isadmin) 
VALUES (?,?,?)");
$kask->bind_param("ssi", $login, $krypt, $_REQUEST["admin"]);
$kask->execute();
$_SESSION['knimi'] = $login;
$_SESSION['admin'] = true;
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/style1.css" type="text/css">
</head>
<body>
<h1>Login vorm</h1>
<div class="modal">
<form action="login.php" method="post" class="modal-content">
    <label for="knimi">Kasutajanimi</label>
    <input type="text" placeholder="Sisesta kasutajanimi"
           name="knimi" id="knimi" required>
    <br>
    <label for="psw">Parool</label>
    <input type="password" placeholder="Sisesta parool"
           name="psw" id="psw" required>
    <br>
    <input type="submit" value="Logi sisse">
      <button type="button"
              onclick="window.location.href='lisamine.php'";
              class="cancelbtn">Cancel</button>
<strong> <?=$error?></strong>
</form>
</div>
</body>