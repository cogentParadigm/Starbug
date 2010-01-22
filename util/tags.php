<?php
$sb->provide("util/tags");
class tags {

	const MAX_TAG_LENGTH = 30;
	const NORMALIZED_VALID_CHARS = 'a-zA-Z0-9';

	function safe_tag($tagset, $linker, $tagger_id, $object_id, $tag) {
		global $sb;
		//if (is_numeric($tag) && (intval($tag) == $tag)) $tag = preg_replace('/^([0-9]+)$/', "$1"."_", $tag);
		$normalized_tag = $sb->db->quote(tags::normalize_tag($tag));
		$tag = $sb->db->quote($tag);
		//$tagger_sql = " AND tagger_id='$tagger_id'";
		$sql = "SELECT COUNT(*) as count FROM ".P($linker)." INNER JOIN ".P($tagset)." ON (tag_id = id) WHERE object_id='$object_id' AND tag=$normalized_tag";
		$rs = $sb->db->query($sql)->fetch();
		if($rs['count'] > 0) return true;
		// Then see if a raw tag in this form exists.
		$sql = "SELECT id FROM ".P($tagset)." WHERE raw_tag=$tag";
		$rs = $sb->db->query($sql);
		if($row = $rs->fetch()) $tag_id = $row['id'];
		else {
			// Add new tag! 
			$sql = "INSERT INTO ".P($tagset)." (tag, raw_tag) VALUES ($normalized_tag, $tag)";
			$rs = $sb->db->query($sql);
			$tag_id = $sb->db->lastInsertId();
		}
		if(!($tag_id > 0)) return false;
		$sql = "INSERT INTO ".P($linker)." (tag_id, owner, object_id, created)	VALUES ($tag_id, $tagger_id, $object_id, NOW())";
		$rs = $sb->db->query($sql);
		return true;
	}
	
	function normalize_tag($tag) {
		$normalized_tag = preg_replace("/[^".tags::NORMALIZED_VALID_CHARS."]/", "", $tag);
		return strtolower($normalized_tag);
	}

	function delete_object_tag($tagset, $linker, $tagger_id, $object_id, $tag) {
		$tag_id = tags::get_raw_tag_id($tag);
		if($tag_id > 0) {
			$sql = "DELETE FROM ".P($linker)." WHERE owner='$tagger_id' AND object_id='$object_id' AND tag_id='$tag_id' LIMIT 1";	
			$rs = $sb->db->query($sql);	
			return true;
		} else return false;
	}

	function delete_all_object_tags($linker, $object_id) {
		if($object_id > 0) {
			$sql = "DELETE FROM ".P($linker)." WHERE	object_id='$object_id'";	
			$rs = $sb->db->query($sql);	
			return true;
		} else return false;
	}

	function get_tag_id($tagset, $tag) {
		$tag = $this->db->quote($tag);
		$sql = "SELECT id FROM ".P($tagset)."	WHERE	tag='$tag' LIMIT 1";	
		$rs = $this->db->query($sql)->fetch();	
		return $rs['id'];
	}
	
	function get_raw_tag_id($tagset, $tag) {
		$tag = $sb->db->quote($tag);
		$sql = "SELECT id FROM ".P($tagset)."	WHERE	raw_tag='$tag' LIMIT 1";	
		$rs = $sb->db->query($sql)->fetch();	
		return $rs['id'];
	}
	/* INCOMPLETE/NON-WORKING FUNCTIONS *//*
	function tag_object($tagger_id_list, $object_id_list, $tag_string, $skip_updates = 1) {
		if($tag_string == '') return true;

		// Break up CSL's for tagger id's and object id's
		$tagger_id_array = split(',', $tagger_id_list);
		$valid_tagger_id_array = array();
		foreach ($tagger_id_array as $id) if (intval($id) > 0) $valid_tagger_id_array[] = intval($id);
		if (count($valid_tagger_id_array) == 0) return true;

		$object_id_array = split(',', $object_id_list);
		$valid_object_id_array = array();
		foreach ($object_id_array as $id) if (intval($id) > 0) $valid_object_id_array[] = intval($id);
		if (count($valid_object_id_array) == 0) return true;

		$tagArray = $this->_parse_tags($tag_string);

		foreach ($valid_tagger_id_array as $tagger_id) {
			foreach ($valid_object_id_array as $object_id) {
				$oldTags = $this->get_tags($object_id, array("tagger_id" => $tagger_id));
				$preserveTags = array();
				if (($skip_updates == 0) && (count($oldTags) > 0)) {
					foreach ($oldTags as $tagItem) {
						if (!in_array($tagItem['raw_tag'], $tagArray)) $this->delete_object_tag($tagger_id, $object_id, $tagItem['raw_tag']);
						else $preserveTags[] = $tagItem['raw_tag'];
					}
				}
				$newTags = array_diff($tagArray, $preserveTags);
				$this->_tag_object_array($tagger_id, $object_id, $newTags);
			}
		}
		return true;
	}
	
	function _tag_object_array($tagger_id, $object_id, $tagArray) {
		foreach($tagArray as $tag) {
			$tag = trim($tag);
			if(($tag != '') && (strlen($tag) <= $this->_MAX_TAG_LENGTH)) {
				if(get_magic_quotes_gpc()) $tag = addslashes($tag);
				$this->safe_tag($tagger_id, $object_id, $tag);
			}
		}
		return true;
	}

	function _parse_tags($tag_string) {
		$newwords = array();
		if ($tag_string == '') return $newwords;
		if(get_magic_quotes_gpc()) $query = stripslashes(trim($tag_string));
		else $query = trim($tag_string);
		$words = preg_split('/(")/', $query,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$delim = 0;
		foreach ($words as $key => $word) {
			if ($word == '"') {
				$delim++;
				continue;
			}
			if (($delim % 2 == 1) && $words[$key - 1] == '"') $newwords[] = $word;
			else $newwords = array_merge($newwords, preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
		}
		return $newwords;
	}
	
	function get_most_popular_tags($tagger_id = NULL, $offset = 0, $limit = 25) {
		if(isset($tagger_id) && ($tagger_id > 0)) $tagger_sql = "AND tagger_id = $tagger_id";
		else $tagger_sql = "";
		$sql = "SELECT tag, COUNT(*) as count	FROM ".P("tag")." INNER JOIN ".P("tags")." ON (id=tag_id)	WHERE 1	$tagger_sql	GROUP BY tag ORDER BY count DESC, tag ASC	LIMIT $offset, $limit";
		$rs = $this->db->query($sql);
		$retarr = array();
		while($row = $rs->fetch()) {
			$retarr[] = array('tag' => $row['tag'], 'count' => $row['count']);
		}
		return $retarr;
	} */
}
