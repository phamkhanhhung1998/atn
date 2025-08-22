<?php
require __DIR__.'/config.php';
function body($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

$name = body('name');
$date = body('date');          // yyyy-mm-dd
$photo_url = body('photo_url');
$wish_text = body('wish_text');
$bgm_on = body('bgm_on');      // 0/1

try{
  $stmt = $pdo->prepare(
    'UPDATE celebration
     SET name=COALESCE(?,name),
         date=COALESCE(?,date),
         photo_url=COALESCE(?,photo_url),
         wish_text=COALESCE(?,wish_text),
         bgm_on=COALESCE(?,bgm_on)
     WHERE id=1'
  );
  $stmt->execute([
    $name?:null, $date?:null, $photo_url?:null, $wish_text?:null,
    ($bgm_on!==null? (int)$bgm_on : null)
  ]);
  echo json_encode(['ok'=>true]);
}catch(Exception $e){
  http_response_code(500);
  echo json_encode(['error'=>'update failed']);
}
