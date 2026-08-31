<?php

use App\Rules\IpOrCidr;

function validateIpOrCidr(array $values): ?string
{
    $error = null;
    (new IpOrCidr)->validate('skip_ips', $values, function (string $msg) use (&$error) {
        $error = $msg;
    });

    return $error;
}

describe('IpOrCidr', function () {
    it('passes a valid IPv4 address', function () {
        expect(validateIpOrCidr(['1.2.3.4']))->toBeNull();
    });

    it('passes a valid IPv4 CIDR range', function () {
        expect(validateIpOrCidr(['10.0.0.0/8']))->toBeNull();
    });

    it('passes a valid IPv6 address', function () {
        expect(validateIpOrCidr(['::1']))->toBeNull();
    });

    it('passes a valid IPv6 CIDR range', function () {
        expect(validateIpOrCidr(['2001:db8::/32']))->toBeNull();
    });

    it('passes an empty array', function () {
        expect(validateIpOrCidr([]))->toBeNull();
    });

    it('fails a hostname string', function () {
        expect(validateIpOrCidr(['example.com']))->not->toBeNull();
    });

    it('fails a CIDR with an invalid prefix length', function () {
        expect(validateIpOrCidr(['10.0.0.0/33']))->not->toBeNull();
    });

    it('fails a CIDR with a non-numeric prefix', function () {
        expect(validateIpOrCidr(['10.0.0.0/abc']))->not->toBeNull();
    });
});
