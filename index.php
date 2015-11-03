<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaDb->setTable('news');

	if (isset($iaCore->requestPath[0]))
	{
		$id = (int)$iaCore->requestPath[0];

		if (!$id)
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}

		$sql =
			'SELECT SQL_CALC_FOUND_ROWS ' .
			'n.`id`, n.`title`, n.`date`, n.`body`, n.`alias`, n.`image`, m.`fullname` ' .
			'FROM `:prefix:table_news` n ' .
			'LEFT JOIN `:prefix:table_members` m ON (n.`member_id` = m.`id`) ' .
			'WHERE n.`id` = :id AND n.`status` = \':status\'';

		$sql = iaDb::printf($sql, array(
			'prefix' => $iaDb->prefix,
			'table_news' => 'news',
			'table_members' => iaUsers::getTable(),
			'id' => $id,
			'status' => iaCore::STATUS_ACTIVE
		));

		$entry = $iaDb->getRow($sql);

		if (empty($entry))
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}

		iaBreadcrumb::toEnd($entry['title'], IA_SELF);
		$openGraph = array(
			'title' => $entry['title'],
			'url' => IA_SELF,
			'description' => $entry['body']
		);

		if ($entry['image'])
		{
			$openGraph['image'] = IA_CLEAR_URL . 'uploads/' . $entry['image'];
		}

		$iaView->set('og', $openGraph);

		$iaView->assign('entry', $entry);

		$iaView->title(iaSanitize::tags($entry['title']));
	}
	else
	{
		$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
		$page = ($page < 1) ? 1 : $page;

		$pagination = array(
			'start' => ($page - 1) * $iaCore->get('news_number'),
			'limit' => (int)$iaCore->get('news_number'),
			'url' => $iaCore->factory('page', iaCore::FRONT)->getUrlByName('news') . '?page={page}'
		);

		$order = ('date' == $iaCore->get('news_order')) ? 'ORDER BY `date` DESC' : 'ORDER BY `title` ASC';

		$stmt = '`status` = :status AND `lang` = :language';
		$iaDb->bind($stmt, array('status' => iaCore::STATUS_ACTIVE, 'language' => $iaView->language));

		$sql =
			'SELECT SQL_CALC_FOUND_ROWS ' .
			'n.`id`, n.`title`, n.`date`, n.`body`, n.`alias`, n.`image`, m.`fullname` ' .
			'FROM `:prefix:table_news` n ' .
			'LEFT JOIN `:prefix:table_members` m ON (n.`member_id` = m.`id`) ' .
			'WHERE n.' . $stmt . $order . ' LIMIT :start, :limit';

		$sql = iaDb::printf($sql, array(
			'prefix' => $iaDb->prefix,
			'table_news' => 'news',
			'table_members' => iaUsers::getTable(),
			'start' => $pagination['start'],
			'limit' => $pagination['limit']
		));

		$rows = $iaDb->getAll($sql);

		$pagination['total'] = $iaDb->foundRows();

		$iaView->assign('news', $rows);
		$iaView->assign('pagination', $pagination);
	}

	$iaView->display('index');

	$iaDb->resetTable();
}