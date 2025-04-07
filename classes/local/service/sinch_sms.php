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

namespace smsgateway_sinch\local\service;

use core_sms\message_status;
use smsgateway_sinch\local\sinch_sms_service_provider as sinch_sms_service_provider;
use stdClass;

/**
 * Sinch SMS service provider implementation.
 *
 * @package    smsgateway_sinch
 * @copyright  2025 SB <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sinch_sms implements sinch_sms_service_provider {

    /**
     * Sends an SMS message using the Sinch API.
     *
     * @param string $messagecontent The content to send in the SMS message
     * @param string $phonenumber The destination for the message
     * @param stdClass $config The gateway configuration
     * @return message_status Status of the message
     */
    public static function send_sms_message(
        string $messagecontent,
        string $phonenumber,
        stdclass $config,
    ): message_status {
        global $SITE;

        try {
            $service_plan_id = $config->service_plan_id;
            $bearer_token = $config->bearer_token;
            $send_from = $config->send_from;
            
            // Get the correct API URL based on the selected region
            $region = $config->api_url;
            $api_url = "https://{$region}.sms.api.sinch.com/xms/v1/{$service_plan_id}/batches";
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    "from" => $send_from,
                    "to" => [$phonenumber],
                    "body" => $messagecontent
                ]),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $bearer_token,
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($err) {
                debugging("Sinch SMS Error: " . $err);
                return message_status::GATEWAY_NOT_AVAILABLE;
            }

            if ($httpcode >= 200 && $httpcode < 300) {
                return message_status::GATEWAY_SENT;
            } else {
                debugging("Sinch SMS Error: HTTP " . $httpcode . " - " . $response);
                return message_status::GATEWAY_NOT_AVAILABLE;
            }
        } catch (\Exception $e) {
            debugging($e->getMessage() . "\n" . $e->getTraceAsString());
            return message_status::GATEWAY_NOT_AVAILABLE;
        }
    }
}
