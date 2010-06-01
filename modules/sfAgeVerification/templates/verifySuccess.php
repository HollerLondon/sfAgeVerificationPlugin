<h2><?php echo __('Please enter your age') ?></h2>

<?php echo $form->renderFormTag(url_for('@sf_age_verify')) ?>
    <?php echo $form ?>
    <input type="submit" value="<?php echo __('Enter') ?>" id="enter" />
</form>
