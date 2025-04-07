<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace smsgateway_sinch;

use core_sms\hook\after_sms_gateway_form_hook;

/**
 * Hook listener for Sinch SMS gateway.
 *
 * @package    smsgateway_sinch
 * @copyright  2024 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener for the SMS gateway setup form.
     *
     * @param after_sms_gateway_form_hook $hook The hook to add to SMS gateway setup.
     */
    public static function set_form_definition_for_sinch_sms_gateway(after_sms_gateway_form_hook $hook): void {
        if ($hook->plugin !== 'smsgateway_sinch') {
            return;
        }

        $mform = $hook->mform;

        $mform->addElement('static', 'information', '', get_string('sinch_information', 'smsgateway_sinch'));

        // Service Plan ID
        $mform->addElement(
            'text',
            'service_plan_id',
            get_string('service_plan_id', 'smsgateway_sinch'),
            'maxlength="255" size="20"',
        );
        $mform->setType('service_plan_id', PARAM_TEXT);
        $mform->addRule('service_plan_id', null, 'required', null, 'client');
        $mform->addRule('service_plan_id', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault('service_plan_id', '');

        // Bearer Token
        $mform->addElement(
            'passwordunmask',
            'bearer_token',
            get_string('bearer_token', 'smsgateway_sinch'),
            'maxlength="255" size="20"',
        );
        $mform->setType('bearer_token', PARAM_TEXT);
        $mform->addRule('bearer_token', null, 'required', null, 'client');
        $mform->addRule('bearer_token', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault('bearer_token', '');

        // Send From Number
        $mform->addElement(
            'text',
            'send_from',
            get_string('send_from', 'smsgateway_sinch'),
            'maxlength="255" size="20"',
        );
        $mform->setType('send_from', PARAM_TEXT);
        $mform->addRule('send_from', null, 'required', null, 'client');
        $mform->addRule('send_from', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault('send_from', '');

        // API URL Selection
        $apiurls = [
            'us' => get_string('region_us', 'smsgateway_sinch'),
            'eu' => get_string('region_eu', 'smsgateway_sinch'),
            'au' => get_string('region_au', 'smsgateway_sinch'),
            'br' => get_string('region_br', 'smsgateway_sinch'),
            'ca' => get_string('region_ca', 'smsgateway_sinch'),
        ];

        $mform->addElement(
            'select',
            'api_url',
            get_string('api_url', 'smsgateway_sinch'),
            $apiurls,
        );
        $mform->setType('api_url', PARAM_ALPHA);
        $mform->addRule('api_url', null, 'required', null, 'client');
        $mform->setDefault('api_url', 'us');
        $mform->addHelpButton('api_url', 'api_url', 'smsgateway_sinch');

        // Country Code
        $mform->addElement(
            'text',
            'countrycode',
            get_string('countrycode', 'smsgateway_sinch'),
            'maxlength="4" size="4"',
        );
        $mform->setType('countrycode', PARAM_TEXT);
        $mform->addRule('countrycode', get_string('maximumchars', '', 4), 'maxlength', 4);
        $mform->setDefault('countrycode', '');
    }
} 