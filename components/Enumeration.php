<?php
namespace app\components;

/**
 * Базовый класс для классов, которые содержат наборы констант.
 * <pre>
 *  class TestType extends Enumeration {
 *
 *      const DRAFT = 1;
 *      const ANNOUNCEMENT = 2;
 *      const SELL = 3;
 * </pre>
 *
 *  Помимо возможной реализации методов {@method getLabels} и {@method getDefault}
 * в наследнике должен быть перекрыт статический метод {@method i} для того,
 * чтобы передать собственное имя класса наследника по умолчанию при вызове:
 * <pre>
 *      public static function i($className=__CLASS__) {
 *          return parent::i($className);
 *      }
 * </pre>
 *
 *  Т.к. классы с наборами констант подразумевают их использование как статические,
 * то для доступа к методам нужно использовать точку входа {@method i}.
 * Из-за некоторых ограничений использование статических методов невозможно в
 * данном случае.
 *
 *  При получении списков (пары "значение - перевод", список значений) константы
 * берутся в том порядке, в котором они объявлены в классе.
 *
 *  При получении констант используется рефлексия класса. Работа с рефлексией
 * в среднем лишь на несколько миллисекунд дольше, чем работа с вручную составленным
 * списком соответствий названий констант и их значений. А учитывая то, что
 * полученные через рефлексию списки констант кешируются в статической переменной,
 * время работы первым и вторым способом примерно одинаково. К тому же первый способ
 * гораздо удобнее в использовании, что низвергает его несущественное отстование
 * по времени.
 *
 *  Примеры использования.<br>
 *  1. Доступ к константе
 * <pre>
 *      TestType::DRAFT
 * </pre>
 *
 *  2. Доступ к методу.
 * <pre>
 *      TestType::i()->getMap()
 *      TestType::i()->dontTranslateLabels()->map
 * </pre>
 */
abstract class Enumeration
{

    /**
     * @var string Имя класса наследника.
     */
    protected $_className;
    /**
     * @var boolean Флаг, переводить ли имена констант.
     */
    protected $_translateLabels = true;
    /**
     * @var string Категория словаря с переводами.
     */
    protected $_translationCategory = 'enumerations';

    /**
     * @var array Экземпляры классов наследников.
     */
    private static $_instances;
    /**
     * @var array Экземпляры рефлексий классов наследников.
     */
    private static $_reflections;
    /**
     * @var array Наборы констант классов наследников, где ключ массива - название
     * константы, а значение массива - значение константы.
     */
    private static $_constants;
    /**
     * @var array Инвертированные наборы констант классов наследников, где ключ массива -
     * это значение константы, а значение массива - название константы.
     */
    private static $_inverseConstants;
    /**
     * @var array Наборы значений констант классовв наследников.
     */
    private static $_values;
    /**
     * @var array Наборы названий констант классовв наследников.
     */
    private static $_names;


    /**
     * @param string $className Имя класса.
     */
    protected function __construct($className = __CLASS__)
    {
        $this->_className = $className;
    }

    /**
     *  Метод в стиле шаблона singleton для доступа к методам в статическом стиле.
     * Используется кеширование уже созданных экземпляров классов для более
     * быстрого доступа к ним.
     *
     * @param string $className Имя класса.
     * @return Enumeration Экземпляр класса.
     */
    public static function i($className = __CLASS__)
    {
        if (!isset(self::$_instances[$className])) {
            self::$_instances[$className] = new $className($className);
        }
        return self::$_instances[$className];
    }


    /**
     *  Возвращает рефлексию класса. Используется кеширование уже созданных рефлексий
     * классов для более быстрого доступа к ним.
     *
     * @return ReflectionClass Рефлексия класса.
     */
    public function getReflection()
    {
        if (!isset(self::$_reflections[$this->_className])) {
            self::$_reflections[$this->_className] = new ReflectionClass($this->_className);
        }
        return self::$_reflections[$this->_className];
    }

    /**
     *  Возвращает константы класса. Используется кеширование уже полученных списков
     * констант классов для более быстрого доступа к ним.
     *
     * @return array Массив констант класса. Ключ - название константы, значение -
     * значение константы.
     */
    public function getConstants()
    {
        if (!isset(self::$_constants[$this->_className])) {
            self::$_constants[$this->_className] = $this->getReflection()->getConstants();
        }
        return self::$_constants[$this->_className];
    }

