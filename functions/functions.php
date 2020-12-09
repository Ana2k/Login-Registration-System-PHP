<?php

require '../vendor/autoload.php';

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
        $token = md5(uniqid(mt_rand(),true));
        $_SESSION['token']= $token;
        return $token;
    }

    function validation_errors($error_msg){
$msg = <<<DELIMITER
<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
            </button><strong>Warning!</strong> $error_msg
            </div>
DELIMITER;
return $msg;

    }

    function email_exists($email){
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = query($sql);

        if(row_count($result) == 1){
            return true;
        }
        else{
            return false;
        }
    }

    function user_name_exists($user_name){
        $sql = "SELECT id FROM users WHERE user_name = '$user_name'";
        $result = query($sql);

        if(row_count($result) == 1){
            return true;
        }
        else{
            return false;
        }
    }

    function send_email($email, $subject, $msg, $headers){
        
            // Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer();

            
                  //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.mailtrap.io';                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'user@example.com';                     // SMTP username
                $mail->Password   = 'secret';                               // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = 587;                                       // TCP port to connect to

                $mail->Subject = 'Here is the subject';
                $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if(!$mail->send()){
                   
                echo "Message could not be sent<br>Mailer Error: {$mail->ErrorInfo}";
                
            } 
            else{
                echo "Message has been sent. ";
            }
        
        return mail($email, $subject, $msg, $headers);
    }


/****VALIDATe USER REG *****/
function validate_user_reg(){
    
    $errors = [];

    $min=3;
    $max=20;

    if($_SERVER['REQUEST_METHOD'] == "POST"){
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
        if(user_name_exists($user_name)){
            $errors[] = "Username already taken<br>";

        }//User name

        if(email_exists($email)){
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
        else{
            if(register_user($first_name,$last_name, $user_name, $email , $password)){
                //echo "USER REGISTERED";
                set_message("<p class='bg-success text-center'>Please check your registered mail for an activation link</p>");
                redirect("index.php");
                
            }
            else{
                set_message("<p class='bg-danger text-center'>Sorry user registration failure.</p>");
                redirect("index.php");
            }
        }

    }

}

/****REGUSTER USER FUNCTIONS *****/

function register_user($first_name,$last_name, $user_name, $email , $password){

    $first_name=escape($first_name);
    $last_name=escape($last_name);
    $user_name=escape($user_name);
    $email=escape($email);
    $password=escape($password);

    if(email_exists($email)){
        return false;
    }
    else if(user_name_exists($user_name)){
        return false;
    }
    else{
        $password = md5($password);
        $validation_code = md5($user_name . microtime());

        $sql= "INSERT INTO users(first_name, last_name, user_name, password, validation_code, active,email)";
        $sql.= "VALUES('$first_name', '$last_name', '$user_name', '$password', '$validation_code', 0,'$email')";
        
        $result = query($sql);
        


        //for mails
        $subject= "Activate Account";
        $msg = "Click on the link below to activate your account
        http://localhost/files/activate.php?email=$email&code=$validation_code
        ";

        $header = "From: noreply@website.com";

        
        return true;

    }
}


/****ACTIVATE USER FUNCTIONS *****/
function activate_user(){
    if($_SERVER['REQUEST_METHOD'] == "GET"){
 
        if(isset($_GET['email'])){

            echo $email = clean($_GET['email']);

            echo $validation_code = clean($_GET['code']);
            
            $sql = "SELECT id FROM users WHERE email = '".escape($_GET['email'])."' AND validation_code= '".escape($_GET['code'])."'";
            $result = query($sql);
            

            if(row_count($result) == 1){
                
                $sql2= "UPDATE users SET active =1, validation_code = 0 WHERE email = '".escape($email)."' AND validation_code = '".escape($validation_code)."' ";
                $result2 = query($sql2);
                confirm($result2);
                set_message("<p class= 'bg-success text-center'> Your account has been activated please login</p>");

                redirect("login.php");
            }
            else{
                set_message("<p class= 'bg-danger'> Sorry your account could not be activated.</p>");

                redirect("login.php");
            }
        }
    }
    //echo "activate_user is working";
}

/***VALIDATE USER LOGIN**/

function validate_user_login(){
    $min=3;
    $max=20;
    //echo "BEFORE";
    if($_SERVER['REQUEST_METHOD'] == "POST"){
            
            $email = clean($_POST['email']);
            $password = clean($_POST['password']);
            $remember = clean(isset($_POST['remember']));


            if(empty($email)){
                $errors[] = "Email feild cannot be empty<br>";
            }
            if(empty($password)){
                $errors[] = "Password feild cannot be empty<br>";
            }

            if(!empty($errors)){

                foreach($errors as $err){
    
           
                  echo validation_errors($err);
                
                //DELIMITER in validation_errors follows very strict format like python
                }
            }
            else{
                if(login_user($email, $password, $remember)){
                    redirect("admin.php");
                }
                else{
                    echo validation_errors("Invalid Login<br>");
                    set_message("<p class='bg-danger text-center'>Re-enter credentials or Activate account</p>");
                }
            }
           
    }
}

/*****Login user function */
function login_user($email, $password,$remember){

    $sql = "SELECT password,id FROM users WHERE email = '".escape($email)."' AND active=1 ";

    $result = query($sql);

    if(row_count($result) == 1){
        $row = fetch_array($result);

        $db_password=$row['password'];

        //echo "if1 executes";
    
    if(md5($password) === $db_password){
            //echo $remember."<br>";
        if($remember == 1){
            
            setcookie('email', $email, time()+ 86400);

        }
        //echo "if2 executes";

        $_SESSION['email']= $email;

        return true;
    }
    
    else{
        //echo "else executes";
        return false;
    }
  }

}//function ends here
    
/*****Logged in function */

function logged_in(){

    if(isset($_SESSION['email']) || isset($_COOKIE['email'])){

        return true;
    }
    else{
        return false;
    }
}

/****Recover Password Function****/

function recover_password(){
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        //echo "inside recover<br>";

        if(isset($_SESSION['token']) && $_POST['token'] === $_SESSION['token'])
       {
                     
            $email = clean($_POST['email']);

            if(email_exists($email)){
                
                $validation_code = md5($email.microtime());
                

                setcookie('temp_access_code', $validation_code, time()+ 1000);
                
                $sql = "UPDATE users SET validation_code = '".escape($validation_code)."' WHERE email = '".escape($email)."'";
                                
                $result = query($sql);               
            

                $subject ="Please reset your password";
                $message = "Here is your Password reset code {$validation_code}
                
                Click to reset your password http://localhost/files/code.php?email=$email&code=$validation_code
                
                ";

                $headers = "From: noreply@yourwebsite.com";

                send_email($email, $subject, $message, $headers);

                if(!send_email($email, $subject, $message, $headers)){

                    echo validation_errors("Email could not be sent");

                }//CHANGE THIS CODE TO send_email(...); WHEN ON LIVE SERVER

                set_message("<p class='bg-success text-center'>Please check mail for password reset code</p>");
                redirect("index.php");
            }
            else{
               echo validation_errors("This email id does not exist ");
            }
       }
       else{

        redirect("index.php");
       }

       if(isset($_POST['cancel_submit'])){
           redirect("login.php");
       }
    }//post
}//function


