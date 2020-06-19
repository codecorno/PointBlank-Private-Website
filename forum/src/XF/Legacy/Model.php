<?php

namespace XF\Legacy;

abstract class Model
{
	/**
	* @var \XF\Db\AbstractAdapter
	*/
	protected $_db = null;

	/**
	* Controls whether a cached read is allowed. If not, it should be retrieved
	* from the source.
	*
	* @var boolean
	*/
	protected $_allowCachedRead = true;

	/**
	 * Stores local, instance-specific cached data for each model. This data
	 * is generally treated as canonical, even if {$_allowCachedRead} is false.
	 *
	 * @var array
	 */
	protected $_localCacheData = [];

	/**
	* Constructor. Use {@link create()} statically unless you know what you're doing.
	*/
	public function __construct()
	{
	}

	/**
	 * Injects a local cache value. This should only be used if you know what you're
	 * doing or for testing purposes!
	 *
	 * Note that you cannot get the existing data via the public interface. If you think
	 * you need the set data, use a new object. It defaults to empty. :)
	 *
	 * @param string $name
	 * @param $value
	 */
	public function setLocalCacheData($name, $value)
	{
		$this->_localCacheData[$name] = $value;
	}

	/**
	 * Reset an entry or the entire local cache. This can be used if you know when
	 * some cached data has expired.
	 *
	 * @param string|null $entry If null, resets the whole cache; otherwise, the specified entry
	 */
	public function resetLocalCacheData($name = null)
	{
		if ($name === null)
		{
			$this->_localCacheData = [];
		}
		else if (isset($this->_localCacheData[$name]))
		{
			unset($this->_localCacheData[$name]);
		}
	}

	/**
	 * Gets the named entry from the local cache.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	protected function _getLocalCacheData($name)
	{
		return isset($this->_localCacheData[$name]) ? $this->_localCacheData[$name] : false;
	}

	/**
	 * Gets the specified model object from the cache. If it does not exist,
	 * it will be instantiated.
	 *
	 * @param string $class Name of the class to load
	 *
	 * @return Model
	 */
	public function getModelFromCache($class)
	{
		return self::create($class);
	}

	/**
	* Helper method to get the database object.
	*
	* @return \XF\Db\AbstractAdapter
	*/
	protected function _getDb()
	{
		if ($this->_db === null)
		{
			$this->_db = \XF::db();
		}

		return $this->_db;
	}

	/**
	* Sets whether we're allowed to read values from the cache on a model-level.
	* This may be controllable on an individual level basis, if the implementation
	* allows it.
	*
	* @param boolean
	*/
	public function setAllowCachedRead($allowCachedRead)
	{
		$this->_allowCachedRead = (bool)$allowCachedRead;
	}

	/**
	* Factory method to get the named model. The class must exist or be autoloadable
	* or an exception will be thrown.
	*
	* @param string $class Class to load
	*
	* @return Model
	*/
	public static function create($class)
	{
		if (strpos($class, ':'))
		{
			$createClass = \XF::stringToClass($class, '%s\Legacy\Model\%s');
		}
		else if (strpos($class, '\\'))
		{
			$createClass = $class;
		}
		else
		{
			$createClass = $class;
			$createClass = preg_replace('/^XenForo_/', 'XF\\', $createClass);
			$createClass = preg_replace('/(_|\\\\)Model_/', '\\Legacy\\Model\\', $createClass);
			$createClass = str_replace('_', '\\', $createClass);
		}

		return new $createClass;
	}

	/**
	 * Fetches results from the database with each row keyed according to preference.
	 * The 'key' parameter provides the column name with which to key the result.
	 * For example, calling fetchAllKeyed('SELECT item_id, title, date FROM table', 'item_id')
	 * would result in an array keyed by item_id:
	 * [$itemId] => array('item_id' => $itemId, 'title' => $title, 'date' => $date)
	 *
	 * Note that the specified key must exist in the query result, or it will be ignored.
	 *
	 * @param string SQL to execute
	 * @param string Column with which to key the results array
	 * @param mixed Parameters for the SQL
	 *
	 * @return array
	 */
	public function fetchAllKeyed($sql, $key, $bind = [])
	{
		return $this->_getDb()->fetchAllKeyed($sql, $key, $bind);
	}

