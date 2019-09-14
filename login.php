<?php

ob_start();
if (!isset($_SESSION)): session_start(); endif;

?>

<!doctype html>
<html lang="en">
<head>
    <?php

    include('includes/functions.php');
    include('includes/constants.php');
    include('includes/query.php');

    // INCLUDE FA
    include_fa();
    // INCLUDE JQUERY
    include_jquery();
    // INCLUDE BOOTSTRAP 4
    include_bootstap(true,4);

    $rem = isset($_POST['remember'])?true:false;

    $error = 0;
    if(isset($_POST['submit'])){

        $key = (isset($_POST['password']) && $_POST['password']==SUPER_PASS)? ['user'=>trim($_POST['username'])] : ['user'=>trim($_POST['username']),'pass'=>trim($_POST['password'])];
        $qLogin = new QUERY(['TABLE'=>'users','KEY'=>$key]);

        if($qLogin->numRows()>0){

            if($rem) {
                //Cookie
                $cookieName = 'uid';
                $cookieValue =  $qLogin->fetch->id;
                $duration = time() + 60 * 60 * 24 * 90;
                // SET THE COOKIE
                setcookie($cookieName, $cookieValue, $duration);
            }


            // SET THE SESSIONS
            $_SESSION['location']       = $defaultLocation;
            $_SESSION['login']          = 'true';
            $_SESSION['userid']         = $qLogin->fetch->id;
            $_SESSION['username']       = $qLogin->fetch->user;
            $_SESSION['role']           = $qLogin->fetch->role;
            $_SESSION['user_group_id']  = $qLogin->fetch->user_group_id;


            header('Location: index.php?pg=dashboard');
            exit();

        }else{
            $error = 1;
        }
    }
    ?>


    <title>Employee Management System | Login </title>

    <style>
        body{background:darkgrey}

        div.card .card-header{ background-color: #000; color: #FFFF00;}
        button.btn-main{background-color: #000; color: #FFFF00;font-weight: bolder; border-radius: 30px; display: inline-block;}



        .sidenav {height: 100%;background-color: #000;overflow-x: hidden; padding-top: 20px;}


        .main {padding: 0px 10px;}

        @media screen and (max-height: 450px) {
            .sidenav {padding-top: 15px;}
        }

        @media screen and (max-width: 450px) {
            .login-form{ margin-top: 10%;}

        }

        @media screen and (min-width: 767px){
            .main{margin-left: 40%;}

            .sidenav{
                width: 40%;
                position: fixed;
                z-index: 1;
                top: 0;
                left: 0;
            }

            .login-form{
                margin-top: 40%;
                border:1px solid #000;
                padding: 20px;
                box-shadow: 3px 3px 3px #000;
                background-color: #ffffff;
            }

        }

        @media only screen and (device-width: 768px) {
            /* For general iPad layouts */

            .login-main-text{ margin-top: 0 !important;}
            .sidenav{
                width: 100%;
                height: 50%;
                position: relative;
                z-index: 1;
                top: 0;
                left: 0;
                display: inline-block;
            }

            .login-form{
               width: 70%;
                border:1px solid #000;
                padding: 20px;
                box-shadow: 3px 3px 3px #000;
                background-color: #ffffff;
                margin: 0 auto;
            }

            .main{margin:0;}


        }



        .login-main-text{margin-top: 10%;padding: 60px;color: #fff;}
        .login-main-text h2{ color: #FFFF00; font-weight: bolder;}
        .login-main-text h2 small{ color: #F0F0F0;}

    </style>

    <script>

        var path  = '';
        var numberOfImg = 0;
        var timer = null;
        var imgNumber = 0;

        $(function(){

            var rem_me_users2 = $('#rem_me_users').val().split(", ");
            var user_pick2 = $('#username').val();
            var bool2 = false;

            rem_me_users2.forEach(function(item){
                if(item == user_pick2) {
                    bool2 = true;
                    $('#rem_me').css('display', 'block');
                }
            });

            if(bool2==false){
                $('#rem_me').css('display', 'none');
            }

            path = jQuery.parseJSON($('input#flash-msgs').val());
            numberOfImg = path.length;
            console.log(path[1]['message']);

            $('#flash-msg').html(path[imgNumber]['message']);

            if(path[imgNumber]['link']){
                $('#link').css("display", "inline");
                $('#link').attr('href', path[imgNumber]['link']);
            }else{
                $('#link').css("display", "none");
            }
            setTimer();

            $(".transition").click(function(){
                var dir = $(this).data('dir');
                if(dir === "prev"){
                    --imgNumber;
                    if (imgNumber < 0) {
                        imgNumber = numberOfImg - 1;
                    }
                    $('#flash-msg').html(path[imgNumber]['message']);
                    if(path[imgNumber]['link']){
                        $('#link').css("display", "inline");
                        $('#link').attr('href', path[imgNumber]['link']);
                    }else{
                        $('#link').css("display", "none");
                    }

                    return false;
                }else{
                    ++imgNumber;
                    if (imgNumber > (numberOfImg - 1)) {
                        imgNumber = 0;
                    }
                    $('#flash-msg').html(path[imgNumber]['message']);
                    if(path[imgNumber]['link']){
                        $('#link').css("display", "inline");
                        $('#link').attr('href', path[imgNumber]['link']);
                    }else{
                        $('#link').css("display", "none");
                    }
                    console.log(path[imgNumber]);
                    return false;
                }
            });

            $('#username').change(function(){
                //get the array of users
                var rem_me_users = $('#rem_me_users').val().split(", ");

                //get the user's pick
                var user_pick = $(this).val();

                //create booleanto keep tracl of the checkbox
                var bool = false;

                //go throught the array of users and compare them to the user's pick
                rem_me_users.forEach(function(item){

                    //if the user's pick matches an item in the database then display the checkbox
                    if(item == user_pick) {
                        bool = true;
                        $('#rem_me').css('display', 'block');
                    }
                });

                //if no matches were found do not display the checkbox
                if(bool==false){
                    $('#rem_me').css('display', 'none');
                }

            })
        });

        function slide() {
            imgNumber = (imgNumber + 1) % path.length;
            $('#flash-msg').html(path[imgNumber]['message']);

            if(path[imgNumber]['link']){
                $('#link').css("display", "inline");
                $('#link').attr('href', path[imgNumber]['link']);
            }else{
                $('#link').css("display", "none");
            }
        }

        function setTimer() {

            $('#pause').find('i').toggleClass("fa fa-play fa fa-pause");

            if (timer) {
                clearInterval(timer);
                timer = null;
            } else {
                timer = setInterval(slide, 3000);
            }
            return false;
        }



        function validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }

        function forgotPassword(){

            let valid_email = true;
            let username = $('#username option:selected').val();
            if(!validateEmail(username)) {
                alert(username + "is not a valid email address");
                valid_email = false;
            }
            //return false;
            if(valid_email) {
                $.ajax({
                    type: "POST",
                    url: 'db/forgot_password.php',
                    data: {username: username},
                    success: function (responseTxt) {
                        if(responseTxt==='1'){
                            alert("Email with instructions to retrieve password sent to "+username);
                        }
                        else{
                            alert("Could not send email with instructions to retrieve password");
                        }

                    }
                });
            }
        }


    </script>

</head>
<body>


<input id="rem_me_users" value="<?php echo REMEMBER_ME_USERS?>" hidden />

<div class="main" style="margin-top:50px;">
    <div class="col-lg-6 col-md-12">
        <div class="login-form">
            <form method="post">
                <div class="form-group">
                    <label>User Name</label>
                    <select type="text" name="username" id="username" class="form-control form-control-lg">
                        <option selected>Select Username</option>

                        <?php
                        $localIP = getHostByName(getHostName()); // Get local host name
                        $qUsers = new QUERY(['TABLE'=>'users','KEY'=>['status'=>1],'ORDER'=>'user','COLS'=>['id','user','status','host_name']]); ?>
                        <?php foreach($qUsers->fetchAll() as $user){ $user['id'] == $_COOKIE['uid'] ?>

                            <?php
                            // AUTOSELECT 'shreyas' ON LOCAL DEV AND AUTOSELECT USER BASED ON LOCAL IP
                            $selected = (getenv('SERVER_ENV') == 'DEVELOPMENT')?($user['user']=='shreyas'?'selected':'') : ((isset($_COOKIE['uid']) && $_COOKIE['uid'] == $user['id']) ? 'selected' : '');

                            ?>
                             <option value="<?=$user['user']?>" <?=$selected?>><?=extract_email_name($user['user'])?></option>

                            <?php  } ?>


                    </select>
                </div>
                <div class="form-group">
                    <label>Password <?=isset($_POST['submit'])&&$error?'<small class="text-danger">Invalid Password</small>':''?></label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" />
                </div>

                <div style="float:left;">
                    <div id="rem_me" style="padding-left:20px; display: none;">
                        <label><input type="checkbox" class="form-check-input" name="remember">
                        <a>Remember my username</a></label>
                    </div>
                    <a href="javascript:forgotPassword()">Forgot Password</a><br/>
                </div>
                <div style="float:right;">
                    <button type="submit" name="submit" id="submit" class="btn btn-main float-right btn-lg">Login</button>
                </div>
                <div class="clearfix"></div>

            </form>
        </div>

    </div>

</div>

</body>

</html>

