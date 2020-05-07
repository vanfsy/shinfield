<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <?= $this->html->meta('icon','/assets/ico/favicon.ico', array('fullBase' => true)); ?>

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <?= $this->html->css('bootstrap.min.css', array('fullBase' => true)); ?>

    <!-- Custom styles for this template -->
    <?= $this->html->css('dashboard.css', array('fullBase' => true)); ?>

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><?= $this->html->script('ie8-responsive-file-warning.js', array('fullBase' => true)); ?></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?= $this->html->css('ext.css', array('fullBase' => true)); ?>

  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"><a>Toggle navigation</a></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a>Login <?= $loginname ?></a></li>
            <li><a href="/dashboard/">ダッシュボード</a></li>
            <li><a href="/user/logout/">ログアウト</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">

    <?php if ($this->request->url == 'dashboard/'){ ?>
            <li class="active"><a href="/dashboard/">ダッシュボード</a></li>
    <?php } else { ?>
            <li><a href="/dashboard/">ダッシュボード</a></li>
    <?php } ?>
    <?php if ($this->viewPath == 'Job' && $this->request->url != 'job/entry/'){ ?>
            <li class="active"><a href="/job/">求人情報</a></li>
    <?php } else { ?>
            <li><a href="/job/">求人情報</a></li>
    <?php } ?>
    <?php if ($this->request->url == 'job/entry/'){ ?>
            <li class="active"><a href="/job/entry/">求人情報新規登録</a></li>
    <?php } else { ?>
            <li><a href="/job/entry/">求人情報新規登録</a></li>
    <?php } ?>
    <?php if ($this->viewPath == 'Schedule'){ ?>
            <li class="active"><a href="/schedule/">求人情報公開設定</a></li>
    <?php } else { ?>
            <li><a href="/schedule/">求人情報公開設定</a></li>
    <?php } ?>

    <?php if ($this->viewPath == 'Seeker'){ ?>
            <li class="active"><a href="/seeker/">応募者管理</a></li>
    <?php } else { ?>
            <li><a href="/seeker/">応募者管理</a></li>
    <?php } ?>

    <? if($this->name =='Inbox'){?>
            <li class="active"><a href="/inbox/">受信トレイ</a></li>
    <? } else { ?>
            <li><a href="/inbox/">受信トレイ</a></li>
    <? } ?>

    <? if($this->name =='Outbox'){ ?>
            <li class="active"><a href="/outbox/">送信トレイ</a></li>
    <? } else { ?>
            <li><a href="/outbox/">送信トレイ</a></li>
    <? } ?>

    <?php if ($this->viewPath == 'Company'){ ?>
            <li class="active"><a href="/company/">会社情報</a></li>
    <?php } else { ?>
            <li><a href="/company/">会社情報</a></li>
    <?php } ?>
          </ul>
        </div>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?= $this->fetch('content'); ?>
        </div>

      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <?= $this->html->script('bootstrap.min.js', array('fullBase' => true)); ?>
    <?= $this->html->script('/assets/js/docs.min.js', array('fullBase' => true)); ?>
  </body>

</html>
	<?php echo $this->element('sql_dump'); ?>
