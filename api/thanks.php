<?php
require __DIR__.'/config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if($method === 'POST'){
  $name = trim($_POST['name'] ?? '');
  $msg  = trim($_POST['message'] ?? '');

  if($msg===''){ http_response_code(400); echo json_encode(['error'=>'empty']); exit; }
  if(strlen($name) > 100) $name = substr($name,0,100);
  if(strlen($msg)  > 1000) $msg  = substr($msg,0,1000);

  try{
    $stmt = $pdo->prepare('INSERT INTO thanks_messages(name, message) VALUES(?,?)');
    $stmt->execute([$name!==''?$name:null, $msg]);
    echo json_encode(['ok'=>true]);
  }catch(Exception $e){
    http_response_code(500); echo json_encode(['error'=>'insert failed']);
  }
  exit;
}

// GET: trả về danh sách 50 lời cảm ơn mới nhất
try{
  $stmt = $pdo->query('SELECT id, name, message, created_at FROM thanks_messages ORDER BY id DESC LIMIT 50');
  echo json_encode(['items' => $stmt->fetchAll()]);
}catch(Exception $e){
  http_response_code(500); echo json_encode(['error'=>'query failed']);
}
