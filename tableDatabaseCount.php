<?php
echo "yes";
die('sss');

try {
  $conn = oci_connect($username,$password,"//$servername/$dbName");
  var_dump($conn);


} catch (\Exception $e) {
  var_dump($e->getMessage());
}
die('Test');

session_start();
if ($_SESSION['login'] !== 'yes') {
  $host = $_SERVER['HTTP_HOST'];
  header("Location: signin.php");
  exit;
}
include 'cleanInput.php';
include 'validator.php';
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>BVN LOGS</title>
    <!-- Latest compiled and minified CSS -->
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

   <!-- jQuery library -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

   <!-- Popper JS -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

   <!-- Latest compiled JavaScript -->
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
   <link rel="stylesheet" href="css/base.css">
   <link rel="stylesheet" href="css/palette.css">
  </head>
  <body>
    <header>
         <nav class="navbar dark-primary-color navbar-expand-lg">
           <a class="h2 logo db-text" href="signin.php">Hello <span class="helve"><?php echo $_SESSION['email']; ?></span></a>
           <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
             </button>
           <div class="collapse justify-content-end navbar-collapse" id="navbarSupportedContent">
             <ul class="navbar-nav">
             <li class="nav-item mr-2 ">
               <a class="nav-link db-text" href="tableDatabaseCount.php" data-toggle="tooltip" data-placement="bottom" title="Show User texts"><span>BVN Logs </span><i class="fas fa-database"></i></a>
             </li>
               <li class="nav-item  mr-2">
                 <a class="nav-link db-text" href="logout.php" data-toggle="tooltip" data-placement="bottom" title="Logout from the application"><span>Logout </span><i class="fas fa-sign-out-alt"></i></a>
               </li>
             </ul>
           </div>
         </nav>
    </header>



    <div class="container-fluid">
      <div class="row mt-3 pl-5">
        <div class="col secondary-text-color tableHeader">
          <h1>BVN LOGS</h1>
        </div>
      </div>
      <div class="row mt-2 px-5 secondary-text-color">
        <div class="col-search">
          <span class="text-white p-2 dark-primary-color mr-2 rounded">Search by Operator: </span> <form class="d-inline" method="get" >
            <select class="border border-dark p-1 rounded" name="searchText">
              <option value="all">ALL</option>
              <option value="mtn">MTN</option>
              <option value="airtel">AIRTEL</option>
              <option value="etisalat">ETISALAT</option>
              <option value="glo">GLO</option>
            </select>
          </form>
        </div>
        <div class="col-search-date">
          <form  class="d-inline mr-3" method="get">
                <label for="startDate">Start Date:</label>
                <input class="startDateMargin border border-dark p-1 rounded" type="date" oninput="swapper()" name="startDate" id="startDate">
                <label for="endDate">End Date:</label>
                <input type="date" class="border border-dark p-1 rounded" name="endDate" id="endDate" oninput="swapper()">
          </form>
          <button id="searchBtn" onclick="search()" class="btn btn-black">Search</button>
        </div>
      </div>
      <div class="row d-flex justify-items-center mt-2 px-5">
        <table class="table w-75 light-primary-color secondary-text-color table-hover border border-dark rounded table-bordered">
          <?php
          $errors = array();

          $servername = "192.168.100.170";
          //$servername = "83.138.190.170";
          $username = "bvn";
          $password= "bvn123";
          $dbName = "mydb1.vas2nets";
          $conn = oci_connect($username,$password,"//$servername/$dbName");
          var_dump($conn);
          die('Test');
          $tempTable = "(SELECT ID, TSTAMP,OPERATOR "."OPERATOR"." from bvn_logs  where TSTAMP is not null UNION select ID, TIME_IN as TSTAMP,
          TO_CHAR('GLO') "."OPERATOR"." from SMREQUEST_IN2 where TIME_IN is not null)";
          if (
            (isset($_GET['searchText'])) ||
              (isset($_GET['startDate']) || isset($_GET['endDate']))
            ) {

            $logSql = 'SELECT COUNT(ID),UPPER(OPERATOR) OPERATOR FROM '.$tempTable.' temp WHERE ';
            if (isset($_GET['searchText'])) {
              $searchText = cleanInput($_GET['searchText']);
              $errors = validateInputs(array('search by filter' => $searchText), array());

              if (count($errors) === 0) {

                $searchText = mysql_real_escape_string($searchText);
                if ($searchText !== 'all') {
                  $logSql .= "( LOWER(OPERATOR) like '%$searchText%' ) AND ";
                }
                $case = 1;
              }
            } if (isset($_GET['startDate']) || isset($_GET['endDate'])) {
              $inputArray =  array('startDate', 'endDate');
              $dateArray =  array();
              foreach ($inputArray as $value) {
                if(isset($_GET[$value])){
                  if (cleanInput($_GET[$value]) !== '' && !empty(cleanInput($_GET[$value]))) {

                    $dateVar = cleanInput($_GET[$value]);
                    $dateArray[$value] = $dateVar;
                  }

                }
              }
              $errors = validateInputs($dateArray, array());

              if (count($errors) === 0) {
                foreach ($dateArray as $date => $dateValue) {
                  $dateArray[$date] = mysql_real_escape_string($dateValue);
                }
                if (count($dateArray) === 2) {
                  $startDate = $dateArray['startDate'];
                  $endDate = $dateArray['endDate'];
                  $logSql .= "(to_date(to_char(tstamp, 'YYYY-Mon-DD'), 'YYYY-Mon-DD') >= to_date('$startDate','YYYY-MM-DD') AND to_date(to_char(tstamp, 'YYYY-Mon-DD'), 'YYYY-Mon-DD') <= to_date('$endDate','YYYY-MM-DD')) AND ";
                } elseif (count($dateArray) === 1) {
                  if (array_key_exists("startDate",$dateArray)) {
                    $startDate = $dateArray['startDate'];
                    $logSql .= "to_date(to_char(tstamp, 'YYYY-Mon-DD'), 'YYYY-Mon-DD') >= to_date('$startDate','YYYY-MM-DD')";
                  } elseif (array_key_exists("endDate",$dateArray)) {
                    $endDate = $dateArray['endDate'];
                    $logSql .= "to_date(to_char(tstamp, 'YYYY-Mon-DD'), 'YYYY-Mon-DD') <= to_date('$endDate','YYYY-MM-DD')) AND  ";
                  }
                }
              }

            }


            $logSqlInner = preg_replace('/WHERE\s*$/i', "", $logSql);
            $logSqlInner = preg_replace('/AND\s*$/i', "", $logSqlInner);
            $logSqlInner .= 'group by UPPER(OPERATOR)';
            $logSql = $logSqlInner;
          } else {

            $logSqlInner = "SELECT COUNT(ID),UPPER(OPERATOR) OPERATOR FROM ".$tempTable." WHERE to_date(to_char(tstamp, 'YYYY-Mon-DD'), 'YYYY-Mon-DD') = to_date(to_char(sysdate,'DD-Mon-YYYY'),'DD-Mon-YYYY') group by UPPER(OPERATOR)";

            $logSql = $logSqlInner;
            $case = 4;
          }
          $logsRes = count($errors) !== 0 ? 'errors' : oci_parse($conn,$logSql);

          oci_execute($logsRes);
          $nrows = oci_fetch_all($logsRes, $logs, null, null, OCI_FETCHSTATEMENT_BY_ROW);
          echo "<thead>
          <tr>
            <th>
            OPERATOR
            </th>
            <th>
            RECORD COUNT
            </th>
          </tr>
          </thead>";
          if ( is_array($logs) && $nrows > 0) {
              $number = 1;

              while($nrows > $number-1) {
                $logRow = $logs[$number-1];
                if ($number % 2 === 1) {
                  echo "<tr class='dark-primary-color  db-text'>
                  <td>".$logRow['OPERATOR']."</td>
                  <td>".number_format($logRow['COUNT(ID)'])."</td>
                  </tr>";
                }
                else {
                  echo "<tr>
                    <td>".$logRow['OPERATOR']."</td>
                    <td>".number_format($logRow['COUNT(ID)'])."</td>
                  </tr>";

                }
                $number += 1;
        }
              echo "</tbody>";

          } else {
            echo '<tbody><tr><td class="text-center" colspan="2"><p class="h1">No Records Found!</p></td></tr> </tbody>';
          }

          ?>
        </table>

      </div>
    </div>

    <script type="text/javascript">
      function swap(startDate, endDate){
        if (startDate && endDate) {
          let startDateVar = new Date(startDate);
          let endDateVar = new Date(endDate);
          if (startDateVar > endDateVar) {
            const tempVar = startDate;
            document.getElementById('startDate').value = endDate;
            document.getElementById('endDate').value = startDate;
          }
        }
      }
      function swapper() {
          swap(document.getElementById('startDate').value, document.getElementById('endDate').value)
      };
    </script>
    <script type="text/javascript">

    function search(){
      let urlVar = window.location.href;
      if (urlVar.includes('.php?')) {
        let base = '.php?';
        if (document.getElementsByName('searchText')[0].value) {
          base += `searchText=${document.getElementsByName('searchText')[0].value}&`;
        }

        if(document.getElementsByName('startDate')[0].value || document.getElementsByName('endDate')[0].value){
          base += `startDate=${document.getElementsByName('startDate')[0].value}&endDate=${document.getElementsByName('endDate')[0].value}`;
        }
        base = base.replace(/&$/, '');
        urlVar = urlVar.replace(/\.php\?.+/, base);
      } else {
        let base = '.php?';
        if (document.getElementsByName('searchText')[0].value) {
          base += `searchText=${document.getElementsByName('searchText')[0].value}&`;
        }
        if(document.getElementsByName('startDate')[0].value || document.getElementsByName('endDate')[0].value){
          base += `startDate=${document.getElementsByName('startDate')[0].value}&endDate=${document.getElementsByName('endDate')[0].value}`;
        }
        base = base.replace(/&$/, '');
        urlVar = urlVar.replace('.php', base);

      }
      window.location.replace(urlVar);
    }
    $(document).ready(
      const loader = document.getElementById('loader');
      loader.classList.remove('loader');

    );

    </script>
  </body>
</html>
