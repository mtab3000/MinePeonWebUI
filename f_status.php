<?php
/*
f_status gets values that people want to see in realtime
returns success, status data and errors
*/
header('Content-type: application/json');

include('cgminer.inc.php');

// Miner data
//$r['summary'] = cgminer('summary', '')['SUMMARY'];
$devs=cgminer('devs');
$pools=cgminer('pools');

if(!empty($devs['data']['DEVS'])){
  $r['status']['devs'] = $devs['data']['DEVS'];
}
if(!empty($pools['data']['POOLS'])){
  $r['status']['pools'] = $pools['data']['POOLS'];
  $r['status']['minerUp'] = true;
  $r['status']['minerDown'] = false;
}
else{
  $r['status']['minerUp'] = false;
  $r['status']['minerDown'] = true;
}

// Debug miner data
if(!empty($_REQUEST['dev']) && $r['status']['minerUp']){
  $r['status']['devs'][]=array('Name'=>'Hoeba','ID'=>0,'Temperature'=>rand(20,35),'MHS5s'=>rand(80000,100000),'MHSav'=>rand(90000,100000),'LongPoll'=>'N','Getworks'=>200,'Accepted'=>rand(70,200),'Rejected'=>rand(1,10),'HardwareErrors'=>rand(0,50),'Utility'=>1.2,'LastShareTime'=>time()-rand(0,10));
  $r['status']['devs'][]=array('Name'=>'Debug','ID'=>1,'Temperature'=>rand(20,35),'MHS5s'=>rand(40000,50000),'MHSav'=>rand(45000,50000),'LongPoll'=>'N','Getworks'=>1076,'Accepted'=>1324,'Rejected'=>1,'HardwareErrors'=>46,'Utility'=>1.2,'LastShareTime'=>time()-rand(0,40));
  $r['status']['devs'][]=array('Name'=>'Wut','ID'=>2,'Temperature'=>rand(20,35),'MHS5s'=>rand(6000,9000),'MHSav'=>rand(7000,8000),'LongPoll'=>'N','Getworks'=>1076,'Accepted'=>1324,'Rejected'=>1,'HardwareErrors'=>46,'Utility'=>1.2,'LastShareTime'=>time()-rand(0,300));
  $r['status']['devs'][]=array('Name'=>'More','ID'=>3,'Temperature'=>rand(20,35),'MHS5s'=>rand(500,1000),'MHSav'=>rand(600,800),'LongPoll'=>'N','Getworks'=>1076,'Accepted'=>1324,'Rejected'=>1,'HardwareErrors'=>46,'Utility'=>1.2,'LastShareTime'=>time()-rand(0,300));
  $r['status']['pools'][]=array('POOL'=>5,'URL'=>'http://stratum.mining.eligius.st:3334','Status'=>'Alive','Priority'=>9,'LongPoll'=>'N','Getworks'=>10760,'Accepted'=>50430,'Rejected'=>60,'Discarded'=>21510,'Stale'=>0,'GetFailures'=>0,'RemoteFailures'=>0,'User'=>'1BveW6ZoZmx31uaXTEKJo5H9CK318feKKY','LastShareTime'=>1375501281,'Diff1Shares'=>20306,'ProxyType'=>'','Proxy'=>'','DifficultyAccepted'=>20142,'DifficultyRejected'=>24,'DifficultyStale'=>0,'LastShareDifficulty'=>4,'HasStratum'=>true,'StratumActive'=>true,'StratumURL'=>'stratum.mining.eligius.st','HasGBT'=>false,'BestShare'=>40657);
}

$devices = 0;
$MHSav = 0;
$MHS5s = 0;
$Accepted = 0;
$Rejected = 0;
$HardwareErrors = 0;
$Utility = 0;

if(!empty($r['status']['devs'])){
  foreach ($r['status']['devs'] as $id => $dev) {
    if ($dev['MHS5s'] > 0) {
      $devices++;
      $MHS5s = $MHS5s + $dev['MHS5s'];
      $MHSav = $MHSav + $dev['MHSav'];
      $Accepted = $Accepted + $dev['Accepted'];
      $Rejected = $Rejected + $dev['Rejected'];
      $HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
      $Utility = $Utility + $dev['Utility'];
    }
    $r['status']['devs'][$id]['TotalShares']=$dev['Accepted']+$dev['Rejected']+$dev['HardwareErrors'];
  }
}

$r['status']['dtot']=array(
  'devices'=>$devices,
  'MHS5s'=>$MHS5s,
  'MHSav'=>$MHSav,
  'Accepted'=>$Accepted,
  'Rejected'=>$Rejected,
  'HardwareErrors'=>$HardwareErrors,
  'Utility'=>$Utility,
  'TotalShares'=>$Accepted+$Rejected+$HardwareErrors);

// CPU intensive stuff
if(!empty($_REQUEST['all'])){
  $r['status']['uptime'] = explode(' ', exec('cat /proc/uptime'));
  $r['status']['temp'] = exec('cat /sys/class/thermal/thermal_zone0/temp')/1000;
}

$r['status']['load'] = sys_getloadavg()[0];
$r['status']['time'] = time();

echo json_encode($r);
?>