    /**
     * Возвращает инвертированный набор констант класса наследника, где ключ массива -
     * это значение константы, а значение массива - название константы.
     *
     * При инвертировании используется функция array_flip(), что накладывает определенные
     * ограничения на значения констант:
     *  * тип значений констант должен быть либо string, либо integer,
     *  * все значения констант должны быть разными.
     *
     * @return array Инвертированный массив констант. Ключ - значение константы,
     * значение - название константы.
     */
    public function getInverseConstants()
    {
        if (!isset(self::$_inverseConstants[$this->_className])) {
            $resolvedArray = $this->resolveArrayToInverse($this->getConstants());
            self::$_inverseConstants[$this->_className] = array_flip($resolvedArray);
        }
        return self::$_inverseConstants[$this->_className];
    }

    /**
     *  Подготавливает массив для преобразования функции array_flip(): удаляет
     * все пары, типы значений которых не integer или string.
     *
     * @param array $array Массив для преобразования.
     * @return array Преобразованный массив, из которого удалены все пары, типы
     * значений которых не integer или string.
     *  Если передан не массив (array), то возвращается пустой массив.
     */
    protected function resolveArrayToInverse($array)
    {
        if (!is_array($array)) {
            return array();
        }
        foreach ($array as $key => $value) {
            if (!is_int($value) && !is_string($value)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     *  Возвращает массив значений констант в том порядке, в котором они объявленны
     * в классе.
     *
     * @return array Массив значений констант класса.
     */
    public function getValues()
    {
        if (!isset(self::$_values[$this->_className])) {
            self::$_values[$this->_className] = array_values($this->getConstants());
        }
        return self::$_values[$this->_className];
    }

    /**
     *    Получаем значение константы по ее имени.
     *
     * @param string $name Имя константы.
     * @return mixed Значение константы, либо null, если такого имени в списке
     * не существует.
     */
    public function getValue($name)
    {
        $map = $this->getConstants();
        if (array_key_exists($name, $map)) {
            return $map[$name];
        }
        return null;
    }

    /**
     *    Проверяет, есть ли переданное значение среди значений констант, учитывая
     * тип значения.
     *
     * @param mixed $value Значение для проверки.
     * @return boolean Есть ли значение среди значений констант.
     */
    public function issetValue($value)
    {
        return in_array($value, $this->getConstants(), true);
    }

    /**
     *  Возвращает массив названий констант в том порядке, в котором они объявленны
     * в классе.
     *
     * @return array Массив названий констант класса.
     */
    public function getNames()
    {
        if (!isset(self::$_names[$this->_className])) {
            self::$_names[$this->_className] = array_keys($this->getConstants());
        }
        return self::$_names[$this->_className];
    }

    /**
     *  Получаем название константы по ее значению. Для этого тип значения константы
     * должен быть либо integer, либо string. В противном случае, даже если константа
     * с таким значением существует, будет возвращен null.
     *
     * @param mixed $value Значение константы. В данном случае - это либо integer,
     * либо string.
     * @return string Возвращает имя константы по его значению. Если данному значению
     * ничего не соответствует, то возвращается null.
     */
    public function getName($value)
    {
        $map = $this->getInverseConstants();
        if (isset($map[$value])) {
            return $map[$value];
        }
        return null;
    }

    /**
     *    Проверяет, есть ли имя среди имен констант.
     *
     * @param string $name Имя константы для проверки.
     * @return boolean Есть ли имя среди имен констант.
     */
    public function issetName($name)
    {
        return array_key_exists($name, $this->getConstants());
    }


    /**
     *  Переключает флаг для перевода названий констант.
     *
     * @return Enumeration Возвращает себя же.
     */
    public function translateLabels()
    {
        $this->_translateLabels = true;
        return $this;
    }

    /**
     *  Переключает флаг для возвращения оригинальных названий констант.
     *
     * @return Enumeration Возвращает себя же.
     */
    public function dontTranslateLabels()
    {
        $this->_translateLabels = false;
        return $this;
    }

    public function getTranslationCategory()
    {
        return $this->_translationCategory;
    }

    public function setTranslationCategory($category)
    {
        $this->_translationCategory = $category;
        return $this;
    }

    /**
     *  Возвращает массив соответствий названий констант с их ключами для перевода.
     * В качестве ключей должны быть <strong>точные</strong> названия констант в классе.
     * В качестве значений - фразы на английском, которые являются ключами для
     * перевода по словарю.
     *
     *  В качестве ключей используются названия констант, т.к. они всегда могут
     * быть ключами массива и всегда уникальны. Использовать же в качестве ключей
     * значения констант невозможно, т.к. их тип может быть не только integer или
     * string (смотри функцию getInverseConstants()).
     *
     *  Пример реализации:
     * <pre>
     *       public function getLabels() {
     *           return array(
     *               'DRAFT' => 'Draft',
     *               'ANNOUNCEMENT' => 'Announcement',
     *               'SELL' => 'Sell',
     *               'PURCHASED' => 'Purchased',
     *               'CANCELED' => 'Canceled',
     *           );
     *      }
     * </pre>
     *
     * @return array Массив соответствий названий констант с их ключами для перевода.
     * Если выбран английский язык (en_us), то в качестве значения используется сам ключ.
     */
    public function getLabels()
    {
        return array();
    }

    public function getLabel($param, $byName = true)
    {
        if ($byName) {
            return $this->getLabelByName($param);
        }
        return $this->getLabelByValue($param);
    }

    /**
     *  Получение фразы (ключа перевода) соотвествующей константе по имени этой
     * константы. Если в словаре ({@method getLabels}) ничего не было найдено,
     * то возвращается само название константы. Если тип переданного параметра
     * не является string, то возвращается пустая строка.
     *
     * @param string $name Название константы.
     * @return string Ключ для перевода для константы, либо пустая строка, если
     * Если тип переданного параметра не является string.
     */
    protected function getLabelByName($name)
    {
        if (!is_string($name)) {
            return '';
        }
        $labels = $this->getLabels();
        $label = $name;
        if (isset($labels[$name])) {
            $label = $labels[$name];
        }
        return $label;
    }

    /**
     *  Получаем фразу (ключа перевода) соотвествующую константе по значению этой
     * константы.
     *  Для подробностей см. функции getName() и getLabel().
     *
     * @param mixed $value Значение константы.
     * @return string Фраза (ключа перевода) соотвествующая константе.
     */
    protected function getLabelByValue($value)
    {
        return $this->getLabelByName($this->getName($value));
    }

    public function getTranslation($param, $byName = true)
    {
        if ($byName) {
            return $this->getTranslationByName($param);
        }
        return $this->getTranslationByValue($param);
    }

    protected function getTranslationByName($name)
    {
        return A::t($this->_translationCategory, $this->getLabelByName($name));
    }

    protected function getTranslationByValue($value)
    {
        return A::t($this->_translationCategory, $this->getLabelByValue($value));
    }

    /**
     *  Получаем массив соответствий значений констант в том порядке, в котором они объявленны в классе. Значения
     * переводятся в string, плюс к ним конкатенируется переданный префик.
     * @param string $prefix Префикс к значению константы - что будет ключем в мапе.
     * По умолчанию передается пустая строка.
     * @return array Массив со значениями констант и соответствующими им переводами.
     */
    public function getMap($prefix = '')
    {
        $map = array();
        foreach ($this->getConstants() as $name => $value) {
            $label = $this->getLabelByName($name);
            $map[$prefix . $value] = $label;
        }
        return $map;
    }

    /**
     *  Значение по-умолчанию.
     *
     *  Пример реализации:
     * <pre>
     *      public function getDefault() {
     *          return self::SELL;
     *      }
     * </pre>
     *
     * @return mixed Значение по-умолчанию для данного набора констант.
     */
    public function getDefault()
    {
        return null;
    }


    /**
     *  Очищение кеша. Нужен на тот случай, если классы обновились, но в кеше
     * еще остались.
     *
     * @return Enumeration Возвращает себя, чистого и опрятного.
     */
    public function clearCache()
    {
        self::$_instances = self::$_reflections = self::$_constants = self::$_inverseConstants = self::$_values = self::$_names = null;
        return $this;
    }
}