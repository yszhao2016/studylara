<?php
ini_set("max_execution_time", "0");
ini_set("memory_limit","512M");
$filePath = 'time.log';
if (!file_exists($filePath)) { //第一次从前一天开始跑
    file_put_contents('time.log', strtotime("20180701"));
}

$filetime = file_get_contents('time.log');
$operator = [
    1 => "China Mobile",
    2 => "China Unicom",
    3 => "China Telecom"
];
$tns = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=127.0.0.1)(PORT=1521)))(CONNECT_DATA=(SERVER=DEDICATED)(SID=SMGW)))";
$db_username = "smgw";
$db_password = "smgw!2#";
try {
    $oracleconn = new \PDO("oci:dbname=" . $tns, $db_username, $db_password);
    $mysqlconn = new PDO('mysql:host=82.157.28.150;dbname=tel_summary', 'zys', 'tczaf0w');
} catch (PDOException $e) {
    echo($e->getMessage());
}
$tablePrefixName = 'LG_SM_';
$time = $filetime;


while ($time < time()) {
    $tableName = $tablePrefixName . date('Ymd', intval($time));
    $pagenum = 10;
    $page = 1;
    do
    {
        $sql = "SELECT TO_CHAR(CREATE_TIME,'YYYY-MM-DD HH:mi:ss') FROM (SELECT tt.*, ROWNUM AS rowno FROM (SELECT t.* FROM ".$tableName ." t where TO_CHAR(CREATE_TIME,'YYYY-MM-DD HH:mi:ss')>='" . date("Y-m-d H:i:s", intval($time))."'"."  ORDER BY create_time ASC) tt WHERE ROWNUM <= ".
            $page*$pagenum.") table_alias WHERE table_alias.rowno >=".($page-1)*$pagenum;
        $sth = $oracleconn->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        $num = count($result);
        if(!$num){
            break;
        }
        $sql = "INSERT sms_mobile_info (mobile,province,time,operator,created_at,updated_at) VALUES  ";

        foreach ($result as $arr) {
            $temptime = strtotime($arr['CREATE_TIME']);
            $temptime = time();
            $nowTime = time();
            $operatorName = isset($operator[$arr['MOBILE_TYPE']]) ? $operator[$arr['MOBILE_TYPE']] : "China Mobile";
            $province = getProvince($arr['PROVINCE']);
            $sql .= "('{$arr['MOBILE']}','{$province}',{$temptime},'{$operatorName}',{$nowTime},{$nowTime}),";
        }
        $sql = rtrim($sql, ',');

        $page++;
        $rest = $mysqlconn->exec($sql);
        var_dump($mysqlconn->errorInfo());
        file_put_contents('time.log', $sql);exit;

    }while($num);
    $time += 3600 * 24;
    file_put_contents('time.log', $time);

}


function getProvince($name)
{
    $shi = ['上海', '重庆', '北京', '天津',];
    $zizhiqu = ['新疆', '广西', '宁夏', '西藏', '内蒙古'];
    if (in_array($name, $shi)) {
        $res = $name . "市";
    } else if (in_array($name, $zizhiqu)) {
        if(strpos('广西壮族自治区',$name)){
            $res = "广西壮族自治区";
        }else if(strpos('宁夏回族自治区',$name)){
            $res = "宁夏回族自治区";
        }else if(strpos('新疆维吾尔自治区',$name)){
            $res = "新疆维吾尔自治区";
        }else{
            $res = $name . "自治区";
        }
    } else {
        $res = $name . "省";
    }
    return $res;
}