/*******Code validation******/
function validate_code(){

    if(isset($_COOKIE['temp_access_code'])){

        //echo "first if";

        if(!isset($_GET['email']) && !isset($_GET['code'])){

            //echo "second if";
                redirect("index.php");

            }else if(empty($_GET['email']) || empty($_GET['code'])){
                //echo "3rd if";
                redirect("index.php");
            }
            else{
 
                if(isset($_POST['code'])){

                    $validation_code = clean($_POST['code']);
                    $email = clean($_GET['email']);


                    $sql = "SELECT id FROM users WHERE validation_code = '".escape($validation_code)."' AND email='".escape($email)."'";
                    $result = query($sql);

                    if(row_count($result) ==1){

                        setcookie('temp_access_code', $validation_code, time()+ 600);

                        redirect("reset.php?email=$email&code=$validation_code");
                    }
                    else{

                        echo validation_errors("Sorry wrong validation code");
                    }
                    
                }
                
            }

    }
    else{

        echo "this else was executed Sorry your cookie has expired";

        set_message("<p class='bg-danger text-center'> Sorry your cookie code expired.</p>");

        redirect("recover.php");
    }
}

/*******Password Reset Function******/

function password_reset(){

    if(isset($_COOKIE['temp_access_code'])){

        if(isset($_SESSION['token']) && isset($_POST['token'])){

            if($_POST['token'] === $_SESSION['token']){

                if($_POST['password'] === $_POST['confirm_password']){

                    $updated_password = md5($_POST['password']);

                    $sql = "UPDATE users SET password = '".escape($updated_password)."' WHERE email = '".escape($_GET['email'])."'"; 
                    
                    query($sql);

                    set_message("<p class='bg-success text-center'>Your password has been reset, please login with the new password.</p>");

                    redirect("login.php");
                }
                else{

                    echo validation_errors("Password feilds do not match");
                }

            }
        }
    }
    else{

    set_message("<p class='bg-danger text-center'>Sorry time expired.</p>");
    redirect("recover.php");

    }

}

?>
