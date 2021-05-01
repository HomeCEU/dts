<?php


namespace HomeCEU\DTS\Render;


class RenderHelper {
  public static function extractPartials(string $template): array  {
    // finds strings matching {{> any-text-or-whitespace }}
    preg_match_all('/{{>([^\}}]+)}}/', $template, $matches);
    return !empty($matches[1]) ? array_map('trim', $matches[1]) : [];
  }
}
