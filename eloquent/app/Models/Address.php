<?php

namespace App\Models;

class Address
{
    public string $street;
    public string $city;
    public string $country;
    public string $postal_code;

    /**
     * @param string $street
     * @param string $city
     * @param string $country
     * @param string $postalCode
     */
    public function __construct(string $street, string $city, string $country, string $postal_code)
    {
        $this->street = $street;
        $this->city = $city;
        $this->country = $country;
        $this->postal_code = $postal_code;
    }


}
