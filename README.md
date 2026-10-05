# Magento 2 TFA Bypass by IP

This Magento 2 module bypasses Two-Factor Authentication (TFA) for requests originating from explicitly configured IP addresses.

## Requirements

- Magento 2
- `Magento_TwoFactorAuth` enabled
- A stable, trusted source IP address for the developer or office network

## Installation

The module is located at:

```text
app/code/Dev/BypassTfa
```

From the Magento project root, enable and register the module:

```bash
bin/magento module:enable Dev_BypassTfa
bin/magento setup:upgrade
bin/magento cache:flush
```

For production mode, also run the normal deployment steps required by the project, such as dependency compilation and static content deployment.

## Configuration

Edit [`app/code/Dev/BypassTfa/Plugin/BypassTfaByIp.php`](app/code/Dev/BypassTfa/Plugin/BypassTfaByIp.php) and replace the placeholder value in `ALLOWED_IPS`:

```php
private const ALLOWED_IPS = [
    '203.0.113.10',
];
```

Multiple addresses can be listed:

```php
private const ALLOWED_IPS = [
    '203.0.113.10',
    '198.51.100.25',
];
```

The comparison is an exact match. CIDR ranges, wildcards, and hostname values are not supported.

After changing the list, clear Magento caches and restart PHP workers if the deployment uses opcode caching:

```bash
bin/magento cache:flush
```

## How it works

The module registers a plugin on `Magento\\TwoFactorAuth\\Model\\TfaSession::isGranted`. If TFA has not already been granted and the request's resolved remote address is in `ALLOWED_IPS`, the plugin returns `true`.

## Security warnings

This module weakens account security by disabling TFA for matching IP addresses. Use it only in a controlled development or internal environment.

- Do not enable it on a public production installation unless this risk is explicitly accepted.
- Allow only fixed, trusted addresses. Do not add broad or shared network ranges without review.
- Ensure the web server and proxy configuration resolves the real client address correctly. A misconfigured proxy or trusted-header setup can cause the wrong address to be trusted.
- Restrict administrative access with network controls and Magento roles in addition to this module.
- Remove the module or disable it when it is no longer needed.

## Disable or uninstall

To disable the module:

```bash
bin/magento module:disable Dev_BypassTfa
bin/magento setup:upgrade
bin/magento cache:flush
```

To remove it completely, delete `app/code/Dev/BypassTfa` after disabling it and run the project's standard Magento cleanup and deployment commands.

## Review notes

The committed `ALLOWED_IPS` value (`192.168.x.xxx`) is a placeholder and is not a valid literal client IP, so the bypass will not activate until it is replaced. The implementation otherwise preserves an already-granted TFA result and leaves non-allowlisted requests unchanged.
