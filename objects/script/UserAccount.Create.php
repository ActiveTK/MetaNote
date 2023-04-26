<?php

  /*!
   * ユーザーアカウント作成機能を提供します。
   * タイトル $title が必要です。
   */

  if ( isset( $_GET["s"] ) && $_GET["s"] == "2" )
  {

    if ( !isset( $_POST["licenseread"] ) )
    {
      NCPRedirect( "/new?error=3" );
      exit();
    }
    
    mb_regex_encoding("UTF-8");

    if ( !isset( $_POST["_NoSpamHash"] ) || $_POST["_NoSpamHash"] != hash( 'sha3-512', $_POST["_call"] . "_" . $_POST["_mailaddress"] . $_SESSION['account_create_token'] ) )
    {
      NCPRedirect( "/new?error=6" );
      exit();
    }

    $_SESSION["account_create_token"] = "_" . MetaNote_GetRand(256);

    if ( !isset( $_POST["_call"] ) || empty($_POST["_call"]) ||
         !preg_match( "/^[ぁ-んァ-ヶーa-zA-Z0-9一-龠０-９、。,. \n\r]+$/u" , $_POST["_call"] ) )
    {
      NCPRedirect( "/new?error=4" );
      exit();
    }
    $UserName = htmlspecialchars($_POST["_call"]);

    if ( !isset( $_POST["_mailaddress"] ) || empty($_POST["_mailaddress"]) ||
         !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $_POST["_mailaddress"] ) )
    {
      NCPRedirect( "/new?error=1" );
      exit();
    }

    $MailAddress = strtolower($_POST["_mailaddress"]);
    try {
      $stmt = $dbh->prepare('select UserName from MetaNoteUsers where Mailadd = ?');
      $stmt->execute( [$MailAddress] );
      $row = $stmt->fetch( PDO::FETCH_ASSOC );
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }
    if ( isset( $row["UserName"] ) && !empty( $row["UserName"] ) )
    {
      NCPRedirect( "/new?error=2" );
      exit();
    }

    if ( !isset( $_POST["_password"] ) || empty( $_POST["_password"] ) || strlen( $_POST["_password"] ) < 8 || $_POST["_password"] == "qwertyui" )
    {
      NCPRedirect( "/new?error=5" );
      exit();
    }
    $password = hash( 'sha3-512', $_POST["_password"] );

    try {

      $pdo = new PDO ( DSN, DB_USER, DB_PASS );

      $NowUser = MetaNote_UserCount() + 1;
      $UserIntID = str_pad( $NowUser, 11, 0, STR_PAD_LEFT );

      $emptystr = "";
      $zero = "0";

      $CreateIPadd = $_SERVER["REMOTE_ADDR"];
      $CreateTime = time();

      $DisplayID = dechex($NowUser); 

      $stmt = $pdo->prepare(
        "insert into MetaNoteUsers(
           UserIntID, UserName, password, baninfo, CreateIPAdd, CreateTime, LastLoginIPadd, LastLoginUA, LastLoginTime, AccessCount, ChatCount, MailAdd, DisplayID, Profile, ImageIconSrc
         )
         value(
           ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
         )"
      );
      $stmt->execute( [
        $UserIntID,
        $UserName,
        $password,
        $emptystr,
        $CreateIPadd,
        $CreateTime,
        $emptystr,
        $emptystr,
        $emptystr,
        $zero,
        $zero,
        $MailAddress,
        $DisplayID,
        $emptystr,
        $emptystr
      ] );

      $stmt = $dbh->prepare('select * from MetaNoteUsers where UserIntID = ? limit 1;');
      $stmt->execute( [$UserIntID] );
      $row = $stmt->fetch( PDO::FETCH_ASSOC );
      $_SESSION["logindata"] = json_encode( $row );

    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

    ?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title><?=$title?></title>
    <meta name="robots" content="All">
    <?=MetaNote_Header_Default()?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/style.css">
  </head>
  <body style="background-color:#6495ed;color:#080808;">
    <?=Get_Body_Header();?>
    <div class="mainobj">
      <div align="center" style="background-color:#e6e6fa;text:#363636;width:60%;margin-left:auto;margin-right:auto;">
        <h1>アカウント作成完了</h1>
      </div>
      <div align="center">
        <p>アカウントの作成が完了しました。</p>
        <p><a href="/home">ホーム</a>へ移動してください。</p>
      </div>
      <?=MetaNote_View_Option()?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="/js/navbar.js"></script>
  </body>
