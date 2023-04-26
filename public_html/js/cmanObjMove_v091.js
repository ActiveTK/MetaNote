// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//
//  【 画像やDIVのマウス移動 】  http://www.cman.jp
//
//   商用,改変,再配布はすべて自由ですですが、動作保証はありません
//
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//    maintenance history
//
//    Ver   Date        contents
//    0.9   2016/6/5    New
//    0.91  2016/6/21   スマートフォン対応
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//
//  使用方法
//    移動したいhtmlのタグに以下の設定をしてください
//
//   「 cmanOMat="move" 」or「 cmanOMat="movearea" 」
//
//
//   【注意】
//     引数やユーザ設定内容についてはノーチェックです
//     解析しやすいようにコメントを多く入れています。
//     JavaScriptのファイルサイズを削減する場合は、コメントやスペースを消してください。
//
//
//   詳細は以下でご確認ください
//    https://web-designer.cman.jp/javascript_ref/mouse/move/
//
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

window.addEventListener?window.addEventListener("load",cmanOM_JS_init,!1):window.attachEvent&&window.attachEvent("onload",cmanOM_JS_init);var cmanOM_VAR={},cmanOM_Obj=[],cmanOM_OyaObj=[];function cmanOM_JS_init(){var e,t=["img","div"],n=[];cmanOM_VAR.moveOn=!1,"ontouchstart"in window?cmanOM_VAR.device="mobi":cmanOM_VAR.device="pc";for(var a=0;a<t.length;a++)for(var m=document.getElementsByTagName(t[a]),o=0;o<m.length;o++)n.push(m[o]);for(a=0;a<n.length;a++)null===(e=n[a].getAttribute("cmanOMat"))||""==e||e.toLowerCase().match(/move/)&&cmanOM_Obj.push(n[a]);for(a=0;a<cmanOM_Obj.length;a++){if("absolute"!=cmanOM_Obj[a].style.position.toLowerCase()){var O=window.getComputedStyle(cmanOM_Obj[a],null),c=document.createElement("div");c.setAttribute("id","cmanOM_ID_DMY"+a),c.style.position="relative",c.style.width=cmanOM_Obj[a].offsetWidth+"px",c.style.height=cmanOM_Obj[a].offsetHeight+"px",c.style.marginTop=O.marginTop,c.style.marginRight=O.marginRight,c.style.marginBottom=O.marginBottom,c.style.marginLeft=O.marginLeft,"img"==cmanOM_Obj[a].tagName.toLowerCase()&&(c.style.display="inline-block"),cmanOM_Obj[a].parentNode.insertBefore(c,cmanOM_Obj[a]);var _=cmanOM_Obj[a].cloneNode(!0);_.style.position="absolute",_.style.top=0,_.style.left=0,_.style.margin=0,document.getElementById("cmanOM_ID_DMY"+a).appendChild(_),cmanOM_Obj[a].parentNode.removeChild(cmanOM_Obj[a]),cmanOM_Obj[a]=_}if((e=cmanOM_Obj[a].getAttribute("cmanOMat")).toLowerCase().match(/movearea/)){cmanOM_OyaObj[a]="";var M=cmanOM_Obj[a];for(o=0;o<20&&("object"==typeof(M=M.parentNode)&&"html"!=M.tagName.toLowerCase());o++)if(null===(e=M.getAttribute("cmanOMat"))||""==e);else if(e.toLowerCase().match(/area/)){cmanOM_OyaObj[a]=M;break}}"mobi"==cmanOM_VAR.device?(cmanOM_Obj[a].ontouchstart=cmanOM_JS_mdown,cmanOM_Obj[a].ontouchend=cmanOM_JS_mup,cmanOM_Obj[a].ontouchmove=cmanOM_JS_mmove):(cmanOM_Obj[a].onmousedown=cmanOM_JS_mdown,cmanOM_Obj[a].onmouseup=cmanOM_JS_mup,cmanOM_Obj[a].onmousemove=cmanOM_JS_mmove,cmanOM_Obj[a].onmouseout=cmanOM_JS_mout),cmanOM_Obj[a].style.cursor="pointer",cmanOM_Obj[a].setAttribute("cmanOMno",a)}}function cmanOM_JS_mdown(e){cmanOM_VAR.moveOn=!1;var t=e.target||e.srcElement,n=t.getAttribute("cmanOMat");if(null===n||""==n||n.toLowerCase().match(/move/)&&(cmanOM_VAR.moveOn=!0),cmanOM_VAR.moveOn){for(var a=0;a<cmanOM_Obj.length;a++)1!=cmanOM_Obj[a].style.zIndex&&(cmanOM_Obj[a].style.zIndex=1);return cmanOM_VAR.objNowImg=t,"mobi"==cmanOM_VAR.device?(cmanOM_VAR.sPosX=e.touches[0].pageX,cmanOM_VAR.sPosY=e.touches[0].pageY):(cmanOM_VAR.sPosX=e.pageX,cmanOM_VAR.sPosY=e.pageY),""==cmanOM_VAR.objNowImg.style.top?cmanOM_VAR.sTop=0:cmanOM_VAR.sTop=parseInt(cmanOM_VAR.objNowImg.style.top.replace("px","")),""==cmanOM_VAR.objNowImg.style.left?cmanOM_VAR.sLeft=0:cmanOM_VAR.sLeft=parseInt(cmanOM_VAR.objNowImg.style.left.replace("px","")),cmanOM_VAR.objNowImg.style.zIndex=2,!1}}function cmanOM_JS_mup(e){cmanOM_VAR.moveOn=!1}function cmanOM_JS_mout(e){cmanOM_VAR.moveOn=!1}function cmanOM_JS_mmove(e){if(cmanOM_VAR.moveOn){window.getComputedStyle(cmanOM_VAR.objNowImg.parentNode,null);var t=-1,n=cmanOM_VAR.objNowImg.getAttribute("cmanOMno");if(null===n||""==n||(t=parseInt(n)),"mobi"==cmanOM_VAR.device?(cmanOM_VAR.objNowImg.style.top=cmanOM_VAR.sTop-(cmanOM_VAR.sPosY-e.touches[0].pageY)+"px",cmanOM_VAR.objNowImg.style.left=cmanOM_VAR.sLeft-(cmanOM_VAR.sPosX-e.touches[0].pageX)+"px"):(cmanOM_VAR.objNowImg.style.top=cmanOM_VAR.sTop-(cmanOM_VAR.sPosY-e.pageY)+"px",cmanOM_VAR.objNowImg.style.left=cmanOM_VAR.sLeft-(cmanOM_VAR.sPosX-e.pageX)+"px"),t<0);else if("object"==typeof cmanOM_OyaObj[t]){var a=cmanOM_OyaObj[t].getBoundingClientRect(),m=cmanOM_VAR.objNowImg.getBoundingClientRect(),o=0,O=0;a.top>m.top&&(o+=a.top-m.top),a.left>m.left&&(O+=a.left-m.left),a.top+a.height<m.top+m.height&&(o+=a.top+a.height-(m.top+m.height)),a.left+a.width<m.left+m.width&&(O+=a.left+a.width-(m.left+m.width)),0!=o&&(cmanOM_VAR.objNowImg.style.top=parseInt(cmanOM_VAR.objNowImg.style.top.replace("px",""))+o+"px"),0!=O&&(cmanOM_VAR.objNowImg.style.left=parseInt(cmanOM_VAR.objNowImg.style.left.replace("px",""))+O+"px")}return!1}}
