<?php

  /*!
   * プライバシーポリシーの表示
   * タイトル $title が必要です。
   */

?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <title><?=$title?></title>
    <?=MetaNote_Header_Default()?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/privacy_and_license.css">
  </head>
  <body>
    <?=Get_Body_Header();?>
    <div class="mainobj">
      <br>
      <div align="center" class="fortitle">
        <h1><?=$title?></h1>
      </div>
      <pre align="right">制定 2023年05月03日</pre>
      <div align="center">
        <hr size="1" color="#7fffd4">
        <div align="left" class="ldata">

          <h3>$1. 本サービスで収集する情報</h3>

          <p><b>$1.a 本サービスの提供に伴ってサーバーへアップロードされる情報</b></p>
          本サービスの提供に伴い、ユーザーが入力したテキストや選択したファイルをサーバーへアップロードして、処理を行う事がございます。<br>
          本サービスを通じてお客様がサーバーにアップロードされた記事データやpdfファイルなどは、<a href="https://metanote.org/ctrl" target="_blank">管理画面</a>からお客様の意思に基づいて削除することができます。<br><br>

          <p><b>$1.b Cookieの使用</b></p>
          本サービスは、アカウント制御機能の実装に伴って、必要最低限の範囲でブラウザのCookieを使用します。<br><br>

          <p><b>$1.c Google社のサービス「Google アナリティクス」</b></p>
          本サイトでは、サービスの運営状況の確認や改善を目的に、Google社の提供する、Google アナリティクスを利用しています。<br>
          Google アナリティクスは、アクセス情報の収集のためにCookieを使用しています。<br>
          また、この情報は匿名で収集されており、個人を特定するものではありません。<br>
          詳しい内容については、Google アナリティクスの<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" target="_blank">利用規約</a>並びに<a href="https://policies.google.com/privacy?hl=ja" target="_blank">プライバシーポリシー</a>をご覧ください。<br><br>

          <p><b>$1.d アクセス数カウンター</b></p>
          本サービスでは、アクセス数のカウントを目的としてWebページへのアクセス回数の情報をサーバーに記録しています。<br>
          ただし、これは個人の特定に利用されるものではありません。<br>

          <h3>$2 情報の削除</h3>

          <p><b>$2.e 情報の削除要求</b></p>
          お客様は開発者に対して、サーバーからアクセスログを省いた自らがアップロードした全てのデータの削除を申し立てる事ができるものとします。<br>

          <h3>$3. ユーザーの意思に基づくデータの利用</h3>

          <p><b>$3.f 報告データの利用</b></p>
          ユーザーが、バグの報告やエラーページの報告を行った場合には、当該データをサービスの改善に使用させていただきます。<br>
          なお、バグやエラーの特定を円滑に進めるために、報告を行ったユーザーのIPアドレス、ユーザーエージェント情報及びアクセスしたURLなどのデータを収集する場合がございます。<br><br>

          <p><b>$3.g お問い合わせ</b></p>
          本サイトの「お問い合わせページ」でご記入頂いた情報は、サービスの改善に使用させていただきます。<br>
          また、入力されたメールアドレスが本件お問い合わせ以外に使用される事は絶対にございません。

        </div>
        <hr size="1" color="#7fffd4">
        <?=MetaNote_View_Option()?>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="/js/navbar.js"></script>
  </body>
</html>