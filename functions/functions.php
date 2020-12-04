<?php

/****HELPER FUNCTIONS *****/

    function clean($string){
        return htmlentities($string);
    }

    function redirect($location){
        return header("Location: {$location}");
    }

    function set_message($message){
        if(!empty($message)){
            $_SESSION['message']= $message;
        }
        else{
            $message="";
        }
    }

    function display_message(){
        if(isset($_SESSION['message'])){
           echo $_SESSION['message'];

           unset($_SESSION['message']);
        }
    }

    function token_generator(){
        $token = $_SESSION['token']= md5(uniqid(mt_rand(),true));
        return $token;
    }

    function validation_errors($error_msg){
$msg = <<<DELIMITER
<div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
            </button><strong>Warning!</strong> $error_msg
            </div>
DELIMITER;
return $msg;

    }

    function email_exists($email){
        $sql = "SELECT id FROM users WHERE email = $email";
        $result = query($sql);

        if(row_count($result) == 1){
            return true;
        }
        else{
            return false;
        }
    }

    function user_name_exists($user_name){
        $sql = "SELECT id FROM users WHERE user_name = $user_name";
        $result = query($sql);

        if(row_count($result) == 1){
            return true;
        }
        else{
            return false;
        }
    }


/****VALIDATION FUNCTIONS *****/
function validate_user_reg(){
    $min=3;
    $max=20;

    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $first_name = clean($_POST['first_name']);
        $last_name = clean($_POST['last_name']);
        $user_name = clean($_POST['user_name']);
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        $confirm_password = clean($_POST['confirm_password']);
        //echo "echoing from functions.php function validate_user---<br>Works on POST";

        if(strlen($first_name)< $min){
            $errors[] = "Enter First Name > {$min}<br>";
        }
        if(strlen($first_name)>$max){
            $errors[] = "Enter First Name < {$max}<br>";
        }
        if(empty($first_name)){
            $errors[] = "First Name cannot be empty<br>";

        }//First name

        if(strlen($last_name)< $min){
            $errors[] = "Enter Last Name > {$min}<br>";
        }
        if(strlen($last_name)>$max){
            $errors[] = "Enter Last Name < {$max}<br>";
        }
        if(empty($last_name)){
            $errors[] = "Last Name cannot be empty<br>";

        }//Last name

        if(strlen($user_name)< $min){
            $errors[] = "Enter username > {$min}<br>";
        }
        if(strlen($user_name)>$max){
            $errors[] = "Enter username < {$max}<br>";
        }
        if(empty($user_name)){
            $errors[] = "username cannot be empty<br>";

        }
        if(!user_name_exists($user_name)){
            $errors[] = "Username already taken<br>";

        }//User name

        if(!email_exists($email)){
            $errors[] = "Email already taken<br>";

        }
        //User name

        /*if(strlen($email)>$max){
            $errors[] = "Enter email < {$max}<br>";
        }uncomment this if required 'exception email possible..'*/

        if($password !== $confirm_password){

            $errors[] = "Password feild mismatch";
        }

        if(!empty($errors)){

            foreach($errors as $err){

       
              echo validation_errors($err);
            
            //DELIMITER in validation_errors follows very strict format like python
            }
        }

    }

}

    



    

?>