	/**
	 * Applies a limit clause to the provided query if a limit value is specified.
	 * If the limit value is 0 or less, no clause is applied.
	 *
	 * @param string $query SQL query to run
	 * @param integer $limit Number of records to limit to; ignored if <= 0
	 * @param integer $offset Offset from the start of the records. 0+
	 *
	 * @return string Query with limit applied if necessary
	 */
	public function limitQueryResults($query, $limit, $offset = 0)
	{
		if ($limit > 0)
		{
			if ($offset < 0)
			{
				$offset = 0;
			}
			return $this->_getDb()->limit($query, $limit, $offset);
		}
		else
		{
			return $query;
		}
	}

	/**
	 * Adds a join to the set of fetch options. Join should be one of the constants.
	 *
	 * @param array $fetchOptions
	 * @param integer $join
	 */
	public function addFetchOptionJoin(array &$fetchOptions, $join)
	{
		if (isset($fetchOptions['join']))
		{
			$fetchOptions['join'] |= $join;
		}
		else
		{
			$fetchOptions['join'] = $join;
		}
	}

	/**
	 * Gets a list of SQL conditions in the format for a clause. This always returns
	 * a value that can be used in a clause such as WHERE.
	 *
	 * @param array $sqlConditions
	 *
	 * @return string
	 */
	public function getConditionsForClause(array $sqlConditions)
	{
		if ($sqlConditions)
		{
			return '(' . implode(') AND (', $sqlConditions) . ')';
		}
		else
		{
			return '1=1';
		}
	}

	/**
	 * Gets the order by clause for an SQL query.
	 *
	 * @param array $choices
	 * @param array $fetchOptions
	 * @param string $defaultOrderSql
	 *
	 * @return string Order by clause or empty string
	 */
	public function getOrderByClause(array $choices, array $fetchOptions, $defaultOrderSql = '')
	{
		$orderSql = null;

		if (!empty($fetchOptions['order']) && isset($choices[$fetchOptions['order']]))
		{
			$orderSql = $choices[$fetchOptions['order']];

			if (empty($fetchOptions['direction']))
			{
				$fetchOptions['direction'] = 'asc';
			}

			$dir = (strtolower($fetchOptions['direction']) == 'desc' ? 'DESC' : 'ASC');
			$orderSqlOld = $orderSql;
			$orderSql = sprintf($orderSql, $dir);
			if ($orderSql === $orderSqlOld)
			{
				$orderSql .= ' ' . $dir;
			}
		}

		if (!$orderSql)
		{
			$orderSql = $defaultOrderSql;
		}
		return ($orderSql ? 'ORDER BY ' . $orderSql : '');
	}

	/**
	 * Adds the equivalent of a limit clause using position-based limits.
	 * It no limit value is specified, nothing will be returned.
	 *
	 * This must be added within a WHERE clause. If a clause is required,
	 * it will begin with "AND", so ensure there is a condition before it.
	 *
	 * @param string $table Name of the table alias to prefix. May be blank for no table reference.
	 * @param integer $limit Number of records to limit to; ignored if <= 0
	 * @param integer $offset Offset from the start of the records. 0+
	 * @param string $column Name of the column that is storing the position
	 *
	 * @return string Position limit clause if needed
	 */
	public function addPositionLimit($table, $limit, $offset = 0, $column = 'position')
	{
		if ($limit > 0)
		{
			$columnRef = ($table ? "$table.$column" : $column);

			return " AND ($columnRef >= " . intval($offset) . " AND $columnRef < " . intval($offset + $limit) . ') ';
		}
		else
		{
			return '';
		}
	}

