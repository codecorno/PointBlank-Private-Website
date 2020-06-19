<?php

namespace XF\Less;

class CommentRtl extends \Less_Tree_Comment
{
	public $rtlMode = 'enable';

	public function toCSS()
	{
		return $this->value;
	}

	public function isSilent()
	{
		$isReference = ($this->currentFileInfo && isset($this->currentFileInfo['reference']) && (!isset($this->isReferenced) || !$this->isReferenced) );
		return $this->silent || $isReference;
	}

	public static function cloneFrom(\Less_Tree_Comment $comment, $rtlMode)
	{
		$new = new self($comment->value, false, null, $comment->currentFileInfo);
		$new->isReferenced = $comment->isReferenced;
		$new->rtlMode = $rtlMode;

		return $new;
	}
}