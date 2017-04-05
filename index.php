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

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaNews = $iaCore->factoryModule('news', 'news');

    $iaDb->setTable('news');

    if (isset($iaCore->requestPath[0])) {
        $id = (int)$iaCore->requestPath[0];

        if (!$id) {
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }

        $entry = $iaNews->getById($id);
        if (empty($entry)) {
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }

        iaBreadcrumb::toEnd($entry['title'], IA_SELF);
        $openGraph = [
            'title' => $entry['title'],
            'url' => IA_SELF,
            'description' => $entry['body']
        ];
        $iaView->set('og', $openGraph);

        $iaView->assign('entry', $entry);

        $iaView->title(iaSanitize::tags($entry['title']));
    } else {
        $page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
        $page = ($page < 1) ? 1 : $page;

        $pagination = [
            'start' => ($page - 1) * $iaCore->get('news_number'),
            'limit' => (int)$iaCore->get('news_number'),
            'url' => $iaCore->factory('page', iaCore::FRONT)->getUrlByName('news') . '?page={page}'
        ];

        $rows = $iaNews->get('1=1', $pagination['start'], $pagination['limit']);

        $pagination['total'] = $iaDb->foundRows();

        $iaView->assign('news', $rows);
        $iaView->assign('pagination', $pagination);
    }

    $iaView->display('index');

    $iaDb->resetTable();
}
