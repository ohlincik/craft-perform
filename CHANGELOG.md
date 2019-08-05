# PerForm Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.1.1 - 2019-08-05

### Fixed

- Fixed an issue with invalid `$allowAnonymous` declaration in _Public Controller_
- Link to documentation was missing on the _Control Panel > Settings > Plugins_ page

## 1.0.1 - 2018-11-14

### Added

- `getSubmissionById` plugin variable to retrieve existing Form Submission contents in a template
- Capture and store the **replyTo** email address (if provided) as a part of the Form Submission
- Display the **replyTo** email address on the Form Submission detail screen if available
- Expose the processed Form Submission data as variables for the  `redirectInput` function, which allows them to be used as parameters in the return url
- After the Form Submission is processed, send the **submissionId** as a flash message so that it can be used on the 'Thank You' page to retrieve the Form Submission contents using the new `getSubmissionId` plugin variable

### Removed

- Redundant *flash* notice that Form Submission was completed

## 1.0.0 - 2018-11-08

> {note} This is the initial plugin release.

### Added

- Form Submissions stored in the Control Panel as elements
- Form Submission statuses: NEW, Read and Test
- Form Settings field type
- Email Notifications testing through Mailtrap
- Client-side validation using Parsley
- Spam protection using Google Invisible reCAPTCHA
- The ability to use custom templates for Email Notifications
- PerForm Utility to conveniently delete all form submissions in Test status
- PerForm Widget displaying the form submission counts
