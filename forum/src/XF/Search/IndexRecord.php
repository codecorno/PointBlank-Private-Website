<?php

namespace XF\Search;

class IndexRecord
{
	public $type;
	public $id;
	public $title;
	public $message;
	public $date;
	public $userId = 0;
	public $discussionId = 0;
	public $metadata = [];
	public $hidden = false;

	public function __construct($type, $id, $title, $message, $date = null, $userId = 0, $discussionId = 0, array $metadata = [])
	{
		$this->type = strval($type);
		$this->id = intval($id);
		$this->title = strval($title);
		$this->message = strval($message);
		$date = $date === null ? \XF::$time : intval($date);
		$this->date = $date;
		$this->userId = intval($userId);
		$this->discussionId = intval($discussionId);
		$this->metadata = $metadata;
	}

	public static function create($type, $id, array $data)
	{
		$data = array_merge([
			'title' => '',
			'message' => '',
			'date' => \XF::$time,
			'user_id' => 0,
			'discussion_id' => 0,
			'metadata' => [],
			'hidden' => false
		], $data);

		$index = new self(
			$type, $id,
			$data['title'], $data['message'],
			$data['date'], $data['user_id'],
			$data['discussion_id'], $data['metadata']
		);
		if ($data['hidden'])
		{
			$index->setHidden();
		}

		return $index;
	}

	public function setHidden()
	{
		$this->hidden = true;
	}

	public function indexTags(array $tags, $withMetadata = true)
	{
		if ($tags)
		{
			$tagIds = [];
			$title = '';
			foreach ($tags AS $tagId => $tag)
			{
				$title .= " $tag[tag]";
				$tagIds[] = $tagId;
			}

			$this->title .= $title;
			if ($withMetadata)
			{
				$this->metadata['tag'] = $tagIds;
			}
		}
	}
}