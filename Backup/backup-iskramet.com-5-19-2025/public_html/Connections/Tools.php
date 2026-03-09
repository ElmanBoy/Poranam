<?php


namespace Tools;


class Tools
{
    private $post, $session, $cookie, $db;

    public function __construct ()
    {
        $this->post = $_POST;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
    }

    public function translit($string, $type = '', $mode = 'no_whitespace')
    {
        $string = ($type == 'file') ? preg_replace('/\\.(?![^.]*$)/', '_', $string) : $string;
        $r_trans = array(
            "а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м",
            "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "э",
            "ю", "я", "ъ", "ы", "ь", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М",
            "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Э",
            "Ю", "Я", "Ъ", "Ы", "Ь", "(", ")", "'"
        );
        $e_trans = array(
            "a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m",
            "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sch",
            "e", "yu", "ya", "", "i", "", "A", "B", "V", "G", "D", "E", "E", "J", "Z", "I", "I", "K", "L", "M",
            "N", "O", "P", "R", "S", "T", "U", "F", "H", "C", "Ch", "Sh", "Sch",
            "E", "Yu", "Ya", "", "I", "", "", ""
        );
        if($mode == 'no_whitespace'){
            $string = str_replace(" ", '-', $string);
        }
        $string = str_replace($r_trans, $e_trans, $string);
        return $string;
    }
}