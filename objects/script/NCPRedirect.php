<?php

  /*!
   * NCPRedirect.php
   * (c) 2023 MetaNote.
   * 特定のURLへリダイレクトします。
   * リダイレクト先のURL $url が必要。
   */

  if (!isset($url))
    MetaNote_Fatal_Die( "リダイレクト処理にはURLが必要です。" );

  http_response_code( 308 );
  header( "Location: {$url}" );

?>

<!DOCTYPE html>
<html lang="ja" itemscope="" itemtype="http://schema.org/WebPage" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>HTTP 308 / Permanent Redirect</title>
    <meta name="robots" content="noindex">
    <style>a{color:#00ff00;position:relative;display:inline-block;transition:.3s;}a::after{position:absolute;bottom:0;left:50%;content:'';width:0;height:2px;background-color:#31aae2;transition:.3s;transform:translateX(-50%);}a:hover::after{width:100%;}</style>
  </head>
  <body style="background-color:#e6e6fa;text:#363636;">
    <h1>HTTP 308 / Permanent Redirect</h1>
    <hr color="#363636" size="2">
    <h3>The document has moved <a href="<?=$url?>">here</a>.</h3>
    <hr color="#363636" size="2">
    <div style="position:fixed;bottom:4px;">
      <font style="background-color:#06f5f3;"><?=Copyright?></font>
    </div>
  </body>
</html>
