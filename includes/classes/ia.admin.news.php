<?php
//##copyright##

class iaNews extends abstractPlugin
{
	const ALIAS_SUFFIX = '.html';

	protected static $_table = 'news';

	public $dashboardStatistics = true;


	public function getDashboardStatistics()
	{
		$statuses = array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE);
		$rows = $this->iaDb->keyvalue('`status`, COUNT(*)', '1 GROUP BY `status`', self::getTable());
		$total = 0;

		foreach ($statuses as $status)
		{
			isset($rows[$status]) || $rows[$status] = 0;
			$total += $rows[$status];
		}

		return array(
			'icon' => 'news',
			'item' => iaLanguage::get('news'),
			'rows' => $rows,
			'total' => $total,
			'url' => 'news/'
		);
	}

	public function titleAlias($title)
	{
		$result = iaSanitize::tags($title);

		iaUtil::loadUTF8Functions('ascii', 'utf8_to_ascii');
		utf8_is_ascii($result) || $result = utf8_to_ascii($result);

		$result = rtrim($result, self::ALIAS_SUFFIX);
		$result = iaSanitize::alias($result);
		$result = substr($result, 0, 150); // limitation according to the DB scheme
		$result .= self::ALIAS_SUFFIX;

		return $result;
	}
}