<?php

  /*!
   * 論文を表示します。
   */

  if ( !defined( 'ArticleID' ) )
    MetaNote_Fatal_Die( "論文ファイルを指定せずに閲覧を行おうとしました。" );

  try {
    $stmt = $dbh->prepare('select * from MetaNoteArticles where ArticleID = ?');
    $stmt->execute( [ArticleID] );
    $row = $stmt->fetch( PDO::FETCH_ASSOC );
  } catch ( \Throwable $e ) {
    MetaNote_Fatal_Die( $e->getMessage() );
  }

  if ( !isset( $row["Writers"] ) )
    MetaNote_Fatal_Die( "存在しない論文ファイルを開きました。" );

  if ( $row["InPublic"] !== "true" )
  {
    if ( !isset( $LocalUser["UserIntID"] ) )
      MetaNote_Fatal_Die( "閲覧権限のない論文ファイルを開きました" );
    $Writers = json_decode( $row["Writers"], true );
    $InWriter = false;
    foreach( $Writers as $Writer )
      if ($Writer === $LocalUser["UserIntID"])
      {
        $InWriter = true;
        break;
      }
    if ( !$InWriter )
      MetaNote_Fatal_Die( "閲覧権限のない論文ファイルを開きました。" );
  }

  try {
    if ( !isset( $_GET["pdf"] ) )
    {
      $stmt = $dbh->prepare('update MetaNoteArticles set PVCount = ? where ArticleID = ?');
      $NextPV = $row["PVCount"] + 1;
      $stmt->execute( [$NextPV, ArticleID] );
      $stmt->fetch( PDO::FETCH_ASSOC );
    }
  } catch ( \Throwable $e ) {
    MetaNote_Fatal_Die( $e->getMessage() );
  }

  function is_utf8($str)
  {
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
      $c = ord($str[$i]);
      if ($c > 128) {
        if (($c > 247))
          return false;
        else if ($c > 239)
          $bytes = 4;
        else if ($c > 223)
          $bytes = 3;
        else if ($c > 191)
          $bytes = 2;
        else
          return false;
        if (($i + $bytes) > $len)
          return false;
        while ($bytes > 1) {
          $i++;
          $b = ord($str[$i]);
          if ($b < 128 || $b > 191)
            return false;
          $bytes--;
        }
      }
    }
    return true;
  }

  if ( $row["DateType"] === "Text/MarkDown" )
  {
    $title = htmlspecialchars( $row["ArticleTitle"] );
    $subTitle = htmlspecialchars( $row["ArticleSubtitle"] );

    if ( $row["InPublic"] === "true" )
      $IndexFromBot = "All";
    else
      $IndexFromBot = "noindex, follow";


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
    <link rel="stylesheet" href="/css/view.css">

    <script src="https://cdn.jsdelivr.net/gh/markedjs/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.2/dist/purify.min.js"></script>
    <script>
      window.onload = function () {
        document.getElementById("DATA").innerHTML = DOMPurify.sanitize(marked.parse(document.getElementById("MarkDownSource").innerText));
        
        document.getElementById("paper-font").onchange = e => {
          document.getElementById("paper").style.fontFamily = e.target.value;
        };
      }
    </script>

    <script src="https://www.google.com/recaptcha/api.js?render=6LfQCi8jAAAAAIgnC9Pen1m8Api5zOrFnPLzF2fu"></script>
    <script>
    grecaptcha.ready(function () {
      grecaptcha.execute("6LfQCi8jAAAAAIgnC9Pen1m8Api5zOrFnPLzF2fu", {action: "sent"}).then(function(token) {
        var recaptchaResponse = document.getElementById("recaptchaResponse");
        recaptchaResponse.value = token;
      });
    });
    </script>

  </head>
  <body>
    <?=Get_Body_Header()?>

    <div class="mainobj" align="center">

      <div class="container marketing" id="paper">
        <br><br>
        <h1 align="left" class="titlecomes"><?=$title?></h1>
        <p align="left"><?=$subTitle?></p>
        <p align="right">
          著者: <?php
            foreach( json_decode( $row["Writers"], true ) as $Writer )
              echo htmlspecialchars( MetaNote_GetNameByID_bySQL( $dbh, $Writer )[0] ) . ";";
          ?><br>
          作成日: <?=date("Y/m/d H:i:s", $row["CreateTime"] * 1)?><br>
          更新日: <?=date("Y/m/d H:i:s", $row["LastUpdateTime"] * 1)?><br>
          <div class="controls" align="right">
            <div>
              <label for="paper-font">書体: </label>
              <select id="paper-font">
                <option value="sans-serif">Sans Serif</option>
                <option value="serif">Serif</option>
              </select>
            </div>
          </div>
        </p>
        <br>
        <hr>

        <div align="left" id="DATA"></div>
        <div id="MarkDownSource" style="display:none;"><?
        if (!file_exists(MetaNote_Home . $row["DataSrc"]))
          MetaNote_Fatal_Die( "ソールファイルが存在しません。" );
        $file = fopen(MetaNote_Home . $row["DataSrc"], "r");
        $alltext = "";
        if ($file) {
          while ($line = @fgets($file))
            if (substr($line, 0, 2) === "# ")
              $alltext .= $line . "<hr>";
            else
              $alltext .= $line;
          if (@is_utf8($alltext))
            echo @htmlspecialchars($alltext);
          else
            echo @htmlspecialchars(@mb_convert_encoding($alltext, 'UTF-8', 'SJIS'));
        }
        else
          MetaNote_Fatal_Die( "ソールファイルが存在しません。" );
        fclose($file);
        ?></div>
        <div align="left">
          <hr>
          <h2>公開コメント</h2>
          <p>最新の100件のみ表示されます。</p>
          <?php

          $Comments = file( MetaNote_Home . $row["CommentsJsonfp"], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

          if ( isset( $_POST["add_title"] ) &&  isset( $_POST["add_data"] ) ) {

            if ( isset( $_POST["recaptchaResponse"] ) && !empty( $_POST["recaptchaResponse"] ) ) {
              $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".Google_ReCap_ACTIVETKDOTJP_SecretKey2."&response=".$_POST["recaptchaResponse"]);
              $reCAPTCHA = json_decode($verifyResponse);
              if ( $reCAPTCHA->success )
                define( "recaptcha_done", true );
              else
                echo "<div style='background-color:#404ff0;'><font color='#ff4500'><h1>reCAPTCHAエラーが発生しました。お使いの端末のJavaScriptを有効にして下さい。</h1></font></div>";
            }
            else
              echo "<div style='background-color:#404ff0;'><font color='#ff4500'><h1>reCAPTCHAエラーが発生しました。お使いの端末のJavaScriptを有効にして下さい。</h1></font></div>";

            if ( defined( "recaptcha_done" ) ) {

              if ( !is_string( $_POST["add_title"] ) || strlen( $_POST["add_title"] ) > 120 )
                echo "<div style='background-color:#404ff0;'><font color='#ff4500'><h1>書き込みに失敗しました: タイトルが不正です。</h1></font></div>";
              else if ( !is_string( $_POST["add_data"] ) || strlen( $_POST["add_data"] ) > 1080 )
                echo "<div style='background-color:#404ff0;'><font color='#ff4500'><h1>書き込みに失敗しました: 内容が不正です。</h1></font></div>";
              else
              {
                array_push(
                  $Comments,
                  json_encode(
                    array(
                      'Time' => time(),
                      'Count' => count($Comments) + 1,
                      'CreateUserID' => $LocalUser["UserIntID"],
                      'Title' => htmlspecialchars( $_POST["add_title"] ),
                      'InnerText' => htmlspecialchars( $_POST["add_data"] )
                    )
                  )
                );
                file_put_contents( MetaNote_Home . $row["CommentsJsonfp"], implode("\r\n", $Comments) );
              }

            }

          }

          if ( count( $Comments ) > 100 )
            $Comments = array_slice( $Comments, count( $Comments ) - 1000 );
          foreach( $Comments as $CommentJson ) {
            if ( empty( $CommentJson ) )
              continue;
            $Comment = json_decode( $CommentJson, true );

            ?>
            <div class="comment">
               <span class="titleof"><b><?=$Comment["Title"]?></b></span><br>
               <?=date( "Y/m/d H:i:s", $Comment["Time"] )?>
               <span class="CreateUser"><?=MetaNote_GetNameByID_bySQL( $dbh, $Comment["CreateUserID"] )[0]?></span><br>
               <pre><?=$Comment["InnerText"]?></pre>
            </div>
            <?php
          }
          if ( count( $Comments ) === 0 )
            echo "<p>公開コメントはありません。</p>";

          ?>
          <hr>
          <h2>公開コメントを追加</h2>
          <form action="" enctype="multipart/form-data" method="post">
            <input type="text" class="add_title" name="add_title" maxlength="120" placeholder="ここにタイトルを入力してください(120文字まで)" required>
            <br><br>
            <textarea class="add_data" name="add_data" maxlength="1080" placeholder="ここに内容を入力してください(1080文字まで)" required></textarea>
            <br>
            <input type="hidden" name="recaptchaResponse" id="recaptchaResponse">
            <input type="submit" value="書き込む" class="button2write">
          </form>
        </div>
      </div>
      <br>
      <hr>

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

    <?php
    exit();
  }
  else if ( $row["DateType"] === "Text/LaTeX" )
  {
    $title = htmlspecialchars( $row["ArticleTitle"] );
    $subTitle = htmlspecialchars( $row["ArticleSubtitle"] );

    if ( $row["InPublic"] === "true" )
      $IndexFromBot = "All";
    else
      $IndexFromBot = "noindex, follow";


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
    <link rel="stylesheet" href="/css/view.css">
    <script src="https://cdn.jsdelivr.net/npm/latex.js/dist/latex.js"></script>
    <script>
      window.onload = function () {
        var generator = new latexjs.HtmlGenerator({
          hyphenate: false
        });
        try {
          generator = latexjs.parse(document.getElementById("LaTeXSource").innerText, {
              generator: generator
          });
          document.getElementById("DATA").innerHTML = "";
          document.getElementById("DATA").appendChild(generator.stylesAndScripts("https://cdn.jsdelivr.net/npm/latex.js@0.11.1/dist/"));
          document.getElementById("DATA").appendChild(generator.domFragment());
        } catch (e) {
          if (e.name == "SyntaxError")
            document.getElementById("DATA").replaceWith('<div id="DATA"> <p>' + e.name + '</p><p>line ' + e.location["start"]["line"] + ' (column ' + e.location["start"]["column"] + '): ' + e.message +'</p></div>');
          else
            document.getElementById("DATA").replaceWith('<div id="DATA"> <p>unexpected error: ' + e.message + '</p></div>');
        }
      }
    </script>

  </head>
  <body>
    <?=Get_Body_Header()?>

    <div class="mainobj" align="center" style="font-size:1.4rem;">

      <div class="container marketing">
        <br><br>
        <h1 align="left" class="titlecomes"><?=$title?></h1>
        <p align="left"><?=$subTitle?></p>
        <p align="right">
          著者: <?php
            foreach( json_decode( $row["Writers"], true ) as $Writer )
              echo htmlspecialchars( MetaNote_GetNameByID_bySQL( $dbh, $Writer )[0] ) . ";";
          ?><br>
          作成日: <?=date("Y/m/d H:i:s", $row["CreateTime"] * 1)?><br>
          更新日: <?=date("Y/m/d H:i:s", $row["LastUpdateTime"] * 1)?><br>
        </p>
        <br>
        <hr>

        <div align="left" id="DATA"></div>
        <div id="LaTeXSource" style="display:none;"><?
        if (!file_exists(MetaNote_Home . $row["DataSrc"]))
          MetaNote_Fatal_Die( "ソールファイルが存在しません。" );
        $file = fopen(MetaNote_Home . $row["DataSrc"], "r");
        $alltext = "";
        if ($file) {
          while ($line = @fgets($file))
            if (substr($line, 0, 2) === "# ")
              $alltext .= $line . "<hr>";
            else
              $alltext .= $line;
          if (@is_utf8($alltext))
            echo @htmlspecialchars($alltext);
          else
            echo @htmlspecialchars(@mb_convert_encoding($alltext, 'UTF-8', 'SJIS'));
        }
        else
          MetaNote_Fatal_Die( "ソールファイルが存在しません。" );
        fclose($file);
        ?></div>
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

    <?php
    exit();
  }
  else if ( $row["DateType"] === "application/pdf" )
  {

    if ( isset( $_GET["pdf"] ) )
    {
      refCheck();
      MetaNote_Session_Close();
      $data = gzinflate( file_get_contents( MetaNote_Home . $row["DataSrc"] ) );
      
      header( "Content-Type: application/pdf;" );
      header( "Content-Disposition: inline" );
      header( "Content-Length: " . strlen( $data ) );
      header( "X-Frame-Options: sameorigin" );

      exit($data);
    }

    $title = htmlspecialchars( $row["ArticleTitle"] );
    $subTitle = htmlspecialchars( $row["ArticleSubtitle"] );

    if ( $row["InPublic"] === "true" )
      $IndexFromBot = "All";
    else
      $IndexFromBot = "noindex, follow";

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
    <link rel="stylesheet" href="/css/view.css">

  </head>
  <body>
    <?=Get_Body_Header()?>

    <div class="mainobj" align="center">

      <div class="container marketing">
        <br><br>
        <h1 align="left" class="titlecomes"><?=$title?></h1>
        <p align="left"><?=$subTitle?></p>
        <p align="right">
          著者: <?php
            foreach( json_decode( $row["Writers"], true ) as $Writer )
              echo htmlspecialchars( MetaNote_GetNameByID_bySQL( $dbh, $Writer )[0] ) . ";";
          ?><br>
          作成日: <?=date("Y/m/d H:i:s", $row["CreateTime"] * 1)?><br>
          更新日: <?=date("Y/m/d H:i:s", $row["LastUpdateTime"] * 1)?><br>
        </p>
        <br>
        <hr>

        <embed src="?pdf" type="application/pdf" style="width:100%;height:800px;" sandbox>
      </div>

      <br><br>

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
    <?php
    exit();
  }
  else
    MetaNote_Fatal_Die( "対応していない種類の論文ファイルを開きました。" );
