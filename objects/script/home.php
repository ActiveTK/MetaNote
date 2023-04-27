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
    <link rel="stylesheet" href="/css/home.css">

  </head>
  <body>
    <?=Get_Body_Header($LocalUser["UserName"])?>

    <div class="mainobj" align="center">

      <br><br>
      <h1><?=$title?></h1>
      <p>
        <span class="userName"><?=htmlspecialchars($LocalUser["UserName"])?></span>としてログインしています。
      </p>
      <p>
        <a class="btn btn-outline-primary" href="/setting">ユーザー設定</a>
        <a class="btn btn-outline-primary" href="/logout">ログアウト</a>
      </p>
      <br>
      <hr>

      <a href="/search" class="btn btn--blue btn--border-double">
        <h3 style="color:#212529;">論文を探す</h3>
      </a>
      <br>
      <p>公開されている論文を検索できます。</p>
      <br>
      <a href="/write" class="btn btn--blue btn--border-double">
        <h3 style="color:#212529;">論文を新規作成</h3>
      </a>
      <br>
      <p>論文を新しく執筆できます。</p>
      <br>
      <a href="/ctrl" class="btn btn--blue btn--border-double">
        <h3 style="color:#212529;">管理画面に移動</h3>
      </a>
      <br>
      <p>公開済みの論文を管理できます。</p>
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
