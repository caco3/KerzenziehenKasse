<?

if(isset($_GET['password'])) {
    echo("Password: " . $_GET['password'] . "<br>\n");
    echo("Hash: " . password_hash($_GET['password'], PASSWORD_DEFAULT) . "<br>\n");
}
else{
    echo("Bitte Passwort übergeben: password=xxx");
}
    

?>
