<?php
declare(strict_types=1);

namespace App\Domain;

final class ConsultationValidator
{
    public static function validate(array $input): array
    {
        $errors = [];
        $name = trim((string)($input['customer_name'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));
        $phone = trim((string)($input['phone'] ?? ''));
        $roomType = trim((string)($input['room_type'] ?? ''));

        if ($name === '' || mb_strlen($name) > 120) {
            $errors['customer_name'] = 'Họ tên không được để trống và tối đa 120 ký tự.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors['email'] = 'Email không hợp lệ.';
        }
        if (!preg_match('/^[0-9+() .-]{8,30}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ.';
        }
        if ($roomType === '' || mb_strlen($roomType) > 80) {
            $errors['room_type'] = 'Vui lòng chọn loại không gian.';
        }

        return $errors;
    }
}
