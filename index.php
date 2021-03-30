<?php
/**
 * A file with the task
 * 1.	Создать класс Item, который не наследуется. В конструктор класса передается ID объекта.
 * 2.	Описать свойства (int) id, (string) name, (int) status, (bool) changed. Свойства доступны только внутри класса.
 * 3.	Создать метод init(). Предусмотреть одноразовый вызов метода.
 * 4.	Метод доступен только внутри класса.
 * 5.	Метод получает из таблицы objects. данные name и status и заполняет их в свойства экземпляра 
 * (реализация работы с базой не требуется, представим что класс уже работает с бд). 
 * Эти данные также необходимо хранить в сыром виде внутри объекта, до сохранения.
 * 6.	Сделать возможным получение свойств объекта, используя magic methods.
 * 7.	Сделать возможным задание свойств объекта, используя magic methods с проверкой вводимого 
 * значения на заполненность и тип значения. Свойство ID не поддается записи.
 * 8.	Создать метод save().
 * 9.	Метод публичный.
 * 10.	Метод сохраняет установленные значения name и status в случае, если свойства объекта были изменены извне.
 * 11.	Класс должен быть задокументирован в стиле PHPDocumentor.
 */

/**
 * Item class
 * 
 * Gets an Item from table objects and
 * does some actions to it
 * 
 * @copyright Me, 2021
 * @license MIT
 * @property-read int $id UID of Item
 * @property string $name name of an Item
 * @property int $status status of the state of an Item
 * @property bool $changed if there were changes
 * 
 */
final class Item {
  /** @var int $id - UID of an Item */
  private int $id = -1;
  /** @var string $name name of an Item */
  private string $name = "";
  /** @var int $status what changes did happen */
  private int $status = 0;
  /** @var bool $changed whether the data was changed */
  private bool $changed = false;

  /**
   * Init an object of the class
   * @param int $objID The identifier of an Item
   */
  function __construct(int $objID) {
    $this->id = $objID;
    $this->init();
    print ("INITIALIZED");
  }

  /**
   * private function to read from DB
   * @param string $q a query string to SELECT from db
   * @returns array
   */
  private function db_read(string $q) {
    /** data from db will be serialized, so we decode first */
    $a = json_decode('{"0":"DefaultName", "1":"1"}');
    /** array dereferencing gibberish in order to treat possible bad literals */
    $a = array($a->{"0"}, $a->{"1"});
    return $a;
  }

  /**
   * Initializes an Item
   */
  private function init () {
    /** probable query string to get data from db */
    $q = "SELECT name, status FROM objects WHERE id={$this->id}";
    $a = $this->db_read($q);
    $this->name = $a[0];
    $this->status = $a[1];
    // print_r($a);
  }

  /**
   * getter for the properties
   * @throws Exception on non existent prop name
   */
  public function __get(string $key) {
    /** @var string $props array of possible props */
    $props = ['name', 'id', 'status', 'changed'];
    if (in_array($key, $props)) {
      print($key);
      return $this->{$key};
    } else {
        throw new Exception ("No such property called '$name'");
    }
  }

  /**
   * toString
   * @returns all props in one line
   */
  public function __toString () {
    return "Item {$this->id}: {$this->name} in status {$this->status} {$this->changed}";
  }

  /** 
   * setter for props
   * @param string $name name of a property
   * @value string|int|bool $value type depoends on the prop and is thoroughly checked 
   * @throws Exception on an attempt to set $id
   * @returns bool true if set, false nothing to do
   */
  public function __set(string $key, $value) {
    $retval = true;
    switch ($key) {
      case 'name':
        if (is_string($value) && strlen($value) > 0) {
          $this->name = $value;
          $this->changed = true;
        } else {$retval = false;}
        break;
      case 'id':
        throw new Exception ('ID cannot be set');
      case 'status':
        if (is_int($value)) {
          $this->status = $value;
          $this->changed = true;
        } else {
          throw new Exception("Inappropriave value for '$name' property");
        }
        break;
      case 'changed':
        if (is_bool($value)){
          $this->changed = $value;
        } else {$retval = false;}
        break;
    }
    return $retval;
  }

  /**
   * Internal function to write to DB
   * @param string $table table name
   * @param string $col column name
   * @param string $val value
   * @returns bool true on success
   */
  private function db_write($table, $col, $val) {
    $q = "INSERT INTO $table ($col) VALUES ($val)";
    // DO SOMETHING TO WRITE DOWN THE DATA
    return true;
  }

  /**
   * Saves state of the Item
   */
  public function save() {
    /** probably we should add a timestamp, Item ID and stuff */
    $stored_data = json_encode(array($this->name, $this->status), JSON_UNESCAPED_UNICODE);
    $this->db_write("objects", "Item_s11n", $stored_data);
    print ($stored_data);
  }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <?php 

/**
 * THE MOST PRIMITIVE TESTS
 */

  // print ('Hi."<P>"');
  $it = new Item(101);
  print($it);
  $it->name = "NotDefaultName";
  print($it);
  ?>
</body>
</html>
