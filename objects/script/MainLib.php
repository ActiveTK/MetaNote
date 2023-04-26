<?php

  /*!
   * @namespace utilities.php(MetaNote.Server)
   * (c) 2022 MetaNote.
   * �֗��ȏ������܂Ƃ߂��t�@�C��
   */

  /**
   * �������HTML��p�̕�����ɕϊ�����֐�
   * 
   * e.g.
   * & -> &amp;
   * < -> &lt;
   * 
   * @param string $text �ϊ��O�̕�����
   * @return string �ϊ���̕�����
   */
  function MetaNote_Plain2HtmlCode ( string $text = "" ) : string { 
    return htmlspecialchars( $text );
  }

  /**
   * �����񂪉p����(alphanumeric)�����肷��֐�
   * 
   * @param string $text ���肷�镶����
   * @return bool �p�������ǂ����Btrue�ŉp�����Bfalse�ł����łȂ��B
   */
  function is_alnum(string $text) : bool {
    if ( preg_match( "/^[a-zA-Z0-9]+$/", $text ) )
      return true;
    else
      return false;
  } 

  /**
   * �G���[�𖳎������Z�b�V������unset�֐�
   * 
   * @return bool �G���[��������ꂽ�ꍇ�ɂ�false�B����ȊO��true�ł��B
   */
  function MetaNote_Session_Close() : bool {
    try {
      if ( session_status() == PHP_SESSION_NONE )
        @session_write_close();
      return true;
    } catch ( \Throwable $e ) {
      return false;
    }
  }

  /**
   * �ȈՓI��Fatal�G���[�\���֐�
   */
  function MetaNote_Fatal_Die( string $ErrorInfo = "" ) {

    MetaNote_Session_Close();

    http_response_code( 500 );
    header( "HTTP/1.1 500 Internal Server Error" );
    header( "Content-Type: text/html;charset=UTF-8" );

    include(MetaNote_Home . "public_html/MetaNote.HttpStatus.500.php");

    die();

  }

  /**
   * ���[�U�[�����_�C���N�g������֐�
   * 
   * @param string $url ���_�C���N�g���URL�ł��B
   */
  function NCPRedirect( $url ) {

    include(MetaNote_Home . "objects/script/NCPRedirect.php");

    exit();

  }

  /**
   * ActiveTK.min.js��`�ʂ���֐�
   * 
   * @param string $nonce HTML���nonce�ł��B
   */
  function MetaNote_ActiveTKMinJs( string $nonce = "" ) {
    if ( $nonce == "" )
      return "<script type=\"text/javascript\" src=\"/js/ActiveTK.min.js\"></script>";
    else
      return "<script type=\"text/javascript\" src=\"/js/ActiveTK.min.js\" nonce=\"{$nonce}\"></script>";
  }

  /**
   * SHA512.js��`�ʂ���֐�
   * 
   * @param string $nonce HTML���nonce�ł��B
   */
  function MetaNote_SHA512Js( string $nonce = "" ) {
    if ( $nonce == "" )
      return "<script type=\"text/javascript\" src=\"/js/sha512.js\"></script>";
    else
      return "<script type=\"text/javascript\" src=\"/js/sha512.js\" nonce=\"{$nonce}\"></script>";
  }

  /**
   * �o�̓f�[�^�̃L���b�V����������֐�
   */
  function AllowCache() {
    header( 'Last-Modified: Fri Jan 01 2010 00:00:00 GMT' );
    header( 'Expires: ' . gmdate( 'D, d M Y H:i:s T', time() + 604800 ) );
    header( 'Cache-Control: private, max-age=604800' );
    header( 'Pragma: ' );
  }

  /**
   * �Í��w�I�Ɉ��S�Ƃ���闐���𔭐�������֐�
   * 
   * @param int $len �����̃o�C�g���ł��B
   * @return string �������ꂽ����
   */
  function MetaNote_GetRand( int $len = 32 ) : string {

    $bytes = openssl_random_pseudo_bytes( $len / 2 );
    $str = bin2hex( $bytes );

    $usestr = '1234567890abcdefghijklmnopqrstuvwxyz';
    $str2 = substr( str_shuffle( $usestr ), 0, 12 );

    return substr( str_shuffle( $str . $str2 ) , 0, -12 );

  }

  /**
   * �t�b�^�[��\������֐�
   */
  function MetaNote_View_Option() {

    ?>
    <div class="p-lastinfo" align="center">
      <p>
        <a href="/" style="color:#00ff00 !important;">�z�[��</a>�E
        <a href="/about" style="color:#0403f9 !important;">�T�[�r�X�T�v</a>�E
        <a href="/license" style="color:#ffa500 !important;">���p�K��</a>�E
        <a href="/privacy" style="color:#ff00ff !important;">�v���C�o�V�[</a> 
        <?=Copyright?>
      </p>
    </div>
    <?php
  
  }

  /**
   * ���[�U�[�����v������֐�
   * @return int ���݂̃��[�U�[��
   */
  function MetaNote_UserCount() : int {

    try {
      $pdo = new PDO( DSN, DB_USER, DB_PASS );
      $st = $pdo->query('select count(*) from MetaNoteUsers');
      $usercount = $st->fetchColumn();
      return ((int)$usercount * 1);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * ���[�U�[�����v������֐�(SQL��񂠂�)
   * @parm PDO $pdo �f�[�^�x�[�X�ڑ� 
   * @return int ���݂̃��[�U�[��
   */
  function MetaNote_UserCount_bySQL($pdo) : int {

    try {
      $st = $pdo->query('select count(*) from MetaNoteUsers');
      $usercount = $st->fetchColumn();
      return ((int)$usercount * 1);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * ���[�U�[ID���烆�[�U�[���ƃf�B�X�v���CID���擾����֐�
   * @param string[] $ID ���[�U�[�̃��j�[�NID�ł��B
   * @return array ���[�U�[��(���݂��Ȃ��ꍇ�ɂ́u�폜�ς݂̃��[�U�[�v)�ƃf�B�X�v���CID�̔z��B
   */
  function MetaNote_GetNameByID($ID) : array {

    try {
      if (!is_alnum((string)$ID))
        return array();
      $akm = "";
      $akz = "";
      $dbh = new PDO(DSN, DB_USER, DB_PASS);
      $res = $dbh->query("select * from MetaNoteUsers where UserIntID = '" . $ID . "' limit 2;");
	  foreach($res as $value) {
	    $akm = $value["UserName"];
        $akz = $value["DisplayID"];
	  }
      if (empty($akm))
        $akm = "�폜�ς݂̃��[�U�[";
      if (empty($akz))
        $akz = "DELETED";
      return array($akm, $akz);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * ���[�U�[ID���烆�[�U�[���ƃf�B�X�v���CID���擾����֐�
   * @oarm PDO $dbh �f�[�^�x�[�X�f�[�^�x�[�X���
   * @param string[] $ID ���[�U�[�̃��j�[�NID�ł��B
   * @return array ���[�U�[��(���݂��Ȃ��ꍇ�ɂ́u�폜�ς݂̃��[�U�[�v)�ƃf�B�X�v���CID�̔z��B
   */
  function MetaNote_GetNameByID_bySQL($dbh, $ID) : array {

    try {
      if (!is_alnum((string)$ID))
        MetaNote_Fatal_Die( "�Z�L�����e�B�G���[���������܂����B" );
      $akm = "";
      $akz = "";
      $res = $dbh->query("select * from MetaNoteUsers where UserIntID = '" . $ID . "' limit 2;");
	  foreach($res as $value) {
	    $akm = $value["UserName"];
        $akz = $value["DisplayID"];
	  }
      if (empty($akm))
        $akm = "�폜�ς݂̃��[�U�[";
      if (empty($akz))
        $akz = "DELETED";
      return array($akm, $akz, $ID);
    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * MetaNote�̃f�t�H���g�w�b�_�[��\�����܂��B
   *
   * @param string $IndexFromBot (�C��)�N���[���[����̃C���f�b�N�X�������͋��ۂ��܂��B[All] ���� [noindex] ���w�肵�Ă��������B�K��l�� [All] �ł��B
   * @param string $Charset (�C��)�����R�[�h���w�肵�Ă��������B�K��l�� [utf-8] �ł��B
   */
  function MetaNote_Header_Default( string $IndexFromBot = "All", string $Charset = "utf-8" ) {

    try {
?><meta charset="<?=$Charset?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="robots" content="<?=$IndexFromBot?>">
    <meta name="keywords" content="MetaNote">
    <meta name="favicon" content="/favicon.ico">
    <meta name="description" content="MetaNote�ł͖����ő��̃��[�U�[�ƃ`���b�g���ł��܂��B">
    <meta name="copyright" content="<?=Copyright?>">
    <meta name="thumbnail" content="https://<?=Domain?>/icon/index.jpg">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="MetaNote�ł͖����ő��̃��[�U�[�ƃ`���b�g���ł��܂��B">
    <meta name="twitter:image:src" content="https://<?=Domain?>/icon/index.jpg">
    <meta name="twitter:image:alt" content="MetaNote.">
    <meta name="twitter:domain" content="<?=Domain?>">

    <meta property="og:description" content="MetaNote�ł͖����ő��̃��[�U�[�ƃ`���b�g���ł��܂��B">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:image" content="https://<?=Domain?>/icon/index.jpg">
    <meta property="og:image:width" content="1280">
    <meta property="og:image:height" content="720">

    <link rel="canonical" href="https://<?=Domain?>/">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_16_16.ico" sizes="16x16">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_32_32.ico" sizes="32x32">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_64_64.ico" sizes="64x64">
    <link rel="shortcut icon" href="https://<?=Domain?>/icon/index_192_192.ico" sizes="192x192">
    <link rel="apple-touch-icon-precomposed" href="https://<?=Domain?>/icon/index_150_150.ico">
<?php

    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }

  }

  /**
   * MetaNote�̃{�f�B�[���̃f�t�H���g�w�b�_�[��\�����܂��B
   */
  function Get_Body_Header() {

    include("./component/header.php");

  }

  /**
   * MetaNote�̃{�f�B�[�Ō�̃A�C�R����\�����܂��B
   */
  function MetaNote_View_Icons() {
      try {
?>
      <div align="center">
        <a href="/home" rel="noopener noreferrer">
          <img src="https://<?=Domain?>/icon/home.png" style="width:73px;height:73px;" title="�z�[��">
        </a>
        <a href="/setting" rel="noopener noreferrer">
          <img src="https://<?=Domain?>/icon/setting.png" style="width:73px;height:73px;" title="�ݒ�">
        </a>
        <a href="/logout" rel="noopener noreferrer">
          <img src="https://<?=Domain?>/icon/alllogout.png" style="width:73px;height:73px;" title="���O�A�E�g">
        </a>
      </div>
<?php

    } catch ( \Throwable $e ) {
      MetaNote_Fatal_Die( $e->getMessage() );
    }
  }

  /**
   * MetaNote�̐l�C�L����\�����܂��B
   */
  function MetaNote_List_DESC($dbh) {
      ob_start();
    ?>

      <div align="center" style="display:flex;-webkit-justify-content:center;justify-content:center;-webkit-align-items:center;align-items: center;">

        <div id="slide" class="carousel slide container marketing" data-bs-ride="carousel" style="width:500px;">
          <h3>�y�l�C�}�㏸�z</h3>
          <ol class="carousel-indicators">
            <li data-bs-target="#slide" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#slide" data-bs-slide-to="1"></li>
            <li data-bs-target="#slide" data-bs-slide-to="2"></li>
            <li data-bs-target="#slide" data-bs-slide-to="3"></li>
            <li data-bs-target="#slide" data-bs-slide-to="4"></li>
          </ol>
          <div class="carousel-inner">
          <?php
              $i = 0;
              $Notes = array();
              $AlReadyShowed = array();
              try {
                $Notes = $dbh->query('select * from noteblog where not decinfo = "" order by pvcount desc limit 5;');
                if ($Notes !== false) { }
                else die("SQL�̎��s���ɃG���[���������܂����B");
              } catch (\Throwable $e) { die("SQL�G���[���������܂����B"); }
              foreach($Notes as $value)
              {
                $i++;
          ?>
            <div class="col-lg-4 carousel-item<?php if ($i == 1) echo " active"; ?>" style="border:1px solid; height:<?=com("300", "500")?>px;">
              <h3><?=$value["pagetitle"]?></h3>
              <p><?=$value["writer"]?></p>
              <b><?=$value["decinfo"]?></b>
              <br><br>
              <p><a class="btn btn-secondary" href="https://note.activetk.jp/<?=$value["httppath"]?>" role="button">����������</a></p>
            </div><?php
              }
          ?>
          </div>
          <button type="button" class="carousel-control-prev" data-bs-target="#slide" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">�O��</span>
          </button>
          <button type="button" class="carousel-control-next" data-bs-target="#slide" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">����</span>
          </button>
        </div>

        <div id="slide2" class="carousel slide container marketing" data-bs-ride="carousel" style="width:500px;">
          <h3>�y�ŐV�̋L���z</h3>
          <ol class="carousel-indicators">
            <li data-bs-target="#slide2" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="1"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="2"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="3"></li>
            <li data-bs-target="#slide2" data-bs-slide-to="4"></li>
          </ol>
          <div class="carousel-inner">
          <?php
              $i = 0;
              $Notes = array();
              try {
                $Notes = $dbh->query('select * from noteblog where not decinfo = "" order by lastwritetime desc limit 5;');
                if ($Notes !== false) { }
                else die("SQL�̎��s���ɃG���[���������܂����B");
              } catch (\Throwable $e) { die("SQL�G���[���������܂����B"); }
              foreach($Notes as $value)
              {
                $i++;
          ?>
            <div class="col-lg-4 carousel-item<?php if ($i == 1) echo " active"; ?>" style="border:1px solid; height:<?=com("300", "500")?>px;">
              <h3><?=$value["pagetitle"]?></h3>
              <p><?=$value["writer"]?></p>
              <b><?=$value["decinfo"]?></b>
              <br><br>
              <p><a class="btn btn-secondary" href="https://note.activetk.jp/<?=$value["httppath"]?>" role="button">����������</a></p>
            </div><?php
              }
          ?>
          </div>
          <button type="button" class="carousel-control-prev" data-bs-target="#slide2" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">�O��</span>
          </button>
          <button type="button" class="carousel-control-next" data-bs-target="#slide2" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">����</span>
          </button>
        </div>
      </div>

     <?php
       $buff = ob_get_contents();
       ob_end_clean();
       return $buff;
  }
