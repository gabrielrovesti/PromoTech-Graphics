<?php
    require_once "includes/connection.php";
    require_once "includes/utilities.php";

    use DB\DBAccess;

    $template = file_get_contents('layouts/layout.html');

    $connection = new DBAccess;

    $pageID = 'prodotto';
    $title  = '';
    $breadcrumbs = '';
    $content = '';
    $descrizione = '';

    if ($connection->open_connection()) {
        
        if(isset($_GET['id'])){

            $id = intval(sanitize($_GET['id'],''));
            $products = $connection->exec_select_query("SELECT id, nome, immagine, altImmagine, descrizione, origine, marca, modello, dimensione, peso, categoria, prezzo FROM prodotto WHERE id=$id;");
            

            if(count($products)){

                $product = $products[0];

                $breadcrumbs = '<p>Ti trovi in: <a href="index.php" lang="en">Home</a> >  <a href="prodotti.php">Prodotti</a> > '.parse_lang($product['nome']).'</p> ';
                
                $title = parse_lang($product['nome'],true) . ' - Pop Tech';

                $content .= file_get_contents('layouts/prodotto.html');

                $content = str_replace('{{nome}}',parse_lang($product['nome']),$content);
                $content = str_replace('{{immagine}}',$product['immagine'],$content);
                $content = str_replace('{{altImmagine}}',$product['altImmagine'],$content);
                $content = str_replace('{{descrizione}}',parse_lang($product['descrizione']),$content);
                $content = str_replace('{{origine}}',$product['origine'],$content);
                $content = str_replace('{{marca}}',$product['marca'],$content);
                $content = str_replace('{{modello}}',$product['modello'],$content);
                $content = str_replace('{{dimensioni}}',parse_abbr($product['dimensione']),$content);
                $content = str_replace('{{peso}}',parse_abbr($product['peso']),$content);
                $content = str_replace('{{categoria}}',$product['categoria'],$content);
                $content = str_replace('{{prezzo}}',$product['prezzo'],$content);

                $descrizione = $product['descrizione'];

                $feedbacks = $connection->exec_select_query("SELECT contenuto, punteggio FROM recensione WHERE prodotto=$id;"); 
                $connection->close_connection();

                foreach($feedbacks as $feedback){
                    $content .= '<div class="comic_box">';
                    $i=0;
                    for(;$i<intval($feedback['punteggio']);$i++){
                        $content .= '<img src="images/stella_piena.svg" />';
                    }
                    if(intval($feedback['punteggio'])!=$feedback['punteggio']){
                        $content .= '<img src="images/stella_mezza.svg" />';
                        $i++;
                    }
                    for(;$i<5;$i++){
                        $content .= '<img src="images/stella_vuota.svg" />';
                    }
                    $content.='<p>'.$feedback['contenuto'].'</p></div>';
                }

            }else{
               
                $content .= '<p>Prodotto non trovato</p>';
            }
        }
    }else{
        $content .= getDBConnectionError();
    }

    $menu = get_menu();
    $template = str_replace('{{menu}}',$menu,$template);

    echo replace_in_page($template,$title,$pageID,$breadcrumbs, 'keywords', $descrizione, $content);
?>