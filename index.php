<?php

$config = array();

$html = '';
$configFileName = __DIR__ . '/config.php';
if (!file_exists($configFileName)) {
    $html = 'Произошла ошибка! В приложении отсутствует файл config.php';
} else {
    include __DIR__ . '/config.php';

    $login = $config['login'];
    $restapipassword = $config['restapipassword'];
    $oneboxurl = $config['oneboxurl'];

    if (!$login || !$restapipassword) {
        $html = 'Произошла ошибка! В файле config.php отсутствует apilogin или apipassword';
    }

    if (!$html) {
        include __DIR__ . '/OneboxApiClient.class.php';

        $requestData = array(
            'login' => $login,
            'restapipassword' => $restapipassword
        );

        // получаем api token
        $apiClient = new OneboxApiClient();
        $tokenResoponce = $apiClient->sendRequest($oneboxurl . 'api/v2/token/get/', $requestData);

        $token = false;
        if ($tokenResoponce['status'] == 1) {
            $token = $tokenResoponce['dataArray']['token'];
        } else {
            $html = 'Произошла ошибка при получении токена для api. ' . implode(',', $tokenResoponce['errorArray']);
        }

        if ($token) {
            // получаем последние 10 заказов
            $requestData = array(
                'fields' => array('id', 'name', 'description'),
                'limit' => 10
            );

            $orderResponce = $apiClient->sendRequest($oneboxurl . 'api/v2/order/get/', $requestData, $token);
            if ($orderResponce['status'] == 1) {
                $orderArray = $orderResponce['dataArray'];

                $html = '<table><thead><tr>
                    <td>id процесса</td>
                    <td>Название процесса</td>
                    <td>Описание процесса</td>
                    </tr>
                    </thead>';

                foreach ($orderArray as $index => $item) {
                    $orderUrl = $oneboxurl . $item['id'] . '/';
                    $html .= '<tr><td>'.$item['id'].'</td>
                        <td><a href="'.$orderUrl. '">'.$item['name'].'</a></td>
                        <td>'.$item['description']. '</td>
                        </tr>';
                }

                $html .= '</table>';
            } else {
                $html = 'Произошла ошибка при получении процессов по api. ' .
                    implode(',', $orderResponce['errorArray']);
            }
        }
    }
}

print $html;