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

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'news';

	protected $_gridFilters = array('status' => 'equal');
	protected $_gridQueryMainTableAlias = 'n';


	public function __construct()
	{
		parent::__construct();

		$iaNews = $this->_iaCore->factoryPlugin($this->getModuleName(), iaCore::ADMIN, $this->getName());
		$this->setHelper($iaNews);
	}

	protected function _indexPage(&$iaView)
	{
		$iaView->grid('_IA_URL_modules/' . $this->getModuleName() . '/js/admin/index');
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
		$sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS n.`id`, n.`title`, n.`alias`, n.`date`, n.`status`, m.`fullname` `owner`, 1 `update`, 1 `delete` 
	FROM `:prefix:table_news` n 
LEFT JOIN `:prefix:table_members` m ON (n.`member_id` = m.`id`) 
WHERE :where :order 
LIMIT :start, :limit
SQL;
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

		if (!$this->getMessages())
		{
			if (isset($_FILES['image']['error']) && !$_FILES['image']['error'])
			{
				try
				{
					$iaField = $this->_iaCore->factory('field');

					$path = $iaField->uploadImage($_FILES['image'], 800, 600, 250, 250, 'crop');

					empty($entry['image']) || $iaField->deleteUploadedFile('image', $this->getTable(), $this->getEntryId(), $entry['image']);
					$entry['image'] = $path;
				}
				catch (Exception $e)
				{
					$this->addMessage($e->getMessage(), false);
				}
			}
		}

		unset($entry['owner']);

		return !$this->getMessages();
	}

	protected function _postSaveEntry(array &$entry, array $data, $action)
	{
		$iaLog = $this->_iaCore->factory('log');

		$actionCode = (iaCore::ACTION_ADD == $action) ? iaLog::ACTION_CREATE : iaLog::ACTION_UPDATE;
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

		$entryData['owner'] = $owner['fullname'] . " ({$owner['email']})";
	}

	protected function _setPageTitle(&$iaView, array $entryData, $action)
	{
		$iaView->title(iaLanguage::get($action . '_' . $this->getName(), $iaView->title()));
	}

}