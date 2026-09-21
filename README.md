# Good Fruit PH

GitHub connection setup for https://goodfruitph.com on Z.com cPanel.

## SSH connection

- Host: `18.136.157.239`
- Port: `9022`
- User: `negwrhnd`
- Account home: `/home/negwrhnd`
- WordPress document root: awaiting verification; do not infer it from the domain.

## GitHub Actions secrets

In this repository's **Settings > Secrets and variables > Actions**, create repository secrets:

| Name | Value |
| --- | --- |
| `ZCOM_SSH_PRIVATE_KEY` | Complete contents of the downloaded `good-fruit-deploy` private key, including header and footer. |
| `ZCOM_SSH_PASSPHRASE` | The passphrase used successfully for interactive SSH login. |
| `ZCOM_KNOWN_HOSTS` | The previously trusted host-key line for `[18.136.157.239]:9022` from the local OpenSSH `known_hosts` file. |

Secrets from `a-joyful-life` are not automatically available in this repository. Do not commit private keys, passphrases, WordPress configuration, or database exports.

To find the existing trusted host-key record in PowerShell:

```powershell
ssh-keygen -F '[18.136.157.239]:9022' -f "$env:USERPROFILE\.ssh\known_hosts"
```

Copy the host-key record, not the comment line or a SHA256 fingerprint.

## Connection check

Open **Actions > Check Z.com connection > Run workflow** after adding the secrets. This manual workflow authenticates, prints the account and home directory, and lists WordPress configuration file paths. It does not read configuration contents or modify the server.

Deployment is not configured yet. First verify which WordPress installation serves `goodfruitph.com`, inspect the existing custom code, and define which theme/plugin files this repository will manage. Then add a scoped deployment with backups and verification.
