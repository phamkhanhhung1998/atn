// Sakura petals
const petalCount = 18;
for (let i=0;i<petalCount;i++){
  const p=document.createElement('div');
  p.className='petal';
  const startX=Math.random()*100;
  const drift=(Math.random()*160-80)+'px';
  const duration=9+Math.random()*8;
  const delay=-Math.random()*duration;
  p.style.left=startX+'vw';
  p.style.animation=`fall ${duration}s linear ${delay}s infinite`;
  p.style.setProperty('--dx',drift);
  document.body.appendChild(p);
}

// Confetti
const canvas=document.getElementById('confetti');
const ctx=canvas.getContext('2d');
let confettiPieces=[];
const colors=['#ff4d6d','#5b8cff','#ffd166','#06d6a0','#8338ec'];
function resize(){ canvas.width=innerWidth; canvas.height=innerHeight }
addEventListener('resize',resize); resize();
function spawnConfetti(n=120){
  for(let i=0;i<n;i++){
    confettiPieces.push({
      x:Math.random()*canvas.width, y:-10,
      w:6+Math.random()*6, h:8+Math.random()*10,
      vx:-1+Math.random()*2, vy:2+Math.random()*3,
      r:Math.random()*360, vr:-6+Math.random()*12,
      color:colors[Math.floor(Math.random()*colors.length)], life:0
    });
  }
  burstSpark(); playSfx();
}
function burstSpark(){
  const s=document.createElement('div'); s.className='spark';
  s.style.left='50%'; s.style.top='46%'; document.body.appendChild(s);
  setTimeout(()=>s.remove(),1400);
}
function loop(){
  ctx.clearRect(0,0,canvas.width,canvas.height);
  confettiPieces = confettiPieces.filter(p=>p.y<canvas.height+20);
  for(const p of confettiPieces){
    p.x+=p.vx; p.y+=p.vy; p.r+=p.vr; p.life++;
    p.vx += Math.sin((p.life+p.h)*0.03)*0.02;
    ctx.save(); ctx.translate(p.x,p.y); ctx.rotate(p.r*Math.PI/180);
    ctx.fillStyle=p.color; ctx.fillRect(-p.w/2,-p.h/2,p.w,p.h); ctx.restore();
  }
  requestAnimationFrame(loop);
}
loop();

// Audio controls
const bgm=document.getElementById('bgm');
const sfx=document.getElementById('sfxConfetti');
const audioBtn=document.getElementById('btnAudio');
const audioIcon=document.getElementById('audioIcon');
const audioLabel=document.getElementById('audioLabel');
const audioStatus=document.getElementById('audioStatus');
let audioOn=false;

audioBtn.addEventListener('click', async ()=>{
  try{
    if(!audioOn){ await bgm.play(); audioOn=true; } else { bgm.pause(); audioOn=false; }
    renderAudio();
    fetch('api/update.php',{method:'POST', body: toForm({ bgm_on: audioOn?1:0 })}).catch(()=>{});
  }catch(e){ audioStatus.textContent='Trình duyệt chặn tự phát nhạc. Nhấn lại để phát.'; }
});
function renderAudio(){
  audioIcon.textContent = audioOn ? '🔊' : '🔈';
  audioLabel.textContent = audioOn ? 'Tắt nhạc' : 'Bật nhạc';
  audioStatus.textContent = audioOn ? 'Đang phát nhạc nền' : 'Đã tắt nhạc nền';
}
function playSfx(){ if(audioOn){ sfx.currentTime=0; sfx.play().catch(()=>{}); } }

// Helpers
const $=(id)=>document.getElementById(id);
function toForm(obj){ const fd=new FormData(); Object.entries(obj).forEach(([k,v])=>{ if(v!==undefined&&v!==null) fd.append(k,v); }); return fd; }
function toVNDate(iso){ const d=new Date(iso); const dd=String(d.getDate()).padStart(2,'0'); const mm=String(d.getMonth()+1).padStart(2,'0'); const yyyy=d.getFullYear(); return `${dd}/${mm}/${yyyy}`; }
function toISODate(vn){ const [dd,mm,yyyy]=vn.split('/'); if(!yyyy) return ''; return `${yyyy}-${mm}-${dd}`; }
function setAvatar(url){
  const a=$('avatar'); a.innerHTML='';
  const img=new Image(); img.alt='Ảnh tốt nghiệp';
  img.style.width='100%'; img.style.height='100%'; img.style.objectFit='cover';
  img.onload=()=>{ a.dataset.src=url; a.appendChild(img); };
  img.onerror=()=>{ a.textContent='Ảnh không hợp lệ'; };
  img.src=url;
}

