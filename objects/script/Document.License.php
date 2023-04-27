<?php

  /*!
   * 利用規約の表示
   * タイトル $title が必要です。
   */

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
    <link rel="stylesheet" href="/css/privacy_and_license.css">
  </head>
  <body>
    <?=Get_Body_Header();?>
    <div class="mainobj">
      <br>
      <div align="center" class="fortitle">
        <h1><?=$title?></h1>
      </div>
      <pre align="right">制定 2023年mm月dd日</pre>
      <div align="center">
        <hr size="1" color="#7fffd4">
        <div align="left" class="ldata">

	      <h3>第一章</h3>

          <p><b>第一条 (未定)</b></p>
          内容
          <br>

          <br>
          日本時間 2023年mm月dd日 制定
          </div>
        </div>
        <hr size="1" color="#7fffd4">
        <?=MetaNote_View_Option()?>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="/js/navbar.js"></script>
  </body>
</html>