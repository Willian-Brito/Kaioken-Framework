<?php

namespace KaiokenFramework\Enum;

enum SqlCommandsEnum : int
{
    case Insert = 1;
    case Update = 2;
    case Delete = 3;
    case Select = 4;
}
