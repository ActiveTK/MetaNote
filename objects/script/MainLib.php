<?php

  /*!
   * @namespace utilities.php(MetaNote.Server)
   * (c) 2022 MetaNote.
   * 便利な処理をまとめたファイル
   */

  /**
   * 文字列をHTML専用の文字列に変換する関数
   * 
   * e.g.
   * & -> &amp;
   * < -> &lt;
   * 
   * @param string $text 変換前の文字列
   * @return string 変換後の文字列
   */
  function MetaNote_Plain2HtmlCode ( string $text = "" ) : string { 
    return htmlspecialchars( $text );
  }

  /**
   * 文字列が英数字(alphanumeric)か判定する関数
   * 
   * @param string $text 判定する文字列
   * @return bool 英数字かどうか。trueで英数字。falseでそうでない。
   */
  function is_alnum(string $text) : bool {
    if ( preg_match( "/^[a-zA-Z0-9]+$/", $text ) )
      return true;
    else
      return false;
  } 

  /**
   * エラーを無視したセッションのunset関数
   * 
   * @return bool エラーが投げられた場合にはfalse。それ以外はtrueです。
   */
  function MetaNote_Session_Close() : bool {
    try {
      if ( session_status() == PHP_SESSION_NONE )
        @session_write_close();
      return true;
    } catch ( \Throwable $e ) {
      return false;
    }
  }

  /**
   * 簡易的なFatalエラー表示関数
   */
  function MetaNote_Fatal_Die( string $ErrorInfo = "" ) {

    MetaNote_Session_Close();

    http_response_code( 500 );
    header( "HTTP/1.1 500 Internal Server Error" );
    header( "Content-Type: text/html;charset=UTF-8" );

    include(MetaNote_Home . "public_html/MetaNote.HttpStatus.500.php");

    die();

  }

  /**
   * ユーザーをリダイレクトさせる関数
   * 
   * @param string $url リダイレクト先のURLです。
   */
  function NCPRedirect( $url ) {

    include(MetaNote_Home . "objects/script/NCPRedirect.php");

    exit();

  }

  /**
   * ActiveTK.min.jsを描写する関数
   * 
   * @param string $nonce HTML上のnonceです。
   */
  function MetaNote_ActiveTKMinJs( string $nonce = "" ) {
    if ( $nonce == "" )
      return "<script type=\"text/javascript\" src=\"/js/ActiveTK.min.js\"></script>";
    else
      return "<script type=\"text/javascript\" src=\"/js/ActiveTK.min.js\" nonce=\"{$nonce}\"></script>";
  }

  /**
   * SHA512.jsを描写する関数
   * 
   * @param string $nonce HTML上のnonceです。
   */
  function MetaNote_SHA512Js( string $nonce = "" ) {
    if ( $nonce == "" )
      return "<script type=\"text/javascript\" src=\"/js/sha512.js\"></script>";
    else
      return "<script type=\"text/javascript\" src=\"/js/sha512.js\" nonce=\"{$nonce}\"></script>";
  }

  /**
   * 出力データのキャッシュを許可する関数
   */
  function AllowCache() {
    header( 'Last-Modified: Fri Jan 01 2010 00:00:00 GMT' );
    header( 'Expires: ' . gmdate( 'D, d M Y H:i:s T', time() + 604800 ) );
    header( 'Cache-Control: private, max-age=604800' );
    header( 'Pragma: ' );
  }

  /**
   * 暗号学的に安全とされる乱数を発生させる関数
   * 
   * @param int $len 乱数のバイト数です。
   * @return string 生成された乱数
   */
  function MetaNote_GetRand( int $len = 32 ) : string {

    $bytes = openssl_random_pseudo_bytes( $len / 2 );
    $str = bin2hex( $bytes );

    $usestr = '1234567890abcdefghijklmnopqrstuvwxyz';
    $str2 = substr( str_shuffle( $usestr ), 0, 12 );

    return substr( str_shuffle( $str . $str2 ) , 0, -12 );

  }

  /**
   * フッターを表示する関数
   */
  function MetaNote_View_Option() {

    ?>
    <div class="p-lastinfo" align="center">
      <p>
        <a href="/" style="color:#00ff00 !important;">ホーム</a>・
        <a href="/about" style="color:#0403f9 !important;">サービス概要</a>・
        <a href="/license" style="color:#ffa500 !important;">利用規約</a>・
        <a href="/privacy" style="color:#ff00ff !important;">プライバシー</a> 
        <?=Copyright?>
      </p>
    </div>
    <?php
  
  }

  /**
   * ユーザー数を計測する関数
   * @return int 現在のユーザー数
   */
  function MetaNote_UserCount() : int {

    try {
      $pdo = new PDO( DSN, DB_USER, DB_PASS );
      $st = $pdo->query('select count(*) from MetaNoteUsers');
      $usercount = $st->fetchColumn();
      return ((int)$usercount * 1);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * ユーザー数を計測する関数(SQL情報あり)
   * @parm PDO $pdo データベース接続 
   * @return int 現在のユーザー数
   */
  function MetaNote_UserCount_bySQL($pdo) : int {

    try {
      $st = $pdo->query('select count(*) from MetaNoteUsers');
      $usercount = $st->fetchColumn();
      return ((int)$usercount * 1);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * ユーザーIDからユーザー名とディスプレイIDを取得する関数
   * @param string[] $ID ユーザーのユニークIDです。
   * @return array ユーザー名(存在しない場合には「削除済みのユーザー」)とディスプレイIDの配列。
   */
  function MetaNote_GetNameByID($ID) : array {

    try {
      if (!is_alnum((string)$ID))
        return array();
      $akm = "";
      $akz = "";
      $dbh = new PDO(DSN, DB_USER, DB_PASS);
      $res = $dbh->query("select * from MetaNoteUsers where UserIntID = '" . $ID . "' limit 2;");
	  foreach($res as $value) {
	    $akm = $value["UserName"];
        $akz = $value["DisplayID"];
	  }
      if (empty($akm))
        $akm = "削除済みのユーザー";
      if (empty($akz))
        $akz = "DELETED";
      return array($akm, $akz);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * ユーザーIDからユーザー名とディスプレイIDを取得する関数
   * @oarm PDO $dbh データベースデータベース情報
   * @param string[] $ID ユーザーのユニークIDです。
   * @return array ユーザー名(存在しない場合には「削除済みのユーザー」)とディスプレイIDの配列。
   */
  function MetaNote_GetNameByID_bySQL($dbh, $ID) : array {

    try {
      if (!is_alnum((string)$ID))
        MetaNote_Fatal_Die( "セキュリティエラーが発生しました。" );
      $akm = "";
      $akz = "";
      $res = $dbh->query("select * from MetaNoteUsers where UserIntID = '" . $ID . "' limit 2;");
	  foreach($res as $value) {
	    $akm = $value["UserName"];
        $akz = $value["DisplayID"];
	  }
      if (empty($akm))
        $akm = "削除済みのユーザー";
      if (empty($akz))
        $akz = "DELETED";
      return array($akm, $akz, $ID);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * MetaNoteのデフォルトヘッダーを表示します。
   *
   * @param string $IndexFromBot (任意)クローラーからのインデックスを許可又は拒否します。[All] 又は [noindex] を指定してください。規定値は [All] です。
   * @param string $Charset (任意)文字コードを指定してください。規定値は [utf-8] です。
   */
  function MetaNote_Header_Default( string $IndexFromBot = "All", string $Charset = "utf-8" ) {

    try {
?><meta charset="<?=$Charset?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="robots" content="<?=$IndexFromBot?>">
    <meta name="keywords" content="MetaNote">
    <meta name="favicon" content="/favicon.ico">
    <meta name="description" content="MetaNoteでは無料で他のユーザーとチャットができます。">
    <meta name="copyright" content="<?=Copyright?>">
    <meta name="thumbnail" content="https://<?=Domain?>/icon/index.jpg">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="MetaNoteでは無料で他のユーザーとチャットができます。">
    <meta name="twitter:image:src" content="https://<?=Domain?>/icon/index.jpg">
    <meta name="twitter:image:alt" content="MetaNote.">
    <meta name="twitter:domain" content="<?=Domain?>">

    <meta property="og:description" content="MetaNoteでは無料で他のユーザーとチャットができます。">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:image" content="https://<?=Domain?>/icon/index.jpg">
    <meta property="og:image:width" content="1280">
    <meta property="og:image:height" content="720">

    <link rel="canonical" href="https://<?=Domain?>/">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_16_16.ico" sizes="16x16">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_32_32.ico" sizes="32x32">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_64_64.ico" sizes="64x64">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_192_192.ico" sizes="192x192">
    <link rel="apple-touch-icon-precomposed" href="https://<?=Domain?>/icon/index_150_150.ico">
<?php

    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * MetaNoteのボディー内のデフォルトヘッダーを表示します。
   */
  function Get_Body_Header() {

    include("./component/header.php");

  }

  /**
   * MetaNoteのボディー最後のアイコンを表示します。
   */
  function MetaNote_View_Icons() {
      try {
?>
      <div align="center">
        <a href="/home" rel="noopener noreferrer">
          <img src="https://<?=Domain?>/icon/home.png" style="width:73px;height:73px;" title="ホーム">
        </a>
        <a href="/setting" rel="noopener noreferrer">
          <img src="https://<?=Domain?>/icon/setting.png" style="width:73px;height:73px;" title="設定">
        </a>
        <a href="/logout" rel="noopener noreferrer">
          <img src="https://<?=Domain?>/icon/alllogout.png" style="width:73px;height:73px;" title="ログアウト">
        </a>
      </div>
<?php

    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }
  }

  /**
   * MetaNoteの人気記事を表示します。
   */
  function MetaNote_List_DESC($dbh) {
      ob_start();
    ?>

      <div align="center" style="display:flex;-webkit-justify-content:center;justify-content:center;-webkit-align-items:center;align-items: center;">

        <div id="slide" class="carousel slide container marketing" data-bs-ride="carousel" style="width:500px;">
          <h3>【人気急上昇】</h3>
          <ol class="carousel-indicators">
            <li data-bs-target="#slide" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#slide" data-bs-slide-to="1"></li>
            <li data-bs-target="#slide" data-bs-slide-to="2"></li>
            <li data-bs-target="#slide" data-bs-slide-to="3"></li>
            <li data-bs-target="#slide" data-bs-slide-to="4"></li>
          </ol>
          <div class="carousel-inner">
          <?php
              $i = 0;
              $Notes = array();
              $AlReadyShowed = array();
              try {
                $Notes = $dbh->query('select * from noteblog where not decinfo = "" order by pvcount desc limit 5;');
                if ($Notes !== false) { }
                else die("SQLの実行中にエラーが発生しました。");
              } catch (\Throwable $e) { die("SQLエラーが発生しました。"); }
              foreach($Notes as $value)
              {
                $i++;
          ?>
            <div class="col-lg-4 carousel-item<?php if ($i == 1) echo " active"; ?>" style="border:1px solid; height:<?=com("300", "500")?>px;">
              <h3><?=$value["pagetitle"]?></h3>
              <p><?=$value["writer"]?></p>
              <b><?=$value["decinfo"]?></b>
              <br><br>
              <p><a class="btn btn-secondary" href="https://note.activetk.jp/<?=$value["httppath"]?>" role="button">続きを見る</a></p>
            </div><?php
              }
          ?>
          </div>
          <button type="button" class="carousel-control-prev" data-bs-target="#slide" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">前へ</span>
          </button>
          <button type="button" class="carousel-control-next" data-bs-target="#slide" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">次へ</span>
          </button>
        </div>

        <div id="slide2" class="carousel slide container marketing" data-bs-ride="carousel" style="width:500px;">
          <h3>【最新の記事】</h3>
          <ol class="carousel-indicators">
            <li data-bs-target="#slide2" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="1"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="2"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="3"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="4"></li>
          </ol>
          <div class="carousel-inner">
          <?php
              $i = 0;
              $Notes = array();
              try {
                $Notes = $dbh->query('select * from noteblog where not decinfo = "" order by lastwritetime desc limit 5;');
                if ($Notes !== false) { }
                else die("SQLの実行中にエラーが発生しました。");
              } catch (\Throwable $e) { die("SQLエラーが発生しました。"); }
              foreach($Notes as $value)
              {
                $i++;
          ?>
            <div class="col-lg-4 carousel-item<?php if ($i == 1) echo " active"; ?>" style="border:1px solid; height:<?=com("300", "500")?>px;">
              <h3><?=$value["pagetitle"]?></h3>
              <p><?=$value["writer"]?></p>
              <b><?=$value["decinfo"]?></b>
              <br><br>
              <p><a class="btn btn-secondary" href="https://note.activetk.jp/<?=$value["httppath"]?>" role="button">続きを見る</a></p>
            </div><?php
              }
          ?>
          </div>
          <button type="button" class="carousel-control-prev" data-bs-target="#slide2" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">前へ</span>
          </button>
          <button type="button" class="carousel-control-next" data-bs-target="#slide2" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">次へ</span>
          </button>
        </div>
      </div>

     <?php
       $buff = ob_get_contents();
       ob_end_clean();
       return $buff;
  }
