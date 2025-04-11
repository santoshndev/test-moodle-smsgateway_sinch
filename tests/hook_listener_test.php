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
use moodleform;
use smsgateway_sinch\hook_listener;

/**
 * Hook listener test for Sinch SMS gateway.
 *
 * @package    smsgateway_sinch
 * @copyright  2024 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \smsgateway_sinch\hook_listener
 */
final class hook_listener_test extends \advanced_testcase {

    /**
     * Test that the form is properly configured for Sinch gateway.
     *
     * @return void
     */
    public function test_set_form_definition_for_sinch_sms_gateway(): void {
        $this->resetAfterTest();

        // Create a mock form.
        $mform = $this->getMockBuilder(moodleform::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up expectations for form elements.
        $mform->expects($this->exactly(6))
            ->method('addElement')
            ->withConsecutive(
                [$this->equalTo('static'), $this->equalTo('information'),
                    $this->equalTo(''), $this->anything()],
                [$this->equalTo('text'), $this->equalTo('service_plan_id'),
                    $this->anything(), $this->anything()],
                [$this->equalTo('passwordunmask'), $this->equalTo('bearer_token'),
                    $this->anything(), $this->anything()],
                [$this->equalTo('text'), $this->equalTo('send_from'),
                    $this->anything(), $this->anything()],
                [$this->equalTo('select'), $this->equalTo('api_url'),
                    $this->anything(), $this->anything()],
                [$this->equalTo('text'), $this->equalTo('countrycode'),
                    $this->anything(), $this->anything()]
            );

        // Set up expectations for setType calls.
        $mform->expects($this->exactly(5))
            ->method('setType')
            ->withConsecutive(
                [$this->equalTo('service_plan_id'), $this->equalTo(PARAM_TEXT)],
                [$this->equalTo('bearer_token'), $this->equalTo(PARAM_TEXT)],
                [$this->equalTo('send_from'), $this->equalTo(PARAM_TEXT)],
                [$this->equalTo('api_url'), $this->equalTo(PARAM_ALPHA)],
                [$this->equalTo('countrycode'), $this->equalTo(PARAM_TEXT)]
            );

        // Set up expectations for addRule calls.
        $mform->expects($this->exactly(4))
            ->method('addRule')
            ->withConsecutive(
                [$this->equalTo('service_plan_id'), $this->isNull(), $this->equalTo('required'),
                    $this->isNull(), $this->equalTo('client')],
                [$this->equalTo('bearer_token'), $this->isNull(), $this->equalTo('required'),
                    $this->isNull(), $this->equalTo('client')],
                [$this->equalTo('send_from'), $this->isNull(), $this->equalTo('required'),
                    $this->isNull(), $this->equalTo('client')],
                [$this->equalTo('api_url'), $this->isNull(), $this->equalTo('required'),
                    $this->isNull(), $this->equalTo('client')]
            );

        // Set up expectations for setDefault calls.
        $mform->expects($this->exactly(5))
            ->method('setDefault')
            ->withConsecutive(
                [$this->equalTo('service_plan_id'), $this->equalTo('')],
                [$this->equalTo('bearer_token'), $this->equalTo('')],
                [$this->equalTo('send_from'), $this->equalTo('')],
                [$this->equalTo('api_url'), $this->equalTo('us')],
                [$this->equalTo('countrycode'), $this->equalTo('')]
            );

        // Create a mock hook.
        $hook = $this->getMockBuilder(after_sms_gateway_form_hook::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hook->plugin = 'smsgateway_sinch';
        $hook->mform = $mform;

        // Call the hook listener.
        hook_listener::set_form_definition_for_sinch_sms_gateway($hook);
    }

    /**
     * Test that the form is not configured for non-Sinch gateway.
     *
     * @return void
     */
    public function test_set_form_definition_for_non_sinch_gateway(): void {
        $this->resetAfterTest();

        // Create a mock form.
        $mform = $this->getMockBuilder(MoodleQuickForm::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up expectation that no elements should be added.
        $mform->expects($this->never())
            ->method('addElement');

        // Create a mock hook for a different gateway.
        $hook = $this->getMockBuilder(after_sms_gateway_form_hook::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hook->plugin = 'smsgateway_other';
        $hook->mform = $mform;

        // Call the hook listener.
        hook_listener::set_form_definition_for_sinch_sms_gateway($hook);
    }
}
