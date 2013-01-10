<?php

function print_v($v, &$depth = 0)
{
    switch ($v) {
        case is_bool($v):
            if ($v) {
                return "true";
            }
            return "false";
            break;
        case is_null($v):
            if (false === $v) {
                return "false";
            }
            return "null";
            break;
        case is_int($v):
        case is_float($v):
        case is_double($v):
        default:
            return sprintf('%s',$v);
            break;
        case is_string($v):
            return sprintf('"%s"',
                substr($v, 0, 60)
            );
            break;
        case is_array($v):
            $r = array();
            foreach ($v as $_key => $_var) {
                if ($depth >= $this->_maxdepth) break;
                $depth++;
                $r[] = sprintf('[%s] => %s',
                    $_key,
                    $this->variable($_var, $depth)
                );
            }
            $return = sprintf('array(%s)', implode(", ", $r));
            return ($this->use_short_vars($return)) ? sprintf('%s...)',
                substr($return, 0, 60)) : $return;
            break;
        case is_object($v):
            return sprintf('object(%s)', get_class($v));
        break;
    }
    
    return "unknown";
}