<?php

namespace XF\Less;

class RtlVisitorPre extends \Less_VisitorReplacing
{
	public $isPreVisitor = true;

	public function run(\Less_Tree_Ruleset $root)
	{
		return $this->visitObj($root);
	}

	public function visitComment(\Less_Tree_Comment $comment)
	{
		if (preg_match('#/\*\s*XF-RTL:\s*(enable|disable)\s*\*/#', $comment->value, $match))
		{
			$mode = $match[1];
		}
		else if (preg_match('#//\s*XF-RTL:\s*(enable|disable)(\s|$)#', $comment->value, $match))
		{
			$mode = $match[1];
		}
		else
		{
			return $comment;
		}

		return CommentRtl::cloneFrom($comment, $mode);
	}
}