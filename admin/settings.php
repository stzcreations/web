<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$keys = ['site_name','site_description','contact_email','facebook_url','twitter_url','instagram_url','github_url','footer_text','copyright_text','seo_title','seo_description','download_countdown','download_ad_url','footer_scripts','google_client_id','google_client_secret','google_redirect_uri','default_ad_slot'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    foreach ($keys as $k) {
        $v = trim($_POST[$k] ?? '');
        $stmt = db()->prepare("INSERT INTO website_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?");
        $stmt->execute([$k, $v, $v]);
    }
    // logo upload
    if (!empty($_FILES['site_logo']['name'])) {
        [$ok, $res] = upload_file($_FILES['site_logo'], UPLOAD_DIR . '/thumbnails', ['jpg','jpeg','png','gif','webp','svg'], ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml']);
        if ($ok) {
            $stmt = db()->prepare("INSERT INTO website_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?");
            $stmt->execute(['site_logo', $res, $res]);
        }
    }
    if (!empty($_FILES['site_favicon']['name'])) {
        [$ok, $res] = upload_file($_FILES['site_favicon'], UPLOAD_DIR . '/thumbnails', ['ico','png','jpg','svg'], []);
        if ($ok) {
            $stmt = db()->prepare("INSERT INTO website_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?");
            $stmt->execute(['site_favicon', $res, $res]);
        }
    }
    // meta image
    if (!empty($_FILES['meta_image']['name'])) {
        [$ok, $res] = upload_file($_FILES['meta_image'], UPLOAD_DIR . '/thumbnails', ['jpg','jpeg','png','gif','webp'], ['image/jpeg','image/png','image/gif','image/webp']);
        if ($ok) {
            $stmt = db()->prepare("INSERT INTO website_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?");
            $stmt->execute(['meta_image', $res, $res]);
        }
    }
    redirect('admin/settings');
}
function S($k){ return setting($k); }
?>
<h2 class="text-white mb-3">Website Settings</h2>
<form method="post" enctype="multipart/form-data" class="card-dark p-4">
  <?php echo csrf_field(); ?>
  <div class="mb-3"><label class="form-label">Website Name</label><input type="text" name="site_name" class="form-control" value="<?php echo e(S('site_name')); ?>"></div>
  <div class="mb-3"><label class="form-label">Website Description</label><textarea name="site_description" class="form-control" rows="2"><?php echo e(S('site_description')); ?></textarea></div>
  <div class="mb-3"><label class="form-label">Contact Email</label><input type="email" name="contact_email" class="form-control" value="<?php echo e(S('contact_email')); ?>"></div>
  <div class="mb-3"><label class="form-label">Facebook URL</label><input type="url" name="facebook_url" class="form-control" value="<?php echo e(S('facebook_url')); ?>"></div>
  <div class="mb-3"><label class="form-label">Twitter URL</label><input type="url" name="twitter_url" class="form-control" value="<?php echo e(S('twitter_url')); ?>"></div>
  <div class="mb-3"><label class="form-label">Instagram URL</label><input type="url" name="instagram_url" class="form-control" value="<?php echo e(S('instagram_url')); ?>"></div>
  <div class="mb-3"><label class="form-label">GitHub URL</label><input type="url" name="github_url" class="form-control" value="<?php echo e(S('github_url')); ?>"></div>
  <div class="mb-3"><label class="form-label">Footer Text</label><input type="text" name="footer_text" class="form-control" value="<?php echo e(S('footer_text')); ?>"></div>
  <div class="mb-3"><label class="form-label">Copyright Text</label><input type="text" name="copyright_text" class="form-control" value="<?php echo e(S('copyright_text')); ?>"></div>
  <div class="mb-3"><label class="form-label">SEO Title</label><input type="text" name="seo_title" class="form-control" value="<?php echo e(S('seo_title')); ?>"></div>
  <div class="mb-3"><label class="form-label">SEO Description</label><textarea name="seo_description" class="form-control" rows="2"><?php echo e(S('seo_description')); ?></textarea></div>
  <div class="mb-3"><label class="form-label">Download Countdown (seconds)</label><input type="number" name="download_countdown" class="form-control" value="<?php echo e(S('download_countdown','10')); ?>"></div>
  <div class="mb-3"><label class="form-label">Download Ad URL</label><input type="url" name="download_ad_url" class="form-control" value="<?php echo e(S('download_ad_url')); ?>"></div>
  <div class="mb-3"><label class="form-label">Footer/Global Scripts (Adsterra/Monetag, etc.)</label><textarea name="footer_scripts" class="form-control" rows="4"><?php echo e(S('footer_scripts')); ?></textarea></div>
  <div class="mb-3"><label class="form-label">Default Ad Slot Key</label><input type="text" name="default_ad_slot" class="form-control" value="<?php echo e(S('default_ad_slot')); ?>"></div>
  <h5 class="text-white mt-4">Google OAuth</h5>
  <div class="mb-3"><label class="form-label">Google Client ID</label><input type="text" name="google_client_id" class="form-control" value="<?php echo e(S('google_client_id')); ?>"></div>
  <div class="mb-3"><label class="form-label">Google Client Secret</label><input type="text" name="google_client_secret" class="form-control" value="<?php echo e(S('google_client_secret')); ?>"></div>
  <div class="mb-3"><label class="form-label">Google Redirect URI</label><input type="text" name="google_redirect_uri" class="form-control" value="<?php echo e(S('google_redirect_uri')); ?>" placeholder="<?php echo e(SITE_URL); ?>/google-callback.php"></div>
  <div class="mb-3"><label class="form-label">Logo Upload</label><input type="file" name="site_logo" class="form-control"></div>
  <div class="mb-3"><label class="form-label">Favicon Upload</label><input type="file" name="site_favicon" class="form-control"></div>
  <div class="mb-3"><label class="form-label">Default Meta Image Upload</label><input type="file" name="meta_image" class="form-control"></div>
  <button class="btn btn-glow" type="submit">Save Settings</button>
</form>