	/**
	 * Prepares the limit-related fetching options that can be applied to various queries.
	 * Includes: limit, offset, page, and perPage.
	 *
	 * @param array $fetchOptions Unprepared options
	 *
	 * @return array Limit options; keys: limit, offset
	 */
	public function prepareLimitFetchOptions(array $fetchOptions)
	{
		$limitOptions = ['limit' => 0, 'offset' => 0];
		if (isset($fetchOptions['limit']))
		{
			$limitOptions['limit'] = intval($fetchOptions['limit']);
		}
		if (isset($fetchOptions['offset']))
		{
			$limitOptions['offset'] = intval($fetchOptions['offset']);
		}

		if (isset($fetchOptions['perPage']) && $fetchOptions['perPage'] > 0)
		{
			$limitOptions['limit'] = intval($fetchOptions['perPage']);
		}

		if (isset($fetchOptions['page']))
		{
			$page = intval($fetchOptions['page']);
			if ($page < 1)
			{
				$page = 1;
			}

			$limitOptions['offset'] = intval(($page - 1) * $limitOptions['limit']);
		}

		return $limitOptions;
	}

	/**
	 * Prepares state related fetch limits, based on the list of conditions.
	 * Looks for keys "deleted" and "moderated".
	 *
	 * @param array $fetchOptions
	 * @param string $table Name of the table to prefix the state and user fields with
	 * @param string $stateField Name of the field that holds the state
	 * @param string $userField Name of the field that holds the user ID
	 *
	 * @return string SQL condition to limit state
	 */
	public function prepareStateLimitFromConditions(array $fetchOptions, $table = '', $stateField = 'message_state', $userField = 'user_id')
	{
		$fetchOptions = array_merge(
			[
				'deleted' => false,
				'moderated' => false
			], $fetchOptions
		);

		$stateRef = ($table ? "$table.$stateField" : $stateField);
		$userRef = ($table ? "$table.$userField" : $userField);

		$states = ["'visible'"];
		$moderatedLimit = '';

		if ($fetchOptions['deleted'])
		{
			$states[] = "'deleted'";
		}

		if ($fetchOptions['moderated'])
		{
			if ($fetchOptions['moderated'] === true)
			{
				$states[] = "'moderated'";
			}
			else
			{
				$moderatedLimit = " OR ($stateRef = 'moderated' AND $userRef = " . intval($fetchOptions['moderated']) . ')';
			}
		}

		return "$stateRef IN (" . implode(',', $states) . ")$moderatedLimit";
	}

