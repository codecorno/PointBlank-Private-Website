<?php

namespace XF\Import\DataHelper;

class Tag extends AbstractHelper
{
	public function importTag($tagName, $contentType, $contentId, array $contentExtra = [], array $tagExtra = [])
	{
		$tagId = $this->createTag($tagName, $tagExtra);
		if ($tagId)
		{
			return $this->associateTag($tagId, $contentType, $contentId, $contentExtra);
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param string $tagName
	 * @param array $extra
	 *
	 * @return null|int
	 */
	public function createTag($tagName, array $extra = [])
	{
		$extra = array_replace([
			'tag_url' => null,
			'permanent' => false
		], $extra);

		$tag = $this->em()->create('XF:Tag');
		$tag->setOption('admin_edit', true);
		$tag->tag = $this->dataManager->convertToUtf8($tagName);
		if ($extra['tag_url'])
		{
			$tag->tag_url = $extra['tag_url'];
		}
		$tag->permanent = $extra['permanent'];

		$tag->preSave();

		if ($tag->hasErrors())
		{
			$tag = $this->em()->getFinder('XF:Tag')->where('tag', $tagName)->fetchOne();
			return $tag ? $tag->tag_id : null;
		}

		try
		{
			$tag->save();
		}
		catch (\XF\Db\DuplicateKeyException $e)
		{
			// race condition - tag should just have been created
			$tagId = $this->db()->fetchOne("
				SELECT tag_id
				FROM xf_tag
				WHERE tag = ?
			", $tag->tag);

			return $tagId ?: null;
		}

		return $tag->tag_id;
	}

	public function associateTag($tagId, $contentType, $contentId, array $extra = [])
	{
		$extra = array_replace([
			'add_user_id' => 0,
			'add_date' => \XF::$time,
			'visible' => true,
			'content_date' => 0
		], $extra);

		$rows = $this->db()->insert('xf_tag_content', [
			'content_type' => $contentType,
			'content_id' => $contentId,
			'tag_id' => $tagId,
			'add_user_id' => $extra['add_user_id'],
			'add_date' => $extra['add_date'],
			'visible' => $extra['visible'] ? 1 : 0,
			'content_date' => $extra['content_date']
		], false, false, 'IGNORE');

		if (!$rows)
		{
			return null;
		}

		$newId = $this->db()->lastInsertId();

		if ($extra['visible'])
		{
			$this->db()->query("
				UPDATE xf_tag
				SET use_count = use_count + 1,
					last_use_date = ?
				WHERE tag_id = ?
			", [$extra['add_date'], $tagId]);
		}

		$this->em()->getRepository('XF:Tag')->rebuildContentTagCache($contentType, $contentId);

		return $newId;
	}
}