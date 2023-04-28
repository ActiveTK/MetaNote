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

?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>

    <title><?=$title?></title>

    <?=MetaNote_Header_Default()?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/ctrl.css">

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
        <p>論文一覧</p>
       <br>
       <table border="1" class="table table-striped" style='width:60%;' align='center'>
         <tr>
           <th></th>
           <th>タイトル</th>
           <th>ステータス</th>
           <th>執筆者一覧</th>
           <th>PV数</th>
           <th>いいね数</th>
           <th>作成日</th>
           <th>最終更新日</th>
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
               $IsPublic = "公開済み";
             else
               $IsPublic = "非公開";

             $WritersLookup = "";
             $Writers = json_decode( $value["Writers"], true );
             foreach($Writers as $Writer)
               $WritersLookup .= htmlspecialchars(MetaNote_GetNameByID_bySQL($dbh, $Writer)[0]) . ";";

             echo "<tr>" .
                  "  <th>#" . $i . "</th>" .
                  "  <th><a href='/edit/" . $value["ArticleID"] . "' target='_blank'>" . htmlspecialchars($value["ArticleTitle"]) . "</a></th>" .
                  "  <th>" . $IsPublic . "</th>" .
                  "  <th>" . $WritersLookup . "</th>" .
                  "  <th>" . $value["PVCount"] . "</th>" .
                  "  <th>" . $value["LikedCount"] . "</th>" .
                  "  <th>" . date("Y/m/d - M (D) H:i:s", $value["CreateTime"] * 1) . "</th>" .
                  "  <th>" . date("Y/m/d - M (D) H:i:s", $value["LastUpdateTime"] * 1) . "</th>" .
                  "</tr>";
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
