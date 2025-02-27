<?php 
session_start();
$basePath = dirname($_SERVER['SCRIPT_NAME']); /* echo $basePath;  */
$err = '';

/* บัญชีผู้ใช้ที่บล็อกเอาไว้ */
$blocked_users =
[
"uthaitip.b",
"pornpen.j",
"pranrada.j",
"penpak.g",
"kraiwit.c",
"chaichumpon.c",
"wilawan.d",
"angkana.sr",
"tipawan.k",
"nitwaree.c",
"kanchana.t",
"boonsi.n",
"pakamas.l",
"siriporn.a",
"pakamas.l",
"napaphat.c",
"thananya.c",
"pimpisut.c",
"piyawan.b",
"piyawan.b",
"natthaphon.c",
"aphiwat.r",
"pinchai.k",
"pattraporn.n",
"patcharee.to",
"nopadon.k",
"thanet.s",
"suphannee.s",
"atcha.t",
"nared.s",
"yothaka.s",
"sadudee.c",
"napaporn.s",
"pitchapa.p",
"pornchanok.n",
];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="queries.css">
    <title>Key Cabinet With E-passport System</title>
  </head>

  <?php
  if($_POST) {
  
    $username = $_POST['input_user'];
    $password = $_POST['input_pswd'];

    $usertype = strpos($username, "."); // หาจุดในชื่อผู้ใช้ว่ามีหรือไม่

    if ($usertype === false) { // ไม่ใช่อาจารย์ หรือ เจ้าหน้าที่ เพราะไม่มีจุดในชื่อผู้ใช้ ใช่หรือไม่?
      $err = '<span>Access is Denied</span>';
    }

    elseif (in_array($username, $blocked_users)) { // เป็นผู้ใช้ที่บล็อกเอาไว้ ใช่หรือไม่?
      $err = '<span>Access is Denied</span>';
    }

    else {
      $loginUrl = 'http://elogin.rmutsv.ac.th';
                
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $loginUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      
      $store = curl_exec($ch);
      
      if ($store === false) {
          $err = '<span>เกิดข้อผิดพลาดในการเชื่อมต่อ</span>';
      } elseif ($store != 'none') {
          $_SESSION['name'] = $store;
      } else {
          $err = '<span>ชื่อหรือรหัสผ่าน<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ไม่ถูกต้อง</span>';
      }
    }

  }
  ?>

  <body>
    <div class="Main_Container">
      <div class="logo">
        <img src="image/RMUTSV_Logo.png" width="36" height="60" />
      </div>

      <div class="header">
          <span>ตู้เก็บลูกกุญแจระบบรหัส E-Passport</span> 
          <span>คณะวิศวกรรมศาสตร์ มทร.ศรีวิชัย</span>
      </div>

      <?php 
        if(!isset($_SESSION['name'])) /*ไม่ได้ล็อคอิน*/
        { ?>

          <form class="content" method="post">
            <div class="content-top">ล็อคอินด้วยรหัส E-Passport</div>

            <div class="content-username"><label>UserName :</label></div>
            <input class="content-username-input" type="text" name="input_user" required/>

            <div class="content-password"><label>Password :</label></div>
            <input class="content-password-input" type="password" name="input_pswd" required/>

            <div class="err-message"> <?php echo $err; ?></div>

            <button class="button-login">Login</button>
          </form>
          
          <?php 
        } else { /*ล็อคอินแล้ว*/
        ?>
          <div class="content-logged-in">
            <div class="content-top-logged-in">รหัสเปิดตู้ของเดือน มีนาคม 2568</div>
          
            <div class="content-access-number">6187A</div>
            <div class="content-old-number">(รหัสเปิดตู้ของเดือนก่อนหน้านี้ 1234A)</div>

            <div class="username-showing_button-logout">
              <div class="username-showing"><?php echo "<a>".$_SESSION['name']."</a>";?></div>
              <button class="button-logout" onclick="logout()">Logout</button>
            </div>
            
          </div>
      <?php } ?>


      <div class="footer">
        <div class="footer-header">
          กฎระเบียบในการใช้งานตู้เก็บลูกกุญแจระบบรหัสผ่าน
        </div>
        <div>
          1.ให้สิทธิใช้งานเฉพาะอาจารย์ผู้สอนเท่านั้น<br />
          2.ห้ามเปิดเผยรหัสผ่านให้กับผู้ใด<br />
          3.เปลี่ยนรหัสผ่านตู้เซฟเดือนละ 1 ครั้ง ในวันที่ 1 ของเดือน<br />
          4.นำลูกกุญแจกลับมาคืนไว้ที่ตู้ในตำแหน่งเดิมทุกครั้งหลังใช้งาน<br />
          5.ห้ามนำลูกกุญแจ หรือแม่กุญแจออกนอกพื้นที่อาคาร โดยเด็ดขาด!<br />
          6.หากทำลูกกุญแจสูญหายให้ซื้อแม่กุญแจรุ่นเดิมตัวใหม่มาเปลี่ยนให้เท่านั้น<br />
          7.หากทำแม่กุญแจสูญหายให้ซื้อแม่กุญแจรุ่นเดิมตัวใหม่มาเปลี่ยนให้เท่านั้น<br />
          8.ประตูห้องเรียนห้ามล็อคลูกบิดโดยเด็ดขาด! ให้ล็อคเฉพาะแม่กุญเพียงอย่างเดียว<br />
          9.โปรดช่วยกันรักษาความเป็นระเบียบเรียบร้อยของสถานที่<br />
        </div>
        <div class="footer-credit">
          <div class="the-blue"><span class="say-may-name">©</span>&nbsp;ผลงานวิจัยของ<span class="say-may-name">&nbsp;นายศุภสัณห์ ชัยอนันตกูล</span></div>
          <div>&nbsp;ตำแหน่ง&nbsp;วิศวกร&nbsp;คณะวิศวกรรมศาสตร์&nbsp;มทร.ศรีวิชัย</div>
        </div>
      </div>
    </div>

    <script>
      function logout() {
        window.location.href = "<?php echo $basePath; ?>" + "/logout.php";
      }
    </script>

  </body>
</html>