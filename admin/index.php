<?php
//##copyright##

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'news';

	protected $_gridFilters = array('status' => 'equal');
	protected $_gridQueryMainTableAlias = 'n';


	public function __construct()
	{
		parent::__construct();

		$iaNews = $this->_iaCore->factoryPlugin($this->getPluginName(), iaCore::ADMIN, $this->getName());
		$this->setHelper($iaNews);
	}

	protected function _indexPage(&$iaView)
	{
		$iaView->grid('_IA_URL_plugins/' . $this->getPluginName() . '/js/admin/index');
	}

	protected function _modifyGridParams(&$conditions, &$values, array $params)
	{
		if (!empty($_GET['text']))
		{
			$conditions[] = '(n.`title` LIKE :text OR n.`body` LIKE :text)';
			$values['text'] = '%' . iaSanitize::sql($_GET['text']) . '%';
		}

		if (!empty($params['owner']))
		{
			$conditions[] = '(m.`fullname` LIKE :owner OR m.`username` LIKE :owner)';
			$values['owner'] = '%' . iaSanitize::sql($params['owner']) . '%';
		}
	}

	protected function _gridQuery($columns, $where, $order, $start, $limit)
	{
		$sql =
			'SELECT SQL_CALC_FOUND_ROWS '
			. 'n.`id`, n.`title`, n.`alias`, n.`date`, n.`status`, m.`fullname` `owner`, 1 `update`, 1 `delete` ' .
			'FROM `:prefix:table_news` n ' .
			'LEFT JOIN `:prefix:table_members` m ON (n.`member_id` = m.`id`) ' .
			'WHERE :where :order ' .
			'LIMIT :start, :limit';

		$sql = iaDb::printf($sql, array(
			'prefix' => $this->_iaDb->prefix,
			'table_news' => $this->getTable(),
			'table_members' => iaUsers::getTable(),
			'where' => $where,
			'order' => $order,
			'start' => $start,
			'limit' => $limit
		));

		return $this->_iaDb->getAll($sql);
	}

	protected function _gridRead($params)
	{
		return (isset($params['get']) && 'alias' == $params['get'])
			? array('url' => IA_URL . 'news' . IA_URL_DELIMITER . $this->_iaDb->getNextId() . '-' . $this->getHelper()->titleAlias($params['title']))
			: parent::_gridRead($params);
	}

	protected function _setDefaultValues(array &$entry)
	{
		$entry['title'] = $entry['body'] = '';
		$entry['lang'] = $this->_iaCore->iaView->language;
		$entry['date'] = date(iaDb::DATETIME_FORMAT);
		$entry['status'] = iaCore::STATUS_ACTIVE;
		$entry['member_id'] = iaUsers::getIdentity()->id;
	}

	protected function _entryDelete($id)
	{
		$result = false;
		$stmt = iaDb::convertIds($id);
		if ($row = $this->_iaDb->row(array('title', 'image'), $stmt))
		{
			$result = (bool)$this->_iaDb->delete($stmt);

			if ($row['image'] && $result) // we have to remove the assigned image as well
			{
				$iaPicture = $this->_iaCore->factory('picture');
				$iaPicture->delete($row['image']);
			}

			if ($result)
			{
				$this->_iaCore->factory('log')->write(iaLog::ACTION_DELETE, array('item' => 'news', 'name' => $row['title'], 'id' => (int)$id));
			}
		}

		return $result;
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
		parent::_preSaveEntry($entry, $data, $action);

		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		if (!utf8_is_valid($entry['title']))
		{
			$entry['title'] = utf8_bad_replace($entry['title']);
		}
		if (empty($entry['title']))
		{
			$this->addMessage('title_is_empty');
		}

		if (!utf8_is_valid($entry['body']))
		{
			$entry['body'] = utf8_bad_replace($entry['body']);
		}
		if (empty($entry['body']))
		{
			$this->addMessage('body_is_empty');
		}

		if (empty($entry['date']))
		{
			$entry['date'] = date(iaDb::DATETIME_FORMAT);
		}

		$entry['alias'] = $this->getHelper()->titleAlias(empty($entry['alias']) ? $entry['title'] : $entry['alias']);


		if (!empty($data['owner']))
		{
			if ($memberId = $this->_iaDb->one_bind('id', '`username` = :name OR `fullname` = :name', array('name' => iaSanitize::sql($_POST['owner'])), iaUsers::getTable()))
			{
				$entry['member_id'] = $memberId;
			}
			else
			{
				$this->addMessage('incorrect_owner_specified');
			}
		}
		else
		{
			$entry['member_id'] = iaUsers::getIdentity()->id;
		}

		if ($this->getMessages())
		{
			return false;
		}

		unset($entry['owner']);

		if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'])
		{
			$iaPicture = $this->_iaCore->factory('picture');

			$path = iaUtil::getAccountDir();
			$file = $_FILES['image'];
			$token = iaUtil::generateToken();
			$info = array(
				'image_width' => 800,
				'image_height' => 600,
				'thumb_width' => 250,
				'thumb_height' => 250,
				'resize_mode' => iaPicture::CROP
			);

			if ($image = $iaPicture->processImage($file, $path, $token, $info))
			{
				if ($entry['image']) // it has an already assigned image
				{
					$iaPicture = $this->_iaCore->factory('picture');
					$iaPicture->delete($entry['image']);
				}

				$entry['image'] = $image;
			}
		}

		return true;
	}

	protected function _postSaveEntry(array &$entry, array $data, $action)
	{
		$iaLog = $this->_iaCore->factory('log');

		$actionCode = (iaCore::ACTION_ADD == $action)
			? iaLog::ACTION_CREATE
			: iaLog::ACTION_UPDATE;
		$params = array(
			'module' => 'news',
			'item' => 'news',
			'name' => $entry['title'],
			'id' => $this->getEntryId()
		);

		$iaLog->write($actionCode, $params);
	}

	protected function _assignValues(&$iaView, array &$entryData)
	{
		$iaUsers = $this->_iaCore->factory('users');
		$owner = empty($entryData['member_id']) ? iaUsers::getIdentity(true) :  $iaUsers->getInfo($entryData['member_id']);

		$entryData['owner'] = $owner['fullname'];
	}

	protected function _setPageTitle(&$iaView, array $entryData, $action)
	{
		$iaView->title(iaLanguage::get($action . '_' . $this->getName(), $iaView->title()));
	}

}