	/**
	 * Standardizes a set of permissions and a user ID to always
	 * have appropriate data. If an invalid permission set or user ID is
	 * provided, the current visitor's will be used.
	 *
	 * @param array|null $permissions Global pPermissions or null to use current visitor's permissions
	 * @param integer|null $userId User permissions belong to or null to use current visitor
	 */
	public function standardizePermissionsAndUserId(&$permissions, &$userId = 0)
	{
		if (!is_array($permissions))
		{
			$permissions = \XF::visitor()->getPermissionSet()->getGlobalPerms();
		}

		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}
	}

	/**
	 * Standardizes a set of node permissions and a user ID to always
	 * have appropriate data. If an invalid permission set or user ID is
	 * provided, the current visitor's will be used.
	 *
	 * @param integer $nodeId Node permissions are for
	 * @param array|null $permissions Permissions for node or null to use current visitor's permissions
	 * @param integer|null $userId User permissions belong to or null to use current visitor
	 */
	public function standardizeNodePermissionsAndUserId($nodeId, &$permissions, &$userId = 0)
	{
		if (!is_array($permissions))
		{
			$permissions = \XF::visitor()->getPermissionSet()->getContentPerms('node', $nodeId);
		}

		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}
	}

	/**
	 * Standardizes a permission combination and user ID to always have
	 * appropriate data. If null, users current visitor's values.
	 *
	 * @param integer|null $permissionCombinationId Permission combination ID or null to use current visitor
	 * @param integer|null $userId User permissions belong to or null to use current visitor
	 */
	public function standardizePermissionCombinationIdAndUserId(&$permissionCombinationId, &$userId = 0)
	{
		if ($permissionCombinationId === null)
		{
			$permissionCombinationId = \XF::visitor()->permission_combination_id;
		}

		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}
	}

	/**
	 * Standardizes a viewing user reference array. This array must contain all basic user info
	 * (preferably all user info) and include global permissions in a "permissions" key.
	 * If not an array or missing a user_id, the visitor's values will be used.
	 *
	 * @param array|null $viewingUser
	 */
	public function standardizeViewingUserReference(array &$viewingUser = null)
	{
		if (!is_array($viewingUser) || !isset($viewingUser['user_id']))
		{
			$viewingUser = XenForo_Visitor::getInstance()->toArray();
		}

		return $viewingUser;
	}

	/**
	 * Standardizes the viewing user reference for the specific node.
	 *
	 * @param integer $nodeId
	 * @param array|null $viewingUser Viewing user; if null, use visitor
	 * @param array|null $nodePermissions Permissions for this node; if null, use visitor's
	 */
	public function standardizeViewingUserReferenceForNode($nodeId, array &$viewingUser = null, array &$nodePermissions = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if (!is_array($nodePermissions))
		{
			$nodePermissions = \XF::visitor()->getPermissionSet()->getContentPerms('node', $nodeId);
		}
	}

	/**
	 * Helper to unserialize permissions in a list of items.
	 *
	 * @param array $items List of items
	 * @param string $serializedKey Key where serialized permissions are
	 * @param string $targetKey Key where unserialized permissions will go
	 *
	 * @return array List of items with permissions unserialized
	 */
	public function unserializePermissionsInList(array $items, $serializedKey, $targetKey = 'permissions')
	{
		foreach ($items AS &$item)
		{
			$item[$targetKey] = (!empty($item[$serializedKey])
				? unserialize($item[$serializedKey])
				: []
			);
		}

		return $items;
	}

	/**
	 * Gets the value of the specified field for each content type that has that field.
	 *
	 * @param string $fieldName
	 *
	 * @return array Format: [content type] => field value
	 */
	public function getContentTypesWithField($fieldName)
	{
		if (XenForo_Application::isRegistered('contentTypes'))
		{
			$contentTypes = XenForo_Application::get('contentTypes');
		}
		else
		{
			$contentTypes = XenForo_Model::create('XenForo_Model_ContentType')->getContentTypesForCache();
			XenForo_Application::set('contentTypes', $contentTypes);
		}

		$output = [];
		foreach ($contentTypes AS $contentType => $fields)
		{
			if (isset($fields[$fieldName]))
			{
				$output[$contentType] = $fields[$fieldName];
			}
		}

		return $output;
	}

	/**
	 * Gets the specified field from a content type, if specified for that type.
	 *
	 * @param string $contentType
	 * @param string $fieldName
	 *
	 * @return string|false
	 */
	public function getContentTypeField($contentType, $fieldName)
	{
		if (XenForo_Application::isRegistered('contentTypes'))
		{
			$contentTypes = XenForo_Application::get('contentTypes');
		}
		else
		{
			$contentTypes = XenForo_Model::create('XenForo_Model_ContentType')->getContentTypesForCache();
			XenForo_Application::set('contentTypes', $contentTypes);
		}

		if (isset($contentTypes[$contentType][$fieldName]))
		{
			return $contentTypes[$contentType][$fieldName];
		}
		else
		{
			return false;
		}
	}

	public function getCutOffCondition($column, $condition)
	{
		list($operator, $cutOff) = $condition;

		$this->assertValidCutOffOperator($operator, true);

		$db = $this->_getDb();

		if ($operator === '>=<')
		{
			list( , $cutOffLower, $cutOffHigher) = $condition;

			return "($column >= " . $db->quote($cutOffLower) . " AND $column <= " . $db->quote($cutOffHigher) . ')';
		}
		else
		{
			return "$column $operator " . $db->quote($cutOff);
		}
	}

	/**
	 * Ensures that a valid cut-off operator is passed.
	 *
	 * @param string $operator
	 * @param boolean $allowBetween
	 */
	public function assertValidCutOffOperator($operator, $allowBetween = false)
	{
		switch ($operator)
		{
			case '<':
			case '<=':
			case '=':
			case '>':
			case '>=':
				break;

			case '>=<':
				if ($allowBetween)
				{
					return;
				}
				// break missing intentionally

			default:
				throw new \InvalidArgumentException('Invalid cut off operator.');
		}
	}
}