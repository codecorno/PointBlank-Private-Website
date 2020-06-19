<?php

namespace XF\Db;

interface ReplicationAdapterInterface
{
	public function forceToWriteServer($type = 'implicit');
	public function isForcedToWriteServer();
	public function isForcedToWriteServerExplicit();
	public function getForceToWriteServerLength();
}