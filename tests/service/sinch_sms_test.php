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

namespace smsgateway_sinch\service;

use core_sms\message_status;
use PHPUnit\Framework\MockObject\MockObject;
use smsgateway_sinch\local\service\sinch_sms;

/**
 * Test class for Sinch SMS service.
 *
 * @package    smsgateway_sinch
 * @copyright  2024 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \smsgateway_sinch\local\service\sinch_sms
 */
final class sinch_sms_test extends \advanced_testcase {

    /**
     * Test successful SMS sending.
     *
     * @return void
     */
    public function test_send_sms_message_success(): void {
        $this->resetAfterTest();

        // Create test data.
        $messagecontent = 'Test message';
        $phonenumber = '+31612345678';
        $config = new \stdClass();
        $config->service_plan_id = 'test-plan-id';
        $config->bearer_token = 'test-token';
        $config->send_from = '+31687654321';
        $config->api_url = 'eu';

        // Mock curl functions.
        $this->mock_curl_functions([
            'curl_init' => 'curl_handle',
            'curl_setopt_array' => true,
            'curl_exec' => '{"id": "test-batch-id"}',
            'curl_error' => '',
            'curl_getinfo' => 200,
            'curl_close' => null,
        ]);

        // Send message.
        $status = sinch_sms::send_sms_message($messagecontent, $phonenumber, $config);

        // Assert success.
        $this->assertEquals(message_status::GATEWAY_SENT, $status);
    }

    /**
     * Test SMS sending with curl error.
     *
     * @return void
     */
    public function test_send_sms_message_curl_error(): void {
        $this->resetAfterTest();

        // Create test data.
        $messagecontent = 'Test message';
        $phonenumber = '+31612345678';
        $config = new \stdClass();
        $config->service_plan_id = 'test-plan-id';
        $config->bearer_token = 'test-token';
        $config->send_from = '+31687654321';
        $config->api_url = 'eu';

        // Mock curl functions with error.
        $this->mock_curl_functions([
            'curl_init' => 'curl_handle',
            'curl_setopt_array' => true,
            'curl_exec' => false,
            'curl_error' => 'Connection failed',
            'curl_getinfo' => 0,
            'curl_close' => null,
        ]);

        // Send message.
        $status = sinch_sms::send_sms_message($messagecontent, $phonenumber, $config);

        // Assert failure.
        $this->assertEquals(message_status::GATEWAY_NOT_AVAILABLE, $status);
    }

    /**
     * Test SMS sending with HTTP error.
     *
     * @return void
     */
    public function test_send_sms_message_http_error(): void {
        $this->resetAfterTest();

        // Create test data.
        $messagecontent = 'Test message';
        $phonenumber = '+31612345678';
        $config = new \stdClass();
        $config->service_plan_id = 'test-plan-id';
        $config->bearer_token = 'test-token';
        $config->send_from = '+31687654321';
        $config->api_url = 'eu';

        // Mock curl functions with HTTP error.
        $this->mock_curl_functions([
            'curl_init' => 'curl_handle',
            'curl_setopt_array' => true,
            'curl_exec' => '{"error": "Invalid token"}',
            'curl_error' => '',
            'curl_getinfo' => 401,
            'curl_close' => null,
        ]);

        // Send message.
        $status = sinch_sms::send_sms_message($messagecontent, $phonenumber, $config);

        // Assert failure.
        $this->assertEquals(message_status::GATEWAY_NOT_AVAILABLE, $status);
    }

    /**
     * Test SMS sending with different regions.
     *
     * @return void
     */
    public function test_send_sms_message_different_regions(): void {
        $this->resetAfterTest();

        $regions = ['us', 'eu', 'au', 'br', 'ca'];
        $messagecontent = 'Test message';
        $phonenumber = '+31612345678';

        foreach ($regions as $region) {
            $config = new \stdClass();
            $config->service_plan_id = 'test-plan-id';
            $config->bearer_token = 'test-token';
            $config->send_from = '+31687654321';
            $config->api_url = $region;

            // Mock curl functions.
            $this->mock_curl_functions([
                'curl_init' => 'curl_handle',
                'curl_setopt_array' => true,
                'curl_exec' => '{"id": "test-batch-id"}',
                'curl_error' => '',
                'curl_getinfo' => 200,
                'curl_close' => null,
            ]);

            // Send message.
            $status = sinch_sms::send_sms_message($messagecontent, $phonenumber, $config);

            // Assert success for each region.
            $this->assertEquals(message_status::GATEWAY_SENT, $status, "Failed for region: {$region}");
        }
    }

    /**
     * Mock curl functions for testing.
     *
     * @param array $returns Array of function names and their return values
     * @return void
     */
    private function mock_curl_functions(array $returns): void {
        foreach ($returns as $function => $return) {
            if (!function_exists($function)) {
                eval("function {$function}() { return '{$return}'; }");
            }
        }
    }
}
