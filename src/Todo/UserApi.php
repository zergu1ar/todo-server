<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 04.07.17
 * Time: 10:29
 */

namespace Todo;

class UserApi
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
     * @return string
     */
    public function getApiUrl()
    {
        return $this->url;
    }

    /**
     * @param string $name
     * @param int $userId
     * @param string $token
     *
     * @return int
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
     * @param int $id
     *
     * @return string
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
     * @param int $userId
     * @param string $token
     *
     * @return bool
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