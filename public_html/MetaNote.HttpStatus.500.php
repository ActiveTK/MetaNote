<?php

  /*!
   * MetaNote.Server.ErrorPHP.php
   * (c) 2022 MetaNote.
   * エラーメッセージを表示
   * エラーメッセージの文字列 $ErrorInfo
   */

  // ログを作成
  $LogFile = "/home/activetk/metanote.org/log/MetaNote_Fatal_Die.log";

  $debuginfo = array();

  $debuginfo["Time"] = date("Y/m/d - M (D) H:i:s");
  $debuginfo["Time_Unix"] = microtime(true);

  if ( !isset( $ErrorInfo ) )
    $ErrorInfo = "";

  $debuginfo["Error"] = $ErrorInfo;

  if ( isset( $_SERVER['REMOTE_ADDR'] ) )
    $debuginfo["IP"] = $_SERVER['REMOTE_ADDR'];

  if ( isset( $_SERVER['REQUEST_URI'] ) )
    $debuginfo["PATH"] = $_SERVER['REQUEST_URI'];

  if ( isset( $_GET ) )
    $debuginfo["GET"] = json_encode($_GET);

  if ( isset( $_SESSION ) )
  {
    $debuginfo["SessionID"] = @session_id();
    if ( isset( $_SESSION["logindata"] ) )
      $debuginfo["User"] = $_SESSION["logindata"];
  }

  if ( isset( $_SERVER['HTTP_USER_AGENT'] ) )
    $debuginfo["UserAgent"] = $_SERVER['HTTP_USER_AGENT'];

  $a = fopen($LogFile, "a");
  @fwrite($a, json_encode($debuginfo) . "\n");
  fclose($a);

  try {
    http_response_code( 500 );
    header( "HTTP/1.1 500 Internal Server Error" );
    header( "Content-Type: text/html;charset=UTF-8" );
  } catch (\Exception $e) { }

?>

<html lang="ja" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>HTTP 500 / Internal Server Error</title>
    <meta name="robots" content="noindex, follow">
    <style>a{color:#00ff00;position:relative;display:inline-block;transition:.3s;}a::after{position:absolute;bottom:0;left:50%;content:'';width:0;height:2px;background-color:#31aae2;transition:.3s;transform:translateX(-50%);}a:hover::after{width:100%;}</style>
  </head>
  <body style="background-color:#6495ed;color:#080808;">
    <h1>HTTP 500 / Internal Server Error</h1>
    <hr color="#363636" size="2">
    <h2>
      ご迷惑をお掛けして誠に申し訳ございません。<br>サーバーでのリクエスト処理中に致命的なエラーが発生しました。
    </h2>
    <p><b>詳細情報</b>: <?=htmlspecialchars( $ErrorInfo )?></p>
    <hr color="#363636" size="2">
    <div style="position:fixed;bottom:4px;">
      <font style="background-color:#06f5f3;">(c) MetaNote.</font>
    </div>
  </body>
</html>
