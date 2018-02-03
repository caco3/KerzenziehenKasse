<?  

define('INVALID_CREDENTIALS', 'INVALID_CREDENTIALS');
define('MISSING_CREDENTIALS', 'MISSING_CREDENTIALS');
define('CREDENTIALS_VALID', 'CREDENTIALS_VALID');




if(!empty($_POST['password'])){ //password is set
    $password = $_POST['password'];           
    
    $config_file = "config/config.php";
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
    
    $config_file = "config/config.php";
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
    else{ //config file does not exist (username is wrong)
        goto CREDENTIALS_INVALID;
    }
}
//---------------------------------------------
else{ //no username/password or username/ID given    
//     echo("No POST or Cookie parameters set<br>");    
    goto MISSING_CREDENTIALS;
}

    
      
      
      
CREDENTIALS_INVALID: //password is wrong or ID is wrong
    if(basename($_SERVER['PHP_SELF']) != "login.php"){ //we are not login.php, 
        echo("<head><meta http-equiv=refresh content=\"0; url=login.php\"/></head>"); //redirect to login.php
        die("Access restricted, you get redirected to login.php!");
    }
    else{//we are on index.php, show login form
//         echo("Credentials are invalid<br>");             
        $autorization = INVALID_CREDENTIALS;
        goto END;
    }
          
            
MISSING_CREDENTIALS: //no password or ID given  
    if(basename($_SERVER['PHP_SELF']) != "login.php"){ //we are not login.php, 
        echo("<head><meta http-equiv=refresh content=\"0; url=login.php\"/></head>"); //redirect to login.php
        die("Access restricted, you get redirected to login.php!");
    }
    else{//we are on index.php, show login form
//         echo("Credentials are missing<br>");             
        $autorization = MISSING_CREDENTIALS;
        goto END;
    }
      
 
CREDENTIALS_VALID:
    $autorization = CREDENTIALS_VALID;
    
END:    
?>
