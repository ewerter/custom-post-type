                # Custom Type Plugin

                The Custom Type Plugin is a WordPress plugin that creates a custom post type and taxonomy for managing parks.

                ## Installation

                1. Download the plugin zip file.
                2. Extract the contents to the `wp-content/plugins/custom-type-plugin` directory.
                3. Activate the plugin through the WordPress admin dashboard.

                ## Features

                - Creates a custom post type called "Parks" with support for title, editor, author, thumbnail, and custom fields.
                - Registers custom fields for the park post type: name, location, hours, and short description.
                - Enqueues a JavaScript file (`custom-type-plugin.js`) for Gutenberg compatibility. (Future Improvement)
                - Enqueues a CSS file (`style.css`) for custom styling.
                - Adds a meta box for editing park details.
                - Saves the custom field values when the park is saved.
                - Registers a custom taxonomy called "Facilities" for the park post type.
                - Provides a shortcode `[park_list]` to display a list of parks with customizable attributes.

                ## Usage

                To display a list of parks, use the `[park_list]` shortcode. You can customize the output by passing attributes to the shortcode. Available attributes are:

                - `posts_per_page`: The number of parks to display. Default is -1 (show all).
                - `orderby`: The field to order the parks by. Default is "date".
                - `order`: The order of the parks. Default is "DESC" (descending).

                Example usage: `[park_list posts_per_page="5" orderby="title" order="ASC"]`

                ## License

                This plugin is licensed under the GPL-2.0 License. See the [LICENSE](LICENSE) file for more details.
