<?php 
session_start();
$basePath = dirname($_SERVER['SCRIPT_NAME']); /* echo $basePath;  */

require_once 'config.php';
require_once 'user_blocked_list.php';

require_once 'key_password_fetching.php';
$passwords = getKeyCabinetPasswords($pdo);
$currentPassword = htmlspecialchars($passwords['current']);
$previousPassword = htmlspecialchars($passwords['previous']);

$err = '';
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
if ($_POST) {

    $username = $_POST['input_user'];
    $password = $_POST['input_pswd'];

    $usertype = strpos($username, "."); // หาจุดในชื่อผู้ใช้ว่ามีหรือไม่

    if ($usertype === false) { // ไม่ใช่อาจารย์ หรือ เจ้าหน้าที่ เพราะไม่มีจุดในชื่อผู้ใช้ ใช่หรือไม่?
        $err = '<span>Access is Denied</span>';
    } elseif (in_array($username, $blocked_users)) { // เป็นผู้ใช้ที่บล็อกเอาไว้ ใช่หรือไม่?
        $err = '<span>Access is Denied</span>';
    } else {
        $loginUrl = 'http://elogin.rmutsv.ac.th';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username=' . $username . '&password=' . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $store = curl_exec($ch); /*ได้ชื่อของผู้ใช้งานมา*/

        if ($store === false) {
            $err = '<span>เกิดข้อผิดพลาดในการเชื่อมต่อ</span>';
        } elseif ($store != 'none') {
            $_SESSION['name'] = trim($store); /*เก็บชื่อผู้ใช้งานไว้ใน SESSION*/
            $_SESSION['id'] = strtolower(trim($username)); /*เก็บ username ของผู้ใช้งานไว้ใน SESSION*/

            /*เป็น ADMIN หรือไม่*/
            /*ล้าง Logs ที่เก่ากว่า 30 วัน*/
            if ($_SESSION['id'] === 'suppasan.c') {
                try {
                    // Clean up logs older than 30 days
                    $cleanup = $pdo->prepare("DELETE FROM user_login_logs WHERE login_time < NOW() - INTERVAL 30 DAY");
                    $cleanup->execute();
                } catch (PDOException $e) {
                    echo "❌ Failed to clean up logs older than 30 days: " . $e->getMessage();
                }
            }

            /*Logs ชื่อ, วันที่, เวลา ผู้เข้าใช้งาน*/
            try {
                // Insert log
                $stmt = $pdo->prepare("INSERT INTO user_login_logs (username) VALUES (:username)");
                $stmt->execute(array('username' => $_SESSION['name'])); // ✅ fixed here
            } catch (PDOException $e) {
                echo "❌ Failed to log user: " . $e->getMessage();
            }
        } else {
            $err = '<span>ชื่อหรือรหัสผ่าน<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ไม่ถูกต้อง</span>';
        }
    }
}
?>


  <body>
    <div class="Main_Container">
      <div class="logo">
        <img src="image/RMUTSV_Logo.png" width="36" height="60" onclick="index()" style="cursor: pointer;" />
      </div>

      <div class="header">
          <span>ตู้เก็บลูกกุญแจระบบรหัส E-Passport</span> 
          <span>คณะวิศวกรรมศาสตร์ มทร.ศรีวิชัย</span>
      </div>

      <?php 
          if (!isset($_SESSION['name'])) { // ไม่ได้ล็อคอิน
          ?>
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
          } else { // ล็อคอินแล้ว
          ?>
            <div class="content-logged-in">

              <!-- Current Password -->
              <div class="content-top-logged-in">รหัสเปิดตู้ปัจจุบัน</div>
              <div class="content-access-number"><?php echo $currentPassword; ?></div>

              <!-- Previous Password -->
              <div class="content-old-number"><?php echo "(รหัสเปิดตู้ก่อนหน้านี้ " . $previousPassword . ")"; ?></div>

              <!-- แสดงชื่อผู้ที่เข้ามาใช้งาน -->
              <div class="username-showing_button-logout">
                <div class="name-showing"><?php echo "<a>" . $_SESSION['name'] . "</a>"; ?></div>

                <?php if ($_SESSION['id'] === 'suppasan.c') { // เป็น ADMIN หรือไม่ ?>
                  <button class="button-admin" onclick="admin()">Admin</button>
                <?php } ?>

                <button class="button-logout" onclick="logout()">Logout</button>
              </div>

            </div>
          <?php } ?>


      <div class="footer">
        <div class="footer-header">
          กฎระเบียบในการใช้งานตู้เก็บลูกกุญแจนิรภัยทำงานร่วมกับระบบรหัส E-Passport แบบกึ่งอัตโนมัติ
        </div>
        <div>
          1.ให้สิทธิใช้งานเฉพาะอาจารย์ผู้สอนเท่านั้น<br />
          2.ห้ามเปิดเผยรหัสผ่านในการเปิดตู้กุญแจให้กับผู้ใดโดยเด็ดขาด<br />
          3.ล็อคแม่กุญแจห้องเรียนทุกครั้งหลังเลิกใช้งาน<br />
          4.นำลูกกุญแจกลับมาคืนที่ตู้ในตำแหน่งเดิมอย่างตรงเวลาทุกครั้งหลังเลิกใช้งาน หากไม่นำลูกกุญแจมาคืนอันเป็นเหตุให้อาจารย์ผู้สอนในคาบถัดไปไม่สามารถเข้าห้องเรียนได้ตรงเวลาจะถูกปรับเป็นเงิน 100 บาท<br />
          5.ห้ามนำลูกกุญแจ หรือแม่กุญแจออกนอกพื้นที่อาคาร โดยเด็ดขาด!<br />
          6.หากทำลูกกุญแจสูญหายจะถูกปรับเป็นเงิน 500 บาท (เนื่องจากจะต้องเปลี่ยนแม่กุญแจใหม่)<br />
          7.หากทำแม่กุญแจสูญหายจะถูกปรับเป็นเงิน 500 บาท (เนื่องจากจะต้องเปลี่ยนแม่กุญแจใหม่)<br />
          8.โปรดช่วยกันรักษาความเป็นระเบียบเรียบร้อยของสถานที่<br />
          9.ระบบจะทำการบันทึกข้อมูล ชื่อ วันที่ และเวลา ของผู้ใช้งาน เมื่อผู้ใช้งานทำการล็อคอินเข้าสู่ระบบ เพื่อนำข้อมูลไปใช้ในกรณีจำเป็นต้องตรวจสอบความเรียบร้อยในการ เบิก-คืน ลูกกุญแจ และจะลบข้อมูลดังกล่าวหลังจากผ่านไป 30 วัน<br />
        </div>
        <div class="footer-credit">
          <div class="the-blue"><span class="say-may-name">©</span>&nbsp;ผลงานวิจัยของ<span class="say-may-name">&nbsp;นายศุภสัณห์ ชัยอนันตกูล</span></div>
          <div>&nbsp;ตำแหน่ง&nbsp;วิศวกร&nbsp;คณะวิศวกรรมศาสตร์&nbsp;มทร.ศรีวิชัย</div>
        </div>
      </div>
    </div>

    <script>
      function index() {
        window.location.href = "<?php echo $basePath; ?>/index.php";
      }
    </script>

    <script>
      function admin() {
        window.location.href = "<?php echo $basePath; ?>" + "/admin.php";
      }
    </script>

    <script>
      function logout() {
        window.location.href = "<?php echo $basePath; ?>" + "/logout.php";
      }
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get("status") === "success") {
          alert("✅ รหัสผ่านใหม่ถูกบันทึกเรียบร้อยแล้ว");

          // Remove status=success from the URL without reloading
          const url = new URL(window.location);
          url.searchParams.delete("status");
          window.history.replaceState({}, document.title, url);
        }
      });
    </script>

  </body>
</html>