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

/**
 * Sinch SMS service provider implementation.
 *
 * @package    smsgateway_sinch
 * @copyright  2025 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

    #[\Override]
    public static function send_sms_message(
        string $messagecontent,
        string $phonenumber,
        stdclass $config,
    ): message_status {
        try {
            $serviceplanid = $config->service_plan_id;
            $bearertoken = $config->bearer_token;
            $sendfrom = $config->send_from;

            // Get the correct API URL based on the selected region.
            $region = $config->api_url;
            $apiurl = "https://{$region}.sms.api.sinch.com/xms/v1/{$serviceplanid}/batches";
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    "from" => $sendfrom,
                    "to" => [$phonenumber],
                    "body" => $messagecontent,
                ]),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $bearertoken,
                    "Content-Type: application/json",
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
