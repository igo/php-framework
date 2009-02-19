<?php

class Framework_App_Security_MembershipSecurity extends Framework_App_Security_GenericSecurity {
	
	public function isAllowed($user, $action) {
		if (isset($action->allowedGroups)) {
			$allow = $action->allowedGroups;
		} else {
			$allow = array('Anonymous');
		}
		
		if (in_array('Anonymous', $allow)) {
			return true;
		}
		
		if (isset($user['Groups'])) {
			if (array_intersect($user['Groups'], $allow)) {
				return true;
			}
		}
		return false;
	}
	
	public function user() {
		return $user;
	}
	
}

?>