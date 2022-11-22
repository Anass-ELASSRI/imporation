<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

if (isset($_POST['submit'])) {
    require_once 'database.php';

    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {

        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = new Csv();
        } else {
            $reader = new Xls();
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);

        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        print '<table>';
        if (!empty($sheetData)) {
            for ($i = 1; $i <= count($sheetData); $i++) { //skipping first row
                $matricule = $sheetData[$i][0];
                $nom = $sheetData[$i][1];
                $prenom = $sheetData[$i][2];
                $DNaissance = date("Y-m-d", strtotime($sheetData[$i][3]));
                $DEmbauche =  date("Y-m-d", strtotime($sheetData[$i][4]));
                $cin = $sheetData[$i][5];
                $cnss = $sheetData[$i][6];
                $mutuelle = $sheetData[$i][7];
                $sexe = $sheetData[$i][8] == 'M' ? 'man' : 'woman';
                $sf = $sheetData[$i][9] == "M" ? 1 : 2;
                $ne=$sheetData[$i][10];
                $fonction = $sheetData[$i][12];
                $categorie = $sheetData[$i][13];
                $site = $sheetData[$i][14];
                $rib = $sheetData[$i][15];
                $adresse = $sheetData[$i][16];

                print "<tr><td>$matricule</td><td>$nom</td><td>$prenom</td><td>$DNaissance</td><td>$DEmbauche</td>
                <td>$cin</td><td>$cnss</td><td>$mutuelle</td><td>$sexe</td><td>$sf</td><td>$fonction</td><td>$categorie</td>
                <td>$site</td><td>$rib</td><td>$adresse</td></tr>";

                $sql="INSERT INTO IG_user(lastname, firstname, login, admin, gender, employee, address, dateemployment, birth) 
                VALUES('$nom', '$prenom', '" . substr($prenom, 0, 1)[0] . $nom . "', 0, '$sexe', 1, '$adresse', '$DEmbauche', '$DNaissance')";
                // print $sql;
                // $db->query();
                if($db->query($sql)===TRUE){
                    $id=$db->insert_id;
                    print("<br> created user $nom with id ## $id");
                    
                    $sql1="INSERT INTO IG_Paie_UserInfo(userid, cnss, mutuelle, type) 
                    VALUES($id, '$cnss', '$mutuelle', 'mensuel')";

                    if($db->query($sql1)===TRUE){
                        print("<br> created cnss and mutuelle");
                    }

                    $sql2="INSERT INTO IG_user_extrafields(fk_object, status, situation, enfants, matricule, cin) 
                    VALUES($id, 1, $sf, $ne, '$matricule', '$cin')";

                    if($db->query($sql2)===TRUE){
                        print("<br> created matricule");
                    }
                }
               
            }
        }
        print '</table>';
        echo "Records inserted successfully.";
    } else {
        echo "Upload only CSV or Excel file.";
    }

    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" />
        <p><button type="submit" name="submit">Submit</button></p>
    </form>
</body>

</html>