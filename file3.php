<link href="css/style.css" rel="stylesheet">
<?php
require_once './config.php';
include "./topmenu.php";
?>
<div class="main">
<form method="POST" action="newoperstat.php">
  <fieldset>
    <legend>Выберите период для анализа:</legend>
      <p>Дату начала и дату окончания</p>
      <p><label for="dataStart">Дата начала:</label><input name = "dataStart" type="date" id="dataStart"></p>
      <p><label for="dataEnd">Дата окончания:</label><input name = "dataEnd" type="date" id="dataEnd"></p>
  </fieldset>
    <p><input type="submit" value="Отправить"></p>
</form>

<?php

$datastart = $_POST['dataStart'];
$dataend = $_POST['dataEnd'];

$result01 = $mysqli->query("SELECT `date_reg`, COUNT(id) AS `num` FROM `opers_new` WHERE `date_reg` BETWEEN '$datastart' AND '$dataend' GROUP BY `date_reg`");

$total = 0;
while ($row01 = mysqli_fetch_array($result01)) {
    $num = $row01['num'];
    $data_reg = $row01['date_reg'];

echo $data_reg.' - '.$num.'<br>';
$total = $total+$num;
}
if ($datastart != '') echo "Всего: $total";
echo "<br><br>Статистика по каналам продвижения:<br>";
$result01 = $mysqli->query("SELECT `date_reg`, COUNT(id) AS `num`, `ist` FROM `opers_new` WHERE `date_reg` BETWEEN '$datastart' AND '$dataend' GROUP BY `ist`");
while ($row01 = mysqli_fetch_array($result01)) {
  $ist = $row01['ist'];
  $num = $row01['num'];
if($ist==0){$ist='Нет информации';}
if($ist==1){$ist='Объявление ЯД';}
if($ist==2){$ist='Лендинг';}
if($ist==3){$ist='РаботаРу';}
if($ist==4){$ist='ХХРу';}
if($ist==5){$ist='Авито';}
if($ist==6){$ist='ВоркиРу';}
if($ist==7){$ist='Рассылка';}
echo $ist.' - '.$num.'<br>';

}
?>
