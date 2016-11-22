<?php

final class Sms
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * Sms constructor.
     */
    private function __construct()
    {
        // приватный конструктор ограничивает реализацию getInstance ()
    }


    /**
     * @return self
     */
    static public function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $str
     * @return array
     */
    public function parseSms($str = '')
    {
        if ($str) {
            // вытаскиваем из строки все простые числа и числа с плавающей точкой
            preg_match_all('/\-?\d+(\,\d{1,})?./im', $str, $out);
            $tmp = []; // промежуточный массив для отобранных данных
            foreach ($out[0] as $v) {
                if (preg_match('/,/', $v) or preg_match('/[а-яё]/iU', $v)) { //определяем что это сумма (к примеру 100р. или 100,51р.)
                    $tmp['cost'] = floatval(preg_replace('/,/', '.', $v)); //преобразуем в цифровой формат
                } else {
                    if (strlen($v) > 13) {
                        $tmp['wallet'] = intval($v); // кошелк самое длинное значение
                    } else {
                        $tmp['code'] = intval($v); // остальное это код
                    }
                }
            }

            unset($out); // удаляем массив чтобы освободить память

            if ($tmp) {
                return $tmp;
            } else {
                exit('Ошибка'); // выходим если массив пустой
            }

        } else {
            exit('Строка не найдена'); // выходим, если не была переданна строка
        }
    }

    protected function __clone()
    {
        // ограничивает клонирование объекта
    }

    private function __wakeup()
    {
        // запрещаем десерилизацию обьекта
    }

    private function __sleep()
    {
        // запрещаем серилизацию
    }
}


$str = 'Никому не говорите пароль! Его спрашивают только мошенники.
Пароль: 42021
Перевод на счет 410011875007033
Вы потратите 5025,13р.';

$tmp = Sms::getInstance()->parseSms($str);

?>

<table>
    <tr>
       <td>Кошелек:</td>
       <td><?php echo $tmp['wallet'];?></td>
    </tr>
    <tr>
        <td>Сумма:</td>
        <td><?php echo $tmp['cost'];?></td>
    </tr>
    <tr>
        <td>Код потверждения:</td>
        <td><?php echo $tmp['code'];?></td>
    </tr>
</table>