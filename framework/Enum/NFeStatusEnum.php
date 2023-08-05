<?php

namespace KaiokenFramework\Enum;

enum NFeStatusEnum : int
{
    case NaoAutorizado = 1;
    case Autorizado = 2;
    case Cancelado = 3;
    case Denegado = 4;
    case Inutilizado = 5;
}
