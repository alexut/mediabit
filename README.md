# Base Template for WordPress Projects (Bedrock) 
## Needs to be updated | Needs to have a new readme after the project is built ( maybe even generated from gulp )
## UNDER CONSTRUCTION

This repository serves as a base template for WordPress projects based on [Bedrock](https://roots.io/bedrock/), providing a pre-configured setup, custom theme, essential plugins, and a modern development workflow. It is designed to streamline the development process and ensure consistency across projects.

## Features

- Custom WordPress theme with a responsive bootstrap v5 design
- Essential plugins pre-installed
- Cookie consent management system
- Bedrock structure for improved WordPress organization and security
- Laragon compatibility for local development

## Getting Started

### Prerequisites

- [Laragon](https://laragon.org/) installed
- [Composer](https://getcomposer.org/) installed
- [Git](https://git-scm.com/) installed

### Setting up a new project


1. Clone the base template repository with the project folder name:
```
git clone https://github.com/mediabit-ro/base-theme.git your_project_path
```
2. Install Bedrock dependencies using Composer:
```
cd your_project_path
composer install
```

3.  Use gulp to init the env file or manually create it.

```
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_database_password
DB_HOST=localhost

Optional settings
WP_ENV=development
WP_HOME=http://your_project_name.test
WP_SITEURL=${WP_HOME}/wp

```

7. ? Update the `composer.json` file with the details of your new project. 
8. ? Create a new repository on GitHub or your preferred Git platform, and push your project to the new repository:

```
git remote set-url origin https://github.com/your_username/your_new_repository.git
git add .
git commit -m "Initial commit"
git push -u origin main
```

9. You're now ready to start developing your project!

## Updating Shared Components

Manually update base components by copying the latest files from the base template into your project, ensuring that you retain any customizations made in your project.

## License

This base template is ours, and it's really hard to understand it, but if you manage it, use it as you see fit.

## Contributing

We welcome contributions to the base template. If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

