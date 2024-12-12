
# Inmovilla API Proxy (Unofficial)

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue)]()
[![PHP](https://img.shields.io/badge/php-%5E7.4%20%7C%7C%20%5E8.0-blue)]()
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

`inmovilla-api-proxy` is a tool designed to solve a specific problem when interacting with the Inmovilla API: **IP-based access restrictions**.

> **Note:** This project is not affiliated with, endorsed by, or maintained by Inmovilla.


## Why Use Inmovilla API Proxy?

Inmovilla restricts access to their API to specific IP addresses. This becomes a problem when you're developing from a machine outside the allowed IP range. Here's how the situation looks:

- **Production server with `inmovilla-api-client` installed**  
  ✅ Can connect to the Inmovilla API (IP is allowed).

- **Development machine with `inmovilla-api-client` installed**  
  ❌ Cannot connect to the Inmovilla API (IP is not allowed).

This package solves the issue by enabling the production server to act as a proxy. With `inmovilla-api-proxy` installed on the production server:

1. Your development machine sends requests to the **production server** (acting as the proxy).
2. The production server forwards these requests to the Inmovilla API.
3. The response from the Inmovilla API is sent back to your development machine.

---

## How It Works

Here’s a visual representation of the interaction flow between the Development Server, Production Server, and the Inmovilla API:

![UML Diagram](https://www.plantuml.com/plantuml/png/TO-nJiD0343tV8NL2GJvWGwe88Je5a575YldmTB5T_YS5FuULwTKH4dNytlFlaCnL1k7s1XR93YAaM9ld0HUoCv40gyqKKnv837u99r87w7J5CQApKyemVKXJHmZmdDtR9hiRUuvevkxTUPBxdWMMipSzf5zUhy3BE1ufPQLrU9L96lw-OK7k9s-DBRQY-ilPFt5zH9ed_wvUtW_dJhueE-HYZNpe68kxk4jwHarKBX2_WpjTgNa98KM6GTzzpPt80dZ4Fy0)


### Production Server
- **`inmovilla-api-client`** is installed to send API requests to Inmovilla.
- **`inmovilla-api-proxy`** is installed to act as a proxy for requests from the development machine.

### Development Machine
- **`inmovilla-api-client`** is installed to send requests to the proxy on the production server.

### Example Flow
1. **Without Proxy**
    - Development server → Direct connection to Inmovilla API → ❌ IP not allowed.

2. **With Proxy**
    - Development server → Proxy on production server → Inmovilla API → ✅ Works.

---

## Requirements

- **PHP**: 7.4 or higher.
- **Composer**: For dependency management.
- Libraries:
    - [`inigo-aldama/inmovilla-api-client`](https://packagist.org/packages/inigo-aldama/inmovilla-api-client)

---

## Installation

1. **Install the Proxy on the Production Server**
   Clone the repository or use Composer:
   ```bash
   composer require inigo-aldama/inmovilla-api-proxy
   ```

2. **Configure API Credentials**
   Update the `api.ini` configuration file on both servers (development and production):

   **`api.ini` on the production server:**
   ```ini
   api_url = "https://api.inmovilla.com/v1"
   domain = "production-domain.com"
   agency = "production-agency"
   password = "production-password"
   language = 1
   ```

   **`api.ini` on the development server:**
   ```ini
   api_url = "http://production-server-url/api-proxy"
   domain = "production-domain.com"
   agency = "development-agency"
   password = "development-password"
   language = 1
   ```

---

## Usage

1. **On the Production Server**
   Deploy `inmovilla-api-proxy` with the following setup:
   ```php
   <?php

   require 'vendor/autoload.php';

   use Inmovilla\ApiClient\ApiClientConfig;
   use Inmovilla\Proxy\ProxyService;
   use GuzzleHttp\Client as GuzzleClient;
   use GuzzleHttp\Psr7\HttpFactory;

   $serverConfig = ApiClientConfig::fromIniFile(__DIR__ . '/config/api.ini');
   $httpClient = new GuzzleClient();
   $requestFactory = new HttpFactory();

   $proxyService = new ProxyService($httpClient, $requestFactory, $serverConfig);

   $input = file_get_contents('php://input');
   $response = $proxyService->handleRequest($input);

   header('Content-Type: application/json');
   echo json_encode($response);
   ```

2. **On the Development Machine**
   Configure `api_url` to point to the production server's proxy, and use `inmovilla-api-client` as usual.

---

## Key Points

- The **proxy must be installed on the production server**; it's not needed on the development machine.
- The `api.ini` configuration must exist on both servers:
    - The **production server** uses the real Inmovilla API URL.
    - The **development machine** points `api_url` to the proxy URL.

### Data Sent to Inmovilla API
When using the proxy, the following data is sent to Inmovilla:
- **`api_url`**: From the production server.
- **`domain`**: From the production server.
- **`agency`**: From the development machine.
- **`password`**: From the development machine.
- **`language`**: From the development machine.

---

## Testing

Run PHPUnit tests to validate functionality:
```bash
./vendor/bin/phpunit --testdox
```

---

## Contribution

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/new-feature`).
3. Commit your changes (`git commit -m 'Add new feature'`).
4. Push to your branch (`git push origin feature/new-feature`).
5. Open a pull request.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

## Credits

- **Author**: Iñigo Aldama Gómez
- **Inmovilla API Client Repository**: [inmovilla-api-client](https://github.com/inigo-aldama/inmovilla-api-client)
