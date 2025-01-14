# Spenpo Resume

Contributors:      spenpo
Tested up to:      6.7
Stable tag:        1.0.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Tags:              resume, cv

A WordPress plugin that provides a flexible and customizable way to display your resume/CV through shortcodes.

## Description

This plugin allows you to manage and display your resume content in a structured format. It supports different types of content sections including text, lists, and nested sections, making it perfect for displaying educational history, work experience, skills, and other resume components.

## Features

- Multiple section types:
  - Text sections for basic information
  - List sections for chronological entries
  - Nested sections for detailed experiences
- REST API endpoint for headless implementations
  - [Example implementation](https://spenpo.com/resume)
- Shortcode support `[spenpo_resume]`
- Customizable display order
- Optional authentication for API access

## Installation

1. Upload the plugin files to the `/wp-content/plugins/spenpo-resume` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Resume Settings screen to configure auth settings
4. A GUI for interacting with the resume data is still under development. You must alter or replace the default data directly in the database for now.

## Requirements

- WordPress 6.6 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Usage

### Shortcode
Add your resume to any post or page using the shortcode:

```php
[spenpo_resume]
```

### REST API
Access your resume data through the REST API endpoint:

GET /wp-json/spenpo/v1/resume

### Authentication
If API authentication is enabled in settings, include a WordPress nonce:

GET /wp-json/spenpo/v1/resume?wpnonce=YOUR_NONCE

## Database Structure

The plugin creates the following tables:
- `{prefix}resume_sections`: Main sections table
- `{prefix}resume_section_text_content`: Text content
- `{prefix}resume_section_items`: List items
- `{prefix}resume_nested_sections`: Nested section headers
- `{prefix}resume_nested_section_details`: Nested section details

## Configuration

1. Navigate to Settings -> Resume Settings in your WordPress admin panel
2. Configure the following options:
   - API Authentication requirement
   - Other settings as needed

## Development

### Prerequisites
- Node.js and npm for building blocks
- Composer for PHP dependencies
- WordPress development environment

### Building

```bash
# Install dependencies

npm install

# Build blocks

npm run build
```

## Hooks and Filters

### Actions
- `spcv_before_render`: Fires before the resume is rendered
  - Parameters: `array $sections` - The resume sections data
- `spcv_after_render`: Fires after the resume is rendered
  - Parameters: 
    - `string $html` - The final HTML output
    - `array $sections` - The resume sections data

### Filters
- `spcv_html_output`: Filter the final HTML output
  - Parameters:
    - `string $html` - The generated HTML
    - `array $sections` - The resume sections data
  - Return: `string` - Modified HTML output

### Examples
```php
// Add a wrapper div around the resume
add_filter('spcv_html_output', function($html, $sections) {
return '<div class="my-custom-wrapper">' . $html . '</div>';
}, 10, 2);
// Log resume rendering
add_action('spcv_before_render', function($sections) {
error_log('Resume rendering started with ' . count($sections) . ' sections');
});
// Cache the rendered resume
add_action('spcv_after_render', function($html, $sections) {
wp_cache_set('resume_html', $html, 'spenpo_resume', 3600);
}, 10, 2);
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## Credits

Developed by [spenpo](https://spenpo.com) 

## Support

For support, please [create an issue](https://github.com/spope851/spenpo-resume/issues) on our GitHub repository.