<?php
$userIp = '';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the request path is like /ip/223.5.5.5 (or /ip/223.5.5.5/...), prefer that IP.
// Extract last segment when the first segment equals "ip" and validate it as an IP.
$routeIp = null;
$path = trim($uri, '/');
$parts = $path === '' ? [] : explode('/', $path);
if (count($parts) >= 2 && strtolower($parts[0]) === 'ip') {
    $candidate = $parts[1];
    if($candidate === 'getdns' || $candidate === 'peer')
    {
        switch ($candidate) {
	        case 'getdns':
		        echo 'ipyard.com';
		        break;
	        default:
		        http_response_code(204);
		        break;
        }
        exit;
    }
    if (
        filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
        filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
    ) {
        $routeIp = $candidate;
    } elseif (preg_match('/^([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/', $candidate)) {
        // If it's a domain, resolve to IP (gethostbyname only returns IPv4)
        $resolvedIp = gethostbyname($candidate);
        if (
            filter_var($resolvedIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
            filter_var($resolvedIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
        ) {
            $routeIp = $resolvedIp;
        }
    }
}

if ($routeIp !== null) {
    // Route-provided IP takes precedence
    $userIp = $routeIp;
} else {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $userIp = trim($forwardedIps[0]);
    } else {
        $userIp = $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

$concurrentIpAddr = $userIp;
$asnNum = 0;
$asnName = 'IPYard';
$asnDomain = 'ipyard.com';
$ip_longitude = '0.0';
$ip_latitude = '0.0';
$ip_city = '';
$ip_country = '';
$ip_stateOProvince = '';
$asnCompany = '';
$broadcast_status = false;

/**
 * Fetch IP info from IPDB API and populate variables.
 *
 * Preference for translations: try 'zh-CN' first, then fall back to the default value
 * or to 'en' translation when available.
 */
function fetch_ipdb_info(string $ip): array
{
    $url = 'https://ipdb.purecheat.com/api/v1/paas/ip/fetch';
    $concurrentTime = (int)(microtime(true) * 1000);
    $payload = [
        // API allows appid to be empty; only address is required for a simple lookup.
        'appid' => '色猫娘',
        'address' => $ip,
        'timestamp' => $concurrentTime,
        'datasign' => md5('色猫娘'.$ip.$concurrentTime.'可爱变态二次元萝莉魅魔好色公猫娘'),
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: PHP-IPDB-Client/1.0',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    // If your environment requires you to skip SSL verification (not recommended), uncomment:
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $raw = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = [
        'ok' => false,
        'raw' => $raw,
        'http_code' => $httpCode,
        'curl_error' => $curlErr,
        'data' => null,
    ];

    if ($raw === false || $raw === null || $curlErr) {
        return $result;
    }

    $outer = json_decode($raw, true);
    if (!is_array($outer) || !isset($outer['data'])) {
        return $result;
    }

    // API returns "data" as a JSON string in many samples; handle both cases.
    $dataField = $outer['data'];
    if (is_string($dataField)) {
        $inner = json_decode($dataField, true);
    } elseif (is_array($dataField)) {
        $inner = $dataField;
    } else {
        $inner = null;
    }

    if (!is_array($inner)) {
        return $result;
    }

    // location may itself be a JSON string
    $location = [];
    if (!empty($inner['location']) && is_string($inner['location'])) {
        $locDecoded = json_decode($inner['location'], true);
        if (is_array($locDecoded)) {
            $location = $locDecoded;
        }
    } elseif (!empty($inner['location']) && is_array($inner['location'])) {
        $location = $inner['location'];
    }

    // helper to pick translation with preference for zh-CN, then en, then fallback value
    $pickTranslation = function ($fieldArray, $fallback = null) {
        if (!is_array($fieldArray)) {
            return $fallback;
        }
        if (isset($fieldArray['zh-CN']) && $fieldArray['zh-CN'] !== '') {
            return $fieldArray['zh-CN'];
        }
        if (isset($fieldArray['en']) && $fieldArray['en'] !== '') {
            return $fieldArray['en'];
        }
        // pick first non-empty value
        foreach ($fieldArray as $v) {
            if ($v !== '') {
                return $v;
            }
        }
        return $fallback;
    };

    $city = null;
    if (!empty($inner['cityWithTranslation'])) {
        $city = $pickTranslation($inner['cityWithTranslation'], $inner['city'] ?? null);
    }
    if (empty($city) && !empty($inner['city'])) {
        $city = $inner['city'];
    }

    $country = null;
    if (!empty($inner['countryWithTranslation'])) {
        $country = $pickTranslation($inner['countryWithTranslation'], $inner['country'] ?? null);
    }
    if (empty($country) && !empty($inner['country'])) {
        $country = $inner['country'];
    }

    $state = null;
    if (!empty($inner['stateOrProvinceWithTranslation'])) {
        $state = $pickTranslation($inner['stateOrProvinceWithTranslation'], $inner['stateOrProvince'] ?? null);
    }
    if (empty($state) && !empty($inner['stateOrProvince'])) {
        $state = $inner['stateOrProvince'];
    }

    $asnCode = isset($inner['asnCode']) ? intval($inner['asnCode']) : 0;
    $asnName = $inner['asnName'] ?? ($inner['asnCompany'] ?? '');
    $asnCompany = $inner['asnCompany'] ?? $asnName;
    $asnPrefix = $inner['asnPrefix'] ?? null;

    $lat = isset($location['latitude']) ? (string)$location['latitude'] : ($inner['lat'] ?? '');
    $lon = isset($location['longitude']) ? (string)$location['longitude'] : ($inner['lon'] ?? '');
    $nativeIP = $inner['nativeIPStatus'] ?? null;

    $result['ok'] = true;
    $result['data'] = [
        'city' => $city,
        'country' => $country,
        'state' => $state,
        'asn_code' => $asnCode,
        'asn_name' => $asnName,
        'asn_company' => $asnCompany,
        'asn_prefix' => $asnPrefix,
        'latitude' => $lat,
        'longitude' => $lon,
        'nativeIPStatus' => $nativeIP,
        'raw_inner' => $inner,
        'raw_outer' => $outer,
    ];

    return $result;
}

// Attempt fetch and populate the module-level variables.
// Keep original defaults if the fetch fails.
$fetchResult = ['ok' => false];
if (!empty($concurrentIpAddr)) {
    $fetchResult = fetch_ipdb_info($concurrentIpAddr);
}

if (!empty($fetchResult['ok']) && isset($fetchResult['data'])) {
    $ip_city = $fetchResult['data']['city'] ?? $ip_city;
    $ip_country = $fetchResult['data']['country'] ?? $ip_country;
    $ip_stateOProvince = $fetchResult['data']['state'] ?? $ip_stateOProvince;
    if($ip_city == $ip_stateOProvince) $ip_stateOProvince = '';
    $ip_latitude = $fetchResult['data']['latitude'] ?? $ip_latitude;
    $ip_longitude = $fetchResult['data']['longitude'] ?? $ip_longitude;
    $asnNum = $fetchResult['data']['asn_code'] ?? $asnNum;
    $asnName = $fetchResult['data']['asn_name'] ?? $asnName;
    $asnCompany = $fetchResult['data']['asn_company'] ?? $asnCompany;
    if (!empty($fetchResult['data']['asn_prefix'])) {
        $asnDomain = $fetchResult['data']['asn_prefix'];
    }
    $broadcast_status = !empty($fetchResult['data']['nativeIPStatus']) && $fetchResult['data']['nativeIPStatus'] === 'Broadcast';
}