// UI bindings
$('btnConfetti').addEventListener('click', ()=>spawnConfetti(180));
$('btnWish').addEventListener('click', ()=>$('wishModal').showModal());
$('closeWish').addEventListener('click', ()=>$('wishModal').close());
$('btnEdit').addEventListener('click', ()=>{
  $('inpName').value = $('name').textContent.replaceAll('[','').replaceAll(']','');
  const dd = $('date').textContent.includes('[')?'':toISODate($('date').textContent);
  $('inpDate').value = dd;
  $('inpPhoto').value = $('avatar').dataset.src || '';
  $('inpWish').value = $('wishText').textContent.trim();
  $('editModal').showModal();
});
$('closeEdit').addEventListener('click', ()=>$('editModal').close());
$('saveEdit').addEventListener('click', async ()=>{
  try{
    let photoURL = $('inpPhoto').value.trim();
    const file = $('inpFile').files[0];
    if(file){
      const fd = new FormData(); fd.append('file', file);
      const up = await fetch('api/upload.php',{method:'POST', body:fd});
      const j = await up.json(); if(j.url){ photoURL = j.url; }
    }
    if(photoURL) setAvatar(photoURL);
    const nm = $('inpName').value.trim(); if(nm) $('name').textContent = nm;
    const dt = $('inpDate').value; if(dt) $('date').textContent = toVNDate(dt);
    const wish = $('inpWish').value.trim(); if(wish){ $('wishText').textContent = wish; $('wishPreview').textContent = wish; }
    await fetch('api/update.php',{method:'POST', body: toForm({ name:nm, date:dt, photo_url:photoURL, wish_text:wish })});
  }catch(e){ console.warn(e); }
  $('editModal').close(); spawnConfetti(100);
});

// Guestbook
async function loadThanks(){
  try{
    const r=await fetch('api/thanks.php'); const j=await r.json();
    const list=$('thanksList'); list.innerHTML='';
    (j.items||[]).forEach(it=>{
      const div=document.createElement('div'); div.className='thank-item';
      const name=it.name?`<strong>${it.name}</strong>`:'Ẩn danh';
      div.innerHTML=`${name} · <small>${new Date(it.created_at).toLocaleString()}</small><br/>${escapeHtml(it.message)}`;
      list.appendChild(div);
    });
  }catch(e){ console.warn(e); }
}
function escapeHtml(str){ return str.replace(/[&<>"]/g, s=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s])); }
$('thanksSend').addEventListener('click', async ()=>{
  const name=$('thanksName').value.trim();
  const msg=$('thanksMsg').value.trim();
  if(!msg) return;
  try{
    await fetch('api/thanks.php',{ method:'POST', body: toForm({ name, message:msg }) });
    $('thanksMsg').value=''; loadThanks(); spawnConfetti(40);
  }catch(e){ console.warn(e); }
});

// Initial
(async function init(){
  setTimeout(()=>spawnConfetti(80), 900);
  try{
    const r=await fetch('api/get.php'); if(!r.ok) throw 0;
    const j=await r.json();
    if(j.name) $('name').textContent=j.name;
    if(j.date) $('date').textContent=toVNDate(j.date);
    if(j.photo_url) setAvatar(j.photo_url); else setAvatar('assets/default.jpg');
    if(j.wish_text){ $('wishText').textContent=j.wish_text; $('wishPreview').textContent=j.wish_text; }
    if(typeof j.bgm_on!=='undefined'){ audioOn=!!Number(j.bgm_on); renderAudio(); if(audioOn){ try{ await bgm.play(); }catch{} } }
  }catch(e){
    setAvatar('assets/default.jpg'); renderAudio();
  }
  loadThanks();
})();
