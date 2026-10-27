# Contributing to T3AB – TYPO3 AI Builder (`ns_t3ab`)

Thanks for helping improve ns_t3ab. Contributions of all kinds are welcome: bug reports, fixes, features, and documentation.

## License of your contribution (inbound = outbound)

ns_t3ab is licensed under **GPL-2.0-or-later** (see [LICENSE](LICENSE)). Contributions follow the TYPO3 extension standard of *inbound = outbound*:

> By submitting a contribution (for example a pull request), you agree that it is licensed under GPL-2.0-or-later, the same license as this extension.

There is **no Contributor License Agreement (CLA)**. The TYPO3 Association's CLA applies to the TYPO3 Core, not to extensions, and we do not require one. You keep ownership of your work; you simply license it to the project under the same terms as the rest of the code.

## Sign your commits (DCO)

We use the **Developer Certificate of Origin (DCO)**. It is a lightweight, one-line confirmation that you have the right to submit your contribution. It is not a CLA and transfers no rights.

Add a `Signed-off-by` line to each commit by committing with `-s`:

```
git commit -s -m "Fix: keep the selected AI provider in the Content Blocks dialog"
```

This appends, using your real name and email:

```
Signed-off-by: Your Name <you@example.com>
```

By signing off you agree to the DCO (https://developercertificate.org/).

## How to contribute

1. **Open an issue first** for anything non-trivial, so we can agree on the approach before you invest time.
2. **Branch** from the default branch. Use a short, descriptive branch name.
3. **Keep TYPO3 12.4, 13.4, and 14.3 working.** This extension declares all three in `composer.json`.
4. **Update `context/`** when behaviour changes. Those notes stay in git and are left out of release archives.
5. **Open a pull request** with a clear description of what changed and why. Link the related issue.

## Reporting bugs

Use the issue templates. Include your TYPO3 version, PHP version, ns_t3ab version, and clear steps to reproduce. For **security issues, do not open a public issue**: follow `SECURITY.md`.

## Trademarks

The code is GPL and yours to fork. "T3AB" and "T3Planet" are trademarks of T3Planet / NITSAN Technologies; the GPL covers the code, not the marks. Please rename any public fork so it is not presented as the official product.

## Questions

Open a discussion or issue, or reach us at support@t3planet.de.
