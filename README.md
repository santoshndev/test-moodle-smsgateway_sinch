# Sinch SMS Gateway Plugin for Moodle

A Moodle plugin that enables sending SMS messages through the Sinch SMS API.

## Description

This plugin integrates Sinch's SMS gateway service with Moodle, allowing administrators to send SMS messages to users through the Sinch API. It provides a reliable and efficient way to send notifications and alerts via SMS.

## Features

- Integration with Sinch's SMS API
- Configurable sender name/number
- Support for international phone numbers
- Easy setup and configuration
- Supports Multi-Factor Authentication (MFA)
- High delivery rates through Sinch's global network

## Requirements
- Moodle 4.1 or later
- A Sinch account and API credentials
- Valid phone number for sending SMS

## Installation and Configuration

### Step 1: Install the Plugin
1. Go to Site administration > Plugins > Install plugins
2. Upload the plugin file
3. Follow the installation wizard

### Step 2: Configure the SMS Gateway
1. Go to Site administration > Plugins > Message outputs > Manage SMS gateways
2. Click "Create new SMS gateway"
3. Select "Sinch" from the list of providers
4. Fill in the required configuration:
   - Service Plan ID
   - API Token
   - Sender ID/number
   - Other required settings
5. Click "Save changes"

## Documentation

For detailed documentation on specific features, please refer to:
- [Multi-Factor Authentication Configuration](docs/mfa-configuration.md)
- [Sinch API Documentation](https://developers.sinch.com/docs/sms/)

## Support

For support, please:
- Check the [Moodle documentation](https://docs.moodle.org/)
- Visit the [Sinch documentation](https://developers.sinch.com/docs/sms/)
- Contact the plugin maintainers through the issue tracker
- Visit our [donation page](https://customerpanel.sebsoft.nl/sebsoft/donate/intro.php) for premium support options

## License

This plugin is licensed under the GNU General Public License v3 or later. See the [LICENSE](LICENSE) file for details.

## Authors

- RvD
- RvS
- SB

## Sponsoring

If you find this plugin useful, please consider:
- Sponsoring through [GitHub Sponsors](https://github.com/sponsors/sebsoftnl)
- Making a donation through our [donation page](https://customerpanel.sebsoft.nl/sebsoft/donate/intro.php)

