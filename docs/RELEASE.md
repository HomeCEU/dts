## Release Notes
These release notes are mostly for code changes.  
To view the API documentation: [Click Here](https://documenter.getpostman.com/view/738678/TzCP6n83)
## v1.1
MAY 01, 2021
### Notes
There are breaking changes in this version. (1.0 was an MVP so breaking changes were to be expected.)
### What's New
* Upgrade to PHP 7.4
* Endpoints for saving and retrieving templates
* Improved Partial Support
  * Partials are now their own entity and can be persisted independently of templates.
  * Endpoints for saving and retrieving partials
* Alternative render endpoints
* Hot Render endpoints have been added. This allows previewing of templates.
* Implement monolog
* Improved error handling
### Breaking Changes to the API
* Template parameters `templateId` and `templateKey` have been renamed to `id` and `key` respectively

## v1.0
MAR 20, 2020 - 10:15 CST
### Notes
* Initial Release
### Changes
* Basic features
* Add Document Data
* Render Template
