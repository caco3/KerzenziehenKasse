<? 



function addButton($id) {
    return "<button type=button id=$id class=addToBasket></button> ";
}

function showDeleteButton($basketId) {
    return "<button type=button id=$basketId class=deleteFromBasket></button> ";
}





function listProducts() {
?>
<!--     <h3>Ziehen</h3>         -->
<?
    listDipProducts();
?>


<?


?>
<!--     <h3>Giessen</h3> -->
<br>
<?
        listPourProducts();
?>


<?


}




function sql_transaction_logger($sql){
    $sql = str_replace("\n", " ", $sql);
    $sql = preg_replace('!\s+!', ' ', $sql);
    // TODO: replace absolute path
    file_put_contents("/home/chrisc22/www/kerzenziehen/new/log/db_transaction_log.sql", "-- " . date(DATE_RFC2822) . "\r\n", FILE_APPEND);
    file_put_contents("/home/chrisc22/www/kerzenziehen/new/log/db_transaction_log.sql", "$sql\r\n", FILE_APPEND);
}











/* Sums up the cost of the whole basket optionally including donation
 */
function calculateBasketTotal($includeDOnation){
    $basket = getDbBasket();
    
    $sum = 0;
    foreach($basket as $basketEntry) {      
        $articleId = $basketEntry['article_id'];
        $quantity = $basketEntry['quantity'];
        $free = $basketEntry['free'];
        $price = $basketEntry['price'];
        list($name, $pricePerQuantity, $unit) = getDbArticleData($articleId);
        
          
        
        if($free != 1){ // normal article
            $sum += $quantity * $pricePerQuantity; 
        }
        else{ // manual article
            $sum += $price; 
        }
    }
    
    if($includeDOnation == true){
        $sum += getDbDonation();
    }
    
    return $sum;
}





function roundMoney($num){
    $x = ($num * 1000) % 100;
    
    if($x < 25){
        return $num - $x/1000;
    }
    else if($x >= 75){
        return $num + (50-$x)/1000 + 0.05;
    }
    else {
        return $num + (50-$x)/1000;
    }
}  




?>

