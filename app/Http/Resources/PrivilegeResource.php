<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivilegeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hasRead   = $this->modRead   ? "checked" : null;
        $username = $this->userid;
        return [
            'mod_id' => $this->modID,
            'mod_name' => $this->modName,
            'read' => "<div class='togglebutton'>
                        <label>
                            <input type='checkbox' data-priv-type='modRead' onclick='updatePrivilege(\"{$this->modID}\", \"{$username}\", this)' value='1' {$hasRead}>
                            <span class='toggle'></span>
                        </label>
                    </div>",
        ];
    }
}
