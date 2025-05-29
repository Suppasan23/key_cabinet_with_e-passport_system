<?php 
session_start();

if (!isset($_SESSION['name']) || $_SESSION['id'] !== 'suppasan.c') {
    header("Location: index.php");
    exit;
}
require_once 'config.php';
$basePath = dirname($_SERVER['SCRIPT_NAME']);
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
<body>
  <div class="Main_Container">
    <div class="logo">
      <img src="image/RMUTSV_Logo.png" width="36" height="60" />
    </div>

    <div class="header">
        <span>ตู้เก็บลูกกุญแจระบบรหัส E-Passport</span> 
        <span>คณะวิศวกรรมศาสตร์ มทร.ศรีวิชัย</span>
    </div>

    <div class="content-admin">

      <div class="username-showing_button-logout" style="grid-row: 1/2;">
        <div class="username-showing"><?php echo "<a>".$_SESSION['name']."</a>";?></div>
        <button class="button-home" onclick="index()">Home</button>
        <button class="button-logout" onclick="logout()">Logout</button>
      </div>

      <div class="admin-header">ประวัติการเข้าใช้งาน 1 เดือนย้อนหลัง</div>
      <div class="admin-show-logs">
        <?php 
          $table = 'user_login_logs';

          try {
              // Get latest 100 rows
              $stmt = $pdo->prepare("SELECT * FROM `$table` ORDER BY `id` DESC LIMIT 100");
              $stmt->execute();
              $rows = $stmt->fetchAll();

              if ($rows) {
                  echo "<table border='1' cellpadding='5' cellspacing='0'>";
                  
                  // Table headers
                  echo "<tr>";
                  echo "<th>No.</th>";
                  foreach (array_keys($rows[0]) as $header) {
                      if ($header !== 'id') {
                          echo "<th>" . htmlspecialchars($header) . "</th>";
                      }
                  }
                  echo "</tr>";

                  // Table rows
                  $no = 1;
                  foreach ($rows as $row) {
                      echo "<tr>";
                      echo "<td>" . $no++ . "</td>";
                      foreach ($row as $key => $cell) {
                          if ($key !== 'id') {
                              // Format login_time column to remove seconds
                              if ($key === 'login_time') {
                                  $formattedTime = date('Y-m-d H:i', strtotime($cell));
                                  echo "<td>" . htmlspecialchars($formattedTime) . "</td>";
                              } else {
                                  echo "<td>" . htmlspecialchars($cell) . "</td>";
                              }
                          }
                      }
                      echo "</tr>";
                  }

                  echo "</table>";
              } else {
                  echo "No data found in the table.";
              }
          } catch (PDOException $e) {
              echo "❌ Query failed: " . $e->getMessage();
          }
          ?>
      </div>

    </div>

      <div class="footer" style="background-color:#c0d9ec;">
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
    function index() {
      window.location.href = "<?php echo $basePath; ?>/index.php";
    }
    function logout() {
      window.location.href = "<?php echo $basePath; ?>/logout.php";
    }
  </script>
</body>
</html>
