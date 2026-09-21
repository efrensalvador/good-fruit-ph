# Good Fruit PH

GitHub connection setup for https://goodfruitph.com on Z.com cPanel.

## SSH connection

- Host: `18.136.157.239`
- Port: `9022`
- User: `negwrhnd`
- Account home: `/home/negwrhnd`
- WordPress document root: `/home/negwrhnd/public_html`, confirmed by interactive SSH and `wp option get home` returning `https://goodfruitph.com`.
- Other sites, including `ajoyfull.life`, live in subfolders. Deployment must target explicit Good Fruit theme/plugin paths, never synchronize or delete the entire `public_html` tree.

## GitHub Actions secrets

In this repository's **Settings > Secrets and variables > Actions**, create repository secrets:

| Name | Value |
| --- | --- |
| `SSH_PRIVATE_KEY` | Complete contents of the downloaded `good-fruit-deploy` private key, including header and footer. |
| `SSH_PASSPHRASE` | The passphrase used successfully for interactive SSH login. |
| `ZCOM_KNOWN_HOSTS` | The previously trusted host-key line for `[18.136.157.239]:9022` from the local OpenSSH `known_hosts` file. |

Secrets from `a-joyful-life` are not automatically available in this repository. Do not commit private keys, passphrases, WordPress configuration, or database exports.

To find the existing trusted host-key record in PowerShell:

```powershell
ssh-keygen -F '[18.136.157.239]:9022' -f "$env:USERPROFILE\.ssh\known_hosts"
```

Copy the host-key record, not the comment line or a SHA256 fingerprint.

## Connection check

Open **Actions > Check Z.com connection > Run workflow** after adding the secrets. This manual workflow authenticates, verifies that WordPress in `/home/negwrhnd/public_html` reports exactly `https://goodfruitph.com`, and lists installed themes and plugins. It does not deploy files or intentionally modify the server.

## 3M 9105 product template

The `gfe-industrial` plugin supplies a responsive template only for product **1825**. It retains the theme header/footer, uses WooCommerce's native cart form, and uses the supplied `3M-N95-9105.webp` image. Other products continue using their existing templates. The product's current URL is preserved.

**Actions > Deploy GFE product template > Run workflow** installs the plugin and sets the confirmed price to **PHP 70 per piece**, clears any sale price, and selects the supplied media-library image. Deployment verifies the site URL, currency, product type/name, and image before saving changes. The next deployment corrects inventory to the confirmed 150 pieces, enables product stock tracking, and disables backorders. A new correction marker applies this once even if the earlier 500-piece setup ran. Subsequent deployments preserve remaining stock after orders. WooCommerce global stock management must already be enabled; otherwise deployment stops before product changes. Shipping and payment readiness still require confirmation. Do not treat the template deployment as a completed checkout test.

The workflow backs up existing plugin files and the changed product fields under `~/gfe-product-backups/<timestamp>`. It uploads only this custom plugin. It does not synchronize `public_html`, change the product slug, or touch sibling sites. PHP and shell syntax checks run on push and before deployment.

For a layout rollback, deactivate `gfe-industrial` in WordPress and purge LiteSpeed cache. This restores the prior theme/Elementor product template. Price, featured image, and inventory changes persist: restore those from `product-before.json` in the backup folder if needed. For later plugin updates, restore the backed-up plugin folder as well.
