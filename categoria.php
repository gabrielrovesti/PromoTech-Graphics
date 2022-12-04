<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8" />
    
	<title>Categoria</title>
	
    <meta name="description" content="Categoria | PopTech">
    <meta name="keywords" content="PopTech, videogiochi, negozio videogiochi, gaming, manga, fumetti, giochi da tavolo">
    <meta name="author" content="PopTech">
    
    <!-- Includi i link ai file di stile CSS e script Javascript -->
    <?php include "includes/inports.php"; ?>

</head>
<body id="homePage">

    <a href="#content" class="srOnly">Vai al contenuto</a>
	
    <?php include "includes/header.php"; ?>
    <?php include "includes/menu.php"; ?>
   
    <nav id="breadcrumbs" aria-label="Percorso" role="navigation">
        <p>Ti trovi in: Prodotti > {nome categoria}</p> 
    </nav>    
   
	<main id="content">
       
        <h1>Categoria</h1> 

        <div class="productsRow">

            <?php for($j=0;$j<20;$j++){ ?>

                <article role="article">
                    <header>
                        <img src="images/testImg.jpg" alt="Descrizione Immagine">
                        <h2>Product Name</h2>
                    </header>
                        Descrizione Descrizione Descrizione Descrizione Descrizione
                        <a href="" class="button" title="Vedi prodotto {nome prodotto}">Scopri di più</a>
                </article>

            <?php } ?>
        </div>

	</main>

    <?php include "includes/footer.php"; ?>
	
</body>
</html>