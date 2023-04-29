<?php

  /*!
   * (c) 2023 MetaNote.
   */

  if ( !defined( 'php.config.req' ) )
  {
    if ( session_status() == PHP_SESSION_NONE )
      @session_write_close();
    http_response_code( 500 );
    header( "HTTP/1.1 500 Internal Server Error" );
    header( "Content-Type: text/html;charset=UTF-8" );
    include( "/home/activetk/metanote.org/public_html/MetaNote.HttpStatus.500.php" );
    die();
  }

  if ( isset( $_GET["new"] ) && is_string( $_GET["new"] ) )
  {
    $Typeof = $_GET["new"];
    if ( $Typeof == "markdown" )
    {
      $ArticleID = "MetaNote-" . dechex( time() ) . MetaNote_GetRand( 8 );

      try {

        mkdir( MetaNote_Home . "objects/articles/markdown/{$ArticleID}" );
        chmod( MetaNote_Home . "objects/articles/markdown/{$ArticleID}", 0777 );

        touch( MetaNote_Home. "objects/articles/markdown/{$ArticleID}/Data" );
        touch( MetaNote_Home. "objects/articles/markdown/{$ArticleID}/Comments" );

        $stmt = $dbh->prepare(
          "insert into MetaNoteArticles(
              ArticleID, ArticleTitle, ArticleSubtitle, InPublic, Writers, LikedCount, DonateWayOrBTC, CreateIPAddress, CreateTime, LastUpdateTime, DateType, DataSrc, PVCount, CommentsJsonfp
           )
           value(
             ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           )"
        );
        $stmt->execute( [
          $ArticleID,
          "",
          "",
          "false",
          json_encode( array( $LocalUser["UserIntID"] ) ),
          "0",
          "",
          $_SERVER["REMOTE_ADDR"],
          time(),
          time(),
          "Text/MarkDown",
          "objects/articles/markdown/{$ArticleID}/Data",
          "0",
          "objects/articles/markdown/{$ArticleID}/Comments"
        ] );
      } catch (\Throwable $e) {
        MetaNote_Fatal_Die( $e->getMessage() );
      }

      NCPRedirect( "/edit/" . $ArticleID );
      exit();
    }
    else if ( $Typeof == "latex" )
    {
      $ArticleID = "MetaNote-" . dechex( time() ) . MetaNote_GetRand( 8 );

      try {

        mkdir( MetaNote_Home . "objects/articles/latex/{$ArticleID}" );
        chmod( MetaNote_Home . "objects/articles/latex/{$ArticleID}", 0777 );

        touch( MetaNote_Home. "objects/articles/latex/{$ArticleID}/Data" );
        touch( MetaNote_Home. "objects/articles/latex/{$ArticleID}/Comments" );

        $stmt = $dbh->prepare(
          "insert into MetaNoteArticles(
              ArticleID, ArticleTitle, ArticleSubtitle, InPublic, Writers, LikedCount, DonateWayOrBTC, CreateIPAddress, CreateTime, LastUpdateTime, DateType, DataSrc, PVCount, CommentsJsonfp
           )
           value(
             ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           )"
        );
        $stmt->execute( [
          $ArticleID,
          "",
          "",
          "false",
          json_encode( array( $LocalUser["UserIntID"] ) ),
          "0",
          "",
          $_SERVER["REMOTE_ADDR"],
          time(),
          time(),
          "Text/LaTeX",
          "objects/articles/latex/{$ArticleID}/Data",
          "0",
          "objects/articles/latex/{$ArticleID}/Comments"
        ] );
      } catch (\Throwable $e) {
        MetaNote_Fatal_Die( $e->getMessage() );
      }

      NCPRedirect( "/edit/" . $ArticleID );
      exit();
    }
    else if ( $Typeof == "upload-pdf" )
    {

      if ( isset( $_FILES["file"] ) )
      {
        if ( !isset( $_FILES['file']['error'] ) || !is_int( $_FILES['file']['error'] ) )
          MetaNote_Fatal_Die( "パラメータが不正です。" );

        switch ( $_FILES['file']['error'] ) {
          case UPLOAD_ERR_OK:
            break;
          case UPLOAD_ERR_NO_FILE:
            MetaNote_Fatal_Die( "ファイルが選択されていません。" );
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            MetaNote_Fatal_Die( "ファイルサイズが大きすぎます。" );
          default:
            MetaNote_Fatal_Die( "パラメータが不正です。" );
        }

        if ( $_FILES['file']['size'] > 1024 * 1024 * 200 )
          MetaNote_Fatal_Die( "ファイルサイズが大きすぎます。" );

        $ArticleID = "MetaNote-" . dechex( time() ) . MetaNote_GetRand( 8 );

        try {

          mkdir( MetaNote_Home . "objects/articles/latex/{$ArticleID}" );
          chmod( MetaNote_Home . "objects/articles/latex/{$ArticleID}", 0777 );

          touch( MetaNote_Home. "objects/articles/latex/{$ArticleID}/Data" );
          touch( MetaNote_Home. "objects/articles/latex/{$ArticleID}/Comments" );

          $stmt = $dbh->prepare(
            "insert into MetaNoteArticles(
                ArticleID, ArticleTitle, ArticleSubtitle, InPublic, Writers, LikedCount, DonateWayOrBTC, CreateIPAddress, CreateTime, LastUpdateTime, DateType, DataSrc, PVCount, CommentsJsonfp
             )
             value(
               ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
             )"
          );
          $stmt->execute( [
            $ArticleID,
            basename( $_FILES['file']['name'] ),
            "",
            "false",
            json_encode( array( $LocalUser["UserIntID"] ) ),
            "0",
            "",
            $_SERVER["REMOTE_ADDR"],
            time(),
            time(),
            "application/pdf",
            "objects/articles/latex/{$ArticleID}/Data",
            "0",
            "objects/articles/latex/{$ArticleID}/Comments"
          ] );
        } catch (\Throwable $e) {
          MetaNote_Fatal_Die( $e->getMessage() );
        }

        if ( !move_uploaded_file(
          $_FILES['file']['tmp_name'], MetaNote_Home . "objects/articles/latex/{$ArticleID}/DataFull"
        ) )
          MetaNote_Fatal_Die( "ファイル保存時にエラーが発生しました" );

        file_put_contents( MetaNote_Home . "objects/articles/latex/{$ArticleID}/Data", gzdeflate( file_get_contents( MetaNote_Home . "objects/articles/latex/{$ArticleID}/DataFull" ) ) );
        unlink( MetaNote_Home . "objects/articles/latex/{$ArticleID}/DataFull" );

        NCPRedirect( "/edit/" . $ArticleID );
        exit();
      }

      $title = "PDFファイルをアップロード - MetaNote.";
      ?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>

    <title><?=$title?></title>

    <?=MetaNote_Header_Default()?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/write.css">

    <script>
      function sendForm() {
        if (document.getElementById("file").value === "")
          alert("ファイルを選択して下さい。");
        else
          document.getElementById("select").submit();
        return false;
      }
    </script>

  </head>
  <body>
    <?=Get_Body_Header($LocalUser["UserName"])?>

    <div class="mainobj" align="center">

      <br><br>
      <h1><?=$title?></h1>
      <p>
        アップロードしたいPDFファイルを選択して下さい。<br>
        ファイルは自動的に非公開設定となります(管理画面から公開できます)。
      </p>
      <br>
      <hr>

      <form align="center" action="" enctype="multipart/form-data" method="POST" id="select">
        <div>
          <input type="hidden" name="MAX_FILE_SIZE" value="214748364">
          <input name="file" type="file" title="最大ファイルサイズは200MBです。" accept="application/pdf" id="file" required>
          <br><br>
          <p>200MB以下でJavaScriptが含まれていないPDFファイルが選択できます。</p>
          <br>
          <a href="javascript:sendForm();" class="btn2">
            <h3 style="color:#212529;">アップロード</h3>
          </a>
        </div>
      </form>

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
    else
      MetaNote_Fatal_Die( "不正な種類の論文を新規作成しようとしました。" );
  }

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>

    <title><?=$title?></title>

    <?=MetaNote_Header_Default()?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/write.css">

  </head>
  <body>
    <?=Get_Body_Header($LocalUser["UserName"])?>

    <div class="mainobj" align="center">

      <br><br>
      <h1><?=$title?></h1>
      <p>
        作成したい論文の形式を選択して下さい。
      </p>
      <br>
      <hr>

      <div align="center">
        <div>
          <a href="/write?new=markdown" class="btn2">
            <h3 style="color:#212529;">MarkDown</h3>
          </a>
          <br>
          <p>MarkDownの構文を使用して論文を執筆できます。</p>
        </div>

        <div>
          <a href="/write?new=latex" class="btn2">
            <h3 style="color:#212529;">LaTeX</h3>
          </a>
          <br>
          <p>LaTeXの構文を使用して論文を執筆できます。</p>
        </div>

        <div>
          <a href="/write?new=upload-pdf" class="btn2">
            <h3 style="color:#212529;">作成済みのPDFを公開</h3>
          </a>
          <br>
          <p>PDF形式の論文ドキュメントを公開できます。</p>
        </div>
      </div>

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