</html>
    <?php

    exit();

  }

  $_SESSION["account_create_token"] = "_" . MetaNote_GetRand(256);

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title><?=$title?></title>
    <meta name="robots" content="All">
    <?=MetaNote_Header_Default()?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/UserAccount.Create.css">
    <?=MetaNote_SHA512Js()?>
    <script>
      window.onload = function() {
        document.getElementById("v").innerText = "(非表示)";
        document.getElementById("v").onclick = function() {
          let v = document.getElementById("v");
          if (v.innerText == "(非表示)") {
            v.innerText = "(表示)";
            document.getElementById("_password").type = "password";
          }
          else {
            v.innerText = "(非表示)";
            document.getElementById("_password").type = "text";
          }
        }
        document.getElementById("subm").onsubmit = function() {
          document.getElementById("_password").type = "password";
          document.getElementById("_call").readonly = true;
          document.getElementById("_password").readonly = true;
          document.getElementById("_mailaddress").readonly = true;
          document.getElementById("_createbutton").readonly = true;
          try {
            var shaObj = new jsSHA("SHA3-512","TEXT",{encoding:"UTF8"});
            shaObj.update(document.getElementById("_call").value + "_" + document.getElementById("_mailaddress").value + "<?=$_SESSION['account_create_token']?>");
            document.getElementById("_NoSpamHash").value = shaObj.getHash("HEX").toLowerCase();
          } catch (e) { alert(e); return false;  }
          return true;
        }
      }
    </script>
  </head>
  <body style="">
    <?=Get_Body_Header();?>
    <div class="mainobj">
      <div align="center" class="fortitle">
        <h1><?=$title?></h1>
      </div>
      <?php if ( isset( $_GET["error"] ) ) { ?><div class="errortext" align="center"><h2><?php
        if ( $_GET["error"] == "1" )
          echo "メールアドレスの入力形式が不正です。";
        else if ( $_GET["error"] == "2" )
          echo "指定されたメールアドレスは既に使用されています。";
        else if ( $_GET["error"] == "3" )
          echo "アカウントを作成するには、利用規約に全て同意する必要があります。";
        else if ( $_GET["error"] == "4" )
          echo "指定されたユーザー名は無効です。";
        else if ( $_GET["error"] == "5" )
          echo "指定されたパスワードは脆弱なため、無効です。";
        else
          echo "技術的なエラーが発生しました。";
      ?></h2></div><?php } else { ?><?php } ?>
      <div align="center">
        <br>
        <p title="1分でアカウントを作成できます！">
          ニックネームとメールアドレス、パスワードのみでアカウントを作成できます。
        </p>
        <p>既にアカウントをお持ちですか？<a href="/login">ログイン</a>してください。</p>
        <form action='/new?s=2' method='POST' id='subm' class="formof">
          <br>
          <p id="gousername"><b>ニックネーム:</b> <input type="text" name="_call" value="" id="_call" placeholder="山田太郎" required>(必須)</p>
          <p id="gomailadd"><b>メールアドレス:</b> <input type="email" name="_mailaddress" value="" placeholder="yamada@example.com" id="_mailaddress" required>(必須)</p>
          <p id="gopassword"><b>パスワード:</b> <input type="text" name="_password" id="_password" value="<?=MetaNote_GetRand(8)?>" pattern="^[a-zA-Z0-9!-/:-@\[-`{-~]*$" placeholder="Password" required><a href="javascript:void(0);"><span id="v"></span></a>(必須)</p>
          <pre>※パスワードは必ず8桁以上にしてください。</pre>
          <br>
          <input type="checkbox" name="licenseread" id="license" value="true" class="LicenseAgree" required>
          <label for="license"><b>私は、MetaNoteの <a href="/license">利用規約</a> に全て同意します。</b></label>(必須)
          <br><br>
          <input type="text" name="_NoSpamHash" id="_NoSpamHash" class="Nodisplay" value="">
          <input type="submit" class="CreateAcBtn" id="_createbutton" value="アカウントを新規作成" title="クリックするとアカウントを新規作成します。">
          <br><br>
        </form>
        <br>
      </div>
      <?=MetaNote_View_Option()?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="/js/navbar.js"></script>
  </body>
</html>
