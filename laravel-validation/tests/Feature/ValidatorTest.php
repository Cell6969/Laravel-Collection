<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidator()
    {
        $data = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }

    public function testValidatorInvalid()
    {
        $data = [
            'username' => '',
            'password' => 'admin',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        // get message from key
        $message->get('username');
        $message->get('password');
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorValidationException()
    {
        $data = [
            'username' => '',
            'password' => '',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $validator->validate();
            self::fail('ValidationException not thrown');
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidatorMultipleRules()
    {
        App::setLocale('id');
        $data = [
            'username' => 'test',
            'password' => 'test',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorValidData()
    {
        $data = [
            'username' => 'user@gmail.com',
            'password' => 'testing',
            'admin' => true,
            'city' => 'a',
            'premium' => true
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidatorInlineMessage()
    {
        $data = [
            'username' => 'test',
            'password' => 'test',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $messages = [
            "required" => ":attribute harus diisi",
            "email" => ":attribute harus email",
            "min" => ":attribute harus minimal :min karakter",
            "max" => ":attribute harus maksimal :max karakter",
        ];

        $validator = Validator::make($data, $rules, $messages);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorAdditionalValidation()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $data = $validator->getData();
            if ($data['username'] == $data['password']) {
                $validator->errors()->add('password', 'Password tidak boleh sama dengan username');
            };
        });
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorCustomRule()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => ['required', 'email', 'max:100', new Uppercase()], // add custom rules
            'password' => ['required', 'min:6', 'max:20', new RegistrationRule()],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorCustomFunctionRule()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => ['required', 'email', 'max:100', function (string $attribute, string $value, \Closure $fail) {
                if (strtoupper($value) !== $value) {
                    $fail("The field $attribute must be UPPER");
                }
            }],
            'password' => ['required', 'min:6', 'max:20', new RegistrationRule()],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorRuleClasses()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => ['required', new In(['aldo', 'dono', 'joko'])],
            'password' => ['required', Password::min(6)->letters()->numbers()->symbols()],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testNestedArray()
    {
        $data = [
            "name" => [
                "firstName" => "John",
                "lastName" => "Doe",
            ],
            "address" => [
                "street" => "bekasi",
                "province" => "jawa barat",
                "country" => "indonesia",
            ]
        ];

        $rules = [
            "name.firstName" => ["required", "max:100"],
            "name.lastName" => ["required", "max:100"],
            "address.street" => ["required", "max:100"],
            "address.province" => ["required", "max:100"],
            "address.country" => ["required", "max:100"],

        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testNestedIndexArray()
    {
        $data = [
            "name" => [
                "firstName" => "John",
                "lastName" => "Doe",
            ],
            "address" => [
                [
                    "street" => "bekasi",
                    "province" => "jawa barat",
                    "country" => "indonesia",
                ],
                [
                    "street" => "bekasi",
                    "province" => "jawa barat",
                    "country" => "indonesia",
                ],
                [
                    "street" => "bekasi",
                    "province" => "jawa barat",
                    "country" => "indonesia",
                ],
            ]
        ];

        $rules = [
            "name.firstName" => ["required", "max:100"],
            "name.lastName" => ["required", "max:100"],
            "address.*.street" => ["required", "max:100"],
            "address.*.province" => ["required", "max:100"],
            "address.*.country" => ["required", "max:100"],

        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
}
