<?php

namespace Mega\Modules\User\Tests\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mega\Services\Core\Test\Abstracts\TestCase;

/**
 * Class UpdateUserTest
 *
 * @type     Tests
 * @package  Mega\Interfaces\Api\Tests
 * @author   Mahmoud Zalt <mahmoud@zalt.me>
 */
class UpdateUserTest extends TestCase
{

    use DatabaseMigrations;

    private $endpoint = '/users';

    public function testUpdateExistingUser_()
    {
        $user = $this->getLoggedInTestingUser();

        $data = [
            'name'     => 'Updated Name',
            'password' => 'updated#Password',
        ];

        $endpoint = $this->endpoint . '/' . $user->id;

        // send the HTTP request
        $response = $this->apiCall($endpoint, 'patch', $data);

        // assert response status is correct
        $this->assertEquals($response->getStatusCode(), '200');

        // assert returned user is the updated one
        $this->assertResponseContainKeyValue([
            'email' => $user->email,
            'name'  => $data['name']
        ], $response);

        // assert data was updated in the database
        $this->seeInDatabase('users', ['name' => $data['name']]);
    }

    public function testUpdateExistingUserWithEmptyValues_()
    {
        $user = $this->getLoggedInTestingUser();

        $data = []; // empty data

        $endpoint = $this->endpoint . '/' . $user->id;

        // send the HTTP request
        $response = $this->apiCall($endpoint, 'patch', $data);

        // assert response status is correct
        $this->assertEquals($response->getStatusCode(), '417');

        $this->assertResponseContainKeyValue([
            'message' => 'All inputs are empty.'
        ], $response);

    }

    public function testUpdateDifferentUser_()
    {
        $data = [
            'name'     => 'Updated Name',
            'password' => 'updated#Password',
        ];

        $endpoint = $this->endpoint . '/' . 100;

        // send the HTTP request
        $response = $this->apiCall($endpoint, 'patch', $data);

        // assert response status is correct
        $this->assertEquals($response->getStatusCode(), '403');

        // assert user not allowed to proceed with the request
        $this->assertEquals($response->getContent(), 'Forbidden');
    }


}

