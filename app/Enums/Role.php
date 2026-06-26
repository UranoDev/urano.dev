<?php

namespace App\Enums;

enum Role: string
{
    case Visitor = 'visitor';
    case Author = 'author';
    case Admin = 'admin';
}
