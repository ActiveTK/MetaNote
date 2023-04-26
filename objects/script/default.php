<?php

  /*!
   * MetaNote.Server.Default.php
   * (c) 2023 MetaNote.
   * デフォルト画面を表示
   * タイトル $title が必要
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

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>

    <title><?=$title?></title>

    <?=MetaNote_Header_Default()?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/default.css">

    <script src="https://unpkg.com/typewriter-effect@2.18.2/dist/core.js"></script>
    <script>
    
      window.addEventListener('DOMContentLoaded', () => {

        new Typewriter(document.getElementById("per"), {
          loop: true,
          delay: 75,
          autoStart: true,
          cursor: '|',
          strings: ['']
        });

      });
    
    </script>

  </head>
  <body>
    <?=Get_Body_Header()?>

    <div class="firstPage">

      <div class="welcomemsg" align="center">
        <div class="p-title">
          <p class="c-subtitle u-margin_zero"><?=_MetaNote_SubTitle?></p>
          <br>
          <p class="c-title u-margin_zero">MetaNote.<span id="per"></span></p>
        </div>

        <noscript>
          <div title="NO SCRIPT ERROR" class="p-noscript">
            <h1>JavaScriptが無効です</h1>
            <p>MetaNoteを利用するには、お使いのブラウザのJavaScriptを有効化する必要があります。</p>
          </div>
        </noscript>

        <div class="container p-main_container">
          <div class="row p-img_wrapper"><img class="card p-img_card" src="/icon/home.png" class="p-main_image"></div>
          <div class="row">
            <div class="col p-buttons_container d-flex justify-content-center">
              <a href="/login<?php if (isset($_GET["return"])) echo "?return=" . htmlspecialchars($_GET["return"]); ?>" class="btn btn-lg p-loginbutton">ログイン</a>
              <a href="/new" class="btn btn-lg p-newbutton">新規登録</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div align="center" class="next" id="next">

      <hr class="featurette-divider container marketing">
      <br>

      <div id="about" class="textalignleft">

        <div class="container marketing">
          <br>

          <hr class="featurette-divider">

          <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center" id="about">
            <h1 class="display-4">本サービスの<span style="color:#f06770;">3つの特徴</span></h1>
          </div>

          <div class="row featurette">
            <div class="col-md-7">
              <h2 class="featurette-heading"><span class="text-center"><span class="text-muted">特徴1.</span> 数少ない<span class="inpblue">日本向けの論文投稿サイト</span></span></h2>
              <p class="lead">本サイトは、「ちょっとしたアイディアを気軽に共有できる」という理念の下に開発されている、数少ない日本語の論文投稿サイトです。
              「査読不要・審査なし」を原則とし、誰でも簡単にアイディアを投稿できます。</p>
            </div>
          </div>
      
          <hr class="featurette-divider">

          <div class="row featurette alignright">
            <div class="order-md-2" align="right">
              <h2 class="featurette-heading"><span class="text-muted">特徴2.</span> <span class="inpblue">充実した機能</span></h2>
              <p class="lead">論文の共同編集機能や寄付機能を実装しています。
              また、論文の公開コメントを通じて気軽に執筆者とコンタクトしたり、討論やアイディアの提案を行えます。</p>
            </div>
          </div>
     
          <hr class="featurette-divider">
      
          <div class="row featurette">
            <div class="col-md-7">
              <h2 class="featurette-heading"><span class="text-muted">特徴3.</span> <span class="inpblue">著作権の保護</span></h2>
              <p class="lead">希望する場合には、コイン一枚分の金額でデータのメタデータをブロックチェーンに記録できます。
              ブロックチェーンに書き込まれたデータの改竄はほぼ不可能であり、半永久的に著作の記録が残ります。</p>
            </div>
          </div>
      
          <hr class="featurette-divider">
        </div>

        <div class="container marketing" style="background-color:#e6e6fa;width:100%;">
  
          <hr class="featurette-divider">
    
          <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center" id="userscomment">
            <h1 class="display-4"><b>お問い合わせ</b></h1>
            <p class="lead">管理者へのお問い合わせは、以下のフォームからお願い致します。</p>
          </div>
    
          <div class="row">
    
            <form action='' method='POST' id="contact" align="center">
              お名前又はニックネーム: <input type='text' name='contact_name' style="height:20px;width:200px;" placeholder='お名前' required>(必須)<br>
              ご連絡先のメールアドレス: <input type='email' name='contact_mail' style="height:20px;width:200px;" placeholder='メールアドレス' pattern=".+\.[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]" required>(必須)<br><br>
              【お問い合わせ内容】<br>
              <textarea name="contact_data" placeholder="内容をこちらへ入力してください"  style="height:200px;width:740px;"></textarea><br>
              <pre>※本フォーム内に、個人情報を入力しないでください。</pre>
              <br>
              <label><input type="checkbox" name="license_readme" value="ok" style="height:20px;width:20px;" required> 私は、本サイトの<a href="/license" target="_blank">利用規約</a>を読み、理解しました。(必須)</label><br><br>
              <br>
              <input type="submit" style="height:60px;width:140px;" value="送信">
              <br>
              報告された情報は<a href="/privacy" target="_blank">プライバシーに関する声明</a>に従って処理されます。<br>
              また、入力されたメールアドレスが本件お問い合わせ以外に使用される事は絶対にありません。
            </form>
    
          </div>

          <br>

          <hr class="featurette-divider">
  
        </div>

      </div>

      <div class="container marketing">
        <footer class="pt-4 my-md-5 pt-md-5 border-top">
          <div class="row">
            <div class="col-12 col-md">
              <small class="d-block mb-3 text-muted"><?=Copyright?></small>
            </div>
            <div class="col-6 col-md">
              <h5>サイトマップ</h5>
              <ul class="list-unstyled text-small">
                <li><a class="text-muted" href="#about">サイト概要</a></li>
                <li><a class="text-muted" href="https://github.com/ActiveTK/MetaNote">Githubリポジトリ</a></li>
                <li><a class="text-muted" href="#contact">お問い合わせ</a></li>
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

    <svg class="arrows" id="arrows" onclick='window.scrollTo({top:document.getElementById("next").getBoundingClientRect().top-120,behavior:"smooth"});document.getElementById("arrows").style.display="none";'>
      <path class="a1" d="M0 0 L30 32 L60 0"></path>
      <path class="a2" d="M0 20 L30 52 L60 20"></path>
      <path class="a3" d="M0 40 L30 72 L60 40"></path>
    </svg>

    <script src="/js/navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  </body>
</html>
