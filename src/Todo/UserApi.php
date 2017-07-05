<?php

namespace Zergular\Todo;

/**
 * Class UserApi
 * @package Zergular\Todo
 */
class UserApi implements UserApiInterface
{
    /** @var string */
    private $url;

    /**
     * UserApi constructor.
     * @param string $apiUrl
     */
    public function __construct($apiUrl = 'http://localhost:8080/')
    {
        $this->url = $apiUrl;
    }

    /**
     * @inheritdoc
     */
    public function getApiUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function getIdByName($name, $userId, $token)
    {
        $data = $this->requestToService('findUser/', [
            'username' => $name,
            'userId' => $userId,
            'token' => $token
        ]);

        if (empty($data['error'])) {
            return empty($data['response'])
                ? 0
                : $data['response']['user']['id'];
        }
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getNameById($id)
    {
        $data = $this->requestToService('getUserNameById/', [
            'id' => $id
        ]);

        if (empty($data['error'])) {
            return empty($data['response'])
                ? ''
                : $data['response']['username'];
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function checkAuth($userId, $token)
    {
        $data = $this->requestToService('checkAuth/', [
            'userId' => $userId,
            'token' => $token
        ]);
        return empty($data['response'])
            ? FALSE
            : (bool)$data['response'];
    }

    /**
     * @param string $path
     * @param array $data
     *
     * @return mixed
     */
    private function requestToService($path, $data)
    {
        $params = http_build_query($data);
        return json_decode(file_get_contents($this->url . $path . '?' . $params), TRUE);
    }
}
