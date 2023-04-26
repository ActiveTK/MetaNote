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

      <div id="aboutinfo" class="textalignleft">

        <div class="container marketing">
          <br>

          <hr class="featurette-divider">

          <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center" id="about">
            <h1 class="display-4">本サービスについて</h1>
          </div>

          <div class="row featurette">
            <div class="col-md-7">
              <h2 class="featurette-heading"><span class="text-center"><span class="text-muted">数少ない</span>日本向けの論文投稿サイト</span></h2>
              <p class="lead">本サイトは、「ちょっとしたアイディアを気軽に共有できる」という理念の下に開発されている、数少ない日本語の論文投稿サイトです。
              「査読不要・審査なし」を原則とし、誰でも簡単にアイディアを投稿できます。</p>
            </div>
            <div class="col-md-5">
              <img class="featurette-image img-fluid mx-auto" src="/img/home.png">
            </div>
          </div>
      
          <hr class="featurette-divider">
      
          <div class="row featurette">
            <div class="col-md-7 order-md-2">
              <h2 class="featurette-heading">充実した機能</h2>
              <p class="lead">論文の共同編集機能や寄付機能を実装しています。<br>また、論文の公開コメントを通じて気軽に執筆者とコンタクトできます。<br>
              </p>
            </div>
            <div class="col-md-5 order-md-1">
              <img class="featurette-image img-fluid mx-auto" src="/img/home.png">
            </div>
          </div>
     
          <hr class="featurette-divider">
      
          <div class="row featurette">
            <div class="col-md-7">
              <h2 class="featurette-heading fw-normal lh-1"></h2>
              <p class="lead">ご相談や占いを通して頂いた情報は、厳重に管理し、第三者に一切開示いたしません。また、本サイトへの通信は全てSSL/TLS技術で暗号化されています。</p>
            </div>
            <div class="col-md-5">
              <img class="featurette-image img-fluid mx-auto" src="/img/home.png" >
            </div>
          </div>
      
          <hr class="featurette-divider">
        </div>

      </div>

      <?=MetaNote_View_Option()?>
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
