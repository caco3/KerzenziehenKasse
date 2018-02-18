<?  

define('INVALID_CREDENTIALS', 'INVALID_CREDENTIALS');
define('MISSING_CREDENTIALS', 'MISSING_CREDENTIALS');
define('CREDENTIALS_VALID', 'CREDENTIALS_VALID');
define('INITIAL_PAGE_LOAD', 'INITIAL_PAGE_LOAD');

// echo ($_SERVER['PHP_SELF']);
// echo basename($_SERVER['PHP_SELF']) . "<br>";
// echo("<pre>");
// print_r($_POST);
// echo("</pre>");


if(isset($_POST['password'])){ //password is set
    $password = $_POST['password'];   
    
    if($password == "") {
        goto MISSING_CREDENTIALS;
    }
    
    $config_file = "$root/config/config.php";
    if(file_exists($config_file)){ //config file exists
        require($config_file);
        
        //validate password
        if(password_verify($password, PASSWORD_HASH)){ //password matches
            //Add credentials to cookie
            setcookie('ID',       ID,        time() + 60 * 60 * 24 * 60, "/"); //set ID (expires after 60 days)
            goto CREDENTIALS_VALID;
        } 
        else{ //password does not match 
            goto CREDENTIALS_INVALID;
        }  
    }
    else{ //config file does not exist
        die("Config file missing");
    }        
    
}
//---------------------------------------------
else if(isset($_COOKIE['ID'])){  // ID in cookie is set  
    $ID = $_COOKIE['ID'];  
    
    $config_file = "$root/config/config.php";
    if(file_exists($config_file)){ //config file exists
        require($config_file);
        
        //validate password
        if($ID == ID){ //ID matches
            //Add credentials to cookie
            setcookie('ID',       ID,        time() + 60 * 60 * 24 * 60, "/"); //set ID (expires after 60 days)
            goto CREDENTIALS_VALID;
        } 
        else{ //ID does not match 
            goto CREDENTIALS_INVALID;
        }  
    }
    else{ //config file does not exist
        goto CREDENTIALS_INVALID;
    }
}
//---------------------------------------------
// else{ //no password or ID given    
//     echo("No POST or Cookie parameters set<br>"); 
//     goto MISSING_CREDENTIALS;
// }

goto INITIAL_PAGE_LOAD;

    
      
      
      
CREDENTIALS_INVALID: //password is wrong or ID is wrong        
    $autorization = INVALID_CREDENTIALS;
    if(basename($_SERVER['PHP_SELF']) != "login.php"){ //we are not on the login.php, redirect
//         echo("Credentials are invalid<br>");     
    //         goto END;
        echo("<head><meta http-equiv=refresh content=\"0; url=$root/login.php\"/></head>"); //redirect to login.php
        die("Access restricted, you get redirected to login.php!");
    }
    else { // we are on the login page continue loading page
        goto END;
    }
            
MISSING_CREDENTIALS: //no password or ID given       
    $autorization = MISSING_CREDENTIALS;
    if(basename($_SERVER['PHP_SELF']) != "login.php"){ //we are not on the login.php, redirect
//         echo("Credentials are missing<br>");        
    //         goto END;
        echo("<head><meta http-equiv=refresh content=\"0; url=$root/login.php\"/></head>"); //redirect to login.php
        die("Access restricted, you get redirected to login.php!");
    }
    else { // we are on the login page continue loading page
        goto END;
    }
            
INITIAL_PAGE_LOAD: //no password or ID given  (initial call)     
    $autorization = INITIAL_PAGE_LOAD;
    if(basename($_SERVER['PHP_SELF']) != "login.php"){ //we are not on the login.php, redirect
//         echo("Credentials are missing<br>");        
    //         goto END;
        echo("<head><meta http-equiv=refresh content=\"0; url=$root/login.php\"/></head>"); //redirect to login.php
        die("Access restricted, you get redirected to login.php!");
    }
    else { // we are on the login page continue loading page
        goto END;
    }
      
 
CREDENTIALS_VALID:
    $autorization = CREDENTIALS_VALID; 
    if(basename($_SERVER['PHP_SELF']) == "login.php"){ //we are on the login.php, redirect
//         echo("Credentials are valid<br>");     
    //     if($autorization == CREDENTIALS_VALID){ //credentials are valid, redirect to index.php (default page)
        echo("<head><meta http-equiv=refresh content=\"0; url=$root/index.php\"/></head>");
    //         exit();
    //     }
    }
END:  
?>
