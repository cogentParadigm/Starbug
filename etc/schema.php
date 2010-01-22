<?php
$schemer->table("permits",
	"role	type:string	length:30",
	"who	type:int	default:0",
	"action	type:string	length:100",
	"priv_type	type:string	length:30	default:table",
	"related_table	type:string	length:100",
	"related_id	type:int	default:0"
);
$schemer->table("users",
	"email	type:string	length:128",
	"password	type:password",
	"memberships	type:int"
);
$schemer->table("uris",
	"path	type:string	length:64",
	"template	type:string	length:64",
	"title	type:string	length:128",
	"parent	type:int	default:0",
	"sort_order	type:int	default:0",
	"check_path	type:bool	default:1",
	"prefix	type:string	length:128	default:app/views/",
	"options	type:longtext"
);
$schemer->table("tags",
	"tag	type:string	length:30	default:",
	"raw_tag	type:string	length:50	default:"
);
$schemer->table("uris_tags",
	"tag_id	type:int	default:0	key:primary	index:",
	"tagger_id	type:int	default:0	key:primary	index:",
	"object_id	type:int	default:0	key:primary	index:"
);
$schemer->table("leafs",
	"leaf	type:string	length:128",
	"page	type:string	length:64",
	"container	type:string	length:32",
	"position	type:int"
);
$schemer->table("text_leaf",
	"page	type:string	length:64",
	"container	type:string	length:32",
	"position	type:int",
	"content	type:text	length:5000"
);
?>
