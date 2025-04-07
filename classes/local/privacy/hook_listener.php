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


use core_sms\hook\after_sms_gateway_form_hook;

/**
 * Hook listener for sinch sms gateway.
 *
 * @package    smsgateway_sinch
 * @copyright  2024 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener for the sms gateway setup form.
     *
     * @param after_sms_gateway_form_hook $hook The hook to add to sms gateway setup.
     */
    public static function set_form_definition_for_sinch_sms_gateway(after_sms_gateway_form_hook $hook): void {
        if ($hook->plugin !== 'smsgateway_sinch') {
            return;
        }

        $mform = $hook->mform;

        $mform->addElement('static', 'information', '', get_string('sinch_information', 'smsgateway_sinch'));

        $mform->addElement(
            'passwordunmask',
            'access_key',
            get_string('access_key', 'smsgateway_sinch'),
            'maxlength="255" size="20"',
        );
        $mform->addElement(
            'passwordunmask',
            'service_plan_id',
            get_string('service_plan_id', 'smsgateway_sinch'),
            'maxlength="255" size="20"',
        );

        $mform->setType('access_key', PARAM_TEXT);
        $mform->addRule('access_key', null, 'required', null, 'client');
        $mform->addRule('access_key', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault(
            elementName: 'access_key',
            defaultValue: '',
        );

        $mform->setType('service_plan_id', PARAM_TEXT);
        $mform->addRule('service_plan_id', null, 'required', null, 'client');
        $mform->addRule('service_plan_id', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault(
            elementName: 'service_plan_id',
            defaultValue: '',
        );

    }

}
