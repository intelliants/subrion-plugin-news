<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (iaView::REQUEST_HTML == $iaView->getRequestType())
	{
		if ($iaView->blockExists('latest_news'))
		{
			$stmt = '`status` = :status AND `lang` = :language ORDER BY `date` DESC';
			$iaDb->bind($stmt, array('status' => iaCore::STATUS_ACTIVE, 'language' => $iaView->language));

			$array = $iaDb->all(array('id', 'title', 'date', 'alias', 'body', 'image'), $stmt, 0, $iaCore->get('news_number_block'), 'news');
			$iaView->assign('news_latest', $array);
		}

		if ($iaView->blockExists('newsline'))
		{
			$news_count = $iaCore->get('newsline_row_count') * $iaCore->get('newsline_rows');

			$sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS n.`id`, n.`title`, n.`date`, n.`body`, n.`alias`, n.`image`, m.`fullname` 
	FROM `:prefix:table_news` n 
LEFT JOIN `:prefix:table_members` m ON (n.`member_id` = m.`id`) 
WHERE n.`status` = ':status' && `lang` = ':language'
ORDER BY `date` DESC
LIMIT :start, :limit
SQL;
			$sql = iaDb::printf($sql, array(
				'prefix' => $iaDb->prefix,
				'table_news' => 'news',
				'table_members' => iaUsers::getTable(),
				'status' => iaCore::STATUS_ACTIVE,
				'language' => $iaView->language,
				'start' => 0,
				'limit' => $news_count
			));
			$array = $iaDb->getAll($sql);

			$iaView->assign('newsline', $array);
		}
	}
}