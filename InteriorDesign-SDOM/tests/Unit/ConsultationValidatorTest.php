<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\ConsultationValidator;
use PHPUnit\Framework\TestCase;

final class ConsultationValidatorTest extends TestCase
{
    public function testValidConsultationHasNoErrors(): void
    {
        $data = [
            'customer_name' => 'Nguyen Van A',
            'email' => 'a@example.com',
            'phone' => '0901234567',
            'room_type' => 'Căn hộ',
        ];
        self::assertSame([], ConsultationValidator::validate($data));
    }

    public function testInvalidEmailIsRejected(): void
    {
        $data = [
            'customer_name' => 'Nguyen Van A',
            'email' => 'not-an-email',
            'phone' => '0901234567',
            'room_type' => 'Căn hộ',
        ];
        self::assertArrayHasKey('email', ConsultationValidator::validate($data));
    }

    public function testMissingNameIsRejected(): void
    {
        $data = [
            'customer_name' => '',
            'email' => 'a@example.com',
            'phone' => '0901234567',
            'room_type' => 'Căn hộ',
        ];
        self::assertArrayHasKey('customer_name', ConsultationValidator::validate($data));
    }

    public function testInvalidPhoneIsRejected(): void
    {
        $data = [
            'customer_name' => 'Nguyen Van A',
            'email' => 'a@example.com',
            'phone' => 'abc',
            'room_type' => 'Căn hộ',
        ];
        self::assertArrayHasKey('phone', ConsultationValidator::validate($data));
    }
}
