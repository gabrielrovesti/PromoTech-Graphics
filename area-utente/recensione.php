<?php

    require_once "../includes/connection.php";
    require_once "../includes/utilities.php";

    use DB\DBAccess;

    session_start();

    $template = file_get_contents('layouts/layout.html');
    $form     = file_get_contents('layouts/recensione.html');
    $viewPage = file_get_contents('layouts/recensioneStatic.html');

    $pageID = 'recensione';
    $title = "Pop Tech";
    $breadcrumbs = '<p>Ti trovi in: <a href="index.php">Le tue Recensioni</a> &gt; Visualizza Recensione</p>';

    $utente = '';

    $errorsStr = "";

    if(!isLoggedIn()){

        $content = getAdminLoggedOutError();

    }else{

        $utente = $_SESSION['user'];
        $content = "<h1>Nuova Recensione</h1>";

        $id         = '';
        $utente     = '';
        $prodotto   = '';
        $contenuto  = '';
        $punteggio  = '';
        $nomeProdotto = '';

        $connection = new DBAccess();

        if($connection->open_connection()){

            
            if(isset($_POST['submit'])){
            //Invio del form

                $errors = [];
                $query = "";
                
                //Prelevamento dati
                
                $prodotto  = sanitize($_POST['prodotto'],"");
                $utente    = sanitize($_POST['utente'],"");
                $contenuto = sanitize($_POST['contenuto'],"<p><em><b><strong><ul><li><ol>");
                $punteggio = floatVal(sanitize($_POST['punteggio'],""));

                $prodotti = $connection->exec_select_query("SELECT nome FROM prodotto WHERE id=$prodotto");
                if(isset($prodotti[0])){
                    $nomeProdotto = parse_lang($prodotti[0]['nome']);
                }
                
                //Validazione dati
                if($utente==''){
                    array_push($errors,'<p class="message errorMsg" role="alert">Selezionare un utente.</p>');
                }

                if($prodotto==''){
                    array_push($errors,'<p class="message errorMsg" role="alert">Selezionare un utente.</p>');
                }

                if(strlen($contenuto)<10){
                    array_push($errors,'<p class="message errorMsg" role="alert">Inserire il contenuto della recesione, con almeno 10 caratteri.</p>');
                }
                

                if(count($errors)==0){
                    //Procedi con inserimento o medifica
                    if(isset($_POST['id']) && intval($_POST['id'])!=0){

                        //Richiesta di modifica
                        $id   = intval(sanitize($_POST['id'],""));
                        $query = "UPDATE recensione SET contenuto='$contenuto', punteggio=$punteggio WHERE id=$id;";
                
                    }

                    $queryOK = $connection->exec_alter_query($query);

                    if($queryOK){
                        $content .= '<p class="message successMsg">Recensione salvata con successo</p>';
                    }else{
                        $content .= '<p class="message errorMsg" role="alert">Errore durante l\'inserimento. Contatta il supporto tecnico.</p>';
                    }

                }else{
                    //Ritorna il form con i dati precompilati
                    $errorsStr = '<ul>';
                    foreach($errors as $error){
                        $errorsStr .= '<li>'.$error.'</li>';
                    }
                    $errorsStr .= '<ul>';

                    $form = str_replace('{{id}}',$id,$form);

                    $form = str_replace('{{utente}}',$utente,$form);

                    $form = str_replace('{{prodotto}}',$prodotto,$form);
                    $form = str_replace('{{nomeProdotto}}',$nomeProdotto,$form);
                    $form = str_replace('{{contenuto}}',$contenuto,$form);
                    $form = str_replace('{{punteggio}}',$punteggio,$form);
                                
                    $form = str_replace('{{errors}}',$errorsStr,$form);

                    $content .= $form;

                }

            }else{
                //Richiesta di visualizzazione del form

                if(isset($_GET['id']) && intval($_GET['id'])!=0){
                    //Form per la modifica
                    $id = intval(sanitize($_GET['id'],""));

                    $reviews = $connection->exec_select_query('SELECT * FROM recensione WHERE id='.$id.';');

                    if(isset($reviews[0])){

                        $review = $reviews[0];

                        $content = "<h1>Visualizza Recensione</h1>";
                        $breadcrumbs = '<p>Ti trovi in: <a href="index.php">Recensioni</a> &gt; Visualizza Recensione</p>';

                        $utente     = $review['utente'];
                        $prodotto   = $review['prodotto'];
                        $contenuto  = $review['contenuto'];
                        $punteggio  = $review['punteggio'];

                        $prodotti = $connection->exec_select_query("SELECT nome FROM prodotto WHERE id=$prodotto");
                        if(isset($prodotti[0])){
                            $nomeProdotto = parse_lang($prodotti[0]['nome']);
                        }
                        
                        if($contenuto == "" && $punteggio == 0){

                            //Se la recensione è impostata come "nuova" dall'amministratore mostra il form
                            $form = str_replace('{{id}}',$id,$form);

                            $form = str_replace('{{utente}}',$utente,$form);
                            $form = str_replace('{{prodotto}}',$prodotto,$form);
                            $form = str_replace('{{nomeProdotto}}',$nomeProdotto,$form);
                            $form = str_replace('{{contenuto}}',$contenuto,$form);
                            $form = str_replace('{{punteggio}}',$punteggio,$form);
                                            
                            $form = str_replace('{{errors}}',$errorsStr,$form);

                            $content .= $form;

                        }else{
                            //Altrimenti mostra la pagina statica di riepilogo
                            
                            $viewPage = str_replace('{{nomeProdotto}}',$nomeProdotto,$viewPage);
                            $viewPage = str_replace('{{contenuto}}',$contenuto,$viewPage);
                            $viewPage = str_replace('{{punteggio}}',$punteggio,$viewPage);

                            $content .= $viewPage;
                            
                        }
                    }else{
                        $content .= '<p class="message errorMsg" role="alert">Recensione non trovata.</p>';
                    }           
                }   
            }
            $connection->close_connection();
        }else{
            $content .= getDBConnectionError(true);
        }

    }

    $menu = get_user_menu();
    $template = str_replace('{{menu}}',$menu,$template);
    $template = str_replace('{{pageID}}',$pageID,$template);
    echo replace_in_user_page($template,$title,$pageID,$breadcrumbs,$content,'setUserRecensioneChecks();addFieldsEvent();setRangeListener();');
?>