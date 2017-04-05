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

    // TODO: make one query to db
    $news_count = $iaCore->get('newsline_row_count') * $iaCore->get('newsline_rows');
    $limit = max($iaCore->get('news_number_block'), $news_count);

    if ($iaView->blockExists('latest_news')) {
        $news = $iaNews->get($stmt, 0, $iaCore->get('news_number_block'));
        $iaView->assign('news_latest', $array);
    }

    if ($iaView->blockExists('newsline')) {
        $news = $iaNews->get('1 = 1', 0, $news_count);
        $iaView->assign('newsline', $array);
    }
}
