<?php
declare(strict_types=1);
namespace Tests\Unit;
use App\Domain\ConsultationValidator;
use PHPUnit\Framework\TestCase;
final class ConsultationValidatorTest extends TestCase {
 public function testAcceptsValidInput():void {$this->assertSame([], (new ConsultationValidator())->validate(['customer_name'=>'Nguyen Van A','email'=>'a@example.com','phone'=>'0901234567']));}
 public function testRejectsEmptyNameAndInvalidEmail():void {$e=(new ConsultationValidator())->validate(['customer_name'=>' ','email'=>'wrong']);$this->assertArrayHasKey('customer_name',$e);$this->assertArrayHasKey('email',$e);}
 public function testRejectsInvalidPhone():void {$e=(new ConsultationValidator())->validate(['customer_name'=>'A','email'=>'a@example.com','phone'=>'abc']);$this->assertArrayHasKey('phone',$e);}
}