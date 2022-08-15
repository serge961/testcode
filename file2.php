<?php
require_once 'config.php';
$user = $_COOKIE['ssus'];
$time = time();
$d = getdate();
$timeH = $d[hours];
$data = date("Y-m-d");

if (isset($_POST['STARTCAMP'])) {
    $startcamp = $_POST['STARTCAMP'];
    $sipl = $_POST['SIPL'];
    $campname = $_POST['CAMPNAME'];


    $result = $mysqli->query("SELECT * FROM `campaign` WHERE `quequenum`='$startcamp'");
    $row=mysqli_fetch_array($result);
    $startcampname = $row['campname'];

        $retry = $row['retry'];
        $pause = $row['pause'];
        $timeout = $row['timeout'];
        $concurrent = $row['concurrent']; // количество вызовов на 1 человека
        $limit = $row['limitcall']; //- количество одновременных вызовов, по идее должен расчитываться исходя из числа активных операторов и 
        $exten = $row['exten']; //-номер в контекст которого пойдет вызов например 8005
        $chan_context = $row['chan_context']; // контекст исходящего звонка
        $ext_context = $row['ext_context']; // контекст движения звонка при ответе.
        $quique = $row['quequenum']; // номер очереди
        $active = $row['active']; // активен проект или нет
        $rcall = $row['runcall']; //запущен процесс автодозвона или нет
        $timestrat = $row['timestart'];
        $timestop = $row['timestop'];
        $row = array();
        $user = $_COOKIE['ssus'];


if ($active == '1' and $timeH > $timestrat and $timeH < $timestop){
    $runcallst =  $mysqli->query("UPDATE `campaign` SET `runcall`= 1 WHERE `quequenum`='$quique'");
    $runcallus1 = $mysqli->query("INSERT INTO `runcall`(`campnum`, `user`) VALUES ('$quique','$user')");
    $insertlog = $mysqli->query("SELECT `id` FROM `runcamp` WHERE `numbercamp`='$startcamp' AND `namecamp`='$startcampname' AND `data`='$data'");
    $num = mysqli_num_rows($insertlog);
    if($num == ''){
    $insertlog = $mysqli->query("INSERT INTO `runcamp`(`numbercamp`, `namecamp`, `data`) VALUES ('$startcamp', '$startcampname', '$data')");
    };

    $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `end` = '$time', `dataend`=NOW(), `statusend`='INCOREXIT', `dlit`='-100', `data1`='$data' WHERE `user`='$user' AND `end` IS NULL");
    $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = `end`-`start` WHERE `user`='$user' AND `dlit` IS NULL");
    $insertlog = $mysqli->query("INSERT INTO `logusers_$campname` (`user`,`queue`,`date`,`start`,`status`,`data1`)  VALUES ('$user','$startcamp', NOW(), '$time', 'WAIT', '$data')");


    $output = shell_exec("asterisk -rx 'queue unpause member Local/$sipl@from-queue/n queue $quique'");
    $output2 = shell_exec("ps aux | grep $quique | wc -l");
    if($output2 == 2){
        $output3 = shell_exec('cd /var/lib/asterisk/agi-bin/ && ./'.$quique.'.sh '. $startcampname .' '. $timeout .' '. $concurrent .' '. $limit .' '. $exten .' '. $chan_context .' '. $ext_context .' '.$quique.'');

}else{
    $output1 = shell_exec("asterisk -rx 'queue remove member Local/$sipl@from-queue/n from $quique'");
    $output2 = shell_exec('pkill -9 '.$quique.'.sh');
};
   
};

    
    if (isset($_POST['STOPCAMP'])) {
      
        $campnum = $_POST['STOPCAMP'];
        $sipl = $_POST['SIPL'];
        $campname = $_POST['CAMPNAME'];

        $result = $mysqli->query("SELECT `campname` FROM `campaign` WHERE `quequenum` LIKE '$campnum'");
        $row=mysqli_fetch_array($result);
        $campname = $row['campname'];

        $output = shell_exec("asterisk -rx 'queue pause member Local/$sipl@from-queue/n queue $campnum'");

        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `status`= 'CALL1', `dataend`=NOW(), `end` = '$time', `statusend`='STOP', `data1`='$data' WHERE `user`='$user' AND `status`='CALL' AND `end` IS NULL");
        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dataend`=NOW(), `end` = '$time', `statusend`='STOP', `data1`='$data' WHERE `user`='$user' AND `end` IS NULL");
        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = `end`-`start` WHERE `user`='$user' AND `dlit` IS NULL");
        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = '600' WHERE `status`='WAIT' AND `statusend`='STOP' AND `user`='$user' AND `dlit`>'600'");

        $runcalldel = $mysqli->query("DELETE FROM `runcall` WHERE `user` = '$user' AND `campnum` = '$campnum'");
        $runcall3 = $mysqli->query("SELECT COUNT(id) AS id FROM `runcall` WHERE `campnum` = '$campnum'");
        $row3=mysqli_fetch_array($runcall3);
    
        if ($row3['id'] > 0) {

        }else{
            $runcallst =  $mysqli->query("UPDATE `campaign` SET `runcall`= '0' WHERE `quequenum`='$campnum'");
            $output11 = shell_exec('pkill -9 '.$campnum.'.sh');
        }
    };
       

    if (isset($_POST['CALL'])) {

        $campnum = $_POST['CALL'];
        $sipl = $_POST['SIPL'];
        $campname = $_POST['CAMPNAME'];

        $result = $mysqli->query("SELECT `campname` FROM `campaign` WHERE `quequenum` LIKE '$campnum'");
        $row=mysqli_fetch_array($result);
        $campname = $row['campname'];

        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dataend`=NOW(), `end` = '$time', `statusend`='CALL', `data1`='$data' WHERE `user`='$user' AND `end` IS NULL");
        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = `end`-`start` WHERE `user`='$user' AND `dlit` IS NULL");
        $insertlog = $mysqli->query("INSERT INTO `logusers_$campname` (`user`,`queue`,`date`,`start`,`status`,`data1`)  VALUES ('$user','$campnum', NOW(), '$time', 'CALL', '$data')");

        
    };

    if (isset($_POST['END'])) {

        $campnum = $_POST['END'];
        $sipl = $_POST['SIPL'];
        $campname = $_POST['CAMPNAME'];

        $result = $mysqli->query("SELECT `campname` FROM `campaign` WHERE `quequenum` LIKE '$campnum'");
        $row=mysqli_fetch_array($result);
        $campname = $row['campname'];

        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dataend`=NOW(), `end` = '$time', `statusend`='END', `data1`='$data' WHERE `user`='$user' AND `end` IS NULL");
        $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = `end`-`start` WHERE `user`='$user' AND `dlit` IS NULL");
        $insertlog = $mysqli->query("INSERT INTO `logusers_$campname` (`user`,`queue`,`date`,`start`,`status`, `data1`)  VALUES ('$user','$campnum', NOW(), '$time', 'END', '$data')");

        $output = shell_exec("asterisk -rx 'queue pause member Local/$sipl@from-queue/n queue $campnum'");
    };
    
    if (isset($_POST['RECALL'])) {

        $campnum = $_POST['RECALL'];
        $sipl = $_POST['SIPL'];
        $campname = $_POST['CAMPNAME'];
      
    $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dataend`=NOW(), `end` = '$time', `statusend`='INCOREXIT', `dlit`='-100',`data1`='$data' WHERE `user`='$user' AND `end` IS NULL");
    $insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = `end`-`start` WHERE `user`='$user' AND `dlit` IS NULL");
    $insertlog = $mysqli->query("INSERT INTO `logusers_$campname` (`user`,`queue`,`date`,`start`,`status`,`data1`)  VALUES ('$user','$campnum', NOW(), '$time', 'WAIT', '$data')");


};

if (isset($_POST['ECHOTEST'])) {

    $campnum = $_POST['ECHOTEST'];
    $sipl = $_POST['SIPL'];
    $campname = $_POST['CAMPNAME'];

    $result = $mysqli->query("SELECT `campname` FROM `campaign` WHERE `quequenum` LIKE '$campnum'");
        $row=mysqli_fetch_array($result);
        $campname = $row['campname'];
        
$insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dataend`=NOW(), `end` = '$time', `statusend`='INCOREXIT', `dlit`='-100',`data1`='$data' WHERE `user`='$user' AND `end` IS NULL");
$insertlog = $mysqli->query("UPDATE `logusers_$campname` SET  `dlit` = `end`-`start` WHERE `user`='$user' AND `dlit` IS NULL");
$insertlog = $mysqli->query("INSERT INTO `logusers_$campname` (`user`,`queue`,`date`,`start`,`status`,`data1`)  VALUES ('$user','$campnum', NOW(), '$time', 'ECHOTEST', '$data')");


};
    ?>
