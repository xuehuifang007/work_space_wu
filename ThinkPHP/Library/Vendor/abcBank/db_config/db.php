<?php
$con=mysqli_connect("wuliubang.mysql.rds.aliyuncs.com","wuliubang","xy990622","wuliubang");
if (mysqli_connect_errno($con)){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}else{
    echo '连接数据库成功<br>';
}
mysqli_query($con,'set names utf8');
