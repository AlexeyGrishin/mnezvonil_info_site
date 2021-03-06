<?php

class I18N {

    static function result($count) {
        if ($count == 0) {
            return "нет упоминаний";
        }
        if ($count >= 10 && $count <= 20) {
            return "найдено $count упоминаний";
        }
        switch ($count % 10) {
            case 1:
                return "найдено $count упоминание";
                break;
            case 2:
            case 3:
            case 4:
                return "найдено $count упоминания";
            default:
                return "найдено $count упоминаний";
        }
    }

    static function mobile() {
        return "Мобильные телефоны";
    }

    static function no_result() {
        return "Упоминаний на известных нам сайтах не обнаружено";
    }

    public static function loginError() {
        return "Неправильные логин или пароль";
    }

    public static function insufficientRights() {
        return "Недостаточно прав";
    }

    public static function phoneNotFound($param1) {
        return "Телефон $param1 отсутствует в базе";
    }

    public static function proof_is_empty() {
        return "Доказательство не может быть пустым";
    }

    public static function phone_saved($param1) {
        return "Статус телефона сохранен";
    }
}