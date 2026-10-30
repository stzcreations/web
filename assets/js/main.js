document.addEventListener('DOMContentLoaded', function() {
  // lazy load images
  const lazyImgs = document.querySelectorAll('img.lazy');
  const loadImg = (img) => {
    const src = img.getAttribute('data-src');
    if (src) { img.src = src; img.classList.add('loaded'); img.classList.remove('lazy'); }
  };
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { loadImg(e.target); io.unobserve(e.target); } });
    });
    lazyImgs.forEach(img => io.observe(img));
  } else {
    lazyImgs.forEach(loadImg);
  }

  // Download button: open ad from data-ad, then proceed
  document.querySelectorAll('.dl-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var ad = btn.getAttribute('data-ad');
      if (ad) { window.open(ad, '_blank'); }
    });
  });

  // Join Community popup
  var wrap = document.getElementById('jcWrap');
  if (wrap && !localStorage.getItem('jc_dismissed')) {
    setTimeout(function(){ wrap.style.display = 'flex'; }, 800);
  }
  var jcx = document.getElementById('jcx');
  if (jcx) {
    jcx.addEventListener('click', function(){
      var cb = document.getElementById('jcn');
      if (cb && cb.checked) localStorage.setItem('jc_dismissed', '1');
      if (wrap) wrap.style.display = 'none';
    });
  }
  var jcn = document.getElementById('jcn');
  if (jcn) {
    jcn.addEventListener('change', function(){
      if (jcn.checked) localStorage.setItem('jc_dismissed', '1');
      else localStorage.removeItem('jc_dismissed');
    });
  }
});
