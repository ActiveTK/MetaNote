<?php

  /*!
   * (c) 2023 MetaNote.
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

  if ( isset( $_GET["delete"] ) && is_string( $_GET["delete"] ) )
  {
    refCheck();

    $ArticleID = $_GET["delete"];

    if (1 === 1){

      try {
        $stmt = $dbh->prepare('select * from MetaNoteArticles where ArticleID = ?');
        $stmt->execute( [$ArticleID] );
        $row = $stmt->fetch( PDO::FETCH_ASSOC );
      } catch ( \Throwable $e ) {
        MetaNote_Fatal_Die( $e->getMessage() );
      }

      if ( !isset( $row["Writers"] ) )
        MetaNote_Fatal_Die( "存在しない論文ファイルを開きました。" );

      $Writers = json_decode( $row["Writers"] );
      $InWriter = false;
      foreach( $Writers as $Writer )
        if ($Writer === $LocalUser["UserIntID"])
        {
          $InWriter = true;
          break;
      }
      if ( !$InWriter )
        MetaNote_Fatal_Die( "編集権限のない論文ファイルを開きました。" );

      try {
        $stmt = $dbh->prepare(
          "delete from MetaNoteArticles where ArticleID = ? limit 1;"
        );
        $stmt->execute( [
          $ArticleID
        ] );
      } catch (\Throwable $e) {
        MetaNote_Fatal_Die( $e->getMessage() );
      }
    }
    NCPRedirect( "/ctrl" );
    exit();
  }

  if ( isset( $_GET["public"] ) && isset( $_GET["id"] ) )
  {
    refCheck();

    $ArticleID = $_GET["id"];

    if (1 === 1){

      try {
        $stmt = $dbh->prepare('select * from MetaNoteArticles where ArticleID = ?');
        $stmt->execute( [$ArticleID] );
        $row = $stmt->fetch( PDO::FETCH_ASSOC );
      } catch ( \Throwable $e ) {
        MetaNote_Fatal_Die( $e->getMessage() );
      }

      if ( !isset( $row["Writers"] ) )
        MetaNote_Fatal_Die( "存在しない論文ファイルを開きました。" );

      $Writers = json_decode( $row["Writers"] );
      $InWriter = false;
      foreach( $Writers as $Writer )
        if ($Writer === $LocalUser["UserIntID"])
        {
          $InWriter = true;
          break;
      }
      if ( !$InWriter )
        MetaNote_Fatal_Die( "編集権限のない論文ファイルを開きました。" );

      if ( $_GET["public"] === "true" )
      {
        try {
          $stmt = $dbh->prepare(
            "update MetaNoteArticles set InPublic = 'true' where ArticleID = ? limit 1;"
          );
          $stmt->execute( [
            $ArticleID
          ] );
        } catch (\Throwable $e) {
          MetaNote_Fatal_Die( $e->getMessage() );
        }
      }
      else if ( $_GET["public"] === "false" )
      {
        try {
          $stmt = $dbh->prepare(
            "update MetaNoteArticles set InPublic = 'false' where ArticleID = ? limit 1;"
          );
          $stmt->execute( [
            $ArticleID
          ] );
        } catch (\Throwable $e) {
          MetaNote_Fatal_Die( $e->getMessage() );
        }
      }
      else
        MetaNote_Fatal_Die( "不明な公開ステータスを設定しました。" );

    }

    NCPRedirect( "/ctrl" );
    exit();
  }

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>

    <title><?=$title?></title>

    <?=MetaNote_Header_Default()?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/ctrl.css">

    <script>
    
      function deleteA(id){if(window.confirm("記事 ["+id+"] を削除します。\nよろしいでしょうか？"))location.href="?delete="+id;}
      function SetPublic(id, status) {
        if (status === true)
        {
          if(window.confirm("記事 ["+id+"] を公開します。\nよろしいでしょうか？"))
            location.href = "?public=true&id="+id;
        }
        else
        {
          if(window.confirm("記事 ["+id+"] を非公開にします。\nよろしいでしょうか？"))
            location.href = "?public=false&id="+id;
        }
      }

    </script>

  </head>
  <body>
    <?=Get_Body_Header($LocalUser["UserName"])?>

    <div class="mainobj" align="center">

      <br><br>
      <h1><?=$title?></h1>
      <p>
        編集権限のある論文を管理できます。
      </p>
      <br>
      <hr>

      <div align="center">
       <table border="1" class="table table-striped" style='width:60%;' align='center'>
         <tr>
           <th></th>
           <th>タイトル</th>
           <th>公開状況</th>
           <th>執筆者一覧</th>
           <th>PV数</th>
           <th>コメント数</th>
           <th>作成日</th>
           <th>最終更新日</th>
           <th><nobr>編集</nobr></th>
           <th><nobr>削除</nobr></th>
          </tr>
          <?php
        
          $i = 0;
          $Notes = array();
          try {
            $Notes = $dbh->query("select * from MetaNoteArticles where Writers like '%" . basename($LocalUser["UserIntID"]) . "%' order by LastUpdateTime desc;");
            if ($Notes !== false) { }
            else MetaNote_Fatal_Die( "SQLエラーが発生しました。" );
          } catch (\Throwable $e) {
            MetaNote_Fatal_Die( $e->getMessage() );
          }
          foreach($Notes as $value)
          {
             $i++;
             if ($value["InPublic"] == "true")
               $IsPublic = "<nobr>公開済み</nobr><nobr>(<a href='javascript:SetPublic(\"" . $value["ArticleID"] . "\", false)'>非公開に変更</a>)</nobr>";
             else if ($value["InPublic"] == "lim")
               $IsPublic = "<nobr>限定公開</nobr><nobr>(<a href='javascript:SetPublic(\"" . $value["ArticleID"] . "\", false)'>非公開に変更</a>)</nobr>";
             else
               $IsPublic = "非公開 (<a href='javascript:SetPublic(\"" . $value["ArticleID"] . "\", true)'>公開する</a>)";

             $WritersLookup = "";
             $Writers = json_decode( $value["Writers"], true );
             foreach($Writers as $Writer)
               $WritersLookup .= htmlspecialchars(MetaNote_GetNameByID_bySQL($dbh, $Writer)[0]) . ";";

             $TitleOfArticle = "NoTitle";
             if (!empty($value["ArticleTitle"]))
               $TitleOfArticle = htmlspecialchars($value["ArticleTitle"]);

             echo "<tr>" .
                  "  <th>#" . $i . "</th>" .
                  "  <th><a href='/article/" . $value["ArticleID"] . "' target='_blank'>" . $TitleOfArticle . "</a></th>" .
                  "  <th>" . $IsPublic . "</th>" .
                  "  <th>" . $WritersLookup . "</th>" .
                  "  <th>" . $value["PVCount"] . "</th>" .
                  "  <th>" . count( file( MetaNote_Home . $value["CommentsJsonfp"], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) ) . "</th>" .
                  "  <th>" . date("Y/m/d H:i:s", $value["CreateTime"] * 1) . "</th>" .
                  "  <th>" . date("Y/m/d H:i:s", $value["LastUpdateTime"] * 1) . "</th>" .
                  "  <th><a href='/edit/" . $value["ArticleID"] . "' target='_blank'>編集</a></th>" .
                  "  <th><a href='javascript:deleteA(\"" . $value["ArticleID"] . "\");'>削除</a></th>" .
                  "</tr>\n";
          }
        
          ?>
        </table>
      </div>

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
