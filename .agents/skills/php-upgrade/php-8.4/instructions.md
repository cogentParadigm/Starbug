# Upgrading from PHP 8.2 to 8.4

The included diffs in this directory provide examples of changes needed to upgrade other starbug projects. Use them as a guide to update the local project.

## Directions

1. Analyze the intent in the diffs.
2. Scan the local project.
3. Look beyond the diff, if a dependency was updated, check for other instances of the old dependencies.
4. Apply fixes intelligently
5. Run `ddev restart` for the new PHP version to take effect.
5. Run `ddev composer update -W` to update dependencies.

## Notes

- You will probably need to delete modules/webpack and modules/dojo so that composer can re-install them when you update.
- You will probably need to run the composer update twice since old plugins error the first time.
