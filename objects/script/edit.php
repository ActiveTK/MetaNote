<?php

  /*!
   * エディタです。
   */

  if ( !defined( 'ArticleID' ) )
    MetaNote_Fatal_Die( "論文ファイルを指定せずに編集を行おうとしました。" );

  try {
    $stmt = $dbh->prepare('select * from MetaNoteArticles where ArticleID = ?');
    $stmt->execute( [ArticleID] );
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

  if ( isset( $_POST["save"] ) &&  isset( $_POST["title"] ) ) {

    refCheck();

    file_put_contents( MetaNote_Home . $row["DataSrc"], $_POST["save"] );

    try {
      $stmt = $dbh->prepare(
        "update MetaNoteArticles set LastUpdateTime = ?, ArticleTitle = ? where ArticleID = ?;"
      );
      $stmt->execute( [
          time(),
          $_POST["title"],
          ArticleID
      ] );
    } catch (\Throwable $e) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

    exit();
  }

  if ( isset( $_POST["confTitle"] ) && isset( $_POST["subTitle"] ) ) {

    refCheck();
    try {
      $stmt = $dbh->prepare(
        "update MetaNoteArticles set ArticleTitle = ?, ArticleSubtitle = ? where ArticleID = ?;"
      );
      $stmt->execute( [
          $_POST["confTitle"],
          $_POST["subTitle"],
          ArticleID
      ] );
    } catch (\Throwable $e) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

    exit();
  }

  function is_utf8($str)
  {
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
      $c = ord($str[$i]);
      if ($c > 128) {
        if (($c > 247))
          return false;
        else if ($c > 239)
          $bytes = 4;
        else if ($c > 223)
          $bytes = 3;
        else if ($c > 191)
          $bytes = 2;
        else
          return false;
        if (($i + $bytes) > $len)
          return false;
        while ($bytes > 1) {
          $i++;
          $b = ord($str[$i]);
          if ($b < 128 || $b > 191)
            return false;
          $bytes--;
        }
      }
    }
    return true;
  }

  if ( $row["DateType"] === "Text/MarkDown" )
  {

?>
<html>
  <head>
    <title>MarkDown Editor - MetaNote.</title>
    <script type="text/javascript" src="/js/ActiveTK.min.js" charset="UTF-8"></script>
    <script src="https://cdn.jsdelivr.net/gh/markedjs/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.2/dist/purify.min.js"></script>
    <script type="text/javascript">
    
      var olddata="", maenodata="", starttitle="MarkDown Editor - MetaNote.";

      function save() {
        _("info").innerHTML="保存しています。。",
        $.ajax({
          url: "",
          type: "post",
          data: {
            save: _("naka").value,
            title: _("title").value
          },
          success: function(t) {
            "" == t ? (_("info").innerHTML="変更を保存しました。",$("title").html(starttitle)):_("info").innerHTML="変更を保存できませんでした。"}
        })
        .fail(function(t,a,e){
          _("info").innerHTML="エラー:変更を保存できませんでした。詳細: "+e,alert("変更を保存できませんでした。")
        })}
        
        function marknew() {
          _("markdowndata").innerHTML = DOMPurify.sanitize(marked.parse(_("naka").value))
        }

        function getTitle() {
          return _("title").value || "Untitled"
        }

        $(document).ready(function() {
          starttitle = getTitle() + " - " + starttitle,
          $("title").html(starttitle),
          marknew(),
          $("#naka").on("change", function() {
            olddata = maenodata,
            maenodata = _("naka").value,
            _("back").disabled =! olddata,
            $("title").html("*"+starttitle)
          });

          _("closecreatenew").onclick = function() {
            _("conf").style = "z-index:0;display:none;";
          }
          _("bigcreatenew").onclick = function() {
            _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;left:0px;top:12%;height:88%;width:100%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
            _("toosmallcreatenew").style.display = "block";
            _("bigcreatenew").style.display = "none";
          }
          _("toosmallcreatenew").onclick = function() {
            _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;bottom:10%;left:10%;height:75%;width:80%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
            _("toosmallcreatenew").style.display = "none";
            _("bigcreatenew").style.display = "block";
          }
          _("smallcreatenew").onclick = function() {
            _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;bottom:10%;left:10%;height:15%;width:28%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
          }
        });

        document.onkeydown = function(t) {
          if (event.ctrlKey && 83 == event.keyCode)
            return save(),
            event.keyCode=0,!1
        };

        function OpenConf() {
          _("title2").value = _("title").value;
          _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;bottom:10%;left:10%;height:75%;width:80%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
        }

        function saveconf() {
          _("info").innerHTML="保存しています。。",
          _("title").value = _("title2").value;
          $.ajax({
            url: "",
            type: "post",
            data: {
              confTitle: _("title2").value,
              subTitle: _("stitle").value
            },
            success: function(t) {
              "" == t ? (_("info").innerHTML="設定の変更を保存しました。",_("conf").style = "z-index:0;display:none;"):_("info").innerHTML="変更を保存できませんでした。"}
          })
          .fail(function(t,a,e){
            _("info").innerHTML = "エラー:変更を保存できませんでした。詳細: "+e,
            alert("変更を保存できませんでした。")
          })
        }

    </script>
    <script type="text/javascript">!function(e){e.fn.linedtextarea=function(t){var i=e.extend({},e.fn.linedtextarea.defaults,t),n=function(e,t,n){for(;e.height()-t<=0;)n==i.selectedLine?e.append("<div class='lineno lineselect'>"+n+"</div>"):e.append("<div class='lineno'>"+n+"</div>"),n++;return n};return this.each(function(){var t,s=e(this);s.attr("wrap","off"),s.css({resize:"none"});var a=s.outerWidth();s.wrap("<div class='linedtextarea'></div>");var r=s.parent().wrap("<div class='linedwrap' style='width:"+a+"px'></div>").parent();r.prepend("<div class='lines' style='width:50px'></div>");var d=r.find(".lines");d.height(s.height()+6),d.append("<div class='codelines'></div>");var l=d.find(".codelines");if(t=n(l,d.height(),1),-1!=i.selectedLine&&!isNaN(i.selectedLine)){var c=parseInt(s.height()/(t-2)),h=parseInt(c*i.selectedLine)-s.height()/2;s[0].scrollTop=h}var p=d.outerWidth(),o=parseInt(r.css("border-left-width"))+parseInt(r.css("border-right-width"))+parseInt(r.css("padding-left"))+parseInt(r.css("padding-right")),v=a-o,f=a-p-o-20;s.width(f),r.width(v);var u=null;s.scroll(function(t){if(null===u){var i=this;u=setTimeout(function(){l.empty();var t=e(i)[0].scrollTop,s=Math.floor(t/15+1),a=t/15%1;n(l,d.height(),s),l.css({"margin-top":15*a*-1+"px"}),u=null},150)}}),s.resize(function(t){var i=e(this)[0];d.height(i.clientHeight+6)})})},e.fn.linedtextarea.defaults={selectedLine:-1,selectedClass:"lineselect"}}(jQuery);</script>
    <link rel="stylesheet" href="/css/edit.css">
  </head>
  <body>
    <form action="" method="POST" onsubmit="save();return false;">
      <h2>MarkDown Editor - MetaNote.</h2>
      タイトル: <input type="text" id="title" size="40" placeholder="タイトルを入力" value="<?=htmlspecialchars($row["ArticleTitle"])?>" required>
      <textarea class="lined naka" id="naka"><?
      if (!file_exists(MetaNote_Home . $row["DataSrc"]))
        touch(MetaNote_Home . $row["DataSrc"]);
      $file = fopen(MetaNote_Home . $row["DataSrc"], "r");
      $alltext = "";
      if ($file) {

        while ($line = @fgets($file))
          $alltext .= $line;
        if (empty($alltext)) echo "# Hello MarkDown!\n\nここに論文の内容をMarkDown形式で書き込んで下さい。\n\n右側にはプレビューが表示されます。";

        if (@is_utf8($alltext))
          echo @htmlspecialchars($alltext);
        else
          echo @htmlspecialchars(@mb_convert_encoding($alltext, 'UTF-8', 'SJIS'));
      }
      else
        echo "// ファイルが見つかりませんでした。";
      fclose($file);
      ?></textarea>
      <br>
      <div class="viewer">
        <p><b>MarkDown Viewer</b></p>
        <hr>
        <div id="markdowndata">
        </div>
      </div>
      <div class="btns">
        <input type="submit" value="保存" class="savebtn">
        <input type="button" value="記事の設定を開く" class="Openconf" onclick="OpenConf()">
        <input type="button" value="一つ戻す" id="back" onclick='_("naka").value=olddata;this.disabled=true;' disabled>
        <input type="button" value="置き換え" onclick='let e=_("naka").value,n=window.prompt("置き換えるテキストを入力してください"),o=window.prompt("置き換え後のテキストを入力してください");if(n != null && n != undefined){for(p=e.replace(n,o);p!==e;)e=e.replace(n,o),p=p.replace(n,o);_("naka").value=p}'>
        <input type="button" value="論文を閲覧する" onclick="window.open(location.href.replace('edit','article'))">
        <input type="button" value="文字数取得" onclick="_('info').innerHTML='現在、'+_('naka').value.length+'文字です。';">
        <span id="info"></span>
      </div>
    </form>

    <div id="conf" style="display:none;">
      <div align="right" class="texttoright">
        <span id="closecreatenew" class="btnclose">&#10006;</span>
        <span id="bigcreatenew" class="tobigger">&#9633;</span>
        <span id="toosmallcreatenew" class="toosmall">&#9633;</span>
        <span id="smallcreatenew" class="scr">—</span>
      </div>
      <br>
      <h1>論文の設定</h1>
      <hr size="1" color="#7fffd4">
      <div>
        タイトル: <input type="text" id="title2" class="inputtitle" maxlength="120" placeholder="ここにタイトルを入力してください。。(120文字まで)" required>
        <br><br>
        論文の概要: <textarea id="stitle" class="stitle" placeholder="ここに論文の概要入力してください。。(1080文字まで)" required><?=htmlspecialchars($row["ArticleSubtitle"])?></textarea>
        <br>
        <input type="button" value="保存" class="saveconf" onclick="saveconf()">
        <br>
      </div>
      <hr size="1" color="#7fffd4">
    </div>

    <script>$(function(){$(".lined").linedtextarea();});</script>
    <script>
      let beforeMarkdownData = "";
      function updateMarkdownViewer(){
        const nakaValue = document.getElementById("naka").value;
        if(nakaValue !== beforeMarkdownData){
          beforeMarkdownData = nakaValue;
          marknew();
        }
      }
      setInterval(updateMarkdownViewer, 100);
    </script>
  </body>
</html>

<?php
    exit();
  }
  else if ( $row["DateType"] === "Text/LaTeX" )
  {
    ?>
<!DOCTYPE html>
<html>
  <head>
    <title>LaTeX Editor - MetaNote.</title>
    <script type="text/javascript" src="/js/ActiveTK.min.js" charset="UTF-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/latex.js/dist/latex.js"></script>
    <script type="text/javascript">
    
      var olddata="", maenodata="", starttitle="LaTeX Editor - MetaNote.";

      function save() {
        _("info").innerHTML="保存しています。。",
        $.ajax({
          url: "",
          type: "post",
          data: {
            save: editor.getSession().getValue(),
            title: _("title").value
          },
          success: function(t) {
            "" == t ? (_("info").innerHTML="変更を保存しました。",$("title").html(starttitle)):_("info").innerHTML="変更を保存できませんでした。"}
        })
        .fail(function(t,a,e){
          _("info").innerHTML="エラー:変更を保存できませんでした。詳細: "+e,alert("変更を保存できませんでした。")
        })}
        
        function marknew() {
          var generator = new latexjs.HtmlGenerator({
            hyphenate: false
          });
          try {
            generator = latexjs.parse(editor.getValue(), {
                generator: generator
            });
            $("#output").empty();
            _("output").appendChild(generator.stylesAndScripts("https://cdn.jsdelivr.net/npm/latex.js@0.11.1/dist/"));
            _("output").appendChild(generator.domFragment());
          } catch (e) {
            if (e.name == "SyntaxError") {
                $("#output").replaceWith('<div id="output"> <p>' + e.name + '</p><p>line ' + e.location["start"]["line"] + ' (column ' + e.location["start"]["column"] + '): ' + e.message +'</p></div>');
            } else {
                $("#output").replaceWith('<div id="output"> <p>unexpected error: ' + e.message + '</p></div>');
            }
          }
        }

        function getTitle() {
          return _("title").value || "Untitled"
        }

        $(document).ready(function() {
          starttitle = getTitle() + " - " + starttitle,
          $("title").html(starttitle),
          marknew();

          _("closecreatenew").onclick = function() {
            _("conf").style = "z-index:0;display:none;";
          }
          _("bigcreatenew").onclick = function() {
            _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;left:0px;top:12%;height:88%;width:100%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
            _("toosmallcreatenew").style.display = "block";
            _("bigcreatenew").style.display = "none";
          }
          _("toosmallcreatenew").onclick = function() {
            _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;bottom:10%;left:10%;height:75%;width:80%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
            _("toosmallcreatenew").style.display = "none";
            _("bigcreatenew").style.display = "block";
          }
          _("smallcreatenew").onclick = function() {
            _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;bottom:10%;left:10%;height:15%;width:28%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
          }
        });

        document.onkeydown = function(t) {
          if (event.ctrlKey && 83 == event.keyCode)
            return save(),
            event.keyCode=0,!1
        };

        function OpenConf() {
          _("title2").value = _("title").value;
          _("conf").style = "z-index:2;display:block;text-align:center;position:fixed;bottom:10%;left:10%;height:75%;width:80%;background-color:#00ff7f;color:#080808;overflow-x:hidden;overflow-y:visible;";
        }

        function saveconf() {
          _("info").innerHTML="保存しています。。",
          _("title").value = _("title2").value;
          $.ajax({
            url: "",
            type: "post",
            data: {
              confTitle: _("title2").value,
              subTitle: _("stitle").value
            },
            success: function(t) {
              "" == t ? (_("info").innerHTML="設定の変更を保存しました。",_("conf").style = "z-index:0;display:none;"):_("info").innerHTML="変更を保存できませんでした。"}
          })
          .fail(function(t,a,e){
            _("info").innerHTML = "エラー:変更を保存できませんでした。詳細: "+e,
            alert("変更を保存できませんでした。")
          })
        }

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ace.js"></script>
    <link rel="stylesheet" href="/css/edit.css">
  </head>
  <body>
    <form action="" method="POST" onsubmit="save();return false;">
      <h2>LaTeX Editor - MetaNote.</h2>
      タイトル: <input type="text" id="title" size="40" placeholder="タイトルを入力" value="<?=htmlspecialchars($row["ArticleTitle"])?>" required>

      <div class="lined naka" id="naka"><?
      if (!file_exists(MetaNote_Home . $row["DataSrc"]))
        touch(MetaNote_Home . $row["DataSrc"]);
      $file = fopen(MetaNote_Home . $row["DataSrc"], "r");
      $alltext = "";
      if ($file) {

        while ($line = @fgets($file))
          $alltext .= $line;
        if (empty($alltext)) readfile(MetaNote_Home . "objects/samples/LaTeX");

        if (@is_utf8($alltext))
          echo @htmlspecialchars($alltext);
        else
          echo @htmlspecialchars(@mb_convert_encoding($alltext, 'UTF-8', 'SJIS'));
      }
      else
        echo "// ファイルが見つかりませんでした。";
      fclose($file);
      ?></div>
      <br>
      <div class="viewer" style="font-size:1.4rem;">
        <p><b>LaTeX Viewer</b></p>
        <hr>
        <div id="output"></div>
      </div>
      <div class="btns">
        <input type="submit" value="保存" class="savebtn">
        <input type="button" value="記事の設定を開く" class="Openconf" onclick="OpenConf()">
        <input type="button" value="文字数取得" onclick="_('info').innerHTML='現在、'+editor.getSession().getValue().length+'文字です。';">
        <span id="info"></span>
      </div>
    </form>

    <div id="conf" style="display:none;">
      <div align="right" class="texttoright">
        <span id="closecreatenew" class="btnclose">&#10006;</span>
        <span id="bigcreatenew" class="tobigger">&#9633;</span>
        <span id="toosmallcreatenew" class="toosmall">&#9633;</span>
        <span id="smallcreatenew" class="scr">—</span>
      </div>
      <br>
      <h1>論文の設定</h1>
      <hr size="1" color="#7fffd4">
      <div>
        タイトル: <input type="text" id="title2" class="inputtitle" maxlength="120" placeholder="ここにタイトルを入力してください。。(120文字まで)" required>
        <br><br>
        論文の概要: <textarea id="stitle" class="stitle" placeholder="ここに論文の概要入力してください。。(1080文字まで)" required><?=htmlspecialchars($row["ArticleSubtitle"])?></textarea>
        <br>
        <input type="button" value="保存" class="saveconf" onclick="saveconf()">
        <br>
      </div>
      <hr size="1" color="#7fffd4">
    </div>

    <script>
    
      const editor = ace.edit("naka",{
        minLines: 2
      });
      editor.setFontSize(14);
      editor.getSession().setUseWrapMode(true);
      editor.getSession().setTabSize(4);
      editor.setTheme('ace/theme/monokai');
      editor.getSession().setMode('ace/mode/latex');
      editor.$blockScrolling = Infinity;
      editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
      });
      editor.getSession().on('change', function(){
        olddata = maenodata,
        maenodata = editor.getSession().getValue(),
        $("title").html("*"+starttitle);
        marknew();
      });

    </script>
  </body>
</html>

    <?php
    exit();
  }
  else if ( $row["DateType"] === "application/pdf" )
    MetaNote_Fatal_Die( "pdfファイルを編集することはできません。" );
  else
    MetaNote_Fatal_Die( "対応していない種類の論文ファイルを開きました。" );
