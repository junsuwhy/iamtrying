<?php
//$a = array(1=>"foo", 2=>"bar", 3=>"baz", 4=>"blong");
//echo json_encode($a).'<br>';

class csa{
  public $count=0;
  public $sum=0;
  public $avg=0;
}


//自訂函式
function mylog($a){
  echo "<br>";
  echo "日誌 :  \$s=$a";
  echo "   type=".gettype($a).",print_r($a) : ";
  print_r($a);
}


function StrToArray($str){
  if($str{0}=="["){
    if($str{1}=="{"){
      $str2=substr($str, 2,strlen($str)-4);
      $arr=split("},{",$str2);

      foreach ($arr as $key => $value) {
        
        $arr[$key]=json_decode("{".$value."}");
        
      }
    }
    else{
      $str2=substr($str, 2,strlen($str)-4);
      $arr=split('","',$str2);
    }
  }
  if($str{0}=="{"){
    $obj=json_decode($str);
    $arr=get_object_vars($obj);
    

  }
  return $arr;

}




//秀字串
function ShowStr($var){
  //return (string)$var;

  if(gettype($var)=="integer"){
    return (string)$var;
  }
  
  if(gettype($var)=="array"){
    return ShowArray($var);
  }

  if(gettype($var)=='string'){
  }
  
}

function ShowArray($arr){



  $str='';
  $unit=each($arr);
  //foreach ($arr as $key => $value) {
  //  $str
  //}

  if(gettype($unit[1])=='object'){
    $arr2=array();
    $i=0;
    foreach ($arr as $key => $value) {
      $str2='"'.$key.'":{';
      $str2=$str2.'"count":'.(string)$value->count.',';
      $str2=$str2.'"sum":'.(string)$value->sum.',';
      $str2=$str2.'"avg":'.(string)$value->avg;
      $arr2[$i]=$str2;
      $i+=1;
    }

    $str='{'.implode('},',$arr2).'}}';



  }
  else if(gettype($unit[1])=='integer'){
    $str='{';
    $arr2=array();
    foreach ($arr as $key => $value) {
      $arr2[$key]='"'.$key.'":'.$value;
    }
    $str=$str.implode(',',$arr2);
    $str=$str.'}';

  }
  return $str;  
  
}

  

/*
  csa:count,sum,avg
  'civicrm_contact' => 'cc',  數字  
    'civicrm_contact.gender' => 'cc.g',     json or array[0:2]  ===> array
    'civicrm_contact.birth_date' => 'cc.b',   json
    'civicrm_address.province' => 'ca.p',   json
    'civicrm_contribution' => 'ccb',      int數字
    'civicrm_contribution.amount' => 'ccb.a', int數字
    'civicrm_contribution.hour' => 'ccb.h',   json{hour:{csa}...},有一個是array[{hour:{csa}}]  ==> array
    'civicrm_contribution.week' => 'ccb.w',   array[0,6]{csa},有兩個是json...QQ   ==> array
    'civicrm_contribution.range' => 'ccb.r',  json{"100":{count:xxx,sum:xxx,avg:xxx}...   ==>array
    'civicrm_mailing_event_delivered' => 'cmed',  int數字
    'civicrm_mailing_event_opened' => 'cmeo',     int數字
    'civicrm_mailing_event_opened.hour' => 'cmeo.h',  array[0,23] or json   ==>array
    'civicrm_mailing_event_opened.week' => 'cmeo.w',  array[0,6]        ===>array
    'civicrm_mailing_event_opened.num' => 'cmeo.n',   json 0,1,2.....
    'civicrm_mailing_event_trackable_url_open' => 'cmet',     X
    'civicrm_mailing_event_trackable_url_open.hour' => 'cmet.h',  array[0,23] or json   ==>array
    'civicrm_mailing_event_trackable_url_open.week' => 'cmet.w',  array[0,6] or json    ==>array
    'civicrm_mailing_event_trackable_url_open.num' => 'cmet.n',   json 0,1,2.....
    'civicrm_event' => 'ce',   數字
    'civicrm_participant' => 'cp',    數字
*/
  
    $index = array('cc',
              'cc.g',
              'cc.b',
              'ca.p',
              'ccb',
              'ccb.a',
              'ccb.h',
              'ccb.w',
              'ccb.r',
              'cmed',
              'cmeo',
              'cmeo.h',
              'cmeo.w',
              'cmeo.n',
              'cmet',
              'cmet.h',
              'cmet.w',
              'cmet.n',
              'ce',
              'cp');

    $total = array( 'cc' => 0,
            'cc.g' => array(),
            'cc.b' => array(),
            'ca.p' => array(),
            'ccb' => 0,
            'ccb.a' => 0,
            'ccb.h' => array(),
            'ccb.w' => array(),
            'ccb.r' => array(),
            'cmed' => 0,
            'cmeo' => 0,
            'cmeo.h' => array(),
            'cmeo.w' => array(),
            'cmeo.n' => array(),
            'cmet' => 0,
            'cmet.h' => array(),
            'cmet.w' => array(),
            'cmet.n' => array(),
            'ce' => 0,
            'cp' => 0  );

