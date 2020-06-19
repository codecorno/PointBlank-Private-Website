<?php

namespace XF\Search\Source;

use XF\Search\IndexRecord;
use XF\Search\Query;

class MySqlFt extends AbstractSource
{
	protected $db;
	protected $minWordLength = 4;

	public function __construct(\XF\Db\AbstractAdapter $db, $minWordLength = 4)
	{
		$this->db = $db;
		$this->minWordLength = $minWordLength;
	}

	public function isRelevanceSupported()
	{
		return false;
	}

	protected $bulkIndexRecords = [];

	public function index(IndexRecord $record)
	{
		$metadataPieces = [
			$this->getMetadataKey('user', $record->userId),
			$this->getMetadataKey('content', $record->type)
		];
		foreach ($record->metadata AS $metadataKey => $value)
		{
			$piece = $this->getMetadataKey($metadataKey, $value);
			if (is_array($piece))
			{
				$metadataPieces = array_merge($metadataPieces, $piece);
			}
			else
			{
				$metadataPieces[] = $piece;
			}
		}

		if ($record->hidden)
		{
			$metadataPieces[] = $this->getMetadataKey('hidden', 1);
		}

		$title = $record->title;
		$message = $record->message;

		$maxTitleLen = 250;
		if (utf8_strlen($title) > $maxTitleLen)
		{
			$originalTitle = $title;
			$offset = $maxTitleLen;

			$title = utf8_substr($title, 0, $offset);

			if ($pos = utf8_strpos($title, ' ', $offset - 15))
			{
				$title = utf8_substr($title, 0, $pos);
				$offset = $pos;
			}

			$message .= ' ' . utf8_substr($originalTitle, $offset);

			$title = trim($title);
			$message = trim($message);
		}

		$insert = [
			'content_type' => $record->type,
			'content_id' => $record->id,
			'title' => $title,
			'message' => $message,
			'metadata' => implode(' ', $metadataPieces),
			'item_date' => $record->date,
			'user_id' => $record->userId,
			'discussion_id' => $record->discussionId
		];

		if ($this->bulkIndexing)
		{
			$this->bulkIndexRecords[] = $insert;
			if (count($this->bulkIndexRecords) >= 500)
			{
				$this->flushBulkIndexing();
			}
		}
		else
		{
			$this->db()->insert('xf_search_index', $insert, false,
				'title = VALUES(title), message = VALUES(message), metadata = VALUES(metadata), '
				. 'item_date = VALUES(item_date), user_id = VALUES(user_id), discussion_id = VALUES(discussion_id)'
			);
		}
	}

	protected function flushBulkIndexing()
	{
		if ($this->bulkIndexRecords)
		{
			$this->db()->insertBulk('xf_search_index', $this->bulkIndexRecords, false,
				'title = VALUES(title), message = VALUES(message), metadata = VALUES(metadata), '
				. 'item_date = VALUES(item_date), user_id = VALUES(user_id), discussion_id = VALUES(discussion_id)'
			);
		}

		$this->bulkIndexRecords = [];
	}

	public function delete($type, $ids)
	{
		if (!is_array($ids))
		{
			$ids = [$ids];
		}
		if (!$ids)
		{
			return;
		}

		$db = $this->db();
		$db->delete('xf_search_index', 'content_type = ? AND content_id IN (' . $db->quote($ids) . ')', $type);
	}

	public function truncate($type = null)
	{
		if (!$type)
		{
			$this->db()->emptyTable('xf_search_index');
			return true;
		}
		else
		{
			return false;
		}
	}

