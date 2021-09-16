# grumphp-prettier-task

Adds a [prettier](https://prettier.io/) linting task to [GrumPHP](https://github.com/phpro/grumphp).

## Installation

Install through composer:

```shell
composer require --dev dallask/grumphp-prettier-task
```

## Configuration

Example configuration:

```yaml
# grumphp.yml
grumphp:
  tasks:
    prettier:
      bin: "node_modules/.bin/prettier"
      triggered_by: ["css", "scss"]
      allowed_paths: 
        - /^resources\/scss/
  extensions:
    - Dallask\GrumPHPStylelintTask\Extension
```

Available options:

**bin**

*Default: null*

By default, the task will use `prettier` from your `$PATH`. Use this option to override that. You can specify a path to the prettier executable as a string, or a command to execute prettier as an array, for example, to run prettier through npx: `bin: ["npx", "prettier"]`

**triggered_by**

*Default: ["css", "less", "scss", "sass", "pcss"]*

Define the list of file extensions that will trigger the prettier task.

**allowed_paths**

*Default: []*

This option allows you to specify a list of regex patterns to filter the files that will be linted by the task.

**config**

*Default: null*

Specify an alternative configuration file for prettier. If not specified, will let prettier decide which configuration file will be used ([prettier.io](https://prettier.io/docs/en/cli.html)).


## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

This project is licensed unded the [MIT License](LICENSE.md).
