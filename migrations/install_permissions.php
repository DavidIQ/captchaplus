<?php
/**
 *
 * CAPTCHA 4 post. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\captcha4post\migrations;

class install_permissions extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\phpbb\db\migration\data\v330\v330'];
    }

    /**
     * Add permissions data to the database during extension installation.
     *
     * @return array Array of data update instructions
     */
    public function update_data()
    {
        return [
            // Add new permissions
            ['permission.add', ['f_nocaptcha4post', false, 'f_noapprove']], // Copy settings from "Can post without approval"
        ];
    }

    /**
     * Remove the above permission from the database on extension removal.
     *
     * @return array Array of data removal instructions
     */
    public function revert_data()
    {
        return [
            ['permission.remove', ['f_nocaptcha4post']]
        ];
    }
}