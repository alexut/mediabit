# Base Template for WordPress Projects (Bedrock)

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

1. Create a new project in Laragon:
   - Open Laragon.
   - Click on "Menu" > "Quick app" > "Blank" and enter your project name.
   - Rename the newly created project in www folder to something temporary if there is a html source otherwise delete it. This way you will replace the html folder at step2 with the existing one.

2. Clone the base template repository with the project folder name:
```
git clone https://github.com/mediabit-ro/base-theme.git your_project_path
```
3. Install Bedrock dependencies using Composer:
```
cd your_project_path
composer install
```

4. Laragon will automatically update the `hosts` file and create a new database for your project.
5. Update the domain and web root in the project's Apache configuration file:

   - Open the Laragon application and click the "Menu" button in the top right corner.
   - In the dropdown menu, navigate to `Apache` > `sites-enabled` > `auto.your_project_name.test.conf`. This will open the configuration file in your default text editor.
   - Replace the `ROOT` and `SITE` variable values with the appropriate paths and domain names for your new project. For example:

     ```
     define ROOT "C:/laragon/www/your_project_name/web"
     define SITE "your_project_name.test"
     ```

   - Save the file and restart Laragon.


6. Configure the `.env` file in your project's root directory with your database credentials and other settings:
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

7. Update the `composer.json` file with the details of your new project.
8. Create a new repository on GitHub or your preferred Git platform, and push your project to the new repository:

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

