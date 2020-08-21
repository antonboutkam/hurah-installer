# Hurah installer

This package contains a composer plugin that manages the installation of other packages into the
[Novum innovation app](https://docs.demo.novum.nu). All package types are treated as a plugin. The "type" field inside
composer.json tells the package what type of plugin we have and how to treat it. These are the types of plugin

## Core
Contains all the code that other componens depend on. Autoloading happens from the vendor directory but a symlink is created inside a hidden folder called ```.system```. When other components or plugins are installed, they will be symlinked inside this system folder also. This is required for autoloading and code generation.

```json
{
    "type" : "novum-core"
}
```

## Site
This package type represents a normal website. On installation a folder called `public/<site-name>` is created and symlinked to the original installation directory in the vendor folder. Another symlink is created in the `.system/public_html/<site-name>` folder.

```json
{
    "type" : "novum-site"
}
```

## Api
The API type works the same as the site type but has some different dependencies, hence the distinction.
```json
{
    "type": "novum-api"
}
```
## Domain
A domain type plugin contains all the information that is needed to bootstrap your specific application. This includes a database definition file, migration scrips, style information for the admin panel, images that are to be used in various places etc.


```json
{
    "type" : "novum-domain"
}
```

## Admin module
The core system comes with an admin panel that has a few modules that are included by default. For instance a `User` module that allows you to manage user accounts. Other modules need to be installed via composer.

```json
{
    "type" : "novum-domain"
}
```

# Installation flow
When the installer activates for any kind of plugin it checks the folder structure and creates all the directories needed to run the system. The user will see only the directories relevant for his or her project. Inside the `.system` folder a tree of the actual project is assembled. 
