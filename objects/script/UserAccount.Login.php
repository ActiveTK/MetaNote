<?php

  /*!
   * ログイン画面を表示
   * タイトル $title が必要
   */

?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <title><?=$title?></title>

    <?=MetaNote_Header_Default()?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/UserAccount.Login.css">
    <?=MetaNote_ActiveTKMinJs()?>
    <?=MetaNote_SHA512Js()?>
    <script type="text/javascript">
    window.onload = function() {
      _("javascripton").style = "display:block;";
    }
    function CSubmit() {
      if (_("password").value == "")
      {
        alert("パスワードを空にする事はできません！！！！！！！");
        return false;
      }
      _("login").disabled = "true";
      _("login").value = "ログインしています。。";
      var shaObj = new jsSHA("SHA3-512","TEXT",{encoding:"UTF8"});
      shaObj.update(_("password").value);
      _("_trykey").value = CybozuLabs.MD5.calc(shaObj.getHash("HEX").toLowerCase()+"<?=$_SESSION['login_token']?>");
      _("gopassword").remove();
      _("gousername").style = "display:none;";
      _("username").value = btoa(_("username").value);
      return true;
    }
    </script>
  </head>
  <body>
    <?=Get_Body_Header();?>
    <div class="mainobj">
      <br>
      <div align="center" class="fortitle">
        <h1><?=$title?></h1>
      </div>
      <br>
      <p align="center">アカウントをお持ちではありませんか？<a href="/new<?php if (isset($_GET["return"])) echo "?return=" . urlencode($_GET["return"]); ?>">新規作成</a>してください。</p>
      <br>
      <?php if ( isset( $_GET["error"] ) ) { ?>
        <?php if ( $_GET["error"] == "ban" ) { ?>
          <div class="errortext" align="center"><h2>アカウントは凍結されています</h2></div>
        <?php } else { ?>
          <div class="errortext" align="center"><h2>メールアドレス/ユーザーID又はパスワードが違います</h2></div>
        <?php } ?>
      <?php } else { ?>
        <br>
      <?php } ?>
      <div align="center" id="javascripton" style="display:none;">
        <form action='' method='POST' onsubmit='return CSubmit();' class="formof">
          <br>
          <p id="gousername"><b>メールアドレス:</b> <input type="text" name="_username" id="username" value="" placeholder="メール又はユーザーID(半角英数)" required></p>
          <p id="gopassword"><b>パスワード:</b> <input type="password" id="password" name="password" value="" placeholder="パスワード" required></p>
          <input type="submit" style="height:60px;width:140px;" id="login" value="ログイン" title="ログイン">
          <input type="hidden" name="_login_trykey" id="_trykey" value="" style="display:none;">
          <input type="hidden" name="_return_back_address" value="<?php if (isset($_GET["return"])) echo htmlspecialchars($_GET["return"]); ?>" style="display:none;">
          <br><br>
        </form>
        <br>
        <p title="本サイトではチャレンジレスポンス認証が利用されています。">
          <img style="height:14px;width:14px;" src="data:image/png;base64,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">
          TLSによる暗号化/チャレンジレスポンス認証
        </p>
      </div>

      <noscript>
        <div title="NO SCRIPT ERROR" class="p-noscript">
          <h1>JavaScriptが無効です</h1>
          <p>MetaNoteを利用するには、お使いのブラウザのJavaScriptを有効化する必要があります。</p>
        </div>
      </noscript>

      <?=MetaNote_View_Option()?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="/js/navbar.js"></script>
  </body>
</html>