<?php

namespace XF\ControllerPlugin;

class UserCriteriaAction extends AbstractPlugin
{
	public function getInitializedSearchData(array $addCriteria = [])
	{
		$criteria = $this->getCriteriaInput();
		$originalCriteria = $criteria;
		if ($addCriteria)
		{
			$criteria = array_replace($criteria, $addCriteria);
		}

		$searcher = $this->searcher('XF:User', $criteria);

		$total = $searcher->getFinder()->total();
		if (!$total)
		{
			throw $this->exception($this->error(\XF::phraseDeferred('no_users_matched_specified_criteria')));
		}

		return [
			'searcher' => $searcher,
			'total' => $total,
			'originalCriteria' => $originalCriteria,
			'criteria' => $criteria
		];
	}

	public function getCriteriaInput()
	{
		if ($this->request->exists('json_criteria'))
		{
			return $this->filter('json_criteria', 'json-array');
		}
		else
		{
			return $this->filter('criteria', 'array');
		}
	}
}