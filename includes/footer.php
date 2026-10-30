</div>
<?php echo render_ad(get_ads('footer')); ?>
<?php echo render_ad(get_ads('socialbar')); ?>
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-3">
        <h5 class="text-white"><?php echo e(setting('site_name')); ?></h5>
        <p><?php echo e(setting('footer_text')); ?></p>
      </div>
      <div class="col-md-4 mb-3">
        <h6 class="text-white">Links</h6>
        <ul class="list-unstyled">
          <li><a href="<?php echo base_url(); ?>">Home</a></li>
          <li><a href="<?php echo base_url('browse'); ?>">Browse</a></li>
          <li><a href="<?php echo base_url('categories'); ?>">Categories</a></li>
          <li><a href="<?php echo base_url('latest'); ?>">Latest</a></li>
          <li><a href="<?php echo base_url('popular'); ?>">Popular</a></li>
        </ul>
      </div>
      <div class="col-md-4 mb-3">
        <h6 class="text-white">Contact</h6>
        <p><?php echo e(setting('contact_email')); ?></p>
        <div class="d-flex gap-3">
          <?php if (setting('facebook_url')): ?><a href="<?php echo e(setting('facebook_url')); ?>" target="_blank"><i data-lucide="facebook"></i></a><?php endif; ?>
          <?php if (setting('twitter_url')): ?><a href="<?php echo e(setting('twitter_url')); ?>" target="_blank"><i data-lucide="twitter"></i></a><?php endif; ?>
          <?php if (setting('instagram_url')): ?><a href="<?php echo e(setting('instagram_url')); ?>" target="_blank"><i data-lucide="instagram"></i></a><?php endif; ?>
          <?php if (setting('github_url')): ?><a href="<?php echo e(setting('github_url')); ?>" target="_blank"><i data-lucide="github"></i></a><?php endif; ?>
        </div>
      </div>
    </div>
    <hr style="border-color:var(--border)">
    <p class="text-center small mb-0"><?php echo e(setting('copyright_text')); ?></p>
  </div>
</footer>
<!-- Join Community Popup -->
<div id="jcWrap" class="jc-wrap" style="display:none;">
  <div class="jc-card">
    <button type="button" class="jc-x" id="jcx" aria-label="Close"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 5l14 14M19 5L5 19"></path></svg></button>
    <div class="jc-ic"><svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="#12d6ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L3 10.5l6 2.3L11 20l3-4.5L19 20z"></path><path d="M9 12.8L21 3"></path></svg></div>
    <h2 id="jct">Join Community</h2>
    <p class="jc-en">Facing issues with your projects? Don't worry! Join our official Telegram community. Whether you need help, guidance, or just want to connect, we are always here to support you.</p>
    <p class="jc-bn">যেকোনো প্রজেক্ট বানাতে গিয়ে কোনো সমস্যায় পড়েছেন? চিন্তার কিছু নেই! আমাদের টেলিগ্রাম কমিউনিটিতে জয়েন করুন। এখানে যেকোনো সমস্যায় আপনি খুব সহজেই সাহায্য এবং গাইডলাইন পেয়ে যাবেন।</p>
    <div class="jc-btns">
      <a class="jc-b" href="https://t.me/lyuna16" target="_blank" rel="noopener">Telegram <i class="jc-c"><svg viewBox="0 0 24 24" width="20" height="20" fill="#0a1224"><path d="M20.7 4.2L2.9 11c-.9.4-.9 1 0 1.3l4.5 1.4 1.7 5.2c.2.6.4.8.8.8.4 0 .6-.2.9-.5l2.2-2.1 4.5 3.3c.8.5 1.4.2 1.6-.8l3-14.2c.3-1.2-.4-1.7-1.4-1.2zM9 13.5l9.2-5.8c.4-.3.8-.1.5.2l-7.6 6.9-.3 3.1z"></path></svg></i></a>
      <a class="jc-b" href="https://www.youtube.com/@lyuna16" target="_blank" rel="noopener">YouTube <i class="jc-y"><svg viewBox="0 0 24 24" width="22" height="22"><rect x="1" y="4" width="22" height="16" rx="5" fill="#fff"></rect><path d="M10 8.7v6.6l5.5-3.3z" fill="#0a1224"></path></svg></i></a>
    </div>
    <label class="jc-nd"><input type="checkbox" id="jcn"><span class="jc-ck"></span>Don't show this again</label>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
<script>if(window.lucide) lucide.createIcons();</script>
<?php echo setting('footer_scripts', ''); ?>
</body>
</html>
<?php track_visitor(); ?>
