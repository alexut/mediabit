<?php

namespace Mediabit\Templates\Partials;

class Avatar {
    private function get_initials_color($initials) {
        // Hash the initials to get a random but consistent color
        $hash = md5($initials);
        $r = hexdec(substr($hash, 0, 2));
        $g = hexdec(substr($hash, 2, 2));
        $b = hexdec(substr($hash, 4, 2));
        
        // Adjust the color to be more pastel-like
        $r = ($r + 255) / 2;
        $g = ($g + 255) / 2;
        $b = ($b + 255) / 2;

        $primary_color = 'rgb('.$r.','.$g.','.$b.')';
        // find a high contrast color
        $luminance = 0.2126 * pow($r / 255, 2.2) + 0.7152 * pow($g / 255, 2.2) + 0.0722 * pow($b / 255, 2.2);
        if ($luminance > 0.179) {
          $luminance = '#000';
        } else {
          $luminance = '#fff';
        }
        return array($primary_color, $luminance);
    }

    private function get_initials($name) {
        $words = explode(' ', $name);
        // keep first two words
        if (count($words) > 2) {
          $words = array_slice($words, 0, 2);
        }
        //  if there is only one word take the first two letters of the word
        if (count($words) == 1) {
          return strtoupper(substr($words[0], 0, 2));
        }
        $initials = '';
        foreach ($words as $word) {
          $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }

    // function to get user avatar
  private function get_user_avatar($user_id) {
    $image = get_field('avatar', 'user_'.$user_id);
    if ($image) {
      return '<span class="rounded-circle d-inline-block" style="background-image: url('.$image['url'].');"></span>';
    } else {
      $name = get_the_author_meta('display_name', $user_id);
      $initials = $this->get_initials($name);
      $color = $this->get_initials_color($initials);
      return '<span class="rounded-circle d-inline-block p-3" style="background-color: '.$color[0].';color: '.$color[1].'">'.$initials.'</span>';
    }
  }

  public function render()
    {
        $user_id = get_current_user_id();
        $avatarHtml = $this->get_user_avatar($user_id);
        return $avatarHtml;
    }

}