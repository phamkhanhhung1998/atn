<?php // index.php - trang giao diện chính (responsive + no jump on scroll) ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title>Chúc mừng tốt nghiệp 🎓</title>
  <style>
    :root{
      --bg1:#fff7f9; --bg2:#eef6ff; --accent:#ff4d6d; --accent2:#5b8cff; --ink:#222; --muted:#5b5b5b; --card:#ffffffcc; --shadow:0 10px 30px rgba(0,0,0,.08);
      --vh: 1vh; /* sẽ được cập nhật bằng JS để chống jump trên mobile */
      --safe-top: env(safe-area-inset-top, 0px);
      --safe-bottom: env(safe-area-inset-bottom, 0px);
      --safe-left: env(safe-area-inset-left, 0px);
      --safe-right: env(safe-area-inset-right, 0px);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Noto Sans,"Helvetica Neue",Arial;
      color:var(--ink);
      background:
        radial-gradient(1200px 800px at 10% 0%, var(--bg1), transparent 60%),
        radial-gradient(1000px 700px at 90% 10%, var(--bg2), transparent 60%),
        linear-gradient(180deg, #ffffff, #f7fbff);
      overflow-x:hidden;
    }

    /* Container chống "trôi" nhờ dùng 100dvh + --vh fallback */
    .container{
      min-height:100dvh; /* trình duyệt hiện đại */
      min-height:calc(var(--vh,1vh)*100); /* fallback + fix iOS address bar */
      display:grid; place-items:center;
      padding:32px;
      padding-top:calc(32px + var(--safe-top));
      padding-bottom:calc(32px + var(--safe-bottom));
      padding-left:calc(24px + var(--safe-left));
      padding-right:calc(24px + var(--safe-right));
    }

    .card{
      width:min(960px,92vw);
      background:var(--card);
      border-radius:24px;
      box-shadow:var(--shadow);
      backdrop-filter:blur(10px);
      border:1px solid rgba(255,255,255,.6);
      position:relative; overflow:hidden;
      margin:auto; /* luôn giữa */
    }

    .ribbon{position:absolute; inset:auto -10% -10% auto; width:220px; height:220px; transform:rotate(25deg);
      background:linear-gradient(135deg,var(--accent),var(--accent2)); filter:blur(40px); opacity:.15; pointer-events:none; transition:.3s opacity}
    header{padding:48px 32px 16px; text-align:center; position:relative}

    .badge{display:inline-flex; align-items:center; gap:10px; padding:8px 14px; border-radius:999px;
      background:rgba(255,255,255,.75); box-shadow:var(--shadow); border:1px solid rgba(0,0,0,.05); font-size:14px; color:#444}

    .hero{display:flex; flex-direction:column; align-items:center; gap:16px; padding:24px 24px 12px}
    .avatar{width:140px; height:140px; border-radius:16px; overflow:hidden; border:4px solid #fff; box-shadow:var(--shadow); display:grid; place-items:center; color:#999; font-weight:600; background:#fff}
    .avatar img{width:100%; height:100%; object-fit:cover; display:block}
    .title{font-size:clamp(28px,4vw,44px); line-height:1.1; margin:0; letter-spacing:.2px}
    .subtitle{margin:6px 0 0; color:var(--muted)}
    .korean{font-size:clamp(16px,2.2vw,22px); color:#333; opacity:.9}

    .actions{display:flex; gap:12px; flex-wrap:wrap; justify-content:center; padding:20px}
    .btn{padding:12px 18px; border-radius:12px; border:0; cursor:pointer; font-weight:600; font-size:15px; box-shadow:var(--shadow); transition:.2s transform,.2s box-shadow; touch-action:manipulation}
    .btn.primary{background:linear-gradient(90deg,var(--accent),var(--accent2)); color:#fff}
    .btn.ghost{background:#fff; color:#333; border:1px solid rgba(0,0,0,.08)}
    .btn:hover{transform:translateY(-1px); box-shadow:0 14px 32px rgba(0,0,0,.12)}
    .icon-btn{border-radius:999px; width:44px; height:44px; display:inline-grid; place-items:center; font-size:20px; padding:0}

    .grid{display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:16px; padding:0 24px 36px}
    .cardlet{background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:16px; padding:20px; box-shadow:var(--shadow)}
    .cardlet h3{margin:0 0 6px; font-size:18px}
    .cardlet p{margin:0; color:var(--muted); font-size:14px}
    footer{padding:14px 24px 28px; text-align:center; color:#666; font-size:13px}

    /* rơi cánh hoa */
    .petal{position:fixed; top:-10vh; width:16px; height:14px; background:radial-gradient(40% 50% at 40% 50%, #ffd0db, #ff9ab3 60%, #ff6f90);
      border-radius:70% 30% 70% 30% / 70% 30% 70% 30%; filter:drop-shadow(0 6px 6px rgba(255,0,76,.15)); opacity:.8; pointer-events:none; z-index:0}
    @keyframes fall{0%{transform:translateY(-10vh) translateX(0) rotate(0)}100%{transform:translateY(110vh) translateX(var(--dx,0)) rotate(360deg)}}

    #confetti{position:fixed; inset:0; pointer-events:none}

    dialog{border:0; border-radius:16px; padding:0; box-shadow:var(--shadow); max-width:min(680px,92vw)}
    .modal{background:#fff; border-radius:16px; overflow:hidden}
    .modal header{padding:20px 24px; text-align:left; border-bottom:1px solid rgba(0,0,0,.06)}
    .modal .content{padding:20px 24px; color:#444; line-height:1.6}
    .modal footer{padding:16px 24px; border-top:1px solid rgba(0,0,0,.06)}
    .close{padding:10px 14px; border-radius:10px; border:1px solid rgba(0,0,0,.1); background:#fff; cursor:pointer}

    .spark{position:absolute; width:10px; height:10px; background:conic-gradient(from 0deg,var(--accent),var(--accent2)); border-radius:50%; filter:blur(6px); animation:spark 1.4s ease-out forwards}
    @keyframes spark{to{transform:scale(18); opacity:0}}
    .audioStatus{font-size:13px; color:#555}

    .thanks{padding:0 24px 32px}
    .thanks form{display:grid; gap:10px; grid-template-columns:1fr auto}
    .thanks input,.thanks textarea{width:100%; padding:10px; border:1px solid #ddd; border-radius:10px}
    .thanks button{padding:10px 14px; border-radius:10px; border:0; background:#111; color:#fff; cursor:pointer}
    .thanks-list{margin-top:14px; display:grid; gap:10px}
    .thank-item{background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:12px; padding:12px}

    /* ⭐ Starry mode – nền tối + chữ sáng, tương phản cao */
    body.starry{
      color:#f6f9ff;
      background:
        radial-gradient(1200px 800px at 10% 0%, #0e1630, transparent 60%),
        radial-gradient(1000px 700px at 90% 10%, #0b0f1f, transparent 60%),
        linear-gradient(180deg, #0b0f1f, #0f1b3a);
    }
    body.starry .card{background:rgba(255,255,255,.10); border-color:rgba(255,255,255,.24); color:#f6f9ff}
    body.starry .cardlet{background:rgba(255,255,255,.10); border-color:rgba(255,255,255,.24); color:#f6f9ff}
    body.starry h1, body.starry h3, body.starry p, body.starry .subtitle, body.starry .korean, body.starry footer{color:#f6f9ff; text-shadow:0 1px 2px rgba(0,0,0,.35)}
    body.starry .badge{background:rgba(255,255,255,.22); color:#ffffff}
    body.starry .btn.ghost{background:rgba(255,255,255,.18); color:#ffffff; border-color:rgba(255,255,255,.28)}
    body.starry .thanks input, body.starry .thanks textarea{ background:rgba(255,255,255,.12); color:#ffffff; border-color:rgba(255,255,255,.28) }
    body.starry .thanks input::placeholder, body.starry .thanks textarea::placeholder{color:#dbe4ff}
    body.starry .ribbon{opacity:0}
    /* Lời cảm ơn tương phản cao khi bầu trời đêm */
    body.starry .thank-item{
      background:rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.28);
      color:#f6f9ff;
      text-shadow:0 1px 2px rgba(0,0,0,.55);
    }

    /* Lớp sao nền */
    .stars{position:fixed; inset:0; pointer-events:none; z-index:0}
    .star{position:absolute; width:2px; height:2px; background:#fff; border-radius:50%;
      opacity:.95; animation:twinkle 1.8s ease-in-out infinite alternate; box-shadow:0 0 8px rgba(255,255,255,.8)}
    @keyframes twinkle{to{opacity:.35; transform:scale(0.7)}}

    /* ⭐ Sao băng */
    .meteor{
      position:fixed; top:-10vh; left:-10vw; width:2px; height:2px; background:#fff; border-radius:50%;
      box-shadow:0 0 10px rgba(255,255,255,.9), 0 0 20px rgba(120,180,255,.85), 0 0 35px rgba(120,180,255,.7);
      opacity:0.95; pointer-events:none; z-index:1; transform:translate3d(0,0,0) rotate(45deg);
    }
    .meteor::after{content:""; position:absolute; right:0; top:50%; transform:translateY(-50%);
      width:160px; height:2px; background:linear-gradient(90deg, rgba(255,255,255,.85), rgba(120,180,255,.6), rgba(0,0,0,0)); filter:blur(0.6px); }
    @keyframes shoot{ 0%{ transform:translate3d(0,0,0) rotate(45deg); opacity:0 } 5%{ opacity:1 } 100%{ transform:translate3d(140vw,110vh,0) rotate(45deg); opacity:0 } }

    /* 📱 Responsive tweaks */
    @media (max-width: 640px){
      .container{padding:20px; padding-top:calc(16px + var(--safe-top)); padding-bottom:calc(20px + var(--safe-bottom))}
      header{padding:28px 16px 8px}
      .actions{padding:14px; gap:10px}
      .btn{padding:10px 14px; font-size:14px}
      .grid{padding:0 14px 24px; gap:12px}
      .cardlet h3{font-size:16px}
      .cardlet p{font-size:13px}
      .avatar{width:120px; height:120px}
    }

    /* Người dùng giảm chuyển động */
    @media (prefers-reduced-motion: reduce){
      .petal, .star, .meteor, .spark{animation: none !important}
    }
  </style>
</head>
<body>
  <canvas id="confetti"></canvas>
  <audio id="bgm" src="assets/bgm.mp3" preload="auto" loop></audio>
  <audio id="sfxConfetti" src="assets/confetti.mp3" preload="auto"></audio>

  <div class="container">
    <div class="card" role="article" aria-label="Thiệp chúc mừng tốt nghiệp">
      <div class="ribbon" aria-hidden="true"></div>

      <header>
        <span class="badge">🇰🇷 <i>졸업 축하해요!</i> · Chúc mừng tốt nghiệp 🎓</span>
        <div class="hero">
          <div class="avatar" id="avatar" aria-label="Ảnh tốt nghiệp">
            <img id="avatarImg" src="assets/default.jpg" alt="Ảnh tốt nghiệp">
          </div>
          <h1 class="title">Chúc mừng <span id="name">[Tên bạn nữ]</span> đã tốt nghiệp!</h1>
          <p class="subtitle">Từ Việt Nam đến Hàn Quốc – Gửi lời chúc mừng tới em 💙</p>
          <div class="korean">오늘까지 달려온 너의 노력에 큰 박수를 보낼게요!</div>
        </div>
      </header>

      <section class="actions">
        <button class="btn primary" id="btnConfetti">Thả pháo giấy</button>
        <button class="btn ghost" id="btnWish">Đọc lời chúc</button>
        <button class="btn ghost" id="btnEdit">Chỉnh tên & ảnh</button>
        <button class="btn ghost" id="btnAudio"><span id="audioIcon">🔈</span> <span id="audioLabel">Bật nhạc</span></button>
        <button class="btn ghost icon-btn" id="btnSakura" title="Đổi nền 🌸/⭐">🌸</button>
        <span class="audioStatus" id="audioStatus" aria-live="polite"></span>
      </section>

      <section class="grid" aria-label="Những cột mốc nhỏ">
        <div class="cardlet">
          <h3>🎓 Khoảnh khắc đáng nhớ</h3>
          <p>Ngày tốt nghiệp: <span id="date">[DD/MM/YYYY]</span></p>
        </div>
        <div class="cardlet">
          <h3>📍 Nơi đánh dấu</h3>
          <p>Seoul, Korea (대한민국)</p>
        </div>
        <div class="cardlet">
          <h3>💌 Lời nhắn</h3>
          <p id="wishPreview">Luôn giữ nụ cười và mơ ước thật lớn nhé!</p>
        </div>
        <div class="cardlet">
          <h3> Biểu tượng</h3>
          <p id="symbolText">Sakura rơi là chương mới bắt đầu.</p>
        </div>
      </section>

      <section class="thanks" aria-label="Lời cảm ơn">
        <h3>🙏 Để lại lời cảm ơn</h3>
        <form id="thanksForm" onsubmit="return false">
          <input id="thanksName" placeholder="Tên của em (tuỳ chọn)" />
          <textarea id="thanksMsg" rows="2" placeholder="Lời comment..."></textarea>
          <button id="thanksSend">Gửi</button>
        </form>
        <div class="thanks-list" id="thanksList"></div>
      </section>

      <footer>Tạo bởi <strong>người bạn bí mật</strong> · Với thật nhiều yêu thương ✨</footer>
    </div>
  </div>

  <!-- Modal: wish -->
  <dialog id="wishModal">
    <div class="modal">
      <header><strong>💌 Lời chúc gửi đến cậu</strong></header>
      <div class="content" id="wishText">Chúc mừng cậu đã hoàn thành một chặng đường tuyệt đẹp!... ✨</div>
      <footer><button class="close" id="closeWish">Đóng</button></footer>
    </div>
  </dialog>

  <!-- Modal: edit -->
  <dialog id="editModal">
    <div class="modal">
      <header><strong>✏️ Chỉnh sửa nhanh</strong></header>
      <div class="content">
        <label>Họ tên: <input id="inpName" placeholder="Nhập tên" /></label>
        <div style="height:12px"></div>
        <label>Ngày tốt nghiệp: <input id="inpDate" type="date" /></label>
        <div style="height:12px"></div>
        <label>Ảnh (URL): <input id="inpPhoto" placeholder="Dán link ảnh (tùy chọn)" /></label>
        <div style="height:12px"></div>
        <label>Hoặc tải ảnh mới: <input id="inpFile" type="file" accept="image/png,image/jpeg" /></label>
        <div style="height:12px"></div>
        <label>Lời chúc: <textarea id="inpWish" rows="4"></textarea></label>
      </div>
      <footer style="display:flex; gap:10px; justify-content:flex-end">
        <button class="close" id="saveEdit">Lưu</button>
        <button class="close" id="closeEdit">Hủy</button>
      </footer>
    </div>
  </dialog>

  <script>
    /* ======= Fix 100vh trên mobile để card không "trôi" khi kéo ======= */
    function setVH(){
      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    setVH();
    window.addEventListener('resize', setVH);
    window.addEventListener('orientationchange', setVH);

    // ======= Sakura petals =======
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

    // ======= Confetti + SFX =======
    const canvas=document.getElementById('confetti');
    const ctx=canvas.getContext('2d');
    let confettiPieces=[];
    const colors=['#ff4d6d','#5b8cff','#ffd166','#06d6a0','#8338ec'];
    function resize(){ canvas.width=innerWidth; canvas.height=innerHeight }
    addEventListener('resize',resize); resize();
    function spawnConfetti(n=120){
      for(let i=0;i<n;i++){
        confettiPieces.push({x:Math.random()*canvas.width,y:-10,w:6+Math.random()*6,h:8+Math.random()*10,vx:-1+Math.random()*2,vy:2+Math.random()*3,r:Math.random()*360,vr:-6+Math.random()*12,color:colors[Math.floor(Math.random()*colors.length)],life:0});
      }
      burstSpark();
      playSfx(true); // luôn phát tiếng pháo khi bấm
    }
    function burstSpark(){ const s=document.createElement('div'); s.className='spark'; s.style.left='50%'; s.style.top='46%'; document.body.appendChild(s); setTimeout(()=>s.remove(),1400); }
    (function loop(){ ctx.clearRect(0,0,canvas.width,canvas.height);
      confettiPieces=confettiPieces.filter(p=>p.y<canvas.height+20);
      for(const p of confettiPieces){
        p.x+=p.vx; p.y+=p.vy; p.r+=p.vr; p.life++;
        p.vx+=Math.sin((p.life+p.h)*0.03)*0.02;
        ctx.save(); ctx.translate(p.x,p.y); ctx.rotate(p.r*Math.PI/180);
        ctx.fillStyle=p.color; ctx.fillRect(-p.w/2,-p.h/2,p.w,p.h); ctx.restore();
      }
      requestAnimationFrame(loop);
    })();

    // ======= Audio controls =======
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
        fetch('api/update.php',{method:'POST', body:toForm({ bgm_on: audioOn?1:0 })}).catch(()=>{});
      }catch(e){
        audioStatus.textContent='Trình duyệt chặn tự phát nhạc. Hãy nhấn lần nữa nhé.';
      }
    });
    function renderAudio(){
      audioIcon.textContent= audioOn?'🔊':'🔈';
      audioLabel.textContent= audioOn?'Tắt nhạc':'Bật nhạc';
      audioStatus.textContent= audioOn?'Đang phát nhạc nền':'Đã tắt nhạc nền';
    }
    function playSfx(force=false){
      try{ if(force || audioOn){ sfx.currentTime=0; sfx.play().catch(()=>{}); } }catch(e){}
    }

    // ======= Helpers =======
    const $=(id)=>document.getElementById(id);
    function toForm(obj){ const fd=new FormData(); Object.entries(obj).forEach(([k,v])=>{ if(v!==undefined && v!==null) fd.append(k,v); }); return fd; }
    function toVNDate(iso){ const d=new Date(iso); const dd=String(d.getDate()).padStart(2,'0'); const mm=String(d.getMonth()+1).padStart(2,'0'); const yyyy=d.getFullYear(); return `${dd}/${mm}/${yyyy}`; }
    function toISODate(vn){ const [dd,mm,yyyy]=vn.split('/'); if(!yyyy) return ''; return `${yyyy}-${mm}-${dd}`; }
    function setAvatar(url){
      const img=document.getElementById('avatarImg');
      const bust = (url.includes('?') ? '&' : '?') + 't=' + Date.now();
      img.onerror = ()=>{ console.warn('Không tải được ảnh:', url); img.src='assets/default.jpg'; };
      img.onload  = ()=>{ document.getElementById('avatar').dataset.src = url; };
      img.src = url + bust;
    }

    // ======= UI bindings =======
    $('btnConfetti').addEventListener('click', ()=>spawnConfetti(180));
    $('btnWish').addEventListener('click', ()=>$('wishModal').showModal());
    $('closeWish').addEventListener('click', ()=>$('wishModal').close());
    $('btnEdit').addEventListener('click', ()=>{
      $('inpName').value=$('name').textContent.replaceAll('[','').replaceAll(']','');
      const dd=$('date').textContent.includes('[')?'':toISODate($('date').textContent);
      $('inpDate').value=dd;
      $('inpPhoto').value=$('avatar').dataset.src||'';
      $('inpWish').value=$('wishText')?.textContent.trim() || '';
      $('editModal').showModal();
    });
    $('closeEdit').addEventListener('click', ()=>$('editModal').close());

    $('saveEdit').addEventListener('click', async ()=>{
      try{
        let photoURL=$('inpPhoto').value.trim();
        const file=$('inpFile').files[0];
        if(file){
          const fd=new FormData(); fd.append('file',file);
          const up=await fetch('api/upload.php',{method:'POST', body:fd});
          const j=await up.json();
          if(j.url){ photoURL=j.url; }
        }
        if(photoURL) setAvatar(photoURL);

        const nm=$('inpName').value.trim(); if(nm) $('name').textContent=nm;
        const dt=$('inpDate').value; if(dt) $('date').textContent=toVNDate(dt);
        const wish=$('inpWish').value.trim();
        if(wish){ const wt=$('wishText'); if(wt) wt.textContent=wish; $('wishPreview').textContent=wish; }

        await fetch('api/update.php',{method:'POST', body:toForm({ name:nm, date:dt, photo_url:photoURL, wish_text:wish })});
      }catch(e){ console.warn(e); }
      $('editModal').close();
      spawnConfetti(100);
    });

    // ======= Starry toggle + Meteor =======
    const sakuraBtn = $('btnSakura');
    let starLayer = null, starry = false, meteorTimer = null;
    const symbolDefault = 'Sakura rơi là chương mới bắt đầu.';
    const symbolStarry  = 'Sao hôm nay cũng đẹp ấy nhỉ.';
    sakuraBtn.addEventListener('click', toggleStarry);

    function toggleStarry(){
      starry = !starry;
      document.body.classList.toggle('starry', starry);
      document.querySelector('.ribbon').style.opacity = starry ? '0' : '0.15';
      const sym = $('symbolText'); if(sym) sym.textContent = starry ? symbolStarry : symbolDefault;
      sakuraBtn.textContent = starry ? '⭐' : '🌸';
      if(starry){ if(!starLayer){ createStarfield(); } startMeteorShower(); }
      else{ clearStarfield(); stopMeteorShower(); }
    }
    function createStarfield(){
      starLayer = document.createElement('div'); starLayer.className = 'stars';
      const area = innerWidth * innerHeight;
      const n = Math.max(400, Math.min(1000, Math.floor(area / 2500)));
      for(let i=0;i<n;i++){
        const s=document.createElement('div'); s.className='star';
        const size = (1.5 + Math.random()*1.7).toFixed(2);
        s.style.width = size + 'px'; s.style.height = size + 'px';
        s.style.left = Math.random()*100 + 'vw'; s.style.top = Math.random()*100 + 'vh';
        s.style.animationDuration = (1 + Math.random()*2).toFixed(2) + 's';
        s.style.opacity = (0.55 + Math.random()*0.45).toFixed(2);
        if(Math.random()<0.08){ s.style.boxShadow='0 0 12px rgba(255,255,255,.95)'; }
        starLayer.appendChild(s);
      }
      document.body.appendChild(starLayer);
    }
    function clearStarfield(){ if(starLayer && starLayer.parentNode){ starLayer.parentNode.removeChild(starLayer); } starLayer = null; }

    function createMeteor(){
      if(!starry) return;
      const m = document.createElement('div'); m.className = 'meteor';
      const startX = -10 - Math.random()*10; const startY = -10 - Math.random()*10;
      m.style.left = startX + 'vw'; m.style.top  = startY + 'vh';
      const tail = 120 + Math.random()*120; m.style.setProperty('--tail', tail + 'px');
      m.style.animation = `shoot ${2.2 + Math.random()*1.8}s linear forwards`;
      document.body.appendChild(m); m.addEventListener('animationend', ()=> m.remove());
    }
    function startMeteorShower(){
      stopMeteorShower();
      const burst = 1 + Math.floor(Math.random()*2);
      for(let i=0;i<burst;i++){ setTimeout(createMeteor, i*300); }
      const nextDelay = ()=> 4000 + Math.random()*3000;
      const schedule = ()=>{ if(!starry) return; createMeteor(); meteorTimer = setTimeout(schedule, nextDelay()); };
      meteorTimer = setTimeout(schedule, nextDelay());
    }
    function stopMeteorShower(){ if(meteorTimer){ clearTimeout(meteorTimer); meteorTimer = null; } document.querySelectorAll('.meteor').forEach(el=>el.remove()); }

    // ======= Thanks (guestbook) =======
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
    function escapeHtml(str){ return str.replace(/[&<>"]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s])); }
    $('thanksSend').addEventListener('click', async ()=>{
      const name=$('thanksName').value.trim();
      const msg=$('thanksMsg').value.trim();
      if(!msg) return;
      try{
        await fetch('api/thanks.php',{ method:'POST', body:toForm({ name, message:msg }) });
        $('thanksMsg').value=''; loadThanks(); spawnConfetti(40);
      }catch(e){ console.warn(e); }
    });

    // ======= Load initial from backend =======
    async function loadData(){
      try{
        const r=await fetch('api/get.php'); if(!r.ok) throw 0;
        const j=await r.json();
        if(j.name) $('name').textContent=j.name;
        if(j.date) $('date').textContent=toVNDate(j.date);
        if(j.photo_url) setAvatar(j.photo_url); else setAvatar('assets/default.jpg');
        if(j.wish_text){ const wt=$('wishText'); if(wt) wt.textContent=j.wish_text; $('wishPreview').textContent=j.wish_text; }
        if(typeof j.bgm_on!=='undefined'){ audioOn=!!Number(j.bgm_on); renderAudio(); if(audioOn){ try{ await bgm.play(); }catch{} } }
      }catch(e){ setAvatar('assets/default.jpg'); renderAudio(); }
    }
    setTimeout(()=>spawnConfetti(80), 900);
    loadData(); loadThanks();
  </script>
</body>
</html>
