<?php

if (!defined('SAFE_ACCESS')) {
    exit(0);
}

?><!DOCTYPE html>
<html lang="ru">
  	<head>
    	<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<meta name="description" content="LZZ.SU - умный сокращатель ссылок!">
    	<meta name="keywords" content="lzz, сокращатель, умный, многофункционалный, ссылка, ссылок">
        <meta name="generator" content="Alexander Mayorov (zerobone.net)">
    	<meta property="og:title" content="LZZ.SU - умный сокращатель ссылок!">
    	<meta property="og:type" content="landing">
    	<meta property="og:url" content="http://lzz.su">
    	<meta property="og:image" content="/images/lzz.png">
    	<link href="/stylesheets/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
    	<link rel="icon" href="/images/icon.ico" type="image/x-icon">
    	<link rel="shortcut icon" href="/images/icon.ico" type="image/x-icon">
    	<title>LZZ.SU - умный сокращатель ссылок!</title>
    	<!--[if lt IE 9]>
      		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
  	</head>
  	<body>
     	<div class="main-container">
       		<div class="header-image">
         		<img src="/images/header.png" class="inner-background" alt="Лого lzz.su">
       		</div>
       		<header id="header">
         		<ul>
           			<li><a href="/" class="btn"><span>Главная</span></a></li>
<!--           			<li><a href="/api" class="btn"><span>API</span></a></li>-->
<!--           			<li><a href="/widgets" class="btn"><span>Виджеты</span></a></li>-->
           			<!-- <li><a href="/rules" class="btn"><span>Правила</span></a></li> -->
                <!-- <li><a href="/contact" class="btn"><span>Контакты</span></a></li> -->
         		</ul>
          		<div class="user-profile">
          			<?php

                    if ($lzz->account->loggedIn()) {

                        ?>
                        <img style='margin-right:5px;' class='user-avatar' src='/images/avatars/default.png'>
                        <p> <a href='/account/cabinet' class='no-underline'>
                                <span class='username text-white no-underline'>
                                <?= $lzz->account->tokenPayload['name'] ?>
                                </span>
                            </a> <br>
                            <a href='/account/cabinet' class='no-underline'>
                                <span class='balance text-white no-underline'>Личный кабинет</span>
                            </a>
                        </p>
                        <?php

                    }
                    else {

                        ?>
                        <img style='margin-right:5px;' class='user-avatar' src='/images/avatars/default.png'>
                        <p> <a href='/account/register' class='no-underline'>
                            <span class='username text-white no-underline'>Регистрация</span>
                        </a> <br>
                        <a href='/account/login' class='no-underline'>
                            <span class='balance text-white no-underline'>Вход</span>
                        </a>
                        </p>
                        <?php

                    }

                    ?>
          		</div>
       		</header>
          	<div class="user-profile mobile-up">
                <?php

                if ($lzz->account->loggedIn()) {

                    ?>
                    <a href="/"><img src="/images/avatars/default.png" alt="Аватарка пользователя" class="user-avatar"></a>
                    <p> <span class="username"><?= $lzz->account->tokenPayload['name'] ?></span> <br>
                    <a href="/account/cabinet">
                        <span class="balance">Личный кабинет</span>
                    </a></p>
                    <?php
                }
                else {

                    ?>
                    <a href="/account/login">
                        <img src="/images/avatars/default.png" alt="Аватарка пользователя" class="user-avatar">
                    </a>
                    <p> <a href="/account/login"><span class="username">Войти</span> </a><br>
                    <a href="/account/register">
                        <span class="balance">Зарегистрироваться</span>
                    </a></p>
                    <?php

                }

                ?>
          	</div>
  			<div class="content-wrapper">
       			<div class="content_container">