<?php

  /*!
   * ユーザー設定ページ
   */

  $title = "アカウント設定";
  $subTitle = "アカウントの設定を管理/変更できます。";

  $IndexFromBot = "noindex, nofollow";

  if ( isset( $_POST["_call"] ) && isset( $_POST["_mailaddress"] ) && isset( $_POST["_profile"] ) ) {
    mb_regex_encoding("UTF-8");

    if ( empty( $_POST["_call"] ) ||
         !preg_match( "/^[ぁ-んァ-ヶーa-zA-Z0-9一-龠０-９、。,. \n\r]+$/u" , $_POST["_call"] ) )
    {
      NCPRedirect( "?error=1" );
      exit();
    }
    $UserName = htmlspecialchars( $_POST["_call"] );

    if ( empty( $_POST["_mailaddress"] ) ||
         !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $_POST["_mailaddress"] ) )
    {
      NCPRedirect( "?error=2" );
      exit();
    }

    try {

      $stmt = $dbh->prepare('update MetaNoteUsers set UserName = ?, MailAdd = ?, Profile = ? where UserIntID = ? limit 1;');
      $stmt->execute( [$UserName, $_POST["_mailaddress"], htmlspecialchars( $_POST["_profile"], ENT_QUOTES ) , $LocalUser["UserIntID"]] );

      $stmt2 = $dbh->prepare('select * from MetaNoteUsers where UserIntID = ? limit 1;');
      $stmt2->execute( [$LocalUser["UserIntID"]] );
      $row2 = $stmt2->fetch( PDO::FETCH_ASSOC );
      $_SESSION["logindata"] = json_encode( $row2 );

    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

    header( "Location: /setting" );
    exit();
  }

?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>

    <title><?=$title?> - MetaNote.</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="robots" content="<?=$IndexFromBot?>">
    <meta name="keywords" content="MetaNote">
    <meta name="favicon" content="/favicon.ico">
    <meta name="description" content="<?=$subTitle?>">
    <meta name="copyright" content="<?=Copyright?>">

    <meta name="twitter:description" content="<?=$subTitle?>">
    <meta name="twitter:domain" content="<?=Domain?>">

    <meta property="og:description" content="<?=$subTitle?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja_JP">

    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_16_16.ico" sizes="16x16">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_32_32.ico" sizes="32x32">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_64_64.ico" sizes="64x64">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_192_192.ico" sizes="192x192">
    <link rel="apple-touch-icon-precomposed" href="https://<?=Domain?>/icon/index_150_150.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/setting.css">
  </head>
  <body>
    <?=Get_Body_Header()?>

    <div class="mainobj" align="center">

      <div class="container marketing">
        <br><br>
        <h1><?=$title?></h1>
      <?php if ( isset( $_GET["error"] ) ) { ?><div class="errortext" align="center"><h2><?php
        if ( $_GET["error"] == "2" )
          echo "メールアドレスの入力形式が不正です。";
        else if ( $_GET["error"] == "1" )
          echo "指定されたユーザー名は無効です。";
        else
          echo "技術的なエラーが発生しました。";
      ?></h2></div><?php } ?>
        <form action="" method="POST">
          <hr size="10" color="#7fffd4">
          <p><b>ニックネーム:</b> <input type="text" name="_call" value="<?=htmlspecialchars( $LocalUser["UserName"], ENT_QUOTES )?>" id="_call" placeholder="山田太郎" required></p>
          <p><b>メールアドレス:</b> <input type="email" name="_mailaddress" value="<?=htmlspecialchars( $LocalUser["MailAdd"], ENT_QUOTES )?>" placeholder="yamada@example.com" id="_mailaddress" required></p>
          <p><b>プロフィール(公開): </b></p>
          <textarea name="_profile" placeholder="公開されるプロフィールの内容をこちらへ入力してください(1080文字まで)" style="height:200px;width:320px;"><?=htmlspecialchars( $LocalUser["Profile"], ENT_QUOTES )?></textarea><br>
          <input type="submit" value="設定を保存" class="btn2">
          <hr size="10" color="#7fffd4">
        </form>
      </div>

      <div class="container marketing">
        <footer class="pt-4 my-md-5 pt-md-5 border-top">
          <div class="row">
            <div class="col-12 col-md">
              <small class="d-block mb-3 text-muted"><?=Copyright?><br>Developed by ActiveTK.</small>
            </div>
            <div class="col-6 col-md">
              <h5>サイトマップ</h5>
              <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="/#about">サイト概要</a></li>
                <li><a class="text-muted" href="https://github.com/ActiveTK/MetaNote">Githubリポジトリ</a></li>
                <li><a class="text-muted" href="/#contact">お問い合わせ</a></li>
              </ul>
            </div>
            <div class="col-6 col-md">
              <h5>その他</h5>
              <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="/license">利用規約</a></li>
                <li><a class="text-muted" href="/privacy">プライバシーポリシー</a></li>
                <li><a class="text-muted" href="https://profile.activetk.jp/">開発者</a></li>
              </ul>
            </div>
          </div>
        </footer>
      </div>

      <br><br>

    </div>

    <script src="/js/navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  </body>
</html>
  

