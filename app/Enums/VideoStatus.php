<?php

namespace App\Enums;

enum VideoStatus: string
{
    case Draft = 'draft';
    case Processing = 'processing';
    case Ready = 'ready';
    case Failed = 'failed';
    case Hidden = 'hidden';
}
