<?php
  $factory = $config->get("models", "factory");
  $factory = isset($factory[$model]) ? $factory[$model] : array();
  extract($config->get($model, "json"));
  echo '<?php'."\n";
?>
namespace Starbug\Core;
/**
 * <?php echo $name; ?> model base
 * @ingroup models
 */
class <?php echo ucwords($name); ?>Model extends Table {

  public $type = "<?php echo $name; ?>";
  public $base = "<?php echo $base; ?>";
  public $label = "<?php echo $label; ?>";
  public $singular = "<?php echo $singular; ?>";
  public $singular_label = "<?php echo ucwords(str_replace(array("-", "_"), array(" ", " "), $singular)); ?>";
  public $label_select = "<?php echo empty($label_select) ? $name.".id" : $label_select; ?>";


  function __construct(DatabaseInterface $db, ModelFactoryInterface $models<?php foreach ($factory as $n => $t) echo ', '.$t.' $'.$n; ?>) {
    $this->db = $db;
    $this->models = $models;<?php foreach ($factory as $n => $t) echo "\n\t\t\$this->".$n.' = $'.$n.';'; ?>

    $this->init();
  }

	public $hooks = array(<?php $count = 0; foreach ($fields as $column => $field) { if (!empty($field)) { $fcount = 0; if ($count > 0) echo ','; $count++; echo "\n"; ?>
		"<?php echo $column; ?>" => array(<?php foreach ($field as $k => $v) { ?><?php if ($fcount > 0) echo ", "; $fcount++ ?>"<?php echo $k; ?>" => "<?php echo $v; ?>"<?php } ?>)<?php } } echo "\n"; ?>
	);

}
<?php echo '?>'; ?>
