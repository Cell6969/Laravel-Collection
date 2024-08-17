<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories');
    }

    public function testTransactionSuccess()
    {
        DB::transaction(function () {
            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "GADGET",
                "name" => "Gadget",
                "description" => "Gadget Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);

            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "FOOD",
                "name" => "Food",
                "description" => "Food Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);
        });

        $result = DB::select("select * from categories");
        self::assertCount(2, $result);
    }

    public function testTransactionFailed()
    {
        try {
            DB::transaction(function () {
                DB::insert('insert into categories(id, name, description, created_at) 
                values (:id, :name,:description, :created_at)', [
                    "id" => "GADGET",
                    "name" => "Gadget",
                    "description" => "Gadget Category",
                    "created_at" => "2020-10-10 10:10:10"
                ]);
    
                DB::insert('insert into categories(id, name, description, created_at) 
                values (:id, :name,:description, :created_at)', [
                    "id" => "GADGET",
                    "name" => "Gadget",
                    "description" => "Gadget Category",
                    "created_at" => "2020-10-10 10:10:10"
                ]);
            });
        } catch (QueryException $error){
            Log::error($error);
        }

        $result = DB::select("select * from categories");
        self::assertCount(0, $result);
    }

    public function testManualTransaction()
    {
        try {
            //code...
            DB::beginTransaction();

            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "GADGET",
                "name" => "Gadget",
                "description" => "Gadget Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);

            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "FOOD",
                "name" => "Food",
                "description" => "Food Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);
            
            DB::commit();
        } catch (QueryException $th) {
            //throw $th;
            Log::error($th);
        }

        $result = DB::select("select * from categories");
        self::assertCount(2, $result);
    }
}
