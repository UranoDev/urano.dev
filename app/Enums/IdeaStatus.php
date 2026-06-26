<?php

namespace App\Enums;

enum IdeaStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
