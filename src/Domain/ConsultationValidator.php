<?php
declare(strict_types=1);
namespace App\Domain;
final class ConsultationValidator {
 public function validate(array $input): array {
  $errors=[]; $name=trim((string)($input['customer_name']??'')); $email=trim((string)($input['email']??'')); $phone=trim((string)($input['phone']??''));
  if($name==='' || strlen($name)>120) $errors['customer_name']='Họ tên bắt buộc, tối đa 120 ký tự.';
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors['email']='Email không hợp lệ.';
  if($phone!=='' && !preg_match('/^[0-9+(). -]{8,20}$/',$phone)) $errors['phone']='Số điện thoại không hợp lệ.';
  return $errors;
 }
}