	public function search(Query\Query $query, $maxResults)
	{
		$db = $this->db();

		$where = [];
		$matchEntries = [];

		$keywords = $query->getParsedKeywords();
		if (strlen($keywords))
		{
			$matchEntries[] = $keywords;
		}

		if ($query->getMinDate())
		{
			$where[] = "search_index.item_date > " . intval($query->getMinDate());
		}
		if ($query->getMaxDate())
		{
			$where[] = "search_index.item_date < " . intval($query->getMaxDate());
		}

		foreach ($query->getMetadataConstraints() AS $metadata)
		{
			$this->addMetadataMatches($metadata, $matchEntries);
		}

		// MySQL has issues with nesting + and - into sub-expressions, so we can't restrict these metadata constraints
		// to specific content types. We have to hope that the metadata restrictions applied use type-specific names
		// so as to not remove elements unexpectedly.
		foreach ($query->getPermissionConstraints() AS $perms)
		{
			foreach ($perms['constraints'] AS $permConstraint)
			{
				$this->addMetadataMatches($permConstraint, $matchEntries);
			}
		}

		if (!$query->getAllowHidden())
		{
			$matchEntries[] = '-' . $this->getMetadataKey('hidden', 1);
		}

		$userIds = $query->getUserIds();
		$skipFtQuery = ($userIds && count($userIds) == 1 && !$matchEntries && $query->getOrder() == 'date');

		if ($userIds)
		{
			if ($skipFtQuery)
			{
				$where[] = 'search_index.user_id = ' . $db->quote(reset($userIds));
			}
			else
			{
				$this->addMetadataMatches(new Query\MetadataConstraint('user', $userIds), $matchEntries);
			}
		}

		$types = $query->getTypes();
		if ($types)
		{
			if ($skipFtQuery)
			{
				$where[] = 'search_index.content_type IN (' . $db->quote($types) . ')';
			}
			else
			{
				$this->addMetadataMatches(new Query\MetadataConstraint('content', $types), $matchEntries);
			}
		}

		if ($matchEntries)
		{
			if ($query->getTitleOnly() || !strlen($keywords))
			{
				$match = 'search_index.title, search_index.metadata';
			}
			else
			{
				$match = 'search_index.title, search_index.message, search_index.metadata';
			}
			$where[] = "MATCH({$match}) AGAINST (" . $db->quote(implode(' ', $matchEntries)) . " IN BOOLEAN MODE)";
		}

		/** @var Query\TableReference[] $tables */
		$tables = [];

		foreach ($query->getSqlConstraints() AS $constraint)
		{
			$where[] = $constraint->getSql($db);
			$tables += $constraint->getTables();
		}

		$order = $query->getOrder();
		if ($order instanceof Query\SqlOrder)
		{
			$tables += $order->getTables();
			$orderByClause = 'ORDER BY ' . $order->getOrder() . ', search_index.item_date DESC';
		}
		else if ($order)
		{
			// Assume a string. Since we don't support relevance, that must be date
			$orderByClause = 'ORDER BY search_index.item_date DESC';
		}
		else
		{
			$orderByClause = 'ORDER BY NULL';
		}

		$joins = '';
		foreach ($tables AS $table)
		{
			$joins .= "INNER JOIN " . $table->getTable() . " AS " . $table->getAlias() . " ON (" . $table->getCondition() . ")\n";
		}

		if ($where)
		{
			$whereClause = 'WHERE ' . implode(' AND ', $where);
		}
		else
		{
			$whereClause = '';
		}

		$groupType = $query->getGroupByType();
		if ($groupType)
		{
			$selectFields = $db->quote($groupType) . ' AS content_type, search_index.discussion_id AS content_id';
			$groupByClause = 'GROUP BY search_index.discussion_id';
		}
		else
		{
			$selectFields = 'search_index.content_type, search_index.content_id';
			$groupByClause = '';
		}

		$maxResults = intval($maxResults);
		if ($maxResults <= 0)
		{
			$maxResults = 1;
		}

		$results = $db->fetchAllNum("
			SELECT {$selectFields}
			FROM xf_search_index AS search_index
			{$joins}
			{$whereClause}
			{$groupByClause}
			{$orderByClause}
			LIMIT {$maxResults}
		");

		return $results;
	}

	public function getStopWords()
	{
		return [
			'a\'s', 'able', 'about', 'above', 'according', 'accordingly', 'across', 'actually',
			'after', 'afterwards', 'again', 'against', 'ain\'t', 'all', 'allow', 'allows',
			'almost', 'alone', 'along', 'already', 'also', 'although', 'always', 'am',
			'among', 'amongst', 'an', 'and', 'another', 'any', 'anybody', 'anyhow',
			'anyone', 'anything', 'anyway', 'anyways', 'anywhere', 'apart', 'appear', 'appreciate',
			'appropriate', 'are', 'aren\'t', 'around', 'as', 'aside', 'ask', 'asking',
			'associated', 'at', 'available', 'away', 'awfully', 'be', 'became', 'because',
			'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'behind', 'being',
			'believe', 'below', 'beside', 'besides', 'best', 'better', 'between', 'beyond',
			'both', 'brief', 'but', 'by', 'c\'mon', 'c\'s', 'came', 'can',
			'can\'t', 'cannot', 'cant', 'cause', 'causes', 'certain', 'certainly', 'changes',
			'clearly', 'co', 'com', 'come', 'comes', 'concerning', 'consequently', 'consider',
			'considering', 'contain', 'containing', 'contains', 'corresponding', 'could', 'couldn\'t', 'course',
			'currently', 'definitely', 'described', 'despite', 'did', 'didn\'t', 'different', 'do',
			'does', 'doesn\'t', 'doing', 'don\'t', 'done', 'down', 'downwards', 'during',
			'each', 'edu', 'eg', 'eight', 'either', 'else', 'elsewhere', 'enough',
			'entirely', 'especially', 'et', 'etc', 'even', 'ever', 'every', 'everybody',
			'everyone', 'everything', 'everywhere', 'ex', 'exactly', 'example', 'except', 'far',
			'few', 'fifth', 'first', 'five', 'followed', 'following', 'follows', 'for',
			'former', 'formerly', 'forth', 'four', 'from', 'further', 'furthermore', 'get',
			'gets', 'getting', 'given', 'gives', 'go', 'goes', 'going', 'gone',
			'got', 'gotten', 'greetings', 'had', 'hadn\'t', 'happens', 'hardly', 'has',
			'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'s', 'hello', 'help',
			'hence', 'her', 'here', 'here\'s', 'hereafter', 'hereby', 'herein', 'hereupon',
			'hers', 'herself', 'hi', 'him', 'himself', 'his', 'hither', 'hopefully',
			'how', 'howbeit', 'however', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 'ie',
			'if', 'ignored', 'immediate', 'in', 'inasmuch', 'inc', 'indeed', 'indicate',
			'indicated', 'indicates', 'inner', 'insofar', 'instead', 'into', 'inward', 'is',
			'isn\'t', 'it', 'it\'d', 'it\'ll', 'it\'s', 'its', 'itself', 'just',
			'keep', 'keeps', 'kept', 'know', 'known', 'knows', 'last', 'lately',
			'later', 'latter', 'latterly', 'least', 'less', 'lest', 'let', 'let\'s',
			'like', 'liked', 'likely', 'little', 'look', 'looking', 'looks', 'ltd',
			'mainly', 'many', 'may', 'maybe', 'me', 'mean', 'meanwhile', 'merely',
			'might', 'more', 'moreover', 'most', 'mostly', 'much', 'must', 'my',
			'myself', 'name', 'namely', 'nd', 'near', 'nearly', 'necessary', 'need',
			'needs', 'neither', 'never', 'nevertheless', 'new', 'next', 'nine', 'no',
			'nobody', 'non', 'none', 'noone', 'nor', 'normally', 'not', 'nothing',
			'novel', 'now', 'nowhere', 'obviously', 'of', 'off', 'often', 'oh',
			'ok', 'okay', 'old', 'on', 'once', 'one', 'ones', 'only',
			'onto', 'or', 'other', 'others', 'otherwise', 'ought', 'our', 'ours',
			'ourselves', 'out', 'outside', 'over', 'overall', 'own', 'particular', 'particularly',
			'per', 'perhaps', 'placed', 'please', 'plus', 'possible', 'presumably', 'probably',
			'provides', 'que', 'quite', 'qv', 'rather', 'rd', 're', 'really',
			'reasonably', 'regarding', 'regardless', 'regards', 'relatively', 'respectively', 'right', 'said',
			'same', 'saw', 'say', 'saying', 'says', 'second', 'secondly', 'see',
			'seeing', 'seem', 'seemed', 'seeming', 'seems', 'seen', 'self', 'selves',
			'sensible', 'sent', 'serious', 'seriously', 'seven', 'several', 'shall', 'she',
			'should', 'shouldn\'t', 'since', 'six', 'so', 'some', 'somebody', 'somehow',
			'someone', 'something', 'sometime', 'sometimes', 'somewhat', 'somewhere', 'soon', 'sorry',
			'specified', 'specify', 'specifying', 'still', 'sub', 'such', 'sup', 'sure',
			't\'s', 'take', 'taken', 'tell', 'tends', 'th', 'than', 'thank',
			'thanks', 'thanx', 'that', 'that\'s', 'thats', 'the', 'their', 'theirs',
			'them', 'themselves', 'then', 'thence', 'there', 'there\'s', 'thereafter', 'thereby',
			'therefore', 'therein', 'theres', 'thereupon', 'these', 'they', 'they\'d', 'they\'ll',
			'they\'re', 'they\'ve', 'think', 'third', 'this', 'thorough', 'thoroughly', 'those',
			'though', 'three', 'through', 'throughout', 'thru', 'thus', 'to', 'together',
			'too', 'took', 'toward', 'towards', 'tried', 'tries', 'truly', 'try',
			'trying', 'twice', 'two', 'un', 'under', 'unfortunately', 'unless', 'unlikely',
			'until', 'unto', 'up', 'upon', 'us', 'use', 'used', 'useful',
			'uses', 'using', 'usually', 'value', 'various', 'very', 'via', 'viz',
			'vs', 'want', 'wants', 'was', 'wasn\'t', 'way', 'we', 'we\'d',
			'we\'ll', 'we\'re', 'we\'ve', 'welcome', 'well', 'went', 'were', 'weren\'t',
			'what', 'what\'s', 'whatever', 'when', 'whence', 'whenever', 'where', 'where\'s',
			'whereafter', 'whereas', 'whereby', 'wherein', 'whereupon', 'wherever', 'whether', 'which',
			'while', 'whither', 'who', 'who\'s', 'whoever', 'whole', 'whom', 'whose',
			'why', 'will', 'willing', 'wish', 'with', 'within', 'without', 'won\'t',
			'wonder', 'would', 'wouldn\'t', 'yes', 'yet', 'you', 'you\'d', 'you\'ll',
			'you\'re', 'you\'ve', 'your', 'yours', 'yourself', 'yourselves', 'zero'
		];
	}

	public function getMinWordLength()
	{
		return $this->minWordLength;
	}

	public function setMinWordLength($length)
	{
		$this->minWordLength = max(1, intval($length));
	}

	protected function finalizeParsedKeywords(array $parsed)
	{
		$final = '';
		foreach ($parsed AS $part)
		{
			if ($part[0] == '')
			{
				$part[0] = '+';
			}
			else if ($part[0] == '|')
			{
				$part[0] = ''; // default in mysql
			}

			$final .= ' ' . $part[0] . $part[1];
		}

		return trim($final);
	}

	/**
	 * Gets the string form of a piece of metadata.
	 *
	 * @param string $keyName Type of metadata
	 * @param string|array $value Metadata value; if an array, gets metadata for each value
	 *
	 * @return string|array String if $value was a string, array if $value was an array
	 */
	protected function getMetadataKey($keyName, $value)
	{
		if (is_array($value))
		{
			$output = [];
			foreach ($value AS $childValue)
			{
				$output[] = '_md_' . $keyName . '_' . preg_replace('/[^a-z0-9_]/i', '', strval($childValue));
			}

			return $output;
		}
		else
		{
			return '_md_' . $keyName . '_' . preg_replace('/[^a-z0-9_]/i', '', $value);
		}
	}

	protected function addMetadataMatches(Query\MetadataConstraint $metadata, &$matchList)
	{
		$match = $this->getMetadataMatchString($metadata);
		if ($match)
		{
			$matchList[] = $match;
		}
	}

	protected function getMetadataMatchString(Query\MetadataConstraint $metadata)
	{
		$options = $this->getMetadataKey($metadata->getKey(), $metadata->getValues());
		if (!$options)
		{
			return null;
		}

		switch ($metadata->getMatchType())
		{
			case Query\MetadataConstraint::MATCH_ANY:
				if (count($options) > 1)
				{
					return '+(' . implode(' ', $options) . ')';
				}
				else
				{
					return '+' . reset($options);
				}

			case Query\MetadataConstraint::MATCH_ALL:
				return '+' . implode(' +', $options);

			case Query\MetadataConstraint::MATCH_NONE:
				return '-' . implode(' -', $options);

			default:
				return null;
		}
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	protected function db()
	{
		return $this->db;
	}
}