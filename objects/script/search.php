<?php

  /*!
   * 論文を検索します。
   */


  $title = "論文を検索";
  $subTitle = "公開されている論文を探すことができます。";

  $IndexFromBot = "All";

  if ( isset( $_POST["search"] ) )
  {
    header( "Content-Type: text/html;charset=UTF-8" );

    if ( empty( $_POST["search"] ) || !is_string( $_POST["search"] ) || strlen( $_POST["search"] ) > 800)
    {
    ?>
      <p>検索結果はありません。</p>
      <p>別のキーワードをお試し下さい。</p>
      <hr size="10" color="#7fffd4">
    <?php
      exit();
    }

    $Words = explode( " ", $_POST["search"] );
    $WordsArr = array();

    $limit = " limit 10";
    if ( isset( $_POST["limit"] ) && $_POST["limit"] == "unlimited" )
      $limit = " limit 1000";

    $QueryTitle = "select * from MetaNoteArticles where ";
    if ( isset( $_POST["type"] ) && $_POST["type"] == "and" )
    {
      foreach($Words as $word)
      {
        $QueryTitle .= "ArticleTitle like ? and ";
        $WordsArr[] = $word;
      }
      $QueryTitle .= "'1' = '1' ";
    }
    else
    {
      foreach($Words as $word)
      {
        $QueryTitle .= "ArticleTitle like ? or ";
        $WordsArr[] = $word;
      }
      $QueryTitle .= "'1' = '0' ";
    }
    $QueryTitle .= " and InPublic = 'true'";
    $QueryTitle .= " order by PVCount desc";
    $QueryTitle .= $limit;

    $QueryDesc = "select * from MetaNoteArticles where ";
    if ( isset( $_POST["type"] ) && $_POST["type"] == "and" )
    {
      foreach($Words as $word)
      {
        $Query .= "ArticleSubtitle like ? and ";
        $WordsArr[] = $word;
      }
      $QueryDesc .= "'1' = '1' ";
    }
    else
    {
      foreach($Words as $word)
      {
        $QueryDesc .= "ArticleSubtitle like ? or ";
        $WordsArr[] = $word;
      }
      $QueryDesc .= "'1' = '0' ";
    }
    $QueryDesc .= " and InPublic = 'true'";
    $QueryDesc .= " order by PVCount desc";
    $QueryTitle .= $limit;

    $Query = "(" . $QueryTitle . ") UNION (" . $QueryDesc . ")";

    exit($Query);
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
    <link rel="stylesheet" href="/css/search.css">
    <script src="https://code.activetk.jp/ActiveTK.min.js" type="text/javascript" charset="UTF-8"></script>
    <script src="https://unpkg.com/typewriter-effect@2.18.2/dist/core.js"></script>
    <script>
      window.onload = function() {
        new Typewriter(_("per1"), {
          loop: true,
          delay: 75,
          autoStart: true,
          cursor: '|',
          strings: ['']
        });
        new Typewriter(_("per2"), {
          loop: true,
          delay: 75,
          autoStart: true,
          cursor: '|',
          strings: ['']
        });
        _("td").onsubmit = function() {
          _("status").style.display = "block";
          _("result").style.display = "none";
          _("status_complete").style.display = "none";
          _("status_loading").style.display = "block";

          try {
            let fdata = new FormData();
            fdata.append("search", _("save").value);
            if (_("andsearch").checked)
              fdata.append("type", "and");
            else
              fdata.append("type", "or");
            if (_("unlimitsearch").checked)
              fdata.append("limit", "default");
            else
              fdata.append("limit", "unlimited");
            $.ajax({
              url: "",
              type: "POST",
              data: fdata,
              cache: !1,
              contentType: !1,
              processData: !1
            })
            .done(function (t) {
              _("result").innerHTML = t;
              _("result").style.display = "block";
              _("status_complete").style.display = "block";
              _("status_loading").style.display = "none";
            })
            .fail(function (t, e, o) {
               $("#status").text("通信に失敗しました。詳細:" + o);
            });
          } catch(e) { 
            console.log(e);
          }
          return false;
        }

      }
    </script>

  </head>
  <body>
    <?=Get_Body_Header()?>

    <div class="mainobj" align="center">

      <div class="container marketing">
        <br><br>
        <h1 align="left" class="titlecomes"><?=$title?></h1>
        <form action="" method="POST" id="td">
          <hr size="10" color="#7fffd4">
          <h2>$ Search <input type="text" id="save" style="height:40px;width:200px;" placeholder="検索ワード" style="font-size:2rem;" maxlength="800"></h2>
          <p><span title="チェックすると検索結果の精度が上がりますが、曖昧な検索をしたい場合にはチェックを外して下さい。"><input type="checkbox" id="andsearch" checked> AND検索 </span>
             <span title="チェックを外すと全ての検索結果を表示します。"><input type="checkbox" id="unlimitsearch" checked> 最初の20件のみを表示 </span></p>
          <input type="submit" value="検索" class="btn btn--yellow btn--cubic">
          <div id="status" style="display:none;">
            <p id="status_loading">Status: Loading..<span id="per1"></span></p>
            <p id="status_complete">Status: Complete!<span id="per2"></span></p>
          </div>
          <div id="options" style="display:none;">

            <br>
            <hr size="10" color="#7fffd4">

            AND検索: 
            <div class="switchArea">
              <input type="checkbox" id="switch1" checked>
              <label for="switch1"><span></span></label>
              <div id="swImg"></div>
            </div><br>

          </div>
          <hr size="10" color="#7fffd4">
        </form>
        <div id="result" style="display:none;"></div>
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