for ($i=0; $i < 3; $i++) { 
  array_push($total['cc.g'], 0);
}
for ($i=0; $i < 7; $i++) { 
  array_push($total['ccb.w'], new csa);
  array_push($total['cmeo.w'], 0);
  array_push($total['cmet.w'], 0);
}

for ($i=0; $i < 24; $i++) { 
  array_push($total['ccb.h'], new csa);
  array_push($total['cmeo.h'], 0);
  array_push($total['cmet.h'], 0);
}

//登入資料庫
$link = mysql_connect('localhost','junsuwhy','netivism#123');
if(!$link){
  echo "db die";
}

//選擇資料庫
mysql_select_db("neticrmtw");

//撈資料囉
$time=$_GET['date'];
if(!$time){
  $time="today";
}
$date=new DateTime($time);
$utstamp=$date->format('U'); 

$sql = "SELECT * FROM `neticrm_stat` WHERE `timestamp` = ".$utstamp;

$result = mysql_query($sql);
//撈出來的資料表

//撈出來每一筆資料
while ($row = mysql_fetch_assoc($result)) {
  //mylog($row['type']);
  //mylog($row['count']);

  switch ($row['type']) {
    case 'cc':
    case 'ccb':
    case 'ccb.a':
    case 'cmed':
    case 'cmeo':
    case 'ce':
    case 'cp':
      //數字
      $total[$row['type']]+=$row['count'];

      break;
    case 'ccb.h':
    case 'ccb.w':
    case 'ccb.r':

      //輸出的格式應該要是csa格式的array

      
      $arrow=StrToArray($row['count']); //要再把$row['count']轉化成array

      $arrTotal=$total[$row['type']];



      foreach ($arrow as $key => $value) {
        //echo "這是".$arrow[0];
        if(gettype($total[$row['type']][$key])=='NULL')$total[$row['type']][$key]=new csa;

        $total[$row['type']][$key]->count+=$arrow[$key]->count;
        $total[$row['type']][$key]->sum+=$arrow[$key]->sum;
        $total[$row['type']][$key]->avg+=$arrow[$key]->avg;
        
      }

      break;
    

    case 'cc.g':
      $strCount=$row['count'];
      $arrow=StrToArray($row['count']);
      if(count($arrow)==4){
        break;
      }
      elseif($strCount{0}=='['){
        $newString=substr($strCount, 1);
        $row['count']='["0",'.$newString;
      }

    case 'cc.b':
    case 'ca.p':

    case 'cmeo.h':  
    case 'cmeo.n':  
    case 'cmeo.w':

    case 'cmet.h': 
    case 'cmet.w':
    case 'cmet.n':  
    
          //輸出的格式應該要是integer的array
          //顯示的方式是json
      $arrow=StrToArray($row['count']); //要再把$row['count']轉化成array
      $arrTotal=$total[$row['type']];
      foreach ($arrow as $key => $value) {
        //echo "這是".$arrow[0];
        //$count=each($arrow);
        $total[$row['type']][$key]+=intval($value);
      }


      break;
    
      

    default:
      # code...
      break;
  }
    
}

//整理結果

//echo "time stamp:".$utstamp."<br>";
$total2=array();
foreach ($total as $key => $value) {
    $total2[$key]='"'.$key.'":'.ShowStr($value) ;
}
$str='{'.implode(',', $total2).'}';
print($str);

mysql_close($link);


?>
