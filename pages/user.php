<?php
$isLoggedIn = $user->isLoggedIn();
$lastLoginAge = $user->loggedOnTime();
$lastLoginText = $lastLoginAge !== null ? timeAgoFromSeconds($lastLoginAge) : 'Not available';
$adminGroup = "CN=IT Support,OU=SEH Groups,DC=SEH,DC=ox,DC=ac,DC=uk";
$isAdminMember = $user->isMemberOf($adminGroup);
$displayName = $user->getDisplayName() ?? $user->getFullname() ?? $user->getUsername() ?? 'Unknown user';
$email = $user->getEmail() ?? 'Not available';
$username = $user->getUsername() ?? 'Not available';
$department = $user->getDepartment();
$title = $user->getTitle();
$telephone = $user->getTelephone();
$office = $user->getOffice();
$distinguishedName = $user->getDn();
$ldapGroups = $user->memberOf();
$localGroups = $user->groups();
$localGroupOuList = array_map(
	fn(array $group): string => (string) ($group['ou'] ?? ''),
	$localGroups
);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Your Account</h1>
</div>

<div class="row g-4">
	<div class="col-lg-4">
		<div class="card shadow-sm h-100">
			<div class="card-body">
				<div class="d-flex align-items-center gap-3 mb-4">
					<div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold" style="width: 64px; height: 64px; font-size: 1.25rem;">
						<?= htmlspecialchars($user->initials()) ?>
					</div>
					<div>
						<h2 class="h4 mb-1"><?= htmlspecialchars($displayName) ?></h2>
						<div class="text-body-secondary">@<?= htmlspecialchars($username) ?></div>
						<?php if ($title): ?>
							<div class="small mt-1"><?= htmlspecialchars($title) ?></div>
						<?php endif; ?>
					</div>
				</div>

				<div class="d-grid gap-2 account-summary-grid">
					<div class="border rounded p-3 account-summary-item">
						<div class="text-uppercase text-body-secondary small mb-1">Authentication</div>
						<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
							<span class="me-2"><?= $isLoggedIn ? 'Signed in' : 'Not signed in' ?></span>
							<span class="badge <?= $isLoggedIn ? 'text-bg-success' : 'text-bg-danger' ?>">
								<?= $isLoggedIn ? 'Active' : 'Inactive' ?>
							</span>
						</div>
						<div class="small text-body-secondary mt-2">Last sign-in: <?= htmlspecialchars($lastLoginText) ?></div>
					</div>

					<div class="border rounded p-3 account-summary-item">
						<div class="text-uppercase text-body-secondary small mb-1">Access</div>
						<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
							<span class="me-2">IT Support group</span>
							<span class="badge <?= $isAdminMember ? 'text-bg-success' : 'text-bg-secondary' ?>">
								<?= $isAdminMember ? 'Member' : 'Not a member' ?>
							</span>
						</div>
						<div class="small text-body-secondary mt-2 text-break"><?= htmlspecialchars($adminGroup) ?></div>
					</div>

					<div class="border rounded p-3 account-summary-item">
						<div class="text-uppercase text-body-secondary small mb-1">Directory groups</div>
						<div class="fs-4 fw-semibold"><?= $user->memberOfCount() ?></div>
						<div class="small text-body-secondary">LDAP groups linked to this account</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-8">
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h3 class="h5 mb-3">Profile Details</h3>
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Display Name</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($displayName) ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Username</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Email</label>
						<input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Department</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($department ?? 'Not available') ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Job Title</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($title ?? 'Not available') ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Telephone</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($telephone ?? 'Not available') ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Office</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($office ?? 'Not available') ?>" readonly>
					</div>
					<div class="col-md-6">
						<label class="form-label text-uppercase text-muted small">Last Sign-In</label>
						<input type="text" class="form-control" value="<?= htmlspecialchars($lastLoginText) ?>" readonly>
					</div>
					<?php if ($distinguishedName): ?>
						<div class="col-12">
							<label class="form-label text-uppercase text-muted small">LDAP Distinguished Name</label>
							<textarea class="form-control" rows="2" readonly><?= htmlspecialchars($distinguishedName) ?></textarea>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="card shadow-sm">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h3 class="h5 mb-0">LDAP Group Membership</h3>
					<span class="badge text-bg-light"><?= count($ldapGroups) ?> groups</span>
				</div>

				<?php if ($ldapGroups !== []): ?>
					<div class="list-group">
						<?php foreach ($ldapGroups as $group): ?>
							<?php $isMappedGroup = in_array($group, $localGroupOuList, true); ?>
							<div class="list-group-item d-flex justify-content-between align-items-start gap-3">
								<div class="me-auto text-break"><?= htmlspecialchars($group) ?></div>
								<?php if ($isMappedGroup): ?>
									<span class="badge text-bg-primary">Mapped in app</span>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="text-body-secondary">No LDAP group memberships were returned for this account.</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="mb-3">
	<label class="form-label text-uppercase text-muted small">Appearance</label>
	<div class="dropdown">
		<button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
			<i class="bi bi-circle-half" aria-hidden="true"></i>
			<span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
		</button>
		<ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
					<i class="bi me-2 opacity-50 bi-sun-fill" aria-hidden="true"></i>
					Light
					<i class="bi ms-auto d-none bi-check2" aria-hidden="true"></i>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
					<i class="bi me-2 opacity-50 bi-moon-stars-fill" aria-hidden="true"></i>
					Dark
					<i class="bi ms-auto d-none bi-check2" aria-hidden="true"></i>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
					<i class="bi me-2 opacity-50 bi-circle-half" aria-hidden="true"></i>
					Auto
					<i class="bi ms-auto d-none bi-check2" aria-hidden="true"></i>
				</button>
			</li>
		</ul>
	</div>
</div>
