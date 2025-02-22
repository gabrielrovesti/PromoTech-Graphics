<?php

    require_once "../includes/connection.php";
    require_once "../includes/utilities.php";

    session_start();

    use DB\DBAccess;

    $template = file_get_contents('layouts/layout.html');
    $form     = file_get_contents('layouts/login.html');

    $pageID = 'login';
    $title = "Pop Tech";
    $breadcrumbs = '<p>Ti trovi in: Accedi';

    $errorsStr = "";

    if(isLoggedIn(true)){

        header("Location: index.php");
        die();

    }else{

        $content = "<h1>Accedi</h1>";

        if(isset($_POST['submit'])){
            //Invio del form

            $errors = [];

            $username = sanitize($_POST['username'],"");
            $password = sanitize($_POST['password'],"");

            if(!preg_match('/\w{4,}/',$username)){
                array_push($errors,'<p class="message errorMsg" role="alert">Formato del nome utente non corretto</p>');
            }

            if(strlen($password)<4){ //"user" ha 4 caratteri
                array_push($errors,'<p class="message errorMsg" role="alert">Formato della <span lang="en">password</span> non corretto.</p>');
            }

            if(count($errors)==0){

                $connection = new DBAccess();

                if($connection->open_connection()){
                    
                    //Cerca utente con quell'id
                    $users = $connection->exec_select_query("SELECT id,admin,password FROM utente WHERE username='$username';");  
                    $connection->close_connection();

                    if(count($users)>0){ //Utente trovato
                       
                        $user = $users[0];

                        if(password_verify($password,$user['password'])){

                            $_SESSION['user'] = $user['id'];

                            header("Location: index.php");
                            die();

                        }else{
                            array_push($errors,'<p class="message errorMsg" role="alert">Nome utente o <span lang="en">password</span> non corretti</p>');
                        }

                    }else{
                        array_push($errors,'<p class="message errorMsg" role="alert">Nome utente o <span lang="en">password</span> non corretti</p>');
                    }

                    $errorsStr = '<ul>';
                    foreach($errors as $error){
                        $errorsStr .= '<li>'.$error.'</li>';
                    }
                    $errorsStr .= '<ul>';
    
                    $form = str_replace("{{errors}}",$errorsStr,$form); //Contiene solo l'ultimo errore
    
                    $content .= $form;
        
                }else{
                    $content .= getDBConnectionError(true);
                }
                                        
            }else{ //Mostra form con errori di formato

                $errorsStr = '<ul>';
                foreach($errors as $error){
                    $errorsStr .= '<li>'.$error.'</li>';
                }
                $errorsStr .= '<ul>';

                $form = str_replace("{{errors}}",$errorsStr,$form);

                $content .= $form;

            }
                

        }else{
            $form = str_replace("{{errors}}",$errorsStr,$form);
            $content .= $form;
        }        
    }

    $menu = get_user_menu();
    $template = str_replace('{{menu}}',$menu,$template);
    $template = str_replace('{{pageID}}',$pageID,$template);
    echo replace_in_user_page($template,$title,$pageID,$breadcrumbs,$content, 'setUserLoginChecks();addFieldsEvent();');
?>