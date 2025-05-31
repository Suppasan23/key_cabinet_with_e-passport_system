<?php 
session_start();

if (!isset($_SESSION['name']) || $_SESSION['id'] !== 'suppasan.c') {
    header("Location: index.php");
    exit;
}

$basePath = dirname($_SERVER['SCRIPT_NAME']); /* echo $basePath;  */

require_once 'config.php';
require_once 'key_password_fetching.php';

$passwords = getKeyCabinetPasswords($pdo);
$currentPassword = htmlspecialchars($passwords['current']);
$previousPassword = htmlspecialchars($passwords['previous']);
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
      <img src="image/RMUTSV_Logo.png" width="36" height="60" onclick="index()" style="cursor: pointer;" />
    </div>

    <div class="header">
        <span>ตู้เก็บลูกกุญแจระบบรหัส E-Passport</span> 
        <span>คณะวิศวกรรมศาสตร์ มทร.ศรีวิชัย</span>
    </div>

    <div class="content-admin">

      <!-- สุ่ม/เปลี่ยน รหัสเปิดตู้ของผู้ใช้งานใหม่ -->
    <form method="post" action="key_password_updating.php">
      <div class="admin-change-access-number">
        <button type="button" id="btn-generate-password" class="admin-button random">🎲<br>สุ่มรหัส</button>

        <div class="container-admin-input-random-number">
          <div id="input-header" style="font-weight: 800;">รหัสเปิดตู้ปัจจุบัน</div>
          <input id="input-password" class="admin-input-random-number" type="text" name="passkey" value="<?= $currentPassword ?>" required />
        </div>

        <button type="submit" id="btn-save" class="admin-button save" disabled>🔒<br>บันทึก</button>
        <button type="reset" id="btn-cancel" class="admin-button cancel" disabled><a style="color: red;">X</a><br>ยกเลิก</button>
      </div>
    </form>


      <!-- รหัสเปิดตู้ของผู้ใช้งานในเดือนก่อนหน้านี้ -->
      <div class="content-old-number-admin">
        (รหัสเปิดก่อนหน้านี้ <?= $previousPassword ?>)</br>
        รหัสลับเปิดตู้สำหรับแอดมิน = 0440
      </div>

      <!-- แสดงชื่อผู้ที่เข้ามาใช้งาน -->
      <div class="admin-name-showing_button-home-logout">
        <div class="name-showing"><?php echo "<a>".$_SESSION['name']."</a>";?></div>
        <button class="button-home" onclick="index()">Home</button>
        <button class="button-logout" onclick="logout()">Logout</button>
      </div>

      <!-- Logs ประวัติการเข้าใช้งานของผู้ใช้งาน -->
      <div class="admin-logs-header">ประวัติการเข้าใช้งาน 1 เดือนย้อนหลัง</div>
      <div class="admin-show-logs">
        <?php 
          $table = 'user_login_logs';

          try {
              // Get latest 100 rows
              $stmt = $pdo->prepare("SELECT * FROM `$table` ORDER BY `id` DESC LIMIT 100");
              $stmt->execute();
              $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ Ensure associative array

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

  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Store DOM elements
      const generateBtn = document.getElementById("btn-generate-password");
      const inputField = document.getElementById("input-password");
      const saveBtn = document.getElementById("btn-save");
      const cancelBtn = document.getElementById("btn-cancel");
      const inputHeader = document.getElementById("input-header");

      // Store original values
      const originalPassword = inputField.value;
      const originalInputHeader = inputHeader.innerText;
      const defaultColor = "#000000";
      const validColor = "#2b8a3e";
      const errorColor = "#c92a2a";

      inputHeader.style.color = defaultColor;

      // Toggle Save/Cancel buttons
      function setButtonsState(enabled) {
        saveBtn.disabled = !enabled;
        cancelBtn.disabled = !enabled;
      }

      // Handle password logic
      function handlePasswordChange() {
        const currentValue = inputField.value;

        if (currentValue === originalPassword) {
          inputHeader.innerText = originalInputHeader;
          inputHeader.style.color = defaultColor;
          setButtonsState(false);
        } else if (currentValue.length === 5 && currentValue.endsWith("A")) {
          inputHeader.innerText = "ตั้งรหัสผ่านใหม่";
          inputHeader.style.color = validColor;
          setButtonsState(true);
        } else {
          inputHeader.innerText = "ตั้งรหัสผ่านไม่ถูกต้อง";
          inputHeader.style.color = errorColor;
          setButtonsState(false);
        }
      }

      // Events
      generateBtn.addEventListener("click", function () {
        const randomNumber = Math.floor(1000 + Math.random() * 9000);
        inputField.value = randomNumber + "A";
        handlePasswordChange();
      });

      cancelBtn.addEventListener("click", function () {
        inputField.value = originalPassword;
        handlePasswordChange();
      });

      inputField.addEventListener("input", handlePasswordChange);
    });
  </script>



  <script>
    function index() {
      window.location.href = "<?php echo $basePath; ?>/index.php";
    }
  </script>
  
  <script>
    function logout() {
      window.location.href = "<?php echo $basePath; ?>/logout.php";
    }
  </script>

</body>
</html>
