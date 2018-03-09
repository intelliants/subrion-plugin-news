<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'news';
    protected $_path = 'news';
    protected $_itemName = 'news';

    protected $_helperName = 'news';

    protected $_gridColumns = ['title', 'body', 'date_added', 'date_modified', 'status'];
    protected $_gridFilters = ['status' => self::EQUAL];

    protected $_tooltipsEnabled = true;

    protected $_activityLog = ['item' => 'news'];

    public function init()
    {
        $this->_path = IA_ADMIN_URL . $this->getName() . IA_URL_DELIMITER;
    }

    protected function _modifyGridParams(&$conditions, &$values, array $params)
    {
        if (!empty($params['text'])) {
            $langCode = $this->_iaCore->language['iso'];
            $conditions[] = "(`title_{$langCode}` LIKE :text OR `body_{$langCode}` LIKE :text)";
            $values['text'] = '%' . iaSanitize::sql($params['text']) . '%';
        }
    }

    protected function _setDefaultValues(array &$entry)
    {
        $entry = [
            'slug' => '',
            'featured' => false,
            'status' => iaCore::STATUS_ACTIVE,
            'member_id' => iaUsers::getIdentity()->id,
        ];
    }
    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        if (empty($data['title_slug'])) {
            $entry['slug'] = iaSanitize::slug($data['title'][iaLanguage::getMasterLanguage()->code]);
        } else {
            $entry['slug'] = $data['title_slug'];
        }

        return !$this->getMessages();
    }

    protected function _entryUpdate(array $entryData, $entryId)
    {
        $entryData['date_modified'] = date(iaDb::DATETIME_FORMAT);

        return parent::_entryUpdate($entryData, $entryId);
    }

    protected function _entryAdd(array $entryData)
    {
        $entryData['date_added'] = date(iaDb::DATETIME_FORMAT);
        $entryData['date_modified'] = date(iaDb::DATETIME_FORMAT);

        return parent::_entryAdd($entryData);
    }

    protected function _getJsonSlug(array $data)
    {
        $title = iaSanitize::slug(isset($data['title']) ? $data['title'] : '');

        $slug = $this->getHelper()->url('view', [
            'id' => empty($data['id']) ? $this->_iaDb->getNextId() : $data['id'],
            'slug' => $title,
        ]);

        return ['data' => $slug];
    }

    protected function _assignValues(&$iaView, array &$entryData)
    {
        $entryData['id'] = $this->getEntryId();

        return parent::_assignValues($iaView, $entryData);
    }
}
