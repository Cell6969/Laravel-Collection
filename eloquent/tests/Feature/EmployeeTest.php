<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    public function testFactor()
    {
        $employee1 = Employee::factory()->juniorProgrammer()->make();
        $employee1->id = '1';
        $employee1->name = 'aldo';
        $employee1->save();

        self::assertNotNull(Employee::whereId('1')->first());

        $employee2 = Employee::factory()->seniorProgrammer()->create([
            'id' => '2',
            'name' => 'ardi'
        ]);

        self::assertNotNull($employee2);
    }

}
