<?php
require __DIR__.'/config.php';
try{
  $row = $pdo->query('SELECT * FROM celebration WHERE id=1 LIMIT 1')->fetch();
  if(!$row){ $row = ['name'=>null,'date'=>null,'photo_url'=>null,'wish_text'=>null,'bgm_on'=>0]; }
  echo json_encode($row);
}catch(Exception $e){
  http_response_code(500);
  echo json_encode(['error'=>'query failed']);
}
