<?
include "config/config.php";
include "framework/functions.php";
include "framework/db.php";

db_connect();


if(isset($_GET['id'])){
    $id = $_GET['id'];
}
else{
    $id = 0;
}

if($id == ""){
    $id = 0;
}
    


header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; Filename=Beleg-$id.doc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=Windows-1252">
    <style type="text/css">
<? // It seems as if it does not support external stylesheets, so lets integrate it directly
    include "receipeStyle.css"
?>
    </style>
</head>
<body>

    <table style="width: 100%">
        <tr>
            <td style="width: 90%"><h1 class=ReciepeH1>Beleg <? echo($id); ?> </h1></td>
            <td style="width: 10%">
                <? /* use https://www.base64-image.de */ ?>
    <img src="data:image/png;base64,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" alt="beastie.png">
            </td>
        </tr>
    </table>
    
    <h2>
    <img width="100px" height="100px" src="data:image/png;base64,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">
    Kerzenziehen Kirche Neuwies</h2>


<?    
    showSummary();    
?>

</body>
</html>
