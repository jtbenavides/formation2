<!DOCTYPE html>
<html>
  <head>
    <title>
      <?= isset($title) ? $title : 'Mon super site'?>
    </title>
      
      <?php use \OCFram\Direction; ?>
 
    <meta charset="utf-8" />
 
    <link rel="stylesheet" href="/css/Envision.css" type="text/css" />
  </head>
 
  <body>
    <div id="wrap">
      <header>
        <h1><a href="/">Mon super site</a></h1>
        <p>Comment ça, il n'y a presque rien ?</p>
      </header>
 
      <nav>
        <ul>
            <li><a href=<?= Direction::askRoute('Frontend','News','index') ?> >Accueil</a></li>
            <?php if ($user->isAuthenticated()) { ?>
                <li><a href=<?= Direction::askRoute('Backend','News','index'); ?>>Admin</a></li>
                <li><a href=<?= Direction::askRoute('Backend','News','insert'); ?>>Ajouter une news</a></li>
                <li><a href=<?= Direction::askRoute('Backend','Connexion','logout'); ?>>Deconnection</a></li>
          <?php }else{ ?>
                <li><a href=<?= Direction::askRoute('Backend','News','index'); ?>>Connection</a></li>
                <li><a href=<?= Direction::askRoute('Frontend','Connection','signin'); ?>>Inscription</a></li>
          <?php } ?>
		  <?php require_once '../vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php'; ?>
				<li><a href="http://mobiledetect.net/">Surf sur <?php $detect = new Mobile_Detect;
                    if ($detect->isMobile()){ ?>
                        mobile.
                      <?php }else if ($detect->isTablet()){ ?>
                        tablette.
                        <?php } else{ ?>
                        PC.
                        <?php } ?>
                        </a></li>
        </ul>
      </nav>
 
      <div id="content-wrap">
        <section id="main">
          <?php if ($user->hasFlash()) echo '<p style="text-align: center;">', $user->getFlash(), '</p>'; ?>
 
          <?= $content ?>
        </section>
      </div>
 
      <footer></footer>
    </div>
  </body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="/js/mon-script.js"></script>