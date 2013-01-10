<?php
namespace docpx;
/**
 * Copyright 2010-12 Nickolas Whiting. All rights reserved.
 * Use of this source code is governed by the Apache 2 license
 * that can be found in the LICENSE file.
 */


/**
 * Docblock
 *
 * Parses a PHP docblock returning its description and tags.
 *
 * @credit Union of Rad, Lithium Framework
 */
class Docblock {

    /**
     * List of supported docblock tags.
     *
     * @var array
     */
    public static $doctags = array(
        'todo', 'fix', 'important', 'var',
        'param', 'return', 'throws', 'see', 'link',
        'task', 'dependencies', 'abstract',
        'access', 'author', 'copyright', 'deprecated',
        'deprec', 'example', 'exception', 'global',
        'ignore', 'internal', 'name', 'package',
        'param', 'since', 'static', 'staticvar',
        'subpackage', 'version', 'license', 'credit'
    );

    /**
     * List of docblock tags which are formatted and need
     * a extra parsing.
     *
     * @var  array
     */
    public static $formattedtags = array(
        'param', 'return', 'throws', 'exception', 'var'
    );

    /**
     * List of docblock tags which return large portions of doc text.
     *
     * @var  array
     */
    public static $puredoctags = array(
        'example'
    );

    /**
     * Parses a docblock comment into a readable array.
     *
     * @param  string  $comment  Docblock comment to parse.
     *
     * @return  array  Array of results from the doc parsing.
     */
    public static function comment($comment)
    {
        // Turn comment into a string removing all comment chars
        // and split the comment into 2 sections description/tags
        // @credit  Union of Rad, Lithium Framework
        $comment = trim(preg_replace('/^(\s*\/\*\*|\s*\*{1,2}\/|\s*\* ?)/m', '', $comment));
        $comment = str_replace("\r\n", "\n", $comment);
        if ($items = preg_split('/\n@/ms', $comment, 2)) {
            list($description, $tags) = $items + array('', '');
        }
        $regex = '/\n@(?P<type>' . join('|', static::$doctags) . ")/msi";
        $result = preg_split($regex, "\n@$tags", -1, PREG_SPLIT_DELIM_CAPTURE);
        $tags = array();
        array_shift($result);
        for ($i = 0; $i < count($result) - 1; $i += 2) {
            $type = strtolower(trim($result[$i]));
            $text = trim($result[$i + 1]);

            if (isset($tags[$type])) {
                $tags[$type][] = $text;
            } else {
                $tags[$type] = array($text);
            }
        }
        // Parse specific formatted tags
        foreach (static::$formattedtags as $_format) {
            if (isset($tags[$_format])) {
                if ($_format == 'param') {
                    $array = $tags[$_format];
                    foreach ($array as $_k => $_v) {
                        $param = preg_split('/\s+/', $_v, 3);
                        $name = $type = $text = null;
                        foreach (array('type', 'name', 'text') as $key => $val) {
                            if (!isset($param[$key])) continue;
                            ${$val} = trim($param[$key]);
                        }
                        $tags[$_format][$_k] = compact('type', 'name', 'text');
                    }
                } else {
                    $array = $tags[$_format];
                    foreach ($array as $_k => $_v) {
                        $param = preg_split('/\s+/', $_v, 2);
                        $type = $text = null;
                        foreach (array('type', 'text') as $key => $val) {
                            if (!isset($param[$key])) continue;
                            ${$val} = trim($param[$key]);
                        }
                        $tags[$_format][$_k] = compact('type', 'text');
                    }
                }
            }
        }

        foreach (static::$puredoctags as $_tag) {
            if (isset($tags[$_tag])) {
                foreach ($tags[$_tag] as $_k => $_v) {
                    $text = $_v;
                    $type = $_tag;
                    $tags[$_tag][$_k] = compact('type', 'text');
                }
            }
        }

        return compact('description', 'tags');
    }
}