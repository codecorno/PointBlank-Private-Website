<?php

namespace XF\Import\DataHelper;

class BookmarkLabel extends AbstractHelper
{
	public function importLabel($labelText, $bookmarkId, $userId, array $labelUseExtra = [], array $labelExtra = [])
	{
		$labelId = $this->createLabel($labelText, $userId, $labelExtra);
		if ($labelId)
		{
			return $this->associateLabel($labelId, $bookmarkId, $labelUseExtra);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param string $labelText
	 * @param array  $extra
	 *
	 * @return null|int
	 */
	public function createLabel($labelText, $userId, array $extra = [])
	{
		$extra = array_replace([
			'label_url' => null
		], $extra);

		$label = $this->em()->create('XF:BookmarkLabel');
		$label->label = $this->dataManager->convertToUtf8($labelText);
		if ($extra['label_url'])
		{
			$label->label_url = $extra['label_url'];
		}

		$label->user_id = $userId;

		$label->preSave();

		if ($label->hasErrors())
		{
			$label = $this->em()->getFinder('XF:BookmarkLabel')
				->where('label', $labelText)
				->where('user_id', $userId)
				->fetchOne();
			return $label ? $label->label_id : false;
		}

		$label->save();

		return $label->label_id;
	}

	public function associateLabel($labelId, $bookmarkId, array $extra = [])
	{
		$extra = array_replace([
			'use_date' => \XF::$time
		], $extra);

		$rows = $this->db()->insert('xf_bookmark_label_use', [
			'bookmark_id' => $bookmarkId,
			'label_id' => $labelId,
			'use_date' => $extra['use_date']
		], false, false, 'IGNORE');

		if (!$rows)
		{
			return false;
		}

		$this->db()->query("
			UPDATE xf_bookmark_label
			SET use_count = use_count + 1,
				last_use_date = ?
			WHERE label_id = ?
		", [$extra['use_date'], $labelId]);

		/** @var \XF\Repository\Bookmark $repo */
		$repo = $this->em()->getRepository('XF:Bookmark');
		$repo->rebuildBookmarkLabelCache($bookmarkId);

		return true;
	}
}