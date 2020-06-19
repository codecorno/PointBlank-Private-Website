<?php

namespace XF;

interface ResultSetInterface
{
	public function getResultSetData($type, array $ids, $filterViewable = true, array $results = null);
}