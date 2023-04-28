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

  if ( isset( $_POST["save"] ) ) {
    file_put_contents( MetaNote_Home . $row["DataSrc"], $_POST["save"] );
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

?>
<html>
  <head>
    <title>MarkDown Editor - MetaNote.</title>
    <script type="text/javascript" src="/js/ActiveTK.min.js" charset="UTF-8"></script>
    <script src="https://cdn.jsdelivr.net/gh/markedjs/marked/marked.min.js"></script>
    <script type="text/javascript">var olddata="",maenodata="",starttitle="MarkDown Editor - MetaNote.";function save(){_("info").innerHTML="保存しています。。",$.ajax({url:"",type:"post",data:{save:_("naka").value},success:function(t){""==t?(_("info").innerHTML="変更を保存しました。",$("title").html(starttitle)):_("info").innerHTML="変更を保存できませんでした。"}}).fail(function(t,a,e){_("info").innerHTML="エラー:変更を保存できませんでした。詳細: "+e,alert("変更を保存できませんでした。")})}function marknew(){_("markdowndata").innerHTML=marked.parse(_("naka").value)}function getTitle(){return _("naka").value.slice(0,_("naka").value.indexOf("\n"))||"Untitled"}$(document).ready(function(){starttitle=getTitle()+" - "+starttitle,$("title").html(starttitle),marknew(),$("#naka").on("change",function(){olddata=maenodata,maenodata=_("naka").value,_("back").disabled=!olddata,$("title").html("*"+starttitle),marknew()})}),document.onkeydown=function(t){if(event.ctrlKey&&83==event.keyCode)return save(),event.keyCode=0,!1};</script>
    <script type="text/javascript">!function(e){e.fn.linedtextarea=function(t){var i=e.extend({},e.fn.linedtextarea.defaults,t),n=function(e,t,n){for(;e.height()-t<=0;)n==i.selectedLine?e.append("<div class='lineno lineselect'>"+n+"</div>"):e.append("<div class='lineno'>"+n+"</div>"),n++;return n};return this.each(function(){var t,s=e(this);s.attr("wrap","off"),s.css({resize:"none"});var a=s.outerWidth();s.wrap("<div class='linedtextarea'></div>");var r=s.parent().wrap("<div class='linedwrap' style='width:"+a+"px'></div>").parent();r.prepend("<div class='lines' style='width:50px'></div>");var d=r.find(".lines");d.height(s.height()+6),d.append("<div class='codelines'></div>");var l=d.find(".codelines");if(t=n(l,d.height(),1),-1!=i.selectedLine&&!isNaN(i.selectedLine)){var c=parseInt(s.height()/(t-2)),h=parseInt(c*i.selectedLine)-s.height()/2;s[0].scrollTop=h}var p=d.outerWidth(),o=parseInt(r.css("border-left-width"))+parseInt(r.css("border-right-width"))+parseInt(r.css("padding-left"))+parseInt(r.css("padding-right")),v=a-o,f=a-p-o-20;s.width(f),r.width(v);var u=null;s.scroll(function(t){if(null===u){var i=this;u=setTimeout(function(){l.empty();var t=e(i)[0].scrollTop,s=Math.floor(t/15+1),a=t/15%1;n(l,d.height(),s),l.css({"margin-top":15*a*-1+"px"}),u=null},150)}}),s.resize(function(t){var i=e(this)[0];d.height(i.clientHeight+6)})})},e.fn.linedtextarea.defaults={selectedLine:-1,selectedClass:"lineselect"}}(jQuery);</script>
    <style>.linedwrap{border:1px solid silver;padding:3px}.linedtextarea{padding:0;margin:0}.linedtextarea textarea,.linedwrap .codelines .lineno{font-size:10pt;font-family:monospace;line-height:15px!important}.linedtextarea textarea{padding-right:.3em;padding-top:.3em;border:0}.linedwrap .lines{margin-top:0;width:50px;float:left;overflow:hidden;border-right:1px solid silver;margin-right:10px}.linedwrap .codelines{padding-top:5px}.linedwrap .codelines .lineno{color:#aaa;padding-right:.5em;padding-top:0;text-align:right;white-space:nowrap}.linedwrap .codelines .lineselect{color:red}</style>
  </head>
  <body style="background-color:#6495ed;color:#080808;overflow-x:hidden;overflow-y:visible;">
    <form action="" method="POST" onsubmit="save();return false;">
      <h2>MarkDown Editor - MetaNote.</h2>
      タイトル: <input type="text" id="title" size="40" placeholder="タイトルを入力" required><br>
      記事の概要: <input type="text" id="stitle" size="80" placeholder="概要を入力">
      <textarea class="lined" id="naka" style="text-align:left;position:fixed;overflow-wrap:break-word;overflow-x:scroll;overflow-y:visible;width:52%;height:75%;background-color:#000000;color:#ffffff;margin:5px 5px;"><?
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
      <div style="text-align:left;position:fixed;bottom:2%;left:55%;height:92%;width:40%;background-color:#e6e6fa;text:#363636;overflow-x:hidden;overflow-y:visible;">
        <p><b>MarkDown Viewer</b></p>
        <hr>
        <div id="markdowndata">
        </div>
      </div>
      <div style="position:fixed;bottom:3%;left:40px;">
        <input type="submit" value="保存" style="width:120px;height:40px;">
        <input type="button" value="一つ戻す" id="back" onclick='_("naka").value=olddata;this.disabled=true;' disabled>
        <input type="button" value="置き換え" onclick='let e=_("naka").value,n=window.prompt("置き換えるテキストを入力してください"),o=window.prompt("置き換え後のテキストを入力してください");if(n != null && n != undefined){for(p=e.replace(n,o);p!==e;)e=e.replace(n,o),p=p.replace(n,o);_("naka").value=p}'>
        <input type="button" value="文字数取得" onclick="_('info').innerHTML='現在、'+_('naka').value.length+'文字です。';">
        <span id="info"></span>
      </div>
    </form>
    <script>$(function(){$(".lined").linedtextarea();});</script>
  </body>
</html>

