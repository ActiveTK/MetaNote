<?php

  /*!
   * @namespace MetaNote / index.php
   * (c) 2023 ActiveTK.
   */

  require_once( "/home/activetk/require/Config.php" );
  require_once( "/home/activetk/metanote.org/objects/script/MainLib.php" );

  define( 'Domain', 'metanote.org' );
  define( 'MetaNote_Home', '/home/activetk/metanote.org/' );
  define( 'Copyright', '(c) 2023 MetaNote.' );
  define( '_MetaNote_SubTitle', '査読不要・審査なしの日本向け論文投稿サイト。' );

  // ヘッダー処理
  if ( empty( $_SERVER['HTTPS'] ) && !isset( $_GET["no-ssl"] ) ) {
    header( "Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" );
    die();
  }
  else if ( !isset( $_GET["no-ssl"] ) )
    header( "Strict-Transport-Security: max-age=63072000; includeSubdomains; preload" );

  header( "X-Frame-Options: deny" );
  header( "X-XSS-Protection: 1; mode=block" );
  header( "X-Content-Type-Options: nosniff" );
  header( "X-Permitted-Cross-Domain-Policies: none" );
  header( "Referrer-Policy: same-origin" );

  // URI処理
  if ( isset( $_GET["_MetaNote_URI"] ) ) {
    if ( substr( $_GET["_MetaNote_URI"] , -1) == '/' )
      define( '_MetaNote_URI', rtrim( $_GET["_MetaNote_URI"], '/' ) );
    else
      define( '_MetaNote_URI', $_GET["_MetaNote_URI"] );
  }
  else
    define( '_MetaNote_URI', '' );

  define( '_MetaNote_URI_LOW' , strtolower( _MetaNote_URI ) );

  // 設定読み込み確認
  if ( !defined( 'php.config.req' ) )
    MetaNote_Fatal_Die( "php.config.req が定義されていません。" );

  error_reporting( E_ALL );
  date_default_timezone_set( 'Asia/Tokyo' );
  ini_set( 'display_startup_errors', 0 );

  if ( !empty( _MetaNote_URI ) ) {

    if ( _MetaNote_URI_LOW == "favicon.ico" )
    {
      AllowCache();
      readfile( '../objects/icon/MetaNote.ico' );
      exit();
    }

    if ( substr( _MetaNote_URI_LOW, 0, 5 ) == "icon/" && file_exists( "../objects/icon/" . basename( _MetaNote_URI_LOW ) ) ) {
      $iconpath = pathinfo( '../objects/icon/' . basename( _MetaNote_URI_LOW ) );
      if ( !isset( $iconpath['extension'] ) )
        MetaNote_Fatal_Die( "/iconオブジェクトに対し拡張子無しのファイルがリクエストされました。" );
      else if ( $iconpath['extension'] == "ico" )
        header( 'Content-Type: image/vnd.microsoft.icon' );
      else if ( $iconpath['extension'] == "jpg" || $iconpath['extension'] == "jpeg" )
        header( 'Content-Type: image/jpeg' );
      else if ( $iconpath['extension'] == "png" )
        header( 'Content-Type: image/png' );
      else
        MetaNote_Fatal_Die( "/iconオブジェクトに対し無効な拡張子のファイルがリクエストされました。" );
      AllowCache();
      readfile( '../objects/icon/' . basename( _MetaNote_URI_LOW ) );
      exit();
    }

    if ( _MetaNote_URI_LOW == "sitemap.xml" )
    {
      header("Content-Type: application/xml;charset=UTF-8");
      echo "<" . "?"; ?>xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

<url>
  <loc>https://metanote.org</loc>
  <lastmod>2023-05-01T06:31:55+00:00</lastmod>
  <priority>1</priority>
</url>

    <?php
      $Notes = array();
      try {
        $Notes = (new PDO(DSN, DB_USER, DB_PASS))->query('select ArticleID, LastUpdateTime from MetaNoteArticles where InPublic = "true" order by LastUpdateTime desc limit 2000;');
        if ($Notes !== false) { }
        else die("SQLの実行中にエラーが発生しました。");
      } catch (\Throwable $e) { die("SQLエラーが発生しました。"); }

      foreach($Notes as $value)
      {
    ?>
<url>
  <loc>https://metanote.org/article/<?=$value["ArticleID"]?></loc>
  <lastmod><?=date('c', $value["LastUpdateTime"])?></lastmod>
  <priority>0.8</priority>
</url>
    <?php } ?>
  </urlset><?php
      exit();
    }

  }

  ini_set( 'session.gc_divisor'    , 1            );
  ini_set( 'session.gc_maxlifetime', 7776000      );
  session_start();

  $dbh = new PDO( DSN, DB_USER, DB_PASS );

  // ログイン処理
  if (
    isset( $_POST["_username"] ) && isset( $_POST["_login_trykey"] ) &&
    isset( $_SESSION["login_token"] ) && !empty( $_SESSION["login_token"] )
  ) {
    $Try_User = @base64_decode( $_POST["_username"] );
    $Try_Key = $_POST["_login_trykey"];
    $Try_Token = $_SESSION["login_token"];

    try {
      $stmt = $dbh->prepare('select * from MetaNoteUsers where Mailadd = ? or UserIntID = ? or DisplayID = ? limit 1;');
      $stmt->execute( [$Try_User, $Try_User, $Try_User] );
      $row = $stmt->fetch( PDO::FETCH_ASSOC );
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }
    $_SESSION["login_token"] = "";
    unset( $_SESSION["login_token"] );

    if ( isset( $row["password"] ) && md5( $row["password"] . $Try_Token ) == $Try_Key )
    {
      if ( !empty( $row["baninfo"] ) )
      {
        NCPRedirect( "/login?error=ban" );
        exit();
      }
      $_SESSION["logindata"] = json_encode( $row );

      try {
        $UA = "";
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) )
          $UA = $_SERVER['HTTP_USER_AGENT'];
        $stmt2 = $dbh->prepare(
          "update MetaNoteUsers set LastLoginIPadd = ?, LastLoginUA = ?, LastLoginTime = ? where UserIntID = ?;"
        );
        $stmt2->execute( [
          $_SERVER["REMOTE_ADDR"],
          $UA,
          time(),
          $row["UserIntID"]
        ] );
      } catch (\Throwable $e) { }

      if ( isset( $_POST["_return_back_address"] ) && !empty( $_POST["_return_back_address"] ) )
        header( "Location: /" . $_POST["_return_back_address"] );
      else
        header( "Location: /home" );
      exit();
    }
    else
    {
      NCPRedirect( "/login?error&return=" . urlencode( $_POST["_return_back_address"] ) );
      exit();
    }

  }

  // ログアウト処理
  else if ( _MetaNote_URI_LOW == "logout" )
  {
    if ( !isset( $_SERVER['HTTP_REFERER'] ) )
      MetaNote_Fatal_Die( "Referrer不一致" );
    $ref = @parse_url( $_SERVER['HTTP_REFERER'] );
    if ( !isset( $ref["host"] ) || $ref["host"] != "metanote.org" )
      MetaNote_Fatal_Die( "Referrer不一致" );

    $_SESSION = array();
    session_destroy();
    if ( isset( $_COOKIE["SeedID"] ) )
      setcookie( "SeedID", '', time() - 1800, '/' );
    NCPRedirect( "/" );
    exit();
  }

  // ログインページ
  else if (
    _MetaNote_URI_LOW == "login" &&
    ( !isset( $_SESSION["logindata"] ) || empty( $_SESSION["logindata"] ) )
  )
  {
    $_SESSION["login_token"] = "_" . MetaNote_GetRand(64);
    $title = "ログイン - MetaNote.";
    include( MetaNote_Home . "objects/script/UserAccount.Login.php" );
    exit();
  }

  // アカウント作成ページ
  else if (
    _MetaNote_URI_LOW == "new" &&
    ( !isset( $_SESSION["logindata"] ) || empty( $_SESSION["logindata"] ) )
  )
  {
    $title = "アカウント作成 - MetaNote.";
    include( MetaNote_Home . "objects/script/UserAccount.Create.php" );
    exit();
  }

  else if ( _MetaNote_URI_LOW == "license" )
  {
    $title = "利用規約 - MetaNote.";
    include( MetaNote_Home . "objects/script/Document.License.php" );
    exit();
  }

  else if ( _MetaNote_URI_LOW == "tos" )
  {
    header("HTTP/1.1 301 Redirect");
    header("Location: /license");
    exit();
  }

  else if ( _MetaNote_URI_LOW == "privacy" )
  {
    $title = "プライバシーポリシー - MetaNote.";
    include( MetaNote_Home . "objects/script/Document.Privacy.php" );
    exit();
  }

  else if ( _MetaNote_URI_LOW == "data" ) {

    if (
        !isset( $_GET["hash"] ) || !isset( $_GET["user"] ) || !isset( $_GET["uniqcode"] ) || !isset( $_GET["type"] ) ||
        !is_alnum( $_GET["hash"] ) || !is_alnum( $_GET["user"] ) || !is_alnum( $_GET["uniqcode"] )
      )
    {
      include(MetaNote_Home . "public_html/MetaNote.HttpStatus.404.php");
      exit();
    }

    $FilePath = MetaNote_Home . "objects/users-data/" . basename( $_GET["user"] ) . "/" . basename( $_GET["hash"] ) . "_" . basename( $_GET["uniqcode"] );
    if ( !file_exists( $FilePath ) ) {
      include(MetaNote_Home . "public_html/MetaNote.HttpStatus.404.php");
      exit();
    }
    $pathinfo = @pathinfo( basename( $_GET["type"] ) );
    if (!isset($pathinfo['extension']) || empty($pathinfo['extension']) || $pathinfo['extension'] == "bat" || $pathinfo['extension'] == "url" || $pathinfo['extension'] == "txt")
      header("Content-Type: text/plain");
    else if ($pathinfo['extension'] == "html")
      header("Content-Type: text/plain");
    else if ($pathinfo['extension'] == "csv")
      header("Content-Type: text/csv");
    else if ($pathinfo['extension'] == "js")
      header("Content-Type: text/javascript");
    else if ($pathinfo['extension'] == "css")
      header("Content-Type: text/css");
    else if ($pathinfo['extension'] == "json")
      header("Content-Type: application/json");
    else if ($pathinfo['extension'] == "pdf")
      header("Content-Type: application/pdf");
    else if ($pathinfo['extension'] == "exe" || $pathinfo['extension'] == "out" || $pathinfo['extension'] == "bin")
      header("Content-Type: application/octet-stream;");
    else if ($pathinfo['extension'] == "zip" || $pathinfo['extension'] == "7z")
      header("Content-Type: application/zip");
    else if ($pathinfo['extension'] == "jpg" || $pathinfo['extension'] == "jpeg")
      header("Content-Type: image/jpeg");
    else if ($pathinfo['extension'] == "png")
      header("Content-Type: image/png");
    else if ($pathinfo['extension'] == "ico")
      header("Content-Type: image/vnd.microsoft.icon;");
    else if ($pathinfo['extension'] == "gif")
      header("Content-Type: image/gif");
    else if ($pathinfo['extension'] == "bmp")
      header("Content-Type: image/bmp");
    else if ($pathinfo['extension'] == "gif")
      header("Content-Type: image/gif");
    else if ($pathinfo['extension'] == "gzip" || $pathinfo['extension'] == "tar")
      header("Content-Type: application/x-tar;");
    else if ($pathinfo['extension'] == "lzh")
      header("Content-Type: application/x-lzh;");
    else if ($pathinfo['extension'] == "mp3")
      header("Content-Type: audio/mp3");
    else if ($pathinfo['extension'] == "3gp")
      header("Content-Type: video/3gpp");
    else if ($pathinfo['extension'] == "mp4")
      header("Content-Type: video/mp4");
    else if ($pathinfo['extension'] == "avi")
      header("Content-Type: video/x-msvideo");
    else if ($pathinfo['extension'] == "mov")
      header("Content-Type: video/quicktime");
    else if ($pathinfo['extension'] == "wmv")
      header("Content-Type: video/x-ms-wmv");
    else if ($pathinfo['extension'] == "mpg" || $pathinfo['extension'] == "mpeg")
      header("Content-Type: video/mpeg");
    else if ($pathinfo['extension'] == "iso" || $pathinfo['extension'] == "img" || pathinfo['extension'] == "ico")
      header("Content-Type: application/octet-stream;");
    else
      header("Content-Type: text/plain;");
    header('Content-Length: ' . filesize( $FilePath ));
    header('Content-Disposition: inline; filename="' . basename( $_GET["type"] ) . '"');
    header('Connection: close');
    while (ob_get_level()) { ob_end_clean(); }
    readfile( $FilePath );
    exit();

  }

  else if ( _MetaNote_URI_LOW == "400" )
  {
    require_once( "./MetaNote.HttpStatus.400.php" );
    die();
  }
  else if ( _MetaNote_URI_LOW == "403" )
  {
    require_once( "./MetaNote.HttpStatus.403.php" );
    die();
  }
  else if ( _MetaNote_URI_LOW == "404" )
  {
    require_once( "./MetaNote.HttpStatus.404.php" );
    die();
  }
  else if ( _MetaNote_URI_LOW == "500" )
  {
    MetaNote_Fatal_Die( "[DEBUG]_MetaNote_Fatal_Die が実行されました。" );
    die();
  }
  else if ( _MetaNote_URI_LOW == "search" )
  {
    include(MetaNote_Home . "objects/script/search.php");
    exit();
  }
  else if ( explode('/', _MetaNote_URI_LOW)[0] == "article" )
  {
    if ( !isset( explode('/', _MetaNote_URI_LOW)[1] ) )
      MetaNote_Fatal_Die( "論文ファイルを指定せずに閲覧しようとしました。" );

    define( 'ArticleID', explode('/', _MetaNote_URI_LOW)[1] );
    if ( !isset( $_SESSION["logindata"] ) || empty( $_SESSION["logindata"] ) )
      $LocalUser = array();
    else
      $LocalUser = json_decode( $_SESSION["logindata"], true );

    include(MetaNote_Home . "objects/script/view.php");
    exit();
  }
  else if ( !isset( $_SESSION["logindata"] ) || empty( $_SESSION["logindata"] ) )
  {
    if ( empty( _MetaNote_URI_LOW ) )
    {
      $title = "MetaNote. - " . _MetaNote_SubTitle;
      include(MetaNote_Home . "objects/script/default.php");
      exit();
    }
    else
    {
      NCPRedirect( "/login?return=" . urlencode( _MetaNote_URI ) );
      exit();
    }
  }

  $LocalUser = json_decode( $_SESSION["logindata"], true );
  
  /*!
   *
   * ここでログイン処理完了
   *
   */

  if ( _MetaNote_URI_LOW == "home" ) {

    $title = "ホーム - MetaNote.";
    include(MetaNote_Home . "objects/script/home.php");
    exit();

  } else if ( _MetaNote_URI_LOW == "write" ) {

    $title = "論文を新規作成 - MetaNote.";
    include(MetaNote_Home . "objects/script/write.php");
    exit();

  } else if ( empty( _MetaNote_URI_LOW ) ) {
      
    define( 'USER_LOGGEDIN', true );
    $title = "MetaNote. - " . _MetaNote_SubTitle;
    include(MetaNote_Home . "objects/script/default.php");
    exit();
    
  } else if ( _MetaNote_URI_LOW == "ctrl" ) {

    $title = "論文の管理 - MetaNote.";
    include(MetaNote_Home . "objects/script/ctrl.php");
    exit();

  } else if ( explode('/', _MetaNote_URI_LOW)[0] == "edit" ) {

    $title = "論文を編集 - MetaNote.";

    if ( !isset( explode('/', _MetaNote_URI_LOW)[1] ) )
      MetaNote_Fatal_Die( "論文ファイルを指定せずに編集を行おうとしました。" );

    define( 'ArticleID', explode('/', _MetaNote_URI_LOW)[1] );
    include(MetaNote_Home . "objects/script/edit.php");
    exit();

  } else if ( _MetaNote_URI_LOW == "setting" ) {

    $title = "アカウント設定 - MetaNote.";

    include(MetaNote_Home . "objects/script/setting.php");
    exit();

  } else if ( _MetaNote_URI_LOW ) {

    try {
      if (!is_alnum((string)_MetaNote_URI_LOW))
        include(MetaNote_Home . "public_html/MetaNote.HttpStatus.404.php");
      else
      {
        try {
          $Try_User = _MetaNote_URI_LOW;
          $stmt = $dbh->prepare('select * from MetaNoteUsers where Mailadd = ? or UserIntID = ? or DisplayID = ? limit 1;');
          $stmt->execute( [$Try_User, $Try_User, $Try_User] );
          $row = $stmt->fetch( PDO::FETCH_ASSOC );
        } catch ( \Throwable $e ) {
          MetaNote_Fatal_Die( $e->getMessage() );
        }
        if (!isset($row["UserIntID"]) || empty($row["UserIntID"]))
          include(MetaNote_Home . "public_html/MetaNote.HttpStatus.404.php");
        else
        {
          $UnLocalUser = $row;
          $title = $UnLocalUser["UserName"] . " - MetaNote.";
          exit($title);
        }
      }
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }
    exit();

  } else {
      
    include(MetaNote_Home . "public_html/MetaNote.HttpStatus.404.php");
    exit();

  }

