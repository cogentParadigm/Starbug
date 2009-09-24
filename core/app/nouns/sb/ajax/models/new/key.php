<div id="new_key">
<?php $loc = next($this->uri); $formid = "new_key_form"; include("core/app/nouns/sb/models/keypair_form.php"); ?>
<a class="button" href="#" onclick="cancel_new_key();return false;">Cancel</a><a class="button" href="#" onclick="save_new_key('<?php echo $loc; ?>');return false;">Save</a>
</div>
