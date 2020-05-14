camaleaun/scaffold-fuque-command
================================

Generates starter code for a theme based on fuque.



Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing) | [Support](#support)

## Using

~~~
wp scaffold fuque <slug> [--activate] [--enable-network] [--theme_name=<title>] [--author=<full-name>] [--author_uri=<uri>] [--sassify] [--woocommerce] [--force]
~~~

**OPTIONS**

	<slug>
		The slug for the new theme, used for prefixing functions.

	[--activate]
		Activate the newly downloaded theme.

	[--enable-network]
		Enable the newly downloaded theme for the entire network.

	[--theme_name=<title>]
		What to put in the 'Theme Name:' header in 'style.css'.

	[--author=<full-name>]
		What to put in the 'Author:' header in 'style.css'.

	[--author_uri=<uri>]
		What to put in the 'Author URI:' header in 'style.css'.

	[--sassify]
		Include stylesheets as SASS.

	[--woocommerce]
		Include WooCommerce boilerplate files.

	[--force]
		Overwrite files that already exist.

**EXAMPLES**

    # Generate a theme with name "Sample Theme" and author "John Doe"
    $ wp scaffold fuque sample-theme --theme_name="Sample Theme" --author="John Doe"
    Success: Created theme 'Sample Theme'.

## Installing

Installing this package requires WP-CLI's latest stable release. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with:

    wp package install git@github.com:camaleaun/scaffold-fuque-command.git

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

For a more thorough introduction, [check out WP-CLI's guide to contributing](https://make.wordpress.org/cli/handbook/contributing/). This package follows those policy and guidelines.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/camaleaun/scaffold-fuque-command/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/camaleaun/scaffold-fuque-command/issues/new). Include as much detail as you can, and clear steps to reproduce if possible. For more guidance, [review our bug report documentation](https://make.wordpress.org/cli/handbook/bug-reports/).

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/camaleaun/scaffold-fuque-command/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, [please follow our guidelines for creating a pull request](https://make.wordpress.org/cli/handbook/pull-requests/) to make sure it's a pleasant experience. See "[Setting up](https://make.wordpress.org/cli/handbook/pull-requests/#setting-up)" for details specific to working on this package locally.

## Support

Github issues aren't for general support questions, but there are other venues you can try: https://wp-cli.org/#support


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
