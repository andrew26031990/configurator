<?php 
include '../functions.php';

if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
{
    $secret = '6LfLXM0UAAAAAIoB2wLKujeWKwT_2dW-GVPJHqfe';
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    $responseData = json_decode($verifyResponse);
    if($responseData->success)
    {
        if (isset($_POST['login']) && isset($_POST['password']))
        {  
            ini_set('session.gc_maxlifetime', 86400);
            ini_set('session.cookie_lifetime', 0);
            session_set_cookie_params(0);  
            session_start();
            ini_set('display_errors', -1);
            //здесь прописываем код обработки формы
            $login = mysqli_real_escape_string($mysqli, $_POST['login']);
            $password = mysqli_real_escape_string($mysqli, $_POST['password']);
            if(CheckLoginPass($mysqli, $login, sha1(md5($password)))){
                $_SESSION['username'] = $_POST['login'];
                echo 'Login';
            }else{
                echo 'False';
                exit();
            }
        }
    }
    else
    {
        echo 'incorrectCaptcha';
    }
}else{
   echo 'incorrectCaptcha';
}



/*if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
{
    $secret = '6LflMs8UAAAAAO5Uxwv5S6F-1WYi7_lmPWss_SXG';
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    $responseData = json_decode($verifyResponse);
    if($responseData->success)
    {
        if (isset($_POST['login']) && isset($_POST['password']))
        {  
            ini_set('session.gc_maxlifetime', 86400);
            ini_set('session.cookie_lifetime', 0);
            session_set_cookie_params(0);  
            session_start();
            ini_set('display_errors', -1);
            //здесь прописываем код обработки формы
            $login = $_POST['login'];
            $password = $_POST['password'];
            if(sha1(md5($password)) == '56f844d968b2ce9c07a388a2a28c92b6f0cc74f3' && $login == 'admin'){
                $_SESSION['username'] = $_POST['login'];
                echo 'Login';
            }else{
                echo 'False';
            }
        }
    }
    else
    {
        echo 'incorrectCaptcha';
    }
}else{
   echo 'incorrectCaptcha';
}*/
?>
