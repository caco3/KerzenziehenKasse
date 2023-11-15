<? 
    $root="..";
    require_once("$root/framework/credentials_check.php");

    require_once("$root/config/config.php");
    require_once("$root/framework/functions.php");
    require_once("$root/framework/db.php");
    
    db_connect();

    $bookingId = getDbBookingId();
    $basket = getDbBasket();
    $basketFormated = array();
    
    $basketFormated["total"] = roundMoney10(getDbTotal());
    $basketFormated["donation"] = roundMoney10(getDbDonation());
    $basketFormated["entries"] = array();
    
    foreach($basket as $basketEntry) {      
        $basketEntryId = $basketEntry['basketEntryId'];
        $articleId = $basketEntry['articleId'];
        $quantity = $basketEntry['quantity'];
        $price = $basketEntry['price'];
        $image1 = $basketEntry['image1'];
        $image2 = $basketEntry['image2'];
        $image3 = $basketEntry['image3'];
        
        list($name, $type, $pricePerQuantity, $unit) = getDbArticleData($articleId);
                            
        if ($unit == "g") {
            $prefix = "";
            $suffix = " g";      
        }            
        else if($unit == "Stk.") {
            $prefix = "";
            $suffix = " Stk. ";                
        }            
        else {
            $prefix = "CHF ";
            $suffix = "";        
        }

        array_push($basketFormated["entries"], array("name"=>$name, "unit"=>$unit, "prefix"=>$prefix, "suffix"=>$suffix, "quantity"=>$quantity, "price"=>$price, "image"=>$image1));
    }

    echo(json_encode($basketFormated));

?>
