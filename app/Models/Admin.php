<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Support\Facades\DB;

class Admin extends Authenticatable
{
  use Notifiable;

  protected $table = 'admin';

  protected $fillable = [
    'firstname', 'lastname', 'mobile_no', 'email', 'password','role_id','zipcode',
  ];

  protected $hidden = [
    'password', 'remember_token',
  ];

  public function sendPasswordResetNotification($token)
  {
    $this->notify(new AdminResetPasswordNotification($token));
  }

  public function role()
  {
    return $this->belongsTo(Role::class);
  }


  public function hasPermission($page, $permissionType)
  {
    // Super Admin has full access
    if ($this->role_id == 1) {
      return true;
    }

    // Get Page ID
    $pageId = DB::table('page')->where('page_name', $page)->value('id');
    if (!$pageId) {
      return false;
    }

    // Get Permission ID
    $permissionId = DB::table('permissions')->where('name', $permissionType)->value('id');
    if (!$permissionId) {
      return false;
    }

    // Check if the permission exists for the role
    return DB::table('role_permission')
      ->where('role_id', $this->role_id)
      ->where('page_id', $pageId)
      ->where('permission_id', $permissionId)
      ->exists();
  }
}
