<h2><?php echo __('Access Denied') ?></h2>

<?php if (!$required_age) : ?>
  <p><?php echo __('Sorry, this site is not available in your country'); ?></p>
<?php else : ?>
  <p><?php echo __('You are under the required age of %required_age%', array('%required_age%' => $required_age)) ?>.</p>
<?php endif; ?>
