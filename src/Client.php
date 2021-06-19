<?php

namespace Warface;

use Warface\Enums\{Location, ErrorMsg};
use Warface\Methods\{Achievement, Clan, Game, Rating, User, Weapon};
use GuzzleHttp\{Exception\GuzzleException, Exception\RequestException};

/**
 * Class Client
 * @package Warface
 *
 * @method Achievement achievement()
 * @method Clan clan()
 * @method Game game()
 * @method Rating rating()
 * @method User user()
 * @method Weapon weapon()
 */
class Client
{
    private \GuzzleHttp\Client $client;

    private string $location;

    private array $locations = [
        Location::RU => 'http://api.warface.ru/',
        Location::EN => 'http://api.wf.my.com/'
    ];

    /**
     * Client constructor.
     * @param string $location
     */
    public function __construct(string $location = Location::RU)
    {
        $this->location = $location;

        if (!(isset($location) && isset($this->locations[$location]))) {
            throw new \InvalidArgumentException(ErrorMsg::REGION);
        }

        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->locations[$location]]);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $class = __NAMESPACE__ . "\Methods\\" . ucfirst($name);

        if (!class_exists($class)) {
            throw new \RuntimeException(ErrorMsg::BRANCH);
        }

        return new $class($this);
    }

    /**
     * @param string $branch
     * @param array $params
     * @return array
     */
    protected function request(string $branch, array $params = []): array
    {
        $response = '';

        try {
            $getR = $this->client->get($branch, ['query' => $params]);
            $response = $getR->getBody();
        }
        catch (RequestException | GuzzleException $e) {
            if ($e->hasResponse())
            {
                $getR = $e->getResponse();
                $getB = $getR->getBody()->getContents();

                if ($getR->getStatusCode() == '400') {
                    throw new \DomainException(json_decode($getB, true)['message']);
                }
            }
        }

        return array_merge(json_decode($response, true), ['location' => $this->location]);
    }
}