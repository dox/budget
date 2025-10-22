<?php
class User {
	protected $ldapConn;
	protected $userData = [];
	protected $loggedIn = false;

	protected $ldapHost;
	protected $baseDn;

	public function __construct() {
		// LDAP settings from constants (you can define these in config.php)
		$this->ldapHost = defined('LDAP_HOST') ? LDAP_HOST : 'ldap://localhost';
		$this->baseDn   = defined('LDAP_BASE_DN') ? LDAP_BASE_DN : 'dc=example,dc=com';
		
		// Attempt connection
		$this->ldapConn = ldap_connect($this->ldapHost);
		if (!$this->ldapConn) {
			throw new Exception("Could not connect to LDAP server: {$this->ldapHost}");
		}

		// Recommended LDAP options
		ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ldapConn, LDAP_OPT_REFERRALS, 0);

		// Restore session if available
		if (isset($_SESSION['user'])) {
			$this->userData = $_SESSION['user'];
			$this->loggedIn = true;
		}
	}
	
	public function authenticate(string $username, string $password): bool {
		// Optional: bind with service account to search
		if (defined('LDAP_BIND_USER') && defined('LDAP_BIND_PASS')) {
			@ldap_bind($this->ldapConn, LDAP_BIND_USER, LDAP_BIND_PASS);
		}
	
		$filter = "(sAMAccountName={$username})"; // or sAMAccountName for AD
		$search = @ldap_search($this->ldapConn, $this->baseDn, $filter);
	
		if (!$search) {
			$this->logout();
			return false;
		}
		
		$entries = ldap_get_entries($this->ldapConn, $search);
		if ($entries['count'] == 0) {
			$this->logout();
			return false;
		}
	
		$dn = $entries[0]['dn'];
		if (@ldap_bind($this->ldapConn, $dn, $password)) {
			$this->userData = $entries[0];
			$this->loggedIn = true;
			$_SESSION['user'] = $this->userData;
			return true;
		}
		
		$this->logout();
		return false;
	}
	
	public function isLoggedIn(): bool {
		return $this->loggedIn;
	}
	
	public function getUsername(): ?string {
		return $this->userData['samaccountname'][0] ?? null;
	}
	
	public function getEmail(): ?string {
		return $this->userData['mail'][0] ?? null;
	}
	
	public function getFullname(): ?string {
		return $this->userData['name'][0] ?? null;
	}
	
	public function isMemberOf(string $group): bool {
		if (!$this->isLoggedIn()) return false;
	
		if (empty($this->memberOf())) return false;
	
		$groups = $this->memberOf();
		
		if (in_array($group, $groups)) {
			return true;
		}
	
		return false;
	}
	
	public function memberOf(): array {
		if (!$this->isLoggedIn()) return [];
	
		if (!isset($this->userData['memberof'])) return [];
	
		$groups = $this->userData['memberof'];
	
		if (isset($groups['count'])) {
			unset($groups['count']);
		}
	
		return $groups;
	}
	
	public function groups(): array {
		$ldapGroups = $this->memberOf();
		
		$groups = [];
		foreach ($ldapGroups AS $ldapGroup) {
			$userGroup = new UserGroup(null, $ldapGroup);
			if ($userGroup->ou === $ldapGroup) {
				$groups[] = $userGroup->ou;
			}
		}
		
		return $groups;
	}
	
	public function logout(): void {
		unset($_SESSION['user']);
		$this->userData = [];
		$this->loggedIn = false;
	}
}
