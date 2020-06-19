<?php

namespace XF\Import\Data;

class ProfilePost extends AbstractEmulatedData
{
	use HasDeletionLogTrait;

	protected $loggedIp;

	/**
	 * @var ProfilePostComment[]
	 */
	protected $comments = [];

	public function getImportType()
	{
		return 'profile_post';
	}

	public function getEntityShortName()
	{
		return 'XF:ProfilePost';
	}

	public function setLoggedIp($loggedIp)
	{
		$this->loggedIp = $loggedIp;
	}

	public function addComment($oldCommentId, ProfilePostComment $comment)
	{
		$this->comments[$oldCommentId] = $comment;
	}

	protected function preSave($oldId)
	{
		$this->forceNotEmpty('username', $oldId);
		$this->forceNotEmpty('message', $oldId);

		if ($this->message === '')
		{
			// do something about it?
		}

		uasort($this->comments, function(ProfilePostComment $c1, ProfilePostComment $c2)
		{
			if ($c1->comment_date == $c2->comment_date)
			{
				return 0;
			}

			return ($c1->comment_date < $c2->comment_date ? -1 : 1);
		});

		$firstComment = reset($this->comments);
		$lastComment = end($this->comments);

		if ($firstComment)
		{
			$this->first_comment_date = $firstComment->comment_date;
			$this->last_comment_date = $lastComment->comment_date;
		}

		$commentCount = 0;
		foreach ($this->comments AS $comment)
		{
			if ($comment->message_state == 'visible')
			{
				$commentCount++;
			}
		}
		$this->comment_count = $commentCount;

		return null;
	}

	protected function postSave($oldId, $newId)
	{
		if ($this->comments)
		{
			foreach ($this->comments AS $oldCommentId => $comment)
			{
				$comment->profile_post_id = $newId;
				$comment->useTransaction(false);
				$comment->checkExisting(false);

				$comment->save($oldCommentId);
			}
		}

		$this->logIp($this->loggedIp, $this->post_date);
		$this->insertStateRecord($this->message_state, $this->post_date);

		// note that the comment cache will be rebuilt when rebuilding profile posts so let's just skip that
	}
}