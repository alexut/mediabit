<?php
// this idea is so stupid: to generate the font list from the css file
// i won't use it, but i will keep it here for future reference of stupidity.

function extract_first_font($font_family) {
    // Remove any quotes
    $font_family = str_replace(["'", '"'], '', $font_family);

    // Split the font-families by comma
    $fonts = explode(',', $font_family);

    // Return the first font-family after trimming any whitespace
    return trim($fonts[0]);
}

function get_bootstrap_fonts($variables_scss_path) {
    $content = file_get_contents($variables_scss_path);
    $regex = '/(\$font-family-base|\$font-weight-base|\$headings-font-family|\$headings-font-style|\$headings-font-weight):\s*([^;]+);/';

    preg_match_all($regex, $content, $matches, PREG_SET_ORDER);

    $fonts = [];
    foreach ($matches as $match) {
        $property = str_replace('$', '', $match[1]);
        if (strpos($property, 'font-family') !== false) {
            $fonts[$property] = extract_first_font($match[2]);
        } else {
            $fonts[$property] = trim($match[2]);
        }
    }

    // Set a default value for font-weight-base if it's not found
    if (!isset($fonts['font-weight-base'])) {
        $fonts['font-weight-base'] = '400';
    }
    return $fonts;
}

$variables_file_path = get_template_directory() . '/sources/scss/_bscore_variables.scss';
$fonts = get_bootstrap_fonts($variables_file_path);

print_r($fonts);
