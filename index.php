<?php
require_once('User.php');

class Employee extends User
{
    public $salary;

    public function __construct($name, $age, $birthday, $salary){
        parent::__construct($name, $age, $birthday);

        $this->salary = $salary;
    }

    public function getSalary()
    {
        return $this->salary;
    }

}

$employee = new Employee('Jon', 34, '1975-01-10', 3000);
echo $employee->getSalary();



include "./config.php";

$user = $_COOKIE['ssus'];

$result = $mysqli->query("SELECT SUM(dlit) AS allsumm FROM `logusers` WHERE `user` = '$user'");
$row = mysqli_fetch_array($result);
$time=$row['allsumm']/60;
echo "</br>$time минут к оплате";

echo '</br>';
echo '</br>';
echo "<b>Операторы на паузе</b>";
echo '</br>';

$output = shell_exec('asterisk -rx "queue show"| grep -e "paused" | grep -e "Not in use"| awk -F/ "{print $3}" |cut -d @ -f1 |tr -d "\n"');

$oparray = preg_split('/\s+/', trim($output));

for ($i = 0; $i <= count($oparray); $i++)
{
  echo substr($oparray[$i],6,4)."</br>";
}

echo "<b>Операторы в ожидании звонка</b>";
echo '</br>';
$output2 = shell_exec('asterisk -rx "queue show"| grep -e "dynamic" | grep -e "Not in use" | grep -v "paused" | awk -F/ "{print $3}" |cut -d @ -f1 |tr -d "\n"');

$oparray2 = preg_split('/\s+/', trim($output2));

for ($i = 0; $i <= count($oparray2); $i++)
{
  echo substr($oparray2[$i],6,4)."</br>";
}

echo "<b>Операторы в звонке</b>";
echo '</br>';
$output3 = shell_exec('asterisk -rx "queue show"| grep -e "In use"| awk -F/ "{print $3}" |cut -d @ -f1 |tr -d "\n"');
$oparray3 = preg_split('/\s+/', trim($output3));

for ($i = 0; $i <= count($oparray3); $i++)
{
  echo substr($oparray3[$i],6,4)."</br>";
}

echo '</br>';
echo "<b>Набираемых номеров на проекте Test2</b>";
echo '</br>';
$output5=shell_exec('ls /var/spool/asterisk/outgoing | grep "Test2" | wc -l | tr -d "\n"');
echo $output5;
echo '</br>';

?>
