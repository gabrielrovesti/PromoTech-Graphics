<?php

    require_once "../includes/connection.php";
    require_once "../includes/utilities.php";

    use DB\DBAccess;

    session_start();

    $template = file_get_contents('layouts/layout.html');

    $pageID = 'utenti';
    $title = "Pop Tech";
    $breadcrumbs = '<p>Ti trovi in: Utenti</p>';

    if(!isLoggedIn(true)){

        $content = getAdminLoggedOutError();

    }else{

        $content = "<h1>Utenti</h1>";

        $content .= '<a href="utente.php" class="btn btn-green">Aggiungi Utente</a>';

        $connection = new DBAccess();

        if($connection->open_connection()){

            $users = $connection->exec_select_query('SELECT id, nome, admin FROM utente ORDER BY admin,nome;');
            $connection->close_connection();

            foreach($users as $user){
                
                $content .= '<div class="listItem">';
                    $content .= '<span>'.parse_lang($user['nome']).(($user['admin'])?' <em>Amministratore</em>':'').'</span>';
                    $content .= '<span><a href="utente.php?id='.$user['id'].'" class="btn btn-info">Modifica</a>';
                    $content .= '<a href="delete.php?id='.$user['id'].'&type=utente" class="btn btn-danger">Elimina</a></span>';
                $content .= '</div>';

            }

        }else{
            $content .= getDBConnectionError(true);
        }

    }

    $menu = get_admin_menu();
    $template = str_replace('{{menu}}',$menu,$template);
    $template = str_replace('{{onload}}','',$template);
    $template = str_replace('{{pageID}}',$pageID,$template);

    $template = str_replace('{{title}}',$title,$template);
    $template = str_replace('{{breadcrumbs}}',$breadcrumbs,$template);
    $template = str_replace('{{content}}',$content,$template);

    echo $template;

?>