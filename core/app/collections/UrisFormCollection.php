<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/UrisFormCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class UrisFormCollection extends FormCollection {
	public function filterRows($rows) {
		if ($this->copying) {
			foreach ($rows as $idx => $item) {
				if (!empty($item['uris_id'])) {
					$blocks = $this->models->get("blocks")->query()->condition("uris_id", $item['uris_id'])->all();
					$item['blocks'] = array();
					foreach ($blocks as $block) {
						$item['blocks'][$block['region']."-".$block['position']] = $block['content'];
					}
				}
				unset($item['id']);
				unset($item['slug']);
				unset($item['path']);
				$rows[$idx] = $item;
			}
		}
		return $rows;
	}
}
?>
