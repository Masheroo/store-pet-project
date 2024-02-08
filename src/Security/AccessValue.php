<?php

namespace App\Security;

enum AccessValue: string
{
    case CreateLot = 'create-lot';
    case UpdateOwnLot = 'update-own-lot';
    case DeleteOwnLot = 'delete-own-lot';
    case ReplenishUserBalance = 'replenish-user-balance';
    case AddUserDiscount = 'add-user-discount';
}
