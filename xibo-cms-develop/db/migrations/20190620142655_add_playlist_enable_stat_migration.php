<?php
/**
 * Copyright (C) 2019 Xibo Signage Ltd
 *
 * Xibo - Digital Signage - http://www.xibo.org.uk
 *
 * This file is part of Xibo.
 *
 * Xibo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Xibo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Xibo.  If not, see <http://www.gnu.org/licenses/>.
 */

use Phinx\Migration\AbstractMigration;

class AddPlaylistEnableStatMigration extends AbstractMigration
{
    /** @inheritdoc */
    public function change()
    {
        $playlistTable = $this->table('playlist');

        if (!$playlistTable->hasColumn('enableStat')) {
            $this->execute('UPDATE `playlist` SET createdDt = \'1970-01-01 00:00:00\' WHERE createdDt < \'2000-01-01\'');
            $this->execute('UPDATE `playlist` SET modifiedDt = \'1970-01-01 00:00:00\' WHERE modifiedDt < \'2000-01-01\'');

            $playlistTable
                ->changeColumn('createdDt', 'datetime', ['null' => true, 'default' => null])
                ->changeColumn('modifiedDt', 'datetime', ['null' => true, 'default' => null])
                ->addColumn('enableStat', 'string', ['null' => true])
                ->save();
        }

        $settingsTable = $this->table('setting');

        if (!$this->fetchRow('SELECT * FROM `setting` WHERE setting = \'PLAYLIST_STATS_ENABLED_DEFAULT\'')) {
            $settingsTable
                ->insert([
                    [
                        'setting' => 'PLAYLIST_STATS_ENABLED_DEFAULT',
                        'value' => 'Inherit',
                        'userSee' => 1,
                        'userChange' => 1
                    ]
                ])
                ->save();
        }
    }
}
