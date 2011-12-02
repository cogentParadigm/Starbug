<textarea <? foreach ($attributes as $key => $val) if (!is_array($val) && !in_array($val, array("label", "nolabel"))) echo $key.'="'.$val.'" '; ?>><?= $value ?></textarea>
