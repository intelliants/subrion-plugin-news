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

class iaNews extends abstractModuleFront
{
    protected static $_table = 'news';

    protected $_itemName = 'news';

    protected $_moduleName = 'news';

    public $coreSearchEnabled = true;
    public $coreSearchOptions = [
        'tableAlias' => 'n',
        'regularSearchFields' => ['title', 'body'],
    ];

    private $_foundRows = 0;


    public function get($where, $start = null, $limit = null)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS n.*, m.`fullname` '
            . 'FROM `' . self::getTable(true) . '`  n '
            . 'LEFT JOIN `:table_members` m ON (n.`member_id` = m.`id`)'
            . 'WHERE ' . ($where ? $where . ' AND' : '') . "  n.`status` = 'active' "
            . 'ORDER BY n.`date_modified` DESC '
            . ($start || $limit ? "LIMIT $start, $limit" : '');
        $sql = iaDb::printf($sql, [
            'table_members' => iaUsers::getTable(true),
        ]);

        $rows = $this->iaDb->getAll($sql);

        $this->_foundRows = $this->iaDb->foundRows();
        $this->_processValues($rows);

        return $rows;
    }

    public function getFoundRows()
    {
        return $this->_foundRows;
    }
}
