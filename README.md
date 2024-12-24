# TiffyCooks AMP Recipe Theme

A specialized WordPress theme for food bloggers with integrated AdSense and AMP support. Built for optimal ad placement and recipe presentation with AI-powered recipe generation.

## Features

- 🚀 AMP-ready for fast loading and better SEO
- 💰 Optimized AdSense integration
- 🤖 AI-powered recipe generation
- 📱 Fully responsive design
- 📝 Schema.org recipe markup
- 🎥 Video support for recipes
- 📊 Recipe metadata fields (prep time, cook time, servings, etc.)
- 🔍 SEO-optimized structure

## Installation

1. Download the theme zip file
2. In your WordPress admin panel, go to Appearance → Themes
3. Click "Add New"
4. Click "Upload Theme"
5. Upload the zip file
6. Activate the theme

## Configuration

### OpenAI API Key

1. Get your OpenAI API key from [OpenAI's platform](https://platform.openai.com)
2. Add the following line to your `wp-config.php` file:
```php
define('TIFFYCOOKS_OPENAI_API_KEY', 'your-api-key-here');
```

### AdSense Integration

1. In your WordPress admin panel, go to Settings → TiffyCooks Settings
2. Enter your AdSense publisher ID
3. Save changes

## Using the Recipe Generator

1. Create a new post
2. Write your recipe content in the post editor
3. Click the "Generate Recipe" button above the recipe meta box
4. Review and edit the generated recipe data
5. Update/Publish your post

## Theme Structure

```
tiffycooks-amp-theme/
├── inc/
│   ├── recipe-agent.php      # AI recipe generation
│   ├── recipe-schema.php     # Schema.org markup
│   └── templates/            # Meta box templates
├── js/
│   └── admin.js             # Admin interface scripts
├── style.css                # Theme styles
├── functions.php            # Theme functions
├── index.php               # Main template
├── single.php              # Single post template
├── header.php              # Header template
└── footer.php              # Footer template
```

## Development

### Prerequisites

- WordPress 5.0+
- PHP 7.4+
- OpenAI API key
- Google AdSense account

### Local Development

1. Clone the repository:
```bash
git clone https://github.com/paulchrisluke/Adsense-Recipe-Food-Blog-AI-Agent.git
```

2. Link to your WordPress themes directory:
```bash
ln -s /path/to/repo /path/to/wp-content/themes/tiffycooks-amp-theme
```

### Building

The theme is ready to use as is, no build step required.

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

Distributed under the GPL v2 or later. See `LICENSE` for more information.

## Support

For support, please visit [TiffyCooks.com](https://tiffycooks.com) or open an issue in this repository.

## Acknowledgments

- Built with WordPress
- Powered by OpenAI's GPT-4
- AMP Project
- Google AdSense 