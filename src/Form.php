<?php


namespace Melit\Form;

use Illuminate\Support\Str;

class Form
{
    public static function open($action, $method = 'GET', $attributes = [])
    {
        $extralines = [];

        $tag = "<form";

        if (isset($action))
        {
            $first_attributes['action'] = $action;
        }

        if (isset($method))
        {
            $method = Str::upper($method);
            switch ($method)
            {
                case 'GET':
                case 'POST':
                    $first_attributes['method'] = $method;
                    break;
                default:
                    $first_attributes['method'] = 'POST';
                    $extralines[]               = self::hidden('_method', $method);
                    break;
            }
        }

        $extralines[] = self::hidden('_token', csrf_token());

        $attributes = array_merge($first_attributes, $attributes);

        foreach ($attributes as $attribute => $value)
        {
            $tag .= ' ' . $attribute . '="' . $value . '"';
        }

        $tag .= '>';

        foreach ($extralines as $line)
        {
            $tag .= "\n" . $line;
        }

        return $tag;
    }

    public static function close()
    {
        return "</form>";
    }

    /**
     * @param $value1
     * @param $value2
     * @param bool $default with default value false
     * @return string|null
     */
    public static function selected($value1, $value2, $default = false)
    {
        if (isset($value1) && isset($value2))
        {
            return ($value1 === $value2) ? 'selected' : null;
        }
        elseif (isset($value2) && $default)
        {
            return 'selected';
        }
        else
        {
            return null;
        }
    }

    /**
     * @param $value
     * @param null $default
     * @return null
     */
    public static function value($value, $default = null)
    {
        return isset($value) ? $value : (isset($default) ? $default : null);
    }


    /**
     * @param $name
     * @param $value
     * @param array $attribs
     * @param null $label
     * @return string
     */
    public static function textarea($name, $value, $attribs = [])
    {
        $input = "<textarea"
            . ' name="' . $name . '"'
            . ' id="' . $name . '"';

        foreach ($attribs as $attrib => $avalue)
        {
            $input .= ' ' . $attrib . '="' . $avalue . '"';
        }

        $input .= ">";

        /*
         * If an object is given as value, the real value should be the attribute with $name of this object
         */
        if (is_object($value))
        {
            $value = $value->$name;
        }

        /*
         * Check if an old value is returned. If it is, this should be used as $value.
         */
        $value = old($name, $value);


        if (isset($value))
        {
            $input .= $value;
        }

        $input .= '</textarea>';


        return $input;
    }

    /**
     * @param $type
     * @param $name
     * @param $value
     * @param array $attribs
     * @param null $label
     * @return string
     */
    public static function input($type, $name, $id, $value, $attribs = [])
    {
        $input = "<input"
            . ' type="' . $type . '"'
            . ' name="' . $name . '"';

        if (isset($id))
        {
            $input .= ' id="' . $id . '"';
        }


        /*
         * If an object is given as value, the real value should be the attribute with $name of this object
         */
        if (is_object($value))
        {
            $value = $value->$name;
        }

        /*
         * Check if an old value is returned. If it is, this should be used as $value.
         */
        $value = old($name, htmlentities($value));

        if (isset($value))
        {
            $input .= ' value="' . $value . '"';
        }

        foreach ($attribs as $attrib => $value)
        {
            $input .= ' ' . $attrib . '="' . $value . '"';
        }

        $input .= ">";

        return $input;
    }

    /**
     * create and return a Html input tag with type 'text'
     * @param $name
     * @param $value
     * @param array $attribs
     * @return string
     */
    public static function text($name, $value, $attribs = [])
    {
        return self::input('text', $name, $name, $value, $attribs);
    }

    /**
     * create and return a Html input tag with type 'email'
     * @param $name
     * @param $value
     * @param array $attribs
     * @return string
     */
    public static function email($name, $value, $attribs = [])
    {
        return self::input('email', $name, $name, $value, $attribs);
    }

    /**
     * create and return a Html input tag with type 'url'
     * @param $name
     * @param $value
     * @param array $attribs
     * @return string
     */
    public static function url($name, $value, $attribs = [])
    {
        return self::input('url', $name, $name, $value, $attribs);
    }

    /**
     * @param $name
     * @param $value
     * @param array $attribs
     * @return string
     */
    public static function hidden($name, $value, $attribs = [])
    {
        return self::input('hidden', $name, null, $value, $attribs);
    }

    /**
     * @param $name
     * @param $value
     * @param array $attribs
     * @return string
     */
    public static function number($name, $value, $attribs = [])
    {
        return self::input('number', $name, $name, $value, $attribs);
    }


    /**
     * @param $name
     * @param $value
     * @param array $attribs
     * @return string
     */
    public static function date($name, $value, $attribs = [])
    {
        return self::input('date', $name, $name, $value, $attribs);
    }

    /**
     * @param $for
     * @param $label
     * @param array $attribs
     * @return string
     */
    public static function label($for, $label, $attribs = [])
    {
        //                     <label for="date">@lang('Date')</label>

        $tag = "<label";


        if (isset($for))
        {
            $tag .= ' for="' . $for . '"';
        }

        foreach ($attribs as $attrib => $value)
        {
            $tag .= ' ' . $attrib . '="' . htmlentities($value) . '"';
        }

        $tag .= ">$label</label>";

        return $tag;
    }


    //<select name="type_id" id="type_id" class="form-control">
    //@foreach ($types as $type)
    //<option
    //value="{{ $type->id }}" {{ Form::selected($menu->type_idx,$type->id,$type->default)  }}>{{ $type->name }}</option>
    //@endforeach

    //                    </select>
    public static function select($name, $options = [], $value, $attribs = [])
    {
        $select = "<select"
            . ' name="' . $name . '"'
            . ' id="' . $name . '"';

        foreach ($attribs as $attrib => $avalue)
        {
            $select .= ' ' . $attrib . '="' . $avalue . '"';
        }

        $select .= ">";

        if (is_object($value))
        {
            $value = $value->$name;
        }

        /*
         * If an old value is given, use it.
         */
        $value = old($name, htmlentities($value));

        foreach ($options as $key => $display)
        {
            $tag = '<option';

            if (isset($key))
            {
                $tag .= ' value="' . $key . '"';
            }

            if (isset($value) && $value === $key)
            {
                $tag .= ' selected="selected"';
            }


            $tag    .= '>' . $display . '</tag>';
            $select .= "\n$tag";
        }

        $select .= '</select>';


        return $select;